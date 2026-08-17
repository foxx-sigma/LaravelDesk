<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory adalah "blueprint" untuk membuat data test/seed yang realistis.
 * Setara dengan test fixtures di Jest atau factory functions di testing TS.
 *
 * Cara pakai:
 *   User::factory()->create()              // buat 1 user acak
 *   User::factory(5)->create()             // buat 5 user acak
 *   User::factory()->admin()->create()     // buat 1 admin
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define: nilai default untuk setiap field.
     * faker->xxx() menghasilkan data palsu tapi realistis.
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'), // semua seed user pakai 'password'
            'role'              => 'user',                 // default: user biasa
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * "State" — variasi dari definition().
     * User::factory()->admin()->create() → buat user dengan role admin
     */
    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function agent(): static
    {
        return $this->state(['role' => 'agent']);
    }

    /**
     * State untuk user yang belum verifikasi email.
     */
    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
