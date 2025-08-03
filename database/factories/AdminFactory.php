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

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (env('TEST_MODE')){
            return [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('test'),
                'remember_token' => Str::random(10),
            ];
        }else{
            return [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456'),
                'remember_token' => Str::random(10),
            ];
        }
    }
}
