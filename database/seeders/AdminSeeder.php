<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::create([
      'name' => 'Imax Store Admin',
      'email' => 'admin@imax.com',
      'password' => Hash::make('password'),
      'role' => 'super_admin',
      'email_verified_at' => now()
    ]);
  }
}
