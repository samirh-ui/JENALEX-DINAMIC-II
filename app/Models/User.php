<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_LABELS = [
        'administrador' => 'Administrador',
        'coordinador' => 'Coordinador',
        'soporte_tecnico' => 'Soporte Técnico',
        'usuario' => 'Usuario',
    ];

    public const ROLE_PERMISSIONS = [
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
            'manage-maintenance',
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
            'manage-maintenance',
        ],
        'soporte_tecnico' => [
            'view-inventory',
            'attend-requests',
            'view-all-request-tracking',
            'register-maintenance',
            'manage-maintenance',
        ],
        'usuario' => [
            'view-inventory',
            'register-requests',
            'view-own-request-tracking',
        ],
    ];

    // Solicitudes registradas por el usuario
    public function solicitudesRegistradas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'usuario_id');
    }

    // Solicitudes asignadas al técnico
    public function solicitudesAsignadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'tecnico_id');
    }

    // Solicitudes escaladas por el usuario de Mesa de ayuda
    public function solicitudesEscaladas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'escalado_por');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'documento',
        'email',
        'telefono',
        'cargo',
        'password',
        'estado',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, self::ROLE_PERMISSIONS[$this->role] ?? [], true);
    }

    public function roleName(): string
    {
        return self::ROLE_LABELS[$this->role] ?? self::ROLE_LABELS['usuario'];
    }

    // Equipos que ha recibido el usuario
    public function asignacionesRecibidas(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'usuario_id');
    }

    // Equipos que ha entregado el usuario
    public function asignacionesEntregadas(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'entregado_por');
    }

    // Devoluciones que ha recibido el usuario
    public function devolucionesRecibidas(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'recibido_por');
    }

    // Mantenimientos realizados por el técnico
    public function mantenimientosRealizados(): HasMany
{
      return $this->hasMany(Mantenimiento::class, 'tecnico_id');
}
}