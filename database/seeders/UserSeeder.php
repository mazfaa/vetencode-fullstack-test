<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $roles = Role::pluck('id', 'name');

    if ($roles->isEmpty()) {
      $this->command->error('Roles table is empty. Please seed roles first.');
      return;
    }

    // User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);

    User::create([
      'name' => 'Admin User',
      'email' => 'admin@example.com',
      'phone' => '081313810593',
      'address' => 'Jalan Ir. H. Juanda, Kota Cianjur',
      'role_id' => $roles['Admin'],
      // 'is_active' => true,
      'photo' => null,
    ]);

    User::create([
      'name' => 'Regular User',
      'email' => 'user@example.com',
      'phone' => '081809161367',
      'address' => 'Jalan KH Abdullah Bin Nuh, Kota Cianjur',
      'role_id' => $roles['User'],
      // 'is_active' => true,
      'photo' => null,
    ]);

    $this->command->info('Users table seeded!');
  }
}
