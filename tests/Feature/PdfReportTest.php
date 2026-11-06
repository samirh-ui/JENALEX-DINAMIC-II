<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_generate_equipment_pdf_without_gd(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrador']));

        Equipo::create([
            'codigo' => 'PDF-001',
            'nombre' => 'Equipo de prueba',
            'categoria' => 'Prueba',
            'estado' => 'disponible',
        ]);

        $response = $this->get(route('pdf.equipos'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
