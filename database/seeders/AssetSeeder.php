<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all department IDs
        $departmentIds = Department::pluck('id')->toArray();

        // Get scanner/manager user IDs for assignment
        $userIds = User::whereIn('role', ['scanner', 'manager'])->pluck('id')->toArray();

        // If no users found, use null
        if (empty($userIds)) {
            $userIds = [null];
        }

        // Asset conditions
        $conditions = ['good', 'fair', 'poor', 'damaged'];

        // Sample assets data
        $assets = [
            // IT Department Assets
            [
                'asset_code' => 'IT-LAPTOP-001',
                'name' => 'Dell XPS 13',
                'description' => 'Developer laptop with 16GB RAM, 512GB SSD',
                'purchase_date' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                'purchase_cost' => 1399.99,
                'expected_lifetime_months' => 36,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'IT'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(5),
                'notes' => 'Assigned to frontend development team',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'IT-LAPTOP-002',
                'name' => 'MacBook Pro M1',
                'description' => 'Designer laptop with 32GB RAM, 1TB SSD',
                'purchase_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'purchase_cost' => 2199.99,
                'expected_lifetime_months' => 48,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'IT'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(2),
                'notes' => 'UX/UI design team equipment',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'IT-MONITOR-001',
                'name' => 'Dell UltraSharp 32" 4K',
                'description' => '32-inch 4K monitor for design work',
                'purchase_date' => Carbon::now()->subMonths(8)->format('Y-m-d'),
                'purchase_cost' => 699.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'fair',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'IT'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(15),
                'notes' => 'Minor scratches on the stand',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // HR Department Assets
            [
                'asset_code' => 'HR-PRINTER-001',
                'name' => 'HP LaserJet Pro',
                'description' => 'Multifunction color laser printer',
                'purchase_date' => Carbon::now()->subMonths(12)->format('Y-m-d'),
                'purchase_cost' => 449.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'HR'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(7),
                'notes' => 'Located in HR office area',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'HR-LAPTOP-001',
                'name' => 'Lenovo ThinkPad X1',
                'description' => 'HR Manager laptop',
                'purchase_date' => Carbon::now()->subMonths(18)->format('Y-m-d'),
                'purchase_cost' => 1299.99,
                'expected_lifetime_months' => 36,
                'current_condition' => 'fair',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'HR'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(10),
                'notes' => 'Some battery degradation noted',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Finance Department Assets
            [
                'asset_code' => 'FIN-SERVER-001',
                'name' => 'Dell PowerEdge R740',
                'description' => 'Finance database server',
                'purchase_date' => Carbon::now()->subMonths(24)->format('Y-m-d'),
                'purchase_cost' => 5699.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Finance'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(30),
                'notes' => 'Located in main server room, rack #3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'FIN-LAPTOP-001',
                'name' => 'HP EliteBook',
                'description' => 'Finance team laptop',
                'purchase_date' => Carbon::now()->subMonths(9)->format('Y-m-d'),
                'purchase_cost' => 1099.99,
                'expected_lifetime_months' => 36,
                'current_condition' => 'poor',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Finance'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(14),
                'notes' => 'Screen hinge damaged, needs repair',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Operations Department Assets
            [
                'asset_code' => 'OPS-PROJECTOR-001',
                'name' => 'Epson PowerLite',
                'description' => 'Conference room projector',
                'purchase_date' => Carbon::now()->subMonths(15)->format('Y-m-d'),
                'purchase_cost' => 649.99,
                'expected_lifetime_months' => 48,
                'current_condition' => 'fair',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Operations'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(21),
                'notes' => 'Bulb replacement due in ~300 hours',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'OPS-VEHICLE-001',
                'name' => 'Ford Transit Connect',
                'description' => 'Delivery van',
                'purchase_date' => Carbon::now()->subYears(2)->format('Y-m-d'),
                'purchase_cost' => 24999.99,
                'expected_lifetime_months' => 84,
                'current_condition' => 'poor',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Operations'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(5),
                'notes' => 'Needs transmission service, 45,000 miles',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Marketing Department Assets
            [
                'asset_code' => 'MKT-CAMERA-001',
                'name' => 'Sony Alpha a7 III',
                'description' => 'Professional mirrorless camera',
                'purchase_date' => Carbon::now()->subMonths(7)->format('Y-m-d'),
                'purchase_cost' => 1999.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Marketing'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(3),
                'notes' => 'Includes 24-70mm lens and carrying case',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'MKT-TABLET-001',
                'name' => 'iPad Pro 12.9"',
                'description' => 'Design tablet with Apple Pencil',
                'purchase_date' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                'purchase_cost' => 1299.99,
                'expected_lifetime_months' => 36,
                'current_condition' => 'damaged',
                'is_active' => false,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Marketing'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(1),
                'notes' => 'Screen cracked, sent for repair',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Additional random assets
            [
                'asset_code' => 'IT-SERVER-001',
                'name' => 'Cisco UCS C240 M5',
                'description' => 'Application server',
                'purchase_date' => Carbon::now()->subMonths(22)->format('Y-m-d'),
                'purchase_cost' => 7599.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'IT'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(45),
                'notes' => 'Production environment server',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'MKT-LAPTOP-001',
                'name' => 'ASUS ProArt StudioBook',
                'description' => 'Video editing laptop',
                'purchase_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'purchase_cost' => 2499.99,
                'expected_lifetime_months' => 48,
                'current_condition' => 'good',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Marketing'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(9),
                'notes' => 'Installed with Adobe Creative Cloud',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'asset_code' => 'OPS-SCANNER-001',
                'name' => 'Symbol DS9808',
                'description' => 'Barcode scanner for inventory',
                'purchase_date' => Carbon::now()->subMonths(14)->format('Y-m-d'),
                'purchase_cost' => 349.99,
                'expected_lifetime_months' => 60,
                'current_condition' => 'fair',
                'is_active' => true,
                'department_id' => $this->getDepartmentIdByName($departmentIds, 'Operations'),
                'user_id' => $userIds[array_rand($userIds)],
                'last_scanned_at' => Carbon::now()->subDays(18),
                'notes' => 'Used at warehouse receiving desk',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert assets into database
        DB::table('assets')->insert($assets);

        // Create condition history for each asset
        $conditionHistories = [];

        foreach ($assets as $asset) {
            // Get the asset ID (assuming it's auto-incremented)
            $assetId = DB::table('assets')->where('asset_code', $asset['asset_code'])->value('id');

            if ($assetId) {
                // Add initial condition assessment
                $conditionHistories[] = [
                    'asset_id' => $assetId,
                    'condition' => $asset['current_condition'],
                    'notes' => 'Initial condition assessment',
                    'recorded_by' => $asset['user_id'],
                    'created_at' => Carbon::now()->subDays(rand(30, 60)),
                    'updated_at' => Carbon::now()->subDays(rand(30, 60)),
                ];

                // Add a second random condition assessment for some assets
                if (rand(0, 1)) {
                    $randomCondition = $conditions[array_rand($conditions)];
                    $conditionHistories[] = [
                        'asset_id' => $assetId,
                        'condition' => $randomCondition,
                        'notes' => 'Periodic assessment: ' . $this->getConditionNote($randomCondition),
                        'recorded_by' => $asset['user_id'],
                        'created_at' => Carbon::now()->subDays(rand(5, 29)),
                        'updated_at' => Carbon::now()->subDays(rand(5, 29)),
                    ];
                }

                // Add the most recent condition assessment matching the current condition
                $conditionHistories[] = [
                    'asset_id' => $assetId,
                    'condition' => $asset['current_condition'],
                    'notes' => 'Latest assessment: ' . $this->getConditionNote($asset['current_condition']),
                    'recorded_by' => $asset['user_id'],
                    'created_at' => $asset['last_scanned_at'],
                    'updated_at' => $asset['last_scanned_at'],
                ];
            }
        }

        // Insert condition histories into database
        DB::table('asset_condition_histories')->insert($conditionHistories);

        $this->command->info('Added ' . count($assets) . ' assets with condition histories.');
    }

    /**
     * Get a department ID by its name if it exists in the available IDs
     *
     * @param array $departmentIds
     * @param string $name
     * @return int|null
     */
    private function getDepartmentIdByName(array $departmentIds, string $name): ?int
    {
        $departmentId = Department::where('name', $name)->value('id');

        // Return the ID if it exists in our available IDs, otherwise return random ID
        if ($departmentId && in_array($departmentId, $departmentIds)) {
            return $departmentId;
        }

        // Return random department ID if specific one not found
        return !empty($departmentIds) ? $departmentIds[array_rand($departmentIds)] : null;
    }

    /**
     * Get a condition note based on condition value
     *
     * @param string $condition
     * @return string
     */
    private function getConditionNote(string $condition): string
    {
        switch ($condition) {
            case 'good':
                return 'Asset is in excellent working condition with no visible damage.';
            case 'fair':
                return 'Asset has minor wear and tear but functions normally.';
            case 'poor':
                return 'Asset has significant wear or functional issues but still usable.';
            case 'damaged':
                return 'Asset has major damage and requires repair before use.';
            default:
                return 'Assessment completed.';
        }
    }
}
