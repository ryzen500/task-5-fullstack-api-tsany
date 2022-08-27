<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'title' => $this->faker->title(),
            'content' => $this->faker->text(),
            'image' => $this->faker->imageUrl(200,200),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            // 'user_id' => $this->faker->unique()->numberBetween(1, User::count()),
            // 'category_id' => $this->faker->numberBetween(1, Category::count()),
        ];
    }
}
