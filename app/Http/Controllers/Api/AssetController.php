<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetConditionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Department;

class AssetController extends Controller
{
    /**
     * Display a listing of the assets.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['department', 'user']);

        // Optional filtering
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('condition')) {
            $query->where('current_condition', $request->condition);
        }

        $assets = $query->paginate(15);

        return response()->json($assets);
    }

    /**
     * Store a newly created asset.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'asset_code' => 'sometimes|string|max:255|unique:assets', // Allow asset_code from request
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'expected_lifetime_months' => 'nullable|integer',
            'department_id' => 'nullable|exists:departments,id',
            'primary_image' => 'nullable|image|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate unique asset code
        $assetCode = $request->asset_code ?? Asset::generateUniqueAssetCode();

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('primary_image')) {
            $imagePath = $request->file('primary_image')->store('assets/images', 'public');
        }

        $data = $request->all();
        $data['asset_code'] = $assetCode;
        $data['primary_image_path'] = $imagePath;
        $data['user_id'] = auth()->id();

        // Set default condition if not provided
        if (!isset($data['current_condition'])) {
            $data['current_condition'] = 'good';
        }

        // Set default active status if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }


        try {
            $asset = Asset::create($data);

            // Log successful creation
            Log::info('Asset created successfully', ['asset_id' => $asset->id, 'asset_code' => $asset->asset_code]);

            return response()->json($asset, 201);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating asset', ['error' => $e->getMessage(), 'data' => $data]);

            return response()->json([
                'message' => 'Failed to create asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset)
    {
        $asset->load(['department', 'user', 'conditionHistories']);
        return response()->json($asset);
    }

    /**
     * Update the specified asset.
     */
    public function update(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'current_condition' => 'sometimes|in:good,fair,poor,damaged',
            'primary_image' => 'nullable|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image update
        if ($request->hasFile('primary_image')) {
            // Delete old image if exists
            if ($asset->primary_image_path) {
                Storage::disk('public')->delete($asset->primary_image_path);
            }

            $imagePath = $request->file('primary_image')->store('assets/images', 'public');
            $request->merge(['primary_image_path' => $imagePath]);
        }

        $asset->update($request->all());

        return response()->json($asset);
    }

    /**
     * Scan an asset and update its condition
     */
    public function scan(Request $request, $assetCode)
    {
        $asset = Asset::where('asset_code', $assetCode)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'condition' => 'required|in:good,fair,poor,damaged',
            'notes' => 'nullable|string',
            'condition_image' => 'nullable|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update asset condition
        $asset->update([
            'current_condition' => $request->condition,
            'notes' => $request->notes,
            'last_scanned_at' => now()
        ]);

        // Create condition history
        $imagePath = null;
        if ($request->hasFile('condition_image')) {
            $imagePath = $request->file('condition_image')->store('assets/condition_images', 'public');
        }

        $conditionHistory = AssetConditionHistory::create([
            'asset_id' => $asset->id,
            'condition' => $request->condition,
            'notes' => $request->notes,
            'condition_image_path' => $imagePath,
            'recorded_by' => auth()->id(),
            'location' => $request->location
        ]);

        return response()->json([
            'asset' => $asset,
            'scan_record' => $conditionHistory
        ]);
    }

    /**
     * Delete the specified asset.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(null, 204);
    }

    /**
     * Get asset counts by department.
     */
    public function countsByDepartment()
    {
        $counts = DB::table('assets')
            ->join('departments', 'assets.department_id', '=', 'departments.id')
            ->select('departments.name as department_name', DB::raw('count(*) as total'))
            ->groupBy('departments.name')
            ->get();

        return response()->json($counts);
    }

/**
 * Get asset counts by condition.
 */
    public function countsByCondition()
    {
        $counts = DB::table('assets')
            ->select('current_condition', DB::raw('count(*) as total'))
            ->groupBy('current_condition')
            ->get();

        return response()->json($counts);
    }


    /**
     * Get total asset count.
     */
    public function totalCount()
    {
        $total = Asset::count();

        return response()->json(['total' => $total]);
    }

    /**
     * Search assets by name or code.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $assets = Asset::where('name', 'like', "%{$query}%")
            ->orWhere('asset_code', 'like', "%{$query}%")
            ->with(['department'])
            ->paginate(10);

        return response()->json($assets);
    }

    public function getAssetByCode($assetCode)
    {
        $asset = Asset::where('asset_code', $assetCode)->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        return response()->json($asset);
    }

    public function assetsByDepartment(Request $request)
    {
        $departmentName = $request->input('department');

        $department = Department::where('name', $departmentName)->first();

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }
        $assets = Asset::where('department_id', $department->id)
            ->with(['department', 'user'])
            ->paginate(15);

        return response()->json($assets);
    }

    public function getByCondition(Request $request)
    {
        $conditions = $request->query('condition');

        if (!$conditions) {
            return response()->json([
                'message' => 'Condition parameter is required'
            ], 400);
        }

        $allowedConditions = ['good', 'fair', 'poor', 'damaged'];
        $conditions = explode(',', $conditions);
        $validConditions = array_intersect($allowedConditions, $conditions);

        if (empty($validConditions)) {
            return response()->json([
                'message' => 'Invalid condition parameter'
            ], 400);
        }

        $assets = Asset::whereIn('current_condition', $validConditions)->paginate(15);

        return response()->json($assets);
    }


    /**
     * Get recently scanned assets (within the last 30 days)
     */
    public function recentlyScannedAssets(Request $request)
    {
        $thirtyDaysAgo = now()->subDays(30);

        $assets = Asset::where('last_scanned_at', '>=', $thirtyDaysAgo)
            ->with(['department', 'user'])
            ->orderBy('last_scanned_at', 'desc')
            ->paginate(15);

        return response()->json($assets);
    }
}
