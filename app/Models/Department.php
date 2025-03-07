<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'location',
    ];

    /**
     * Get the users associated with the department.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the assets associated with the department.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
