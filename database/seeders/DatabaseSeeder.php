<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@seapalace.com',
            'phone' => '01700000000',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'phone' => '01711111111',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // Create sample bank accounts
        BankAccount::create([
            'bank_name' => 'Sonali Bank Ltd.',
            'branch_name' => 'Motijheel Branch',
            'account_name' => 'Sea Palace Properties Ltd.',
            'account_number' => '0102010012345',
            'routing_number' => '150270509',
            'swift_code' => 'BABORADHXXX',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Dutch-Bangla Bank Ltd.',
            'branch_name' => 'Gulshan Branch',
            'account_name' => 'Sea Palace Properties Ltd.',
            'account_number' => '1234567890123',
            'routing_number' => '090261728',
            'swift_code' => 'DBBLBDDH',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'BRAC Bank Ltd.',
            'branch_name' => 'Head Office',
            'account_name' => 'Sea Palace Properties Ltd.',
            'account_number' => '1501204567890',
            'routing_number' => '060261514',
            'swift_code' => 'BABORADHXXX',
            'currency' => 'USD',
            'is_active' => true,
        ]);
    }
}
