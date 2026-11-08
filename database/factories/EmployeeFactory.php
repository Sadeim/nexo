<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<\App\Models\Employee> */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->firstName() . ' ' . $this->faker->lastName();

        return [
            'name'            => $name,
            'slug'            => Str::slug($name) . '-' . Str::lower(Str::random(5)),
            'is_active'       => true,
            'public_profile'  => false,
            'sort_order'      => 0,
            'commission_rate' => 0,
        ];
    }

    /** Published on the website and bookable. */
    public function published(): static
    {
        return $this->state(fn () => [
            'public_profile' => true,
            'is_active'      => true,
            'title'          => 'Master Barber',
            'bio'            => 'Twelve years behind the chair.',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
