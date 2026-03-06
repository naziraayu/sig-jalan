<?php

namespace App\Exports;

use App\Models\Alignment;

class AlignmentKmlExport
{
    protected $filters;

    /**
     * $filters bisa berisi:
     *   - province_code
     *   - kabupaten_code
     *   - link_no
     *   - year
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Generate dan return response KML
     */
    public function download(string $filename = 'alignment.kml')
    {
        $kml = $this->generate();

        return response($kml, 200, [
            'Content-Type'        => 'application/vnd.google-earth.kml+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate string KML dari data Alignment
     */
    public function generate(): string
    {
        // Ambil data alignment, dikelompokkan per link_no + year
        $query = Alignment::query()
            ->select([
                'province_code',
                'kabupaten_code',
                'link_no',
                'year',
                'chainage',
                'east',
                'north',
                'section_wkt_linestring',
            ])
            ->whereNotNull('east')
            ->whereNotNull('north')
            ->orderBy('link_no')
            ->orderBy('year')
            ->orderBy('chainage');

        // Terapkan filter jika ada
        if (!empty($this->filters['province_code'])) {
            $query->where('province_code', $this->filters['province_code']);
        }
        if (!empty($this->filters['kabupaten_code'])) {
            $query->where('kabupaten_code', $this->filters['kabupaten_code']);
        }
        if (!empty($this->filters['link_no'])) {
            $query->where('link_no', $this->filters['link_no']);
        }
        if (!empty($this->filters['year'])) {
            $query->where('year', $this->filters['year']);
        }

        $rows = $query->get();

        // Kelompokkan titik per link_no + year agar jadi satu LineString per ruas
        $grouped = $rows->groupBy(function ($row) {
            return $row->link_no . '_' . $row->year;
        });

        // Mulai build KML
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<kml xmlns="http://www.opengis.net/kml/2.2">' . "\n";
        $xml .= '  <Document>' . "\n";
        $xml .= '    <name>Alignment Jalan</name>' . "\n";
        $xml .= '    <description>Data alignment jalan yang diekspor dari sistem</description>' . "\n";

        // Style untuk garis jalan
        $xml .= '    <Style id="roadStyle">' . "\n";
        $xml .= '      <LineStyle>' . "\n";
        $xml .= '        <color>ff0000ff</color>' . "\n"; // merah (AABBGGRR)
        $xml .= '        <width>3</width>' . "\n";
        $xml .= '      </LineStyle>' . "\n";
        $xml .= '    </Style>' . "\n";

        foreach ($grouped as $key => $points) {
            $first         = $points->first();
            $linkNo        = $first->link_no;
            $year          = $first->year;
            $provinceCode  = $first->province_code;
            $kabupatenCode = $first->kabupaten_code;
            $pointCount    = $points->count();

            // Cek apakah ada WKT LineString yang bisa langsung dipakai
            $wktPoint = $points->first(fn($p) => !empty($p->section_wkt_linestring));

            $xml .= '    <Placemark>' . "\n";
            $xml .= '      <name>' . htmlspecialchars('Link ' . $linkNo . ' (' . $year . ')') . '</name>' . "\n";
            $xml .= '      <styleUrl>#roadStyle</styleUrl>' . "\n";

            // Extended data (atribut)
            $xml .= '      <ExtendedData>' . "\n";
            $xml .= '        <Data name="link_no"><value>'        . htmlspecialchars($linkNo)        . '</value></Data>' . "\n";
            $xml .= '        <Data name="year"><value>'           . htmlspecialchars($year)           . '</value></Data>' . "\n";
            $xml .= '        <Data name="province_code"><value>'  . htmlspecialchars($provinceCode)  . '</value></Data>' . "\n";
            $xml .= '        <Data name="kabupaten_code"><value>' . htmlspecialchars($kabupatenCode) . '</value></Data>' . "\n";
            $xml .= '        <Data name="jumlah_titik"><value>'   . $pointCount                      . '</value></Data>' . "\n";
            $xml .= '      </ExtendedData>' . "\n";

            // Pilih cara render geometri:
            // Prioritas 1 → pakai WKT LineString kalau ada
            // Prioritas 2 → bangun dari kumpulan titik east/north
            if ($wktPoint && $this->isValidWkt($wktPoint->section_wkt_linestring)) {
                $coordinates = $this->wktToKmlCoordinates($wktPoint->section_wkt_linestring);
            } else {
                $coordinates = $this->buildCoordinatesFromPoints($points);
            }

            if (!empty($coordinates)) {
                $xml .= '      <LineString>' . "\n";
                $xml .= '        <tessellate>1</tessellate>' . "\n";
                $xml .= '        <coordinates>' . "\n";
                $xml .= '          ' . $coordinates . "\n";
                $xml .= '        </coordinates>' . "\n";
                $xml .= '      </LineString>' . "\n";
            }

            $xml .= '    </Placemark>' . "\n";
        }

        $xml .= '  </Document>' . "\n";
        $xml .= '</kml>';

        return $xml;
    }

    /**
     * Bangun koordinat KML dari kumpulan titik east/north
     * Format KML: longitude,latitude,altitude (spasi antar titik)
     */
    protected function buildCoordinatesFromPoints($points): string
    {
        $coords = [];

        foreach ($points as $point) {
            if ($point->east !== null && $point->north !== null) {
                // east = longitude, north = latitude
                $lng = number_format((float) $point->east,  8, '.', '');
                $lat = number_format((float) $point->north, 8, '.', '');
                $coords[] = "{$lng},{$lat},0";
            }
        }

        return implode(' ', $coords);
    }

    /**
     * Konversi WKT LineString ke format koordinat KML
     * Input  : "LINESTRING (106.75 -6.50, 106.76 -6.51)"
     * Output : "106.75,-6.50,0 106.76,-6.51,0"
     */
    protected function wktToKmlCoordinates(string $wkt): string
    {
        // Ambil bagian koordinat dari dalam tanda kurung
        if (!preg_match('/LINESTRING\s*\((.+)\)/i', $wkt, $matches)) {
            return '';
        }

        $coordString = trim($matches[1]);
        $pairs       = explode(',', $coordString);
        $coords      = [];

        foreach ($pairs as $pair) {
            $parts = preg_split('/\s+/', trim($pair));
            if (count($parts) >= 2) {
                $lng      = number_format((float) $parts[0], 8, '.', '');
                $lat      = number_format((float) $parts[1], 8, '.', '');
                $coords[] = "{$lng},{$lat},0";
            }
        }

        return implode(' ', $coords);
    }

    /**
     * Cek apakah string WKT valid untuk LineString
     */
    protected function isValidWkt(?string $wkt): bool
    {
        if (empty($wkt)) return false;
        return (bool) preg_match('/LINESTRING\s*\(.+\)/i', $wkt);
    }
}