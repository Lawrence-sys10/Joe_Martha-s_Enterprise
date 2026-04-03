<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetSystemData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all operational data while keeping structure (sales, purchases, customers, suppliers, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║     Joe_Martha\'s Enterprise - System Data Reset            ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->info('');

        if (!$this->option('force')) {
            $this->warn('⚠️  WARNING: This will delete ALL operational data!');
            $this->warn('');
            $this->warn('The following data will be PERMANENTLY DELETED:');
            $this->warn('  • All customers');
            $this->warn('  • All suppliers');
            $this->warn('  • All sales and sale items');
            $this->warn('  • All purchases and purchase items');
            $this->warn('  • All payments (customer and supplier)');
            $this->warn('  • All expenses');
            $this->warn('  • All stock movements');
            $this->warn('  • All transaction ledgers');
            $this->warn('  • All activity logs');
            $this->warn('');
            $this->warn('⚠️  This action CANNOT be undone!');
            $this->info('');

            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('❌ Operation cancelled.');
                return;
            }
        }

        $this->info('🗑️  Resetting system data...');
        $this->info('');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $this->info('  ✓ Foreign key checks disabled');

            // List of tables to clear (operational data) - Fixed duplicates
            $tables = [
                'sale_items' => 'Sale items',
                'sales' => 'Sales',
                'purchase_items' => 'Purchase items',
                'purchases' => 'Purchases',
                'payments' => 'Payments',
                'purchase_payments' => 'Supplier payments',
                'customers' => 'Customers',
                'suppliers' => 'Suppliers',
                'expenses' => 'Expenses',
                'stock_movements' => 'Stock movements',
                'customer_ledgers' => 'Customer ledgers',
                'supplier_ledgers' => 'Supplier ledgers',
                'transactions' => 'Transactions',
                'activity_logs' => 'Activity logs',
                'audit_logs' => 'Audit logs',
                'cctv_logs' => 'CCTV logs',
            ];

            foreach ($tables as $table => $displayName) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("  ✓ Cleared: $displayName");
                } else {
                    $this->info("  ⚠️  Skipped (table not found): $displayName");
                }
            }

            $this->info('');

            // Reset product stock to zero (keep products but set stock to 0)
            if (Schema::hasTable('products')) {
                DB::table('products')->update(['stock_quantity' => 0]);
                $this->info('  ✓ Reset product stock quantities to 0');
            }

            $this->info('');

            // Reset auto-increment sequences
            $tablesWithAutoIncrement = [
                'sales',
                'purchases',
                'customers',
                'suppliers',
                'expenses',
                'payments',
                'purchase_payments',
            ];

            foreach ($tablesWithAutoIncrement as $table) {
                if (Schema::hasTable($table)) {
                    DB::statement("ALTER TABLE $table AUTO_INCREMENT = 1");
                    $this->info("  ✓ Reset sequence: $table");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('  ✓ Foreign key checks re-enabled');

            $this->info('');
            $this->info('✅ System data reset successfully!');
            $this->info('');
            $this->info('📊 Summary:');
            $this->info('  • All customers deleted');
            $this->info('  • All suppliers deleted');
            $this->info('  • All sales and purchases deleted');
            $this->info('  • All payments deleted');
            $this->info('  • All expenses deleted');
            $this->info('  • All stock movements cleared');
            $this->info('  • Product stock reset to 0');
            $this->info('');
            $this->info('💡 Your system is now ready for fresh data!');
            $this->info('');
            $this->info('📝 Next steps:');
            $this->info('   • Add your products (if needed)');
            $this->info('   • Create new customers');
            $this->info('   • Add new suppliers');
            $this->info('   • Start recording sales and purchases');
            $this->info('');

        } catch (\Exception $e) {
            // Re-enable foreign key checks even on error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('');
            $this->error('❌ Failed to reset system data!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('');
            return 1;
        }
    }
}