<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Restaurant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      if (env('TEST_MODE')){
          return [
              'admin_id' => 1,
              'restaurant_name' => 'Restaurant',
              'restaurant_code' => 'testmode',
              'name' => 'Restaurant',
              'phone' => '000 000 00 00',
              'email' => 'test@restaurant.com',
              'password' => Hash::make('test'), // password
              'remember_token' => Str::random(10),
          ];
      }
    }
}
