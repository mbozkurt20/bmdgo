<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    public function definition()
    {
        if (env('TEST_MODE')){
            return [
                'name' => 'Admin',
                'email' => 'test@admin.com',
                'top_up_balance'  => 100,
                'city_id'  => 71,
                'top_up_price'  => 3,
                'password' => Hash::make('test'),
                'remember_token' => Str::random(10),
            ];
        }

        return [];
    }
}
