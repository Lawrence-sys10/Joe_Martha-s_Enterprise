<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Services\SMSService;
use Illuminate\Support\Facades\Log;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check-low';
    protected $description = 'Check for low stock products and send notifications (Email & SMS)';

    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('=================================');
        $this->info('Low Stock Checker');
        $this->info('=================================');
        $this->newLine();
        
        $this->info('Checking for low stock products...');
        
        $lowStockProducts = Product::whereRaw('stock_quantity <= minimum_stock')
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->get();
        
        if ($lowStockProducts->isEmpty()) {
            $this->info('✓ No low stock products found.');
            return 0;
        }
        
        $this->info("⚠ Found {$lowStockProducts->count()} low stock products.");
        $this->newLine();
        
        // Get all admin users
        $admins = User::role('Admin')->get();
        
        if ($admins->isEmpty()) {
            $this->warn('No admin users found to send notifications.');
            return 0;
        }
        
        foreach ($lowStockProducts as $product) {
            $this->line("Product: {$product->name}");
            $this->line("  SKU: {$product->sku}");
            $this->line("  Current Stock: {$product->stock_quantity} {$product->unit}s");
            $this->line("  Minimum Stock: {$product->minimum_stock} {$product->unit}s");
            $this->line("  Shortage: " . ($product->minimum_stock - $product->stock_quantity) . " {$product->unit}s");
            $this->newLine();
            
            foreach ($admins as $admin) {
                // Send Email Notification
                try {
                    $admin->notify(new LowStockNotification($product));
                    $this->line("  ✓ Email sent to: {$admin->email}");
                    Log::info("Low stock email sent to {$admin->email} for product {$product->name}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Email failed to {$admin->email}: " . $e->getMessage());
                    Log::error("Failed to send low stock email: " . $e->getMessage());
                }
                
                // Send SMS Notification if phone number exists
                if ($admin->phone) {
                    try {
                        $smsSent = $this->smsService->sendLowStockAlert($product, $admin);
                        if ($smsSent) {
                            $this->line("  ✓ SMS sent to: {$admin->phone}");
                        } else {
                            $this->warn("  ⚠ SMS failed to: {$admin->phone}");
                        }
                    } catch (\Exception $e) {
                        $this->error("  ✗ SMS error to {$admin->phone}: " . $e->getMessage());
                    }
                } else {
                    $this->warn("  ⚠ No phone number for: {$admin->email}");
                }
            }
            $this->newLine();
        }
        
        $this->info('=================================');
        $this->info('Low stock notifications completed!');
        $this->info('=================================');
        
        return 0;
    }
}