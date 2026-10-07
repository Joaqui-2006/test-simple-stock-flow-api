<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    public function up(): void
    {
        // Seed fixed categories with stable UUIDs
        $categories = [
            ['id' => '2fa85f64-5717-4562-b3fc-2c963f66afa5', 'name' => 'Bebidas'],
            ['id' => '4fa85f64-5717-4562-b3fc-2c963f66afa7', 'name' => 'Snacks'],
            ['id' => '5fa85f64-5717-4562-b3fc-2c963f66afa8', 'name' => 'PanaderÃ­a'],
            ['id' => '6fa85f64-5717-4562-b3fc-2c963f66afa9', 'name' => 'LÃ¡cteos'],
            ['id' => '7fa85f64-5717-4562-b3fc-2c963f66afb0', 'name' => 'Abarrotes'],
        ];

        DB::table('categories')->insert($categories);

        // Seed initial Admin with password hash from password_hash
        $adminId = 'c1a2b3c4-d5e6-7f8a-9b0c-1d2e3f4a5b6c';
        DB::table('users')->insert([
            'id' => $adminId,
            'username' => 'admin',
            'password_hash' => password_hash('Admin12345!', PASSWORD_DEFAULT),
            'role' => 'admin',
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('username', 'admin')->delete();
        DB::table('categories')->truncate();
    }
};