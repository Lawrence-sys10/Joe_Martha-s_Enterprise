# Step2-Migrations.ps1
# Run this script to create all database migrations

Write-Host "Step 2: Creating Database Migrations..." -ForegroundColor Green

# Create migrations directory if not exists
New-Item -ItemType Directory -Force -Path "database\migrations"

# Create users table migration
$timestamp = Get-Date -Format "yyyy_MM_dd_HHmmss"
$migration1 = "${timestamp}_create_users_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['email', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration1" -Encoding UTF8 -NoNewline

# Create password reset tokens table
$timestamp = (Get-Date).AddSeconds(1).ToString("yyyy_MM_dd_HHmmss")
$migration2 = "${timestamp}_create_password_reset_tokens_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration2" -Encoding UTF8 -NoNewline

# Create sessions table
$timestamp = (Get-Date).AddSeconds(2).ToString("yyyy_MM_dd_HHmmss")
$migration3 = "${timestamp}_create_sessions_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration3" -Encoding UTF8 -NoNewline

# Create categories table
$timestamp = (Get-Date).AddSeconds(3).ToString("yyyy_MM_dd_HHmmss")
$migration4 = "${timestamp}_create_categories_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration4" -Encoding UTF8 -NoNewline

# Create products table
$timestamp = (Get-Date).AddSeconds(4).ToString("yyyy_MM_dd_HHmmss")
$migration5 = "${timestamp}_create_products_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('cost_price', 15, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->string('unit')->default('piece');
            $table->boolean('is_active')->default(true);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('image')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sku', 'barcode']);
            $table->index(['category_id', 'is_active']);
            $table->index('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration5" -Encoding UTF8 -NoNewline

# Create stock_movements table
$timestamp = (Get-Date).AddSeconds(5).ToString("yyyy_MM_dd_HHmmss")
$migration6 = "${timestamp}_create_stock_movements_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return']);
            $table->integer('quantity');
            $table->integer('before_quantity');
            $table->integer('after_quantity');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['product_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration6" -Encoding UTF8 -NoNewline

# Create suppliers table
$timestamp = (Get-Date).AddSeconds(6).ToString("yyyy_MM_dd_HHmmss")
$migration7 = "${timestamp}_create_suppliers_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->integer('payment_terms')->default(30);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration7" -Encoding UTF8 -NoNewline

# Create purchases table
$timestamp = (Get-Date).AddSeconds(7).ToString("yyyy_MM_dd_HHmmss")
$migration8 = "${timestamp}_create_purchases_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained();
            $table->string('invoice_number')->unique();
            $table->date('purchase_date');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('payment_status')->default('pending');
            $table->date('due_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['supplier_id', 'status']);
            $table->index('invoice_number');
            $table->index('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration8" -Encoding UTF8 -NoNewline

# Create purchase_items table
$timestamp = (Get-Date).AddSeconds(8).ToString("yyyy_MM_dd_HHmmss")
$migration9 = "${timestamp}_create_purchase_items_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('expiry_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['purchase_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration9" -Encoding UTF8 -NoNewline

# Create customers table
$timestamp = (Get-Date).AddSeconds(9).ToString("yyyy_MM_dd_HHmmss")
$migration10 = "${timestamp}_create_customers_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration10" -Encoding UTF8 -NoNewline

# Create sales table
$timestamp = (Get-Date).AddSeconds(10).ToString("yyyy_MM_dd_HHmmss")
$migration11 = "${timestamp}_create_sales_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('sale_date');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'credit', 'bank']);
            $table->enum('status', ['completed', 'pending', 'cancelled', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('payment_status')->default('paid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index('invoice_number');
            $table->index('sale_date');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration11" -Encoding UTF8 -NoNewline

# Create sale_items table
$timestamp = (Get-Date).AddSeconds(11).ToString("yyyy_MM_dd_HHmmss")
$migration12 = "${timestamp}_create_sale_items_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration12" -Encoding UTF8 -NoNewline

# Create payments table
$timestamp = (Get-Date).AddSeconds(12).ToString("yyyy_MM_dd_HHmmss")
$migration13 = "${timestamp}_create_payments_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank', 'credit']);
            $table->string('reference_number')->nullable();
            $table->datetime('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('transaction_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sale_id', 'payment_method']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration13" -Encoding UTF8 -NoNewline

# Create expenses table
$timestamp = (Get-Date).AddSeconds(13).ToString("yyyy_MM_dd_HHmmss")
$migration14 = "${timestamp}_create_expenses_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['category', 'expense_date']);
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration14" -Encoding UTF8 -NoNewline

# Create transactions table
$timestamp = (Get-Date).AddSeconds(14).ToString("yyyy_MM_dd_HHmmss")
$migration15 = "${timestamp}_create_transactions_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['sale', 'purchase', 'expense', 'payment', 'receipt']);
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->datetime('transaction_date');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['type', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('transaction_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration15" -Encoding UTF8 -NoNewline

# Create cctvs table
$timestamp = (Get-Date).AddSeconds(15).ToString("yyyy_MM_dd_HHmmss")
$migration16 = "${timestamp}_create_cctvs_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->string('camera_name');
            $table->string('camera_ip')->nullable();
            $table->string('camera_location');
            $table->text('stream_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('recording_enabled')->default(true);
            $table->boolean('motion_detection')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['camera_location', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration16" -Encoding UTF8 -NoNewline

# Create cctv_logs table
$timestamp = (Get-Date).AddSeconds(16).ToString("yyyy_MM_dd_HHmmss")
$migration17 = "${timestamp}_create_cctv_logs_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctv_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cctv_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->timestamp('timestamp');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('screenshot_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['cctv_id', 'event_type']);
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_logs');
    }
};
'@ | Out-File -FilePath "database\migrations\$migration17" -Encoding UTF8 -NoNewline

# Create activity_log table for Spatie Activity Log
$timestamp = (Get-Date).AddSeconds(17).ToString("yyyy_MM_dd_HHmmss")
$migration18 = "${timestamp}_create_activity_log_table.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->timestamps();
            
            $table->index('log_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
}
'@ | Out-File -FilePath "database\migrations\$migration18" -Encoding UTF8 -NoNewline

# Create permission tables for Spatie Permission
$timestamp = (Get-Date).AddSeconds(18).ToString("yyyy_MM_dd_HHmmss")
$migration19 = "${timestamp}_create_permission_tables.php"
@'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams) {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
'@ | Out-File -FilePath "database\migrations\$migration19" -Encoding UTF8 -NoNewline

Write-Host "Step 2 Complete: All migrations have been created!" -ForegroundColor Green
Write-Host "Total migrations created: 19" -ForegroundColor Green
Write-Host "Next step will create seeders and database seeders" -ForegroundColor Yellow