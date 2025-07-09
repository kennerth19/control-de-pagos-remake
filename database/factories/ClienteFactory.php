<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'cedula' => $this->faker->numberBetween(1,2),
            'direccion' => $this->faker->address(),
            'estado' => $this->faker->numberBetween(0, 1, 2, 3, 4, 5),
            'plan_id' => $this->faker->numberBetween(1,2),
            'tlf' => $this->faker->numberBetween(1,2),
            'observacion' => $this->faker->numberBetween(1,2),
            'servidor' => $this->faker->numberBetween(1,2),
            'ip' => $this->faker->numberBetween(1,2),
            'dia' =>  Carbon::now(),
            'corte' => Carbon::now(),
            'dia_i' => Carbon::now(),
            'active' => $this->faker->numberBetween(1,2),
            'almacen' => $this->faker->numberBetween(1,2),
            'deuda' => $this->faker->numberBetween(1,2),
            'motivo_deuda' => $this->faker->address(),
            'mac' => $this->faker->numberBetween(1,2),
            'servicio_id' => $this->faker->numberBetween(1,5000),
        ];
    }
}
