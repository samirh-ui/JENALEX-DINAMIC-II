<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Mostrar todos los usuarios
    public function index()
    {
        $usuarios = User::orderBy('id', 'desc')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    // Mostrar formulario de registro
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar un nuevo usuario
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'documento' => 'required|string|max:30|unique:users,documento',
            'email' => 'required|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
        ]);

        User::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    // Consultar un usuario
    public function show(User $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    // Mostrar formulario de edición
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    // Actualizar un usuario
    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',

            'documento' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'documento')->ignore($usuario->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            'telefono' => 'nullable|string|max:20',
            'cargo' => 'required|string|max:100',

            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(array_keys(User::ROLE_LABELS))],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
        ]);

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar un usuario
    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
