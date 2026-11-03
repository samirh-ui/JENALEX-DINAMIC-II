<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}
