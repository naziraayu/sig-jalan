<?php

/**
 * ✅ SDI CALCULATOR TEST SCRIPT
 * 
 * Script untuk test perhitungan SDI dengan berbagai skenario
 * Pastikan hasilnya sesuai dengan Excel PKRMS
 */

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SDICalculator;
use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SDICalculatorTest extends TestCase
{
    /**
     * Test Case 1: Kondisi Sempurna (Tidak ada kerusakan)
     */
    public function test_perfect_condition()
    {
        // Setup
        $condition = new RoadCondition([
            'link_no' => 'TEST001',
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 0,
            'pothole_area' => 0,
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory([
            'link_no' => 'TEST001',
            'chainage_from' => 0,
            'chainage_to' => 100,
            'pave_width' => 3,
        ]);

        // Mock inventory
        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        // Execute
        $result = SDICalculator::calculate($condition, true);

        // Assert
        $this->assertEquals(0, $result['sdi1'], 'SDI1 should be 0');
        $this->assertEquals(0, $result['sdi2'], 'SDI2 should be 0');
        $this->assertEquals(0, $result['sdi3'], 'SDI3 should be 0');
        $this->assertEquals(0, $result['sdi4'], 'SDI4 should be 0');
        $this->assertEquals('Baik', $result['category']);
    }

    /**
     * Test Case 2: Retak Ringan, Lebar Halus
     * Expected: SDI1=5, SDI2=0 (karena bobot lebar ≤3)
     */
    public function test_light_crack_narrow_width()
    {
        $condition = new RoadCondition([
            'link_no' => 'TEST002',
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 15, // 5% dari 300m²
            'oth_crack_area' => 0,
            'crack_width' => 2, // Halus < 1mm
            'pothole_count' => 0,
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory([
            'link_no' => 'TEST002',
            'pave_width' => 3,
        ]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        // SDI1: 5% retak → Bobot 2 → SDI1 = 5
        $this->assertEquals(5, $result['sdi1']);
        
        // SDI2: Lebar bobot 2 (halus) → SDI2 = 0
        $this->assertEquals(0, $result['sdi2'], 'SDI2 harus 0 untuk bobot lebar ≤3');
        
        $this->assertEquals(0, $result['sdi3']);
        $this->assertEquals(0, $result['sdi4']);
        $this->assertEquals('Baik', $result['category']);
    }

    /**
     * Test Case 3: Retak Ringan, Lebar Besar
     * Expected: SDI1=5, SDI2=10 (SDI1 × 2)
     */
    public function test_light_crack_wide_width()
    {
        $condition = new RoadCondition([
            'link_no' => 'TEST003',
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 15, // 5% dari 300m²
            'oth_crack_area' => 0,
            'crack_width' => 4, // Lebar > 3mm
            'pothole_count' => 0,
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory([
            'pave_width' => 3,
        ]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(5, $result['sdi1']);
        $this->assertEquals(10, $result['sdi2'], 'SDI2 harus SDI1 × 2 untuk bobot 4');
        $this->assertEquals('Baik', $result['category']);
    }

    /**
     * Test Case 4: Lubang Sedikit (< 10 per 100m)
     * Expected: SDI3 = SDI2 + 15
     */
    public function test_few_potholes()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 5, // 5 per 100m → Bobot 2
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(0, $result['sdi2']);
        $this->assertEquals(15, $result['sdi3'], 'SDI3 harus SDI2 + 15 untuk < 10 lubang per 100m');
        $this->assertEquals('Baik', $result['category']);
    }

    /**
     * Test Case 5: Lubang Sedang (10-50 per 100m)
     * Expected: SDI3 = SDI2 + 75
     */
    public function test_moderate_potholes()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 25, // 25 per 100m → Bobot 3
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(75, $result['sdi3'], 'SDI3 harus SDI2 + 75 untuk 10-50 lubang per 100m');
        $this->assertEquals('Sedang', $result['category']);
    }

    /**
     * Test Case 6: Lubang Banyak (> 50 per 100m)
     * Expected: SDI3 = SDI2 + 225
     */
    public function test_many_potholes()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 60, // 60 per 100m → Bobot 4
            'rutting_depth' => 1,
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(225, $result['sdi3'], 'SDI3 harus SDI2 + 225 untuk > 50 lubang per 100m');
        $this->assertEquals('Rusak Berat', $result['category']);
    }

    /**
     * Test Case 7: Alur Roda Dangkal (< 1cm)
     * Expected: SDI4 = SDI3 + 2.5
     */
    public function test_shallow_rutting()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 0,
            'rutting_depth' => 2, // < 1cm → +2.5
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(2.5, $result['sdi4'], 'SDI4 harus SDI3 + 2.5 untuk alur < 1cm');
    }

    /**
     * Test Case 8: Alur Roda Sedang (1-3cm)
     * Expected: SDI4 = SDI3 + 10
     */
    public function test_moderate_rutting()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 0,
            'rutting_depth' => 3, // 1-3cm → +10
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(10, $result['sdi4'], 'SDI4 harus SDI3 + 10 untuk alur 1-3cm');
    }

    /**
     * Test Case 9: Alur Roda Dalam (> 3cm)
     * Expected: SDI4 = SDI3 + 20
     */
    public function test_deep_rutting()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 0,
            'rutting_depth' => 4, // > 3cm → +20
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory(['pave_width' => 3]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        $this->assertEquals(20, $result['sdi4'], 'SDI4 harus SDI3 + 20 untuk alur > 3cm');
    }

    /**
     * Test Case 10: KASUS NYATA dari Database
     * Segmen 200-300, Link 35.09.0007
     */
    public function test_real_case_from_database()
    {
        $condition = new RoadCondition([
            'link_no' => '350900000007',
            'chainage_from' => 200,
            'chainage_to' => 300,
            'crack_dep_area' => 0,
            'oth_crack_area' => 0,
            'crack_width' => 1,
            'pothole_count' => 2, // 2 lubang
            'pothole_area' => 105, // Tidak dipakai lagi
            'rutting_depth' => 5, // Akan di-treat sebagai bobot 4
            'pavement' => 'Asphalt',
        ]);

        $inventory = new RoadInventory([
            'link_no' => '350900000007',
            'pave_width' => 3,
        ]);

        RoadInventory::shouldReceive('where')->andReturnSelf();
        RoadInventory::shouldReceive('first')->andReturn($inventory);

        $result = SDICalculator::calculate($condition, true);

        // Expected hasil:
        // SDI1 = 0 (tidak ada retak)
        // SDI2 = 0 (bobot lebar 1)
        // SDI3 = 0 + 15 = 15 (2 lubang per 100m → bobot 2)
        // SDI4 = 15 + 20 = 35 (rutting bobot 5 → treat as 4)

        $this->assertEquals(0, $result['sdi1']);
        $this->assertEquals(0, $result['sdi2']);
        $this->assertEquals(15, $result['sdi3']);
        $this->assertEquals(35, $result['sdi4']);
        $this->assertEquals('Baik', $result['category']);

        // Pastikan pakai pothole_count, bukan pothole_area
        $this->assertEquals(2, $result['pothole_count']);
        $this->assertEquals(2, $result['pothole_per_100m']);
    }

    /**
     * Test Case 11: Non-Asphalt Pavement
     * Expected: SDI = 999
     */
    public function test_non_asphalt_pavement()
    {
        $condition = new RoadCondition([
            'chainage_from' => 0,
            'chainage_to' => 100,
            'pavement' => 'Concrete',
        ]);

        $result = SDICalculator::calculate($condition);

        $this->assertEquals(999, $result['sdi_final']);
        $this->assertEquals('Rusak Berat', $result['category']);
    }
}