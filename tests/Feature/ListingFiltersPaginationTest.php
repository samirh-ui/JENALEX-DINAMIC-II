<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingFiltersPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'administrador']));
    }

    public function test_users_can_be_searched_and_filtered_by_role_and_status(): void
    {
        User::factory(12)->create();
        User::factory()->create([
            'name' => 'Carlos Filtro',
            'documento' => 'DOC-FILTRO',
            'email' => 'carlos.filtro@example.com',
            'role' => 'coordinador',
            'estado' => 'inactivo',
        ]);

        $this->get('/usuarios?buscar=Filtro&role=coordinador&estado=inactivo')
            ->assertOk()
            ->assertSee('Carlos Filtro')
            ->assertViewHas('usuarios', fn ($usuarios) => $usuarios->total() === 1)
            ->assertViewHas('filtros', fn ($filtros) => $filtros['role'] === 'coordinador'
                && $filtros['estado'] === 'inactivo');
    }

    public function test_user_pagination_preserves_active_filters(): void
    {
        User::factory(12)->create(['role' => 'usuario', 'estado' => 'activo']);

        $response = $this->get('/usuarios?role=usuario&estado=activo')
            ->assertOk()
            ->assertViewHas('usuarios', fn ($usuarios) => $usuarios->total() === 12
                && $usuarios->count() === 10);

        $usuarios = $response->viewData('usuarios');
        parse_str((string) parse_url($usuarios->url(2), PHP_URL_QUERY), $query);

        $this->assertSame('usuario', $query['role']);
        $this->assertSame('activo', $query['estado']);

        $this->get('/usuarios?role=usuario&estado=activo&page=2')
            ->assertOk()
            ->assertViewHas('usuarios', fn ($paginator) => $paginator->total() === 12
                && $paginator->count() === 2);
    }

    public function test_equipment_filters_are_combined_and_kept_in_pagination_links(): void
    {
        foreach (range(1, 11) as $number) {
            Equipo::create([
                'codigo' => "BAJA-{$number}",
                'nombre' => "Equipo Filtro {$number}",
                'categoria' => 'Prueba',
                'estado' => 'baja',
            ]);
        }

        Equipo::create([
            'codigo' => 'Filtro-Disponible',
            'nombre' => 'Equipo sin coincidencia',
            'categoria' => 'Prueba',
            'estado' => 'disponible',
        ]);

        Equipo::create([
            'codigo' => 'BAJA-Otro',
            'nombre' => 'Equipo sin coincidencia',
            'categoria' => 'Prueba',
            'estado' => 'baja',
        ]);

        $response = $this->get('/equipos?buscar=Filtro&estado=baja')
            ->assertOk()
            ->assertViewHas('equipos', fn ($equipos) => $equipos->total() === 11
                && $equipos->count() === 10);

        $equipos = $response->viewData('equipos');
        parse_str((string) parse_url($equipos->url(2), PHP_URL_QUERY), $query);

        $this->assertSame('Filtro', $query['buscar']);
        $this->assertSame('baja', $query['estado']);

        $this->get('/equipos?buscar=Filtro&estado=baja&page=2')
            ->assertOk()
            ->assertViewHas('equipos', fn ($paginator) => $paginator->total() === 11
                && $paginator->count() === 1);
    }
}
