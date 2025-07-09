<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\factory as Faker;
use Illuminate\Support\Facades\DB;

class clientes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 2000; $i++) {
            DB::table('clientes')->insert([
                'nombre' => $faker->firstName() . " " . $faker->lastName(),
                'cedula' => $faker->randomNumber(8),
                'direccion' => $faker->lexify('Bartolome salom calle bolivar #26-28 diagonal a la panaderia el turpialito'),
                'estado' => $faker->numberBetween(1, 5),
                'plan_id' => $faker->numberBetween(1, 20),
                'tlf' => $faker->phoneNumber,
                'observacion' => $faker->lexify('???????????????'),
                'servidor' => $faker->numberBetween(1, 5),
                'ip' => $faker->ipv4,
                'dia' => $faker->dateTimeBetween('now', '+ 1 year'),
                'corte' => $faker->dateTimeBetween('now', '+ 1 year'),
                'dia_i' => $faker->dateTimeBetween('now', '+ 1 year'),
                'active' => $faker->numberBetween(0, 1),
                'almacen' => $faker->numberBetween(1,2),
                'deuda' => $faker->numberBetween(1,2),
                'motivo_deuda' => $faker->address(),
                'mac' => $faker->numberBetween(1,2),
                'servicio_id' => $faker->numberBetween(1,5000),
            ]);
        }
    }
}
