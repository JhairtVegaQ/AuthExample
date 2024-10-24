<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Departamento;
use App\Models\Especialidad;
use App\Models\Facultad;
use App\Models\Seccion;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docente>
 */
class DocenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $random_seccion = Seccion::inRandomOrder()->first();
        if (!$random_seccion) {
            $random_departamento = Departamento::inRandomOrder()->first() ?? Departamento::factory()->create();
            $random_seccion = Seccion::factory()->create(['departamento_id' => $random_departamento->id]);
        }

        $factultad = $random_seccion->departamento->facultad;
        $random_especialidad = Especialidad::where('facultad_id', $factultad->id)->inRandomOrder()->first();

        if (!$random_especialidad) {
            $random_especialidad = Especialidad::factory()->create(['facultad_id' => $factultad->id]);
        }

        $random_area = Area::where('especialidad_id', $random_especialidad->id)->inRandomOrder()->first();

        return [
            'usuario_id' => Usuario::factory(),
            'codigoDocente' => $this->faker->unique()->randomNumber(8),
            'tipo' => $this->faker->randomElement(['TPA', 'TC']),
            'seccion_id' => $random_seccion,
            'especialidad_id' => $random_especialidad,
            'area_id' => $random_area ?? Area::factory()->create(['especialidad_id' => $random_especialidad->id]),
        ];
    }
}
