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

    /**
     * A barber who can sign in to /me and is past the forced password change.
     *
     * The default password is the one the portal tests sign in with. The cast
     * on the model hashes it, so this is the plaintext, not a hash.
     */
    public function withPortal(?string $phone = null, string $password = 'portal-pass-1'): static
    {
        return $this->state(fn () => [
            'phone' => $phone ?? (string) $this->faker->unique()->numerify('224#######'),
            'password' => $password,
            'must_change_password' => false,
        ]);
    }

    /** Sees the whole shop in the portal, not just their own chair. */
    public function manager(): static
    {
        return $this->state(fn () => ['portal_role' => \App\Models\Employee::ROLE_MANAGER]);
    }

    /** Portal set up, but the password is still the one the owner typed. */
    public function mustChangePassword(): static
    {
        return $this->state(fn () => ['must_change_password' => true]);
    }
}
