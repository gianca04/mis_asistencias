<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estudiantes = [
            ['nombre' => 'AGUILERA', 'apellido' => 'VIERA STEWAR ADRIAN', 'dni' => '78130178'],
            ['nombre' => 'AGUIRRE', 'apellido' => 'HUAMAN ARIANA DEL ROSARIO', 'dni' => '78299338'],
            ['nombre' => 'ALBERCA', 'apellido' => 'CHOQUEHUANCA ASHLEY PATRICIA', 'dni' => '78084937'],
            ['nombre' => 'AVILA', 'apellido' => 'INFANTE HIKARI MISUKI', 'dni' => strval(random_int(10000000, 99999999))],
            ['nombre' => 'AYOSA', 'apellido' => 'CHAVEZ FATIMA ANTONELLA', 'dni' => '78494377'],
            ['nombre' => 'CAMPUSANO', 'apellido' => 'GUERRERO BRYANNA ABIGAIL', 'dni' => '78146910'],
            ['nombre' => 'CARRASCO', 'apellido' => 'GUTIERREZ VANESSA KRISTHEL', 'dni' => '78543759'],
            ['nombre' => 'CASTILLO', 'apellido' => 'MARQUEZ MAHÍA ALEXA', 'dni' => '78252914'],
            ['nombre' => 'CASTRO', 'apellido' => 'ELÍAS GREESY MILENA', 'dni' => '77643607'],
            ['nombre' => 'CHAVEZ', 'apellido' => 'CHIRA GENESIS YINSU', 'dni' => '78147678'],
            ['nombre' => 'CORO', 'apellido' => 'GUERRERO DANNA KRISTELL', 'dni' => '78507769'],
            ['nombre' => 'CRUZ', 'apellido' => 'ROJAS KISTOM ADRIANO', 'dni' => '78267549'],
            ['nombre' => 'CURO', 'apellido' => 'CARRASCO ANTHONY YAIR', 'dni' => '80770075'],
            ['nombre' => 'GARCÍA', 'apellido' => 'GARCÍA ERICKA XIOMARY', 'dni' => '78512469'],
            ['nombre' => 'HUAMAN', 'apellido' => 'TAFUR MELANI YASMIN', 'dni' => '78222079'],
            ['nombre' => 'HURTADO', 'apellido' => 'VASQUEZ JUSTIN', 'dni' => '78065850'],
            ['nombre' => 'JACINTO', 'apellido' => 'PANTA ZAYRA NAOMI', 'dni' => strval(random_int(10000000, 99999999))],
            ['nombre' => 'JARAMILLO', 'apellido' => 'MARTINEZ THIAGO ALESSANDRO TADEO', 'dni' => strval(random_int(10000000, 99999999))],
            ['nombre' => 'JIMENEZ', 'apellido' => 'SERNAQUÉ MAYELYN JHOANA', 'dni' => '78260477'],
            ['nombre' => 'LESCANO', 'apellido' => 'AREVALO CANDY ABIGAIL', 'dni' => strval(random_int(10000000, 99999999))],
            ['nombre' => 'NEYRA', 'apellido' => 'GUARDADO SERGIO FELIPE', 'dni' => '78400217'],
            ['nombre' => 'NIEVES', 'apellido' => 'VIVANCO DAVID SEBASTIAN', 'dni' => '90557931'],
            ['nombre' => 'ORDINOLA', 'apellido' => 'MOROCHO ASHMIN ITSELL', 'dni' => '78121327'],
            ['nombre' => 'PINTA', 'apellido' => 'VASQUEZ AMMIE BRUSNELA', 'dni' => '78157875'],
            ['nombre' => 'ROJAS', 'apellido' => 'OCUPA SARUMI ZICRI', 'dni' => '78390174'],
            ['nombre' => 'ROMAN', 'apellido' => 'RAMOS WALTER ARON', 'dni' => '78473937'],
            ['nombre' => 'SOSA', 'apellido' => 'CAMACHO NATHALY JASMIN', 'dni' => strval(random_int(10000000, 99999999))],
            ['nombre' => 'TIPACTI', 'apellido' => 'COVEÑAS THIAGO MATIAS JAVIER', 'dni' => '78335597'],
            ['nombre' => 'TOCTO', 'apellido' => 'YANAYACO DULCE VALENTINA', 'dni' => '78309035'],
            ['nombre' => 'VALVERDE', 'apellido' => 'SANDOVAL ORIANA NAOMI', 'dni' => '78511660'],
            ['nombre' => 'VINCES', 'apellido' => 'PUERTAS ORIANA DEL PILAR', 'dni' => '79116027'],
            ['nombre' => 'YANAYACO', 'apellido' => 'SANCHEZ LIDER SMITH', 'dni' => '78468441'],
            ['nombre' => 'ZAPATA', 'apellido' => 'CRUZ ABRAHAM NATHANAEL', 'dni' => '78523558'],
        ];

        $callesPeruanas = [
            'Av. Arequipa', 'Av. Javier Prado', 'Av. La Marina', 'Av. Universitaria', 'Av. Salaverry',
            'Jr. De la Unión', 'Jr. Pizarro', 'Jr. Ayacucho', 'Jr. Cusco', 'Jr. Lima',
            'Calle Las Flores', 'Calle Los Pinos', 'Calle Los Cedros', 'Calle Los Sauces', 'Calle Los Álamos'
        ];

        foreach ($estudiantes as $index => $estudiante) {
            $direccion = $callesPeruanas[array_rand($callesPeruanas)] . ' Mz. ' . chr(65 + ($index % 26)) . ' Lt. ' . rand(1, 20);

            DB::table('estudiantes')->insert([
                'nombre' => $estudiante['nombre'],
                'apellido' => $estudiante['apellido'],
                'dni' => $estudiante['dni'],
                'telefono' => '9' . rand(10000000, 99999999),
                'direccion' => $direccion,
                'codigo_estudiante' => 'EST' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
