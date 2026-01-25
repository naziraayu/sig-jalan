<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\CodeTerrain;
use Illuminate\Http\Request;
use App\Models\RoadCondition;
use App\Models\RoadInventory;
use App\Models\CodeImpassable;
use App\Models\CodeLinkStatus;
use App\Models\CodePavementType;
use App\Models\CodeDrainCondition;
use App\Models\CodeSlopeCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CodeShoulderCondition;
use App\Models\CodeFoothpathCondition;
use App\Services\SDICalculator; // ✅ Import Service

class RoadConditionController extends Controller
{
    // ========================================
    // EXISTING METHODS (tidak berubah)
    // ========================================
    
    public function index()
    { 
        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi   = Province::orderBy('province_name')->get();
        $kabupaten  = Kabupaten::orderBy('kabupaten_name')->get();

        $selectedYear = session('selected_year');
        
        $availableYears = RoadCondition::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('jalan.kondisi-jalan.index', compact(
            'statusRuas', 'provinsi', 'kabupaten', 'availableYears', 'selectedYear'
        ));
    }

    public function create()
    {
        $selectedYear = session('selected_year');
        
        if (!$selectedYear) {
            return redirect()->route('kondisi-jalan.index')
                ->with('error', 'Silakan pilih tahun terlebih dahulu menggunakan filter tahun di pojok kanan atas.');
        }

        $referenceYear = $selectedYear - 1;

        $ruasJalan = Link::with(['linkMaster', 'province', 'kabupaten'])
            ->where('year', $referenceYear)
            ->orderBy('link_code')
            ->get()
            ->unique('link_no');

        $pavementTypes = CodePavementType::orderBy('code')->get();
        $terrainTypes = CodeTerrain::orderBy('code')->get();
        $impassableReasons = CodeImpassable::orderBy('code')->get();
        $shoulderConditions = CodeShoulderCondition::orderBy('code')->get();
        $drainConditions = CodeDrainCondition::orderBy('code')->get();
        $slopeConditions = CodeSlopeCondition::orderBy('code')->get();
        $foothpathConditions = CodeFoothpathCondition::orderBy('code')->get();

        return view('jalan.kondisi-jalan.create', compact(
            'ruasJalan',
            'selectedYear',
            'referenceYear',
            'pavementTypes',
            'terrainTypes',
            'impassableReasons',
            'shoulderConditions',
            'drainConditions',
            'slopeConditions',
            'foothpathConditions'
        ));
    }

