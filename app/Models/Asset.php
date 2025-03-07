<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asset_code',
        'name',
        'description',
        'purchase_date',
        'purchase_cost',
        'expected_lifetime_months',
        'current_condition',
        'is_active',
        'department_id',
        'user_id',
        'last_scanned_at',
        'notes',
        'qr_code_path',
        'primary_image_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'is_active' => 'boolean',
        'last_scanned_at' => 'datetime'
    ];

    /**
     * Get the department that owns the asset.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who last updated the asset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the condition history for the asset.
     */
    public function conditionHistories()
    {
        return $this->hasMany(AssetConditionHistory::class);
    }

    /**
     * Generate a unique asset code
     *
     * @return string
     */
    public static function generateUniqueAssetCode()
    {
        do {
            // Generate a code like 'ASSET-001', 'ASSET-002', etc.
            $code = 'ASSET-' . str_pad(self::max('id') + 1, 3, '0', STR_PAD_LEFT);
        } while (self::where('asset_code', $code)->exists());

        return $code;
    }
}
