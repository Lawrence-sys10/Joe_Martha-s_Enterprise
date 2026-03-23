<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CCTV extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cctvs';
    
    protected $fillable = [
        'camera_name',
        'camera_ip',
        'camera_location',
        'stream_url',
        'is_active',
        'recording_enabled',
        'motion_detection',
        'last_checked_at',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'recording_enabled' => 'boolean',
        'motion_detection' => 'boolean',
        'last_checked_at' => 'datetime'
    ];

    public function logs()
    {
        return $this->hasMany(CCTVLog::class);
    }
}