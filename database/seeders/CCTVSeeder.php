<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CCTV;

class CCTVSeeder extends Seeder
{
    public function run(): void
    {
        // Check if CCTV cameras already exist
        if (CCTV::count() > 0) {
            $this->command->info('CCTV cameras already exist, skipping...');
            return;
        }
        
        $cameras = [
            [
                'camera_name' => 'Main Entrance',
                'camera_ip' => '192.168.1.101',
                'camera_location' => 'Main Shop Entrance',
                'stream_url' => 'rtsp://192.168.1.101:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Front door camera covering entrance and POS area',
            ],
            [
                'camera_name' => 'Stock Room',
                'camera_ip' => '192.168.1.102',
                'camera_location' => 'Inventory Storage Room',
                'stream_url' => 'rtsp://192.168.1.102:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Camera covering stock room and inventory',
            ],
            [
                'camera_name' => 'Checkout Counter',
                'camera_ip' => '192.168.1.103',
                'camera_location' => 'POS Counter',
                'stream_url' => 'rtsp://192.168.1.103:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => false,
                'notes' => 'Direct view of cashier and POS terminal',
            ],
            [
                'camera_name' => 'Back Door',
                'camera_ip' => '192.168.1.104',
                'camera_location' => 'Staff/Back Entrance',
                'stream_url' => 'rtsp://192.168.1.104:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Security camera covering back entrance',
            ],
        ];

        foreach ($cameras as $camera) {
            CCTV::create($camera);
        }

        $this->command->info('CCTV cameras created successfully!');
    }
}