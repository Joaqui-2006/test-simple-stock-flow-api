<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('role', 20);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('COP');
            $table->integer('stock');
            $table->uuid('category_id');
            $table->string('image_url', 500)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->index('name');
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->string('seller_username', 50);
            $table->dateTime('created_at');

            $table->foreign('seller_id')->references('id')->on('users')->onDelete('restrict');
            $table->index('created_at');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id');
            $table->uuid('product_id');
            $table->string('product_name', 150);
            $table->decimal('unit_price', 12, 2);
            $table->string('currency', 3)->default('COP');
            $table->integer('quantity');

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->index(['product_id', 'sale_id']);
        });

        // 9 CHECK Constraints manuales para defensa en profundidad
        DB::statement("ALTER TABLE users ADD CONSTRAINT ck_users_role CHECK (role IN ('admin', 'seller'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT ck_users_username_not_empty CHECK (LENGTH(TRIM(username)) > 0)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT ck_products_stock_non_negative CHECK (stock >= 0)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT ck_products_price_positive CHECK (price > 0)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT ck_products_version_positive CHECK (version >= 1)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT ck_products_currency CHECK (currency = 'COP')");
        DB::statement("ALTER TABLE sale_items ADD CONSTRAINT ck_sale_items_quantity_positive CHECK (quantity > 0)");
        DB::statement("ALTER TABLE sale_items ADD CONSTRAINT ck_sale_items_unit_price_positive CHECK (unit_price > 0)");
        DB::statement("ALTER TABLE sale_items ADD CONSTRAINT ck_sale_items_currency CHECK (currency = 'COP')");
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('categories');
    }
};