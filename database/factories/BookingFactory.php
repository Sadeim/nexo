<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Booking> */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'email'      => $this->faker->safeEmail(),
            'phone'      => null,
            'persons'    => 1,
            'date'       => '2026-11-11',
            'time'       => '10:00:00',
            'status'     => 'pending',
            'service_id' => null,
            'barber_id'  => null,
        ];
    }

    public function at(string $date, string $time): static
    {
        return $this->state(fn () => ['date' => $date, 'time' => $time]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
