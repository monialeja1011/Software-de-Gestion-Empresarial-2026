<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = Category::query()->forceCreate([
            'name' => fake()->unique()->words(2, true),
            'active' => true,
        ]);

        return [
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'price' => 100000,
            'cost_price' => 70000,
            'markup_percentage' => 20,
            'iva_percentage' => 19,
            'stock' => 10,
            'category_id' => $category->id,
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
