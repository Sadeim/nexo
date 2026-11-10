<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Customer> */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Ten digits, already normalised: the model stores what
            // App\Support\Phone produced, never what somebody typed.
            'phone' => (string) $this->faker->unique()->numerify('224#######'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'sms_marketing_consent' => false,
            'bookings_count' => 0,
        ];
    }

    /** Ticked the box, with the evidence that goes with it. */
    public function consented(): static
    {
        return $this->state(fn () => [
            'sms_marketing_consent' => true,
            'sms_consent_at' => now(),
            'consent_source' => 'booking_modal',
        ]);
    }

    /** All we have is the number — step one, abandoned. */
    public function anonymous(): static
    {
        return $this->state(fn () => [
            'first_name' => null,
            'last_name' => null,
            'email' => null,
        ]);
    }
}
