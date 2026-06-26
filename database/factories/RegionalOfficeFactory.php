<?php

namespace Database\Factories;

use App\Models\RegionalOffice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegionalOffice>
 */
class RegionalOfficeFactory extends Factory
{
    protected $model = RegionalOffice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = fake()->city();
        $head = fake()->name();
        $address = fake()->address();

        return [
            'region' => ['tg' => $region, 'ru' => $region, 'en' => $region],
            'head' => ['tg' => $head, 'ru' => $head, 'en' => $head],
            'phone' => fake()->phoneNumber(),
            'address' => ['tg' => $address, 'ru' => $address, 'en' => $address],
            'sort_order' => 0,
        ];
    }
}
