<?php

namespace App\Imports;

use App\Models\Alignment;
use App\Models\LinkMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlignmentKmlImport
{
    protected $importedCount  = 0;
    protected $skippedCount   = 0;
    protected $errors         = [];
    protected $linkMasterCache = [];

    /**
     * Proses file KML yang diupload
     *
     * @param  string $filePath  Path file KML di storage/tmp
     * @return $this
     */
    public function import(string $filePath): self
    {
        $this->importedCount  = 0;
        $this->skippedCount   = 0;
        $this->errors         = [];
        $this->linkMasterCache = [];

        if (!file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan: {$filePath}";
            return $this;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->errors[] = "Gagal membaca file KML.";
            return $this;
        }

        // Suppress warning dari XML yang kurang valid, tangkap lewat libxml
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $xmlErrors = libxml_get_errors();
            libxml_clear_errors();
            $this->errors[] = "File bukan XML/KML yang valid: " . ($xmlErrors[0]->message ?? 'Unknown error');
            return $this;
        }

        libxml_clear_errors();

        // Register namespace KML agar XPath bisa dipakai
        $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

        // Cari semua Placemark — coba dengan namespace dulu, fallback tanpa namespace
        $placemarks = $xml->xpath('//kml:Placemark');
        if (empty($placemarks)) {
            $placemarks = $xml->xpath('//Placemark');
        }

        if (empty($placemarks)) {
            $this->errors[] = "Tidak ada Placemark yang ditemukan di file KML.";
            return $this;
        }

        DB::beginTransaction();

        try {
            foreach ($placemarks as $placemark) {
                $this->processSinglePlacemark($placemark);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Import KML Alignment gagal: ' . $e->getMessage());
            $this->errors[] = "Import dibatalkan karena error: " . $e->getMessage();
            $this->importedCount = 0;
        }

        return $this;
    }

    /**
     * Proses satu Placemark dari KML
     * Satu Placemark = satu ruas jalan (LineString)
     * LineString akan dipecah menjadi banyak baris Alignment (per titik koordinat)
     */
    protected function processSinglePlacemark($placemark): void
    {
        // ── 1. Ambil atribut dari ExtendedData ──────────────────────────────
        $linkNo        = null;
        $year          = null;
        $provinceCode  = null;
        $kabupatenCode = null;

        if (!empty($placemark->ExtendedData)) {
            foreach ($placemark->ExtendedData->Data as $data) {
                $name  = strtolower((string) $data['name']);
                $value = (string) $data->value;

                switch ($name) {
                    case 'link_no':        $linkNo        = $value; break;
                    case 'year':           $year          = $value; break;
                    case 'province_code':  $provinceCode  = $value; break;
                    case 'kabupaten_code': $kabupatenCode = $value; break;
                }
            }
        }

        // Fallback: coba ambil dari <name> tag kalau ExtendedData kosong
        // Format nama Placemark dari export kita: "Link 001 (2024)"
        if (empty($linkNo) || empty($year)) {
            $name = trim((string) $placemark->name);
            if (preg_match('/Link\s+(\S+)\s+\((\d{4})\)/i', $name, $m)) {
                $linkNo = $linkNo ?: $m[1];
                $year   = $year   ?: $m[2];
            }
        }

        // ── 2. Validasi field wajib ─────────────────────────────────────────
        if (empty($linkNo)) {
            $this->skippedCount++;
            $this->errors[] = "Placemark dilewati: link_no tidak ditemukan di ExtendedData maupun nama Placemark.";
            return;
        }
        if (empty($year)) {
            $this->skippedCount++;
            $this->errors[] = "Placemark dilewati: year tidak ditemukan (link_no: {$linkNo}).";
            return;
        }
        if (empty($provinceCode) || empty($kabupatenCode)) {
            $this->skippedCount++;
            $this->errors[] = "Placemark dilewati: province_code atau kabupaten_code tidak ditemukan (link_no: {$linkNo}).";
            return;
        }

        // ── 3. Lookup link_master_id ─────────────────────────────────────────
        $linkMasterId = $this->getLinkMasterId($linkNo, $provinceCode, $kabupatenCode);
        if (!$linkMasterId) {
            $this->skippedCount++;
            $this->errors[] = "Placemark dilewati: link_no '{$linkNo}' tidak ditemukan di link_master. Import Ruas Jalan terlebih dahulu.";
            return;
        }

        // ── 4. Ambil koordinat LineString ────────────────────────────────────
        $coordinatesRaw = $this->extractLineStringCoordinates($placemark);
        if (empty($coordinatesRaw)) {
            $this->skippedCount++;
            $this->errors[] = "Placemark dilewati: tidak ada koordinat LineString ditemukan (link_no: {$linkNo}).";
            return;
        }

        // ── 5. Parse koordinat dan simpan per titik ──────────────────────────
        $points  = $this->parseKmlCoordinates($coordinatesRaw);
        $chainage = 0; // mulai dari chainage 0, naik per titik

        foreach ($points as $point) {
            Alignment::create([
                'province_code'          => $provinceCode,
                'kabupaten_code'         => $kabupatenCode,
                'link_master_id'         => $linkMasterId,
                'link_no'                => $linkNo,
                'year'                   => (int) $year,
                'chainage'               => $chainage,
                'east'                   => $point['lng'],  // east  = longitude
                'north'                  => $point['lat'],  // north = latitude
                'section_wkt_linestring' => null,           // tidak disimpan per titik
                'chainage_rb'            => null,
                'gpspoint_north_deg'     => null,
                'gpspoint_north_min'     => null,
                'gpspoint_north_sec'     => null,
                'gpspoint_east_deg'      => null,
                'gpspoint_east_min'      => null,
                'gpspoint_east_sec'      => null,
            ]);

            $chainage++;
            $this->importedCount++;
        }
    }

    /**
     * Cari koordinat LineString dari dalam Placemark
     * Menangani nested struktur seperti <MultiGeometry>
     */
    protected function extractLineStringCoordinates($placemark): ?string
    {
        // Kasus 1: LineString langsung di Placemark
        if (!empty($placemark->LineString->coordinates)) {
            return trim((string) $placemark->LineString->coordinates);
        }

        // Kasus 2: Di dalam MultiGeometry
        if (!empty($placemark->MultiGeometry)) {
            foreach ($placemark->MultiGeometry->LineString as $ls) {
                if (!empty($ls->coordinates)) {
                    return trim((string) $ls->coordinates);
                }
            }
        }

        // Kasus 3: Coba XPath
        $placemark->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
        $coords = $placemark->xpath('.//kml:LineString/kml:coordinates');
        if (empty($coords)) {
            $coords = $placemark->xpath('.//LineString/coordinates');
        }
        if (!empty($coords)) {
            return trim((string) $coords[0]);
        }

        return null;
    }

    /**
     * Parse string koordinat KML menjadi array titik
     *
     * Format KML  : "106.75,-6.50,0 106.76,-6.51,0 ..."
     * Format hasil: [['lng' => 106.75, 'lat' => -6.50], ...]
     */
    protected function parseKmlCoordinates(string $raw): array
    {
        $points = [];

        // Pisah per spasi/newline
        $tokens = preg_split('/[\s\n\r\t,]+/', trim($raw));

        // Setiap 2 atau 3 token = satu titik (lng, lat, alt)
        // KML format: longitude,latitude,altitude → tapi kalau sudah pisah jadi token tunggal
        // perlu deteksi apakah masih dalam format "lng,lat,alt" atau sudah terpisah

        // Cek apakah raw masih berformat "lng,lat,alt lng,lat,alt"
        if (strpos($raw, ',') !== false) {
            // Format: "106.75,-6.50,0 106.76,-6.51,0"
            $triplets = preg_split('/\s+/', trim($raw));
            foreach ($triplets as $triplet) {
                $triplet = trim($triplet);
                if (empty($triplet)) continue;

                $parts = explode(',', $triplet);
                if (count($parts) >= 2) {
                    $lng = (float) $parts[0];
                    $lat = (float) $parts[1];

                    // Validasi range koordinat Indonesia
                    if ($this->isValidCoordinate($lng, $lat)) {
                        $points[] = ['lng' => $lng, 'lat' => $lat];
                    }
                }
            }
        }

        return $points;
    }

    /**
     * Validasi koordinat masuk dalam range Indonesia
     * Longitude : 95° - 141° BT
     * Latitude  : -11° - 6° LS/LU
     */
    protected function isValidCoordinate(float $lng, float $lat): bool
    {
        return $lng >= 95.0  && $lng <= 141.0
            && $lat >= -11.0 && $lat <= 6.0;
    }

    /**
     * Lookup link_master_id dengan caching agar tidak query berulang
     */
    protected function getLinkMasterId(string $linkNo, string $provinceCode, string $kabupatenCode): ?int
    {
        $cacheKey = "{$linkNo}_{$provinceCode}";

        if (!isset($this->linkMasterCache[$cacheKey])) {
            // ✅ FIX: kabupaten_code di tabel link_master bisa null/kosong,
            // cukup lookup berdasarkan link_no saja (sudah unik)
            $linkMaster = LinkMaster::where('link_no', $linkNo)
                ->select('id')
                ->first();

            $this->linkMasterCache[$cacheKey] = $linkMaster ? $linkMaster->id : null;
        }

        return $this->linkMasterCache[$cacheKey];
    }

    // ── Getters ─────────────────────────────────────────────────────────────

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount():  int { return $this->skippedCount;  }
    public function getErrors():      array { return $this->errors;        }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getSummary(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped'  => $this->skippedCount,
            'errors'   => $this->errors,
        ];
    }
}