<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    // Daftar nama kategori yang realistis untuk helpdesk
    private static array $names = [
        'Hardware', 'Software', 'Network', 'Account', 'Access', 'Other',
        'Email', 'Printer', 'VPN', 'Database',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        // Rotasi nama agar unik, fallback ke random jika habis
        $name = self::$names[self::$index % count(self::$names)] . ' ' . (self::$index > 9 ? self::$index : '');
        self::$index++;

        return [
            'name'        => trim($name),
            'description' => fake()->sentence(),
        ];
    }
}
