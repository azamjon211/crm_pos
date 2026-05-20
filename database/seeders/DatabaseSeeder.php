<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['username' => 'superadmin'], [
            'shop_id'  => null,
            'name'     => 'Super Admin',
            'password' => Hash::make('superadmin123'),
            'role'     => User::ROLE_SUPERADMIN,
        ]);

        $shop = Shop::firstOrCreate(
            ['name' => "Asosiy do'kon"],
            ['address' => 'Toshkent', 'is_active' => true]
        );

        User::firstOrCreate(['username' => 'admin'], [
            'shop_id'  => $shop->id,
            'name'     => 'Admin',
            'password' => Hash::make('admin123'),
            'role'     => User::ROLE_ADMIN,
        ]);

        User::firstOrCreate(['username' => 'manager'], [
            'shop_id'  => $shop->id,
            'name'     => 'Menejer',
            'password' => Hash::make('manager123'),
            'role'     => User::ROLE_MANAGER,
        ]);

        User::firstOrCreate(['username' => 'cashier'], [
            'shop_id'  => $shop->id,
            'name'     => 'Kassir',
            'password' => Hash::make('cashier123'),
            'role'     => User::ROLE_CASHIER,
        ]);

        $this->command->info('✅ Seeder bajarildi!');
        $this->command->info('superadmin / superadmin123');
        $this->command->info('admin / admin123');
        $this->command->info('manager / manager123');
        $this->command->info('cashier / cashier123');
    }
}
