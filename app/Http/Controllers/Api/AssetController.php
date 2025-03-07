<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetConditionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

        // Paginate results
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
        $assetCode = Asset::generateUniqueAssetCode();

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('primary_image')) {
            $imagePath = $request->file('primary_image')->store('assets/images', 'public');
        }

        $asset = Asset::create([
            'asset_code' => $assetCode,
            'name' => $request->name,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'purchase_cost' => $request->purchase_cost,
            'expected_lifetime_months' => $request->expected_lifetime_months,
            'department_id' => $request->department_id,
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'primary_image_path' => $imagePath
        ]);

        return response()->json($asset, 201);
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
        ->select('departments.name as department_name', DB::raw('count(*) as total'))
        ->join('departments', 'assets.department_id', '=', 'departments.id')
        ->groupBy('departments.name', 'assets.department_id')
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
        $count = Asset::count();
        return response()->json(['total' => $count]);
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
}