    public function getRuasByYear(Request $request)
    {
        $surveyYear = $request->get('year');
        
        if (!$surveyYear) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak valid',
                'data' => []
            ]);
        }

        try {
            $referenceYear = $surveyYear - 1;

            $linkNos = RoadCondition::where('year', $surveyYear)
                ->distinct()
                ->pluck('link_no')
                ->filter()
                ->unique()
                ->values();

            if ($linkNos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data ruas untuk tahun ' . $surveyYear,
                    'data' => []
                ]);
            }

            $ruasList = Link::whereIn('link_no', $linkNos)
                ->where('year', $referenceYear)
                ->orderBy('link_code')
                ->get()
                ->unique('link_no')
                ->values();

            if ($ruasList->isEmpty()) {
                $ruasList = Link::whereIn('link_no', $linkNos)
                    ->orderBy('year', 'desc')
                    ->orderBy('link_code')
                    ->get()
                    ->unique('link_no')
                    ->values();
            }

            $ruasList = $ruasList->map(function($link) {
                return [
                    'link_no' => $link->link_no,
                    'link_code' => $link->link_code ?? $link->link_no,
                    'link_name' => $link->link_name ?? 'Ruas ' . $link->link_no,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $ruasList->values(),
                'count' => $ruasList->count(),
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear
            ]);

        } catch (\Exception $e) {
            Log::error('Error getRuasByYear: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getChainageByRuas(Request $request)
    {
        $linkNo = $request->get('link_no');
        $surveyYear = $request->get('year');
        $mode = $request->get('mode', 'create');

        if (!$linkNo) {
            return response()->json([
                'success' => false,
                'message' => 'Link No tidak valid'
            ]);
        }

        try {
            $chainageList = collect();

            $existingConditions = RoadCondition::where('link_no', $linkNo)
                ->where('year', $surveyYear)
                ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
                ->get();

            if ($existingConditions->isNotEmpty()) {
                $chainageList = $existingConditions->map(function($cond) {
                    return [
                        'chainage_from' => floatval($cond->chainage_from),
                        'chainage_to' => floatval($cond->chainage_to),
                        'pave_width' => floatval($cond->inventory->pave_width ?? 0),
                        'pave_type' => $cond->inventory->pave_type ?? null,
                        'has_condition_data' => true,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $chainageList,
                    'count' => $chainageList->count(),
                    'source' => 'condition'
                ]);
            }

            $inventories = RoadInventory::where('link_no', $linkNo)
                ->when($surveyYear, function($query) use ($surveyYear) {
                    return $query->where('year', $surveyYear);
                })
                ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
                ->get();

            if ($inventories->isEmpty() && $surveyYear) {
                $inventories = RoadInventory::where('link_no', $linkNo)
                    ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
                    ->get();
            }

            if ($inventories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data inventarisasi untuk ruas ini.',
                    'data' => []
                ]);
            }

            $chainageList = $inventories->map(function($inv) {
                return [
                    'chainage_from' => floatval($inv->chainage_from),
                    'chainage_to' => floatval($inv->chainage_to),
                    'pave_width' => floatval($inv->pave_width ?? 0),
                    'pave_type' => $inv->pave_type,
                    'has_condition_data' => false,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $chainageList,
                'count' => $chainageList->count(),
                'source' => 'inventory'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getChainageByRuas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $surveySetup = $request->input('survey_setup');
            $conditionData = $request->input('condition_data');

            if (!$surveySetup || !$conditionData || count($conditionData) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ], 400);
            }

            $surveyYear = $surveySetup['year'];
            $referenceYear = $surveyYear - 1;
            $linkNo = $surveySetup['link_no'];
            $linkId = $surveySetup['link_id'];

            $hasInventory = RoadInventory::where('link_no', $linkNo)->exists();

            if (!$hasInventory) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Ruas ini belum memiliki data inventarisasi.'
                ], 400);
            }

            $link = Link::where('link_no', $linkNo)
                ->where('year', $referenceYear)
                ->first();

            if (!$link) {
                $link = Link::where('link_no', $linkNo)
                    ->orderBy('year', 'desc')
                    ->first();
            }

            if (!$link) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data ruas tidak ditemukan'
                ], 400);
            }

            $savedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($conditionData as $data) {
                try {
                    $exists = RoadCondition::where('link_no', $linkNo)
                        ->where('year', $surveyYear)
                        ->where('chainage_from', $data['chainage_from'])
                        ->where('chainage_to', $data['chainage_to'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }

                    $conditionRecord = [
                        'year' => $surveyYear,
                        'reference_year' => $referenceYear,
                        'link_id' => $linkId,
                        'link_no' => $linkNo,
                        'province_code' => $link->province_code,
                        'kabupaten_code' => $link->kabupaten_code,
                        'chainage_from' => $data['chainage_from'],
                        'chainage_to' => $data['chainage_to'],
                        'survey_by' => $surveySetup['surveyor_name'] ?? null,
                        'survey_by2' => $surveySetup['surveyor_name_2'] ?? null,
                        'survey_date' => $surveySetup['survey_date'] ?? null,
                        
                        // Data kerusakan
                        'roughness' => $data['roughness'] ?? null,
                        'bleeding_area' => $data['bleeding_area'] ?? null,
                        'ravelling_area' => $data['ravelling_area'] ?? null,
                        'desintegration_area' => $data['desintegration_area'] ?? null,
                        'crack_dep_area' => $data['crack_dep_area'] ?? null,
                        'patching_area' => $data['patching_area'] ?? null,
                        'oth_crack_area' => $data['oth_crack_area'] ?? null,
                        'pothole_area' => $data['pothole_area'] ?? null,
                        'pothole_count' => $data['pothole_count'] ?? null,
                        'rutting_area' => $data['rutting_area'] ?? null,
                        'rutting_depth' => $data['rutting_depth'] ?? null,
                        'edge_damage_area' => $data['edge_damage_area'] ?? null,
                        'crack_width' => $data['crack_width'] ?? null,
                        'crossfall_area' => $data['crossfall_area'] ?? null,
                        'depressions_area' => $data['depressions_area'] ?? null,
                        'erosion_area' => $data['erosion_area'] ?? null,
                        'waviness_area' => $data['waviness_area'] ?? null,
                        'gravel_thickness_area' => $data['gravel_thickness_area'] ?? null,
                        'concrete_cracking_area' => $data['concrete_cracking_area'] ?? null,
                        'concrete_spalling_area' => $data['concrete_spalling_area'] ?? null,
                        'concrete_structural_cracking_area' => $data['concrete_structural_cracking_area'] ?? null,
                        'concrete_corner_break_no' => $data['concrete_corner_break_no'] ?? null,
                        'concrete_pumping_no' => $data['concrete_pumping_no'] ?? null,
                        'concrete_blowouts_area' => $data['concrete_blowouts_area'] ?? null,
                        
                        // Kondisi bahu, drainase, dll
                        'shoulder_l' => $data['shoulder_l'] ?? null,
                        'shoulder_r' => $data['shoulder_r'] ?? null,
                        'drain_l' => $data['drain_l'] ?? null,
                        'drain_r' => $data['drain_r'] ?? null,
                        'slope_l' => $data['slope_l'] ?? null,
                        'slope_r' => $data['slope_r'] ?? null,
                        'footpath_l' => $data['footpath_l'] ?? null,
                        'footpath_r' => $data['footpath_r'] ?? null,
                        
                        'iri' => $data['iri'] ?? null,
                        'rci' => $data['rci'] ?? null,
                        
                        // ✅ SDI akan di-calculate otomatis oleh Observer
                        'sdi_value' => null,
                        'sdi_category' => null,
                    ];

                    RoadCondition::create($conditionRecord);
                    // ☝️ Observer akan auto-calculate SDI dan save ke DB
                    
                    $savedCount++;

                } catch (\Exception $e) {
                    Log::error('Error saving individual condition record', [
                        'link_no' => $linkNo,
                        'chainage_from' => $data['chainage_from'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    $errors[] = [
                        'chainage' => ($data['chainage_from'] ?? '?') . ' - ' . ($data['chainage_to'] ?? '?'),
                        'error' => $e->getMessage()
                    ];
                    $skippedCount++;
                }
            }

            DB::commit();

            $message = "Berhasil menyimpan {$savedCount} data kondisi jalan untuk tahun {$surveyYear}";
            if ($skippedCount > 0) {
                $message .= ". {$skippedCount} data dilewati (duplikat atau error).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'saved_count' => $savedCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing road condition: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($linkNo, $chainageFrom, $chainageTo, $year)
    {
        try {
            $condition = RoadCondition::where('link_no', $linkNo)
                ->where('chainage_from', $chainageFrom)
                ->where('chainage_to', $chainageTo)
                ->where('year', $year)
                ->firstOrFail();

            $referenceYear = $year - 1;
            $link = Link::with(['linkMaster', 'province', 'kabupaten'])
                ->where('link_no', $linkNo)
                ->where('year', $referenceYear)
                ->first();

            if (!$link) {
                $link = Link::with(['linkMaster', 'province', 'kabupaten'])
                    ->where('link_no', $linkNo)
                    ->orderBy('year', 'desc')
                    ->firstOrFail();
            }

            $inventory = RoadInventory::where('link_no', $linkNo)
                ->where('chainage_from', '<=', $chainageFrom)
                ->where('chainage_to', '>=', $chainageTo)
                ->first();

            $condition->inventory = $inventory;

            // Auto-detect data type berdasarkan field yang terisi
            $dataType = 'Aspal'; // Default
            
            if (!empty($condition->pavement)) {
                $pavementMap = [
                    'AS' => 'Aspal',
                    'BL' => 'Blok',
                    'BT' => 'Beton',
                    'NA' => 'Non Aspal',
                ];
                
                $dataType = $pavementMap[$condition->pavement] ?? 'Aspal';
            } else {
                // Auto-detect logic (existing)
                if ($condition->concrete_cracking_area > 0 || 
                    $condition->concrete_spalling_area > 0 || 
                    $condition->concrete_structural_cracking_area > 0) {
                    $dataType = 'Beton';
                }
                elseif ($condition->crossfall_area > 0 || 
                        $condition->gravel_thickness_area > 0) {
                    $dataType = 'Non Aspal';
                }
                elseif ($condition->desintegration_area > 0 && 
                        !$condition->bleeding_area && 
                        !$condition->ravelling_area) {
                    $dataType = 'Blok';
                }
            }

            return view('jalan.kondisi-jalan.edit', compact(
                'condition',
                'link',
                'inventory',
                'dataType'
            ));

        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());

            return redirect()->route('kondisi-jalan.index')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $linkNo, $chainageFrom, $chainageTo, $year)
    {
        try {
            DB::beginTransaction();

            $condition = RoadCondition::where('link_no', $linkNo)
                ->where('chainage_from', $chainageFrom)
                ->where('chainage_to', $chainageTo)
                ->where('year', $year)
                ->firstOrFail();

            $updateData = [
                'pavement' => $this->getDataTypeToPavementCode($request->input('data_type')),
                'survey_by' => $request->input('survey_by'),
                'survey_by2' => $request->input('survey_by2'),
                'survey_date' => $request->input('survey_date'),
                'roughness' => $request->input('roughness'),
                'bleeding_area' => $request->input('bleeding_area'),
                'ravelling_area' => $request->input('ravelling_area'),
                'desintegration_area' => $request->input('desintegration_area'),
                'patching_area' => $request->input('patching_area'),
                'crack_type' => $request->input('crack_type'),
                'crack_width' => $request->input('crack_width'),
                'oth_crack_area' => $request->input('oth_crack_area'),
                'crack_dep_area' => $request->input('crack_dep_area'),
                'edge_damage_area' => $request->input('edge_damage_area'),
                'edge_damage_area_r' => $request->input('edge_damage_area_r'),
                'pothole_count' => $request->input('pothole_count'),
                'pothole_size' => $request->input('pothole_size'),
                'pothole_area' => $request->input('pothole_area'),
                'rutting_area' => $request->input('rutting_area'),
                'rutting_depth' => $request->input('rutting_depth'),
                'concrete_cracking_area' => $request->input('concrete_cracking_area'),
                'concrete_spalling_area' => $request->input('concrete_spalling_area'),
                'concrete_structural_cracking_area' => $request->input('concrete_structural_cracking_area'),
                'concrete_blowouts_area' => $request->input('concrete_blowouts_area'),
                'concrete_pumping_no' => $request->input('concrete_pumping_no'),
                'concrete_corner_break_no' => $request->input('concrete_corner_break_no'),
                'should_cond_l' => $request->input('should_cond_l'),
                'crossfall_shape' => $request->input('crossfall_shape'),
                'crossfall_area' => $request->input('crossfall_area'),
                'depressions_area' => $request->input('depressions_area'),
                'erosion_area' => $request->input('erosion_area'),
                'waviness_area' => $request->input('waviness_area'),
                'gravel_size' => $request->input('gravel_size'),
                'gravel_thickness' => $request->input('gravel_thickness'),
                'gravel_thickness_area' => $request->input('gravel_thickness_area'),
                'distribution' => $request->input('distribution'),
                'iri' => $request->input('iri'),
                'rci' => $request->input('rci'),
            ];

            $updateData = array_filter($updateData, function($value) {
                return $value !== null && $value !== '';
            });

            $condition->update($updateData);
            // ☝️ Observer akan auto-recalculate SDI

            DB::commit();

            return redirect()->route('kondisi-jalan.index')
                ->with('success', 'Data kondisi jalan berhasil diupdate! SDI telah dihitung ulang.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating road condition: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getDataTypeToPavementCode($dataType)
    {
        $mapping = [
            'Aspal' => 'AS',
            'Blok' => 'BL',
            'Beton' => 'BT',
            'Non Aspal' => 'NA',
            'Tak Dapat Dilalui' => 'TD',
        ];
        
        return $mapping[$dataType] ?? 'AS';
    }

    public function destroy($linkNo, $chainageFrom, $chainageTo, $year)
    {
        try {
            DB::beginTransaction();

            $condition = RoadCondition::where('link_no', $linkNo)
                ->where('chainage_from', $chainageFrom)
                ->where('chainage_to', $chainageTo)
                ->where('year', $year)
                ->firstOrFail();

            $condition->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kondisi jalan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting road condition: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // ✅ UPDATED: BACA SDI DARI DATABASE
    // ========================================

    public function getYears(Request $request)
    {
        $linkNo = $request->get('link_no');
        
        if (!$linkNo) {
            return response()->json([
                'success' => false,
                'message' => 'Link No tidak valid'
            ]);
        }

        $years = RoadCondition::where('link_no', $linkNo)
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }
    
    public function getDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $year = $request->get('year');

        if (!$year) {
            $year = session('selected_year');
        }

        if (!$linkNo || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih ruas dan tahun terlebih dahulu',
            ]);
        }

        try {
            $conditions = RoadCondition::where('link_no', $linkNo)
                ->where('year', $year)
                ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
                ->get();

            if ($conditions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk ruas ' . $linkNo . ' tahun ' . $year,
                ]);
            }

            // ✅ LANGSUNG BACA DARI DATABASE (tidak calculate ulang)
            $dataWithSDI = $conditions->map(function($item) {
                return [
                    'chainage_from' => $item->chainage_from,
                    'chainage_to' => $item->chainage_to,
                    'year' => intval($item->year),
                    'iri' => $item->iri ? floatval($item->iri) : null,
                    'rci' => $item->rci ? floatval($item->rci) : null,
                    'sdi_final' => floatval($item->sdi_value ?? 0), // ✅ Dari DB
                    'sdi_category' => $item->sdi_category ?? 'Data Tidak Lengkap', // ✅ Dari DB
                    'link_no' => $item->link_no,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $dataWithSDI,
                'count' => $dataWithSDI->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getDetail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✅ PERBAIKAN: Langsung baca SDI dari database
     */
public function show($link_no)
{
    $selectedYear = session('selected_year');
    $referenceYear = $selectedYear ? $selectedYear - 1 : null;

    $ruas = Link::with(['province', 'kabupaten'])
        ->where('link_no', $link_no)
        ->when($referenceYear, function($query) use ($referenceYear) {
            return $query->where('year', $referenceYear);
        })
        ->orderBy('year', 'desc')
        ->first();

    if (!$ruas) {
        $ruas = Link::with(['province', 'kabupaten'])
            ->where('link_no', $link_no)
            ->orderBy('year', 'desc')
            ->firstOrFail();
    }

    $availableYears = RoadCondition::where('link_no', $link_no)
        ->select('year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    // ✅ PERBAIKAN 1: Tambah eager load 'inventory'
    $conditions = RoadCondition::where('link_no', $link_no)
        ->with('inventory')  // ✅ Tambahkan ini
        ->when($selectedYear, function($query) use ($selectedYear) {
            return $query->where('year', $selectedYear);
        })
        ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
        ->get();

    // ✅ PERBAIKAN 2: Calculate SDI1-SDI4 on-the-fly untuk tabel overview
    $conditionsWithSDI = $conditions->map(function($condition) {
        // Panggil SDICalculator dengan detailed = true
        $sdiDetail = SDICalculator::calculate($condition, true);
        
        $condition->sdi_data = [
            'sdi1' => $sdiDetail['sdi1'] ?? 0,
            'sdi2' => $sdiDetail['sdi2'] ?? 0,
            'sdi3' => $sdiDetail['sdi3'] ?? 0,
            'sdi_final' => $sdiDetail['sdi_final'] ?? 0,
            'category' => $sdiDetail['category'] ?? 'Data Tidak Lengkap',
        ];
        
        return $condition;
    });

    $statistics = [
        'total_segments' => $conditions->count(),
        'total_length' => $conditions->sum(function($item) {
            return $item->chainage_to - $item->chainage_from;
        }),
        'avg_iri' => $conditions->where('iri', '>', 0)->avg('iri'),
        'avg_rci' => $conditions->where('rci', '>', 0)->avg('rci'),
        'avg_sdi' => $conditions->avg('sdi_value'),
        'good_condition' => $conditions->where('sdi_category', 'Baik')->count(),
        'fair_condition' => $conditions->where('sdi_category', 'Sedang')->count(),
        'poor_condition' => $conditions->where('sdi_category', 'Rusak Ringan')->count(),
        'very_poor_condition' => $conditions->where('sdi_category', 'Rusak Berat')->count(),
    ];

    $damage_analysis = [
        'total_crack_area' => $conditions->sum('crack_dep_area') + $conditions->sum('oth_crack_area'),
        'total_bleeding_area' => $conditions->sum('bleeding_area'),
        'total_ravelling_area' => $conditions->sum('ravelling_area'),
        'total_desintegration_area' => $conditions->sum('desintegration_area'),
        'total_patching_area' => $conditions->sum('patching_area'),
        'total_pothole_area' => $conditions->sum('pothole_area'),
        'total_pothole_count' => $conditions->sum('pothole_count'),
        'total_potholes' => $conditions->sum('pothole_count'),
        'total_rutting_area' => $conditions->sum('rutting_area'),
        'avg_rutting_depth' => $conditions->avg('rutting_depth'),
        'total_edge_damage' => $conditions->sum('edge_damage_area'),
        'avg_crack_width' => $conditions->avg('crack_width'),
        'segments_with_bleeding' => $conditions->where('bleeding_area', '>', 0)->count(),
    ];

    $sdi_by_year = $conditions->groupBy('year')->map(function($items, $year) {
        return [
            'avg_sdi' => $items->avg('sdi_value'),
            'min_sdi' => $items->min('sdi_value'),
            'max_sdi' => $items->max('sdi_value'),
            'count' => $items->count(),
        ];
    })->sortKeysDesc();

    return view('jalan.kondisi-jalan.show', compact(
        'ruas',
        'conditions',
        'conditionsWithSDI',
        'statistics',
        'availableYears',
        'damage_analysis',
        'sdi_by_year'
    ));
}

    /**
     * ✅ PERBAIKAN: Pakai Service untuk detail calculation
     */
    public function getSegmentDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $chainageFrom = $request->get('chainage_from');
        $chainageTo = $request->get('chainage_to');
        $year = $request->get('year');

        if (!$linkNo || !$chainageFrom || !$chainageTo || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        try {
            $condition = RoadCondition::where('link_no', $linkNo)
                ->where('chainage_from', $chainageFrom)
                ->where('chainage_to', $chainageTo)
                ->where('year', $year)
                ->first();

            if (!$condition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            $referenceYear = $year - 1;
            $link = Link::where('link_no', $linkNo)
                ->where('year', $referenceYear)
                ->first();

            if (!$link) {
                $link = Link::where('link_no', $linkNo)
                    ->orderBy('year', 'desc')
                    ->first();
            }

            // ✅ PERBAIKAN: Panggil SDICalculator dengan detailed = true
            $sdiDetail = SDICalculator::calculate($condition, true);

            return response()->json([
                'success' => true,
                'data' => [
                    'condition' => [
                        'link_no' => [
                            'link_code' => $link->link_code ?? $linkNo,
                            'link_name' => $link->link_name ?? 'Ruas ' . $linkNo,
                        ],
                        'chainage_from' => $condition->chainage_from,
                        'chainage_to' => $condition->chainage_to,
                        'year' => $condition->year,
                    ],
                    'sdi_detail' => [
                        'raw_data' => array_merge(
                            $sdiDetail['raw_data'] ?? [],
                            [
                                'pavement_type' => $condition->pavement ?? 'AS',
                                'crack_width' => $sdiDetail['raw_data']['crack_width_bobot'] ?? 0,
                                'pothole_count' => $condition->pothole_count ?? 0,
                                'rutting_depth' => $sdiDetail['raw_data']['rutting_depth_bobot'] ?? 0,
                            ]
                        ),
                        'calculations' => [
                            'step1' => $sdiDetail['explanations']['step1'] ?? [],
                            'step2' => $sdiDetail['explanations']['step2'] ?? [],
                            'step3' => $sdiDetail['explanations']['step3'] ?? [],
                            'step4' => $sdiDetail['explanations']['step4'] ?? [],
                        ],
                        'final' => [
                            'sdi_final' => $sdiDetail['sdi_final'],
                            'category' => $sdiDetail['category'],
                            'note' => ($sdiDetail['category'] === 'Rusak Berat' && $sdiDetail['sdi_final'] == 999) 
                                ? 'Non-Aspal - SDI tidak applicable' 
                                : null
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getSegmentDetail', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll()
    {
        RoadCondition::query()->delete();
        
        return redirect()->route('kondisi-jalan.index')
            ->with('success', 'Semua data kondisi jalan berhasil dihapus.');
    }
}