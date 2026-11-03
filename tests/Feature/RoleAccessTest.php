<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_available_and_inventory_requires_login(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/equipos')->assertRedirect('/login');
    }

    public function test_active_user_can_login_and_inactive_user_cannot(): void
    {
        $activeUser = User::factory()->create([
            'email' => 'active@example.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $this->post('/login', [
            'email' => $activeUser->email,
            'password' => 'password',
        ])->assertRedirect(route('equipos.index'));

        $this->assertAuthenticatedAs($activeUser);
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        $inactiveUser = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
            'estado' => 'inactivo',
        ]);

        $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_roles_enforce_current_inventory_and_user_permissions(): void
    {
        $coordinator = User::factory()->create(['role' => 'coordinador']);
        $this->actingAs($coordinator)
            ->get('/equipos/create')
            ->assertOk();
        $this->get('/usuarios')->assertForbidden();

        $support = User::factory()->create(['role' => 'soporte_tecnico']);
        $this->actingAs($support)
            ->get('/equipos/create')
            ->assertForbidden();

        $user = User::factory()->create(['role' => 'usuario']);
        $this->actingAs($user)
            ->get('/equipos')
            ->assertOk();
        $this->get('/equipos/create')->assertForbidden();
    }

    public function test_administrator_can_assign_a_role_when_creating_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);
        $this->actingAs($admin);

        $this->get('/usuarios/create')
            ->assertOk()
            ->assertSee('name="role"', false)
            ->assertSee('Soporte Técnico');

        $this->post('/usuarios', [
            'name' => 'Soporte Nuevo',
            'documento' => '12345678',
            'email' => 'support@example.com',
            'telefono' => '3001234567',
            'cargo' => 'Técnico',
            'role' => 'soporte_tecnico',
            'estado' => 'activo',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'support@example.com',
            'role' => 'soporte_tecnico',
        ]);
    }

    public function test_role_permission_matrix_matches_requested_roles(): void
    {
        $expectedPermissions = [
            'administrador' => [
                'manage-users',
                'view-inventory',
                'register-equipment',
                'edit-equipment',
                'delete-equipment',
                'assign-equipment',
                'register-requests',
                'assign-requests',
                'view-all-request-tracking',
                'register-maintenance',
                'generate-reports',
            ],
            'coordinador' => [
                'view-inventory',
                'register-equipment',
                'edit-equipment',
                'delete-equipment',
                'assign-equipment',
                'register-requests',
                'assign-requests',
                'view-all-request-tracking',
                'register-maintenance',
                'generate-reports',
            ],
            'soporte_tecnico' => [
                'view-inventory',
                'attend-requests',
                'view-all-request-tracking',
                'register-maintenance',
            ],
            'usuario' => [
                'view-inventory',
                'register-requests',
                'view-own-request-tracking',
            ],
        ];

        $this->assertSame($expectedPermissions, User::ROLE_PERMISSIONS);

        foreach ($expectedPermissions as $role => $permissions) {
            $user = new User(['role' => $role]);

            foreach (array_unique(array_merge(...array_values($expectedPermissions))) as $permission) {
                $this->assertSame(
                    in_array($permission, $permissions, true),
                    $user->hasPermission($permission),
                    "{$role} permission {$permission} does not match the role matrix."
                );
            }
        }
    }
}
