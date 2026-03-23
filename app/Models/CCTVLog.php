<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CCTVLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cctv_logs';
    
    protected $fillable = [
        'cctv_id',
        'event_type',
        'event_data',
        'timestamp',
        'user_id',
        'screenshot_path'
    ];

    protected $casts = [
        'event_data' => 'array',
        'timestamp' => 'datetime'
    ];

    public function cctv()
    {
        return $this->belongsTo(CCTV::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}