<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetConditionHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asset_id',
        'condition',
        'notes',
        'condition_image_path',
        'recorded_by',
        'location'
    ];

    /**
     * Get the asset that owns the condition history.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who recorded this condition history.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
