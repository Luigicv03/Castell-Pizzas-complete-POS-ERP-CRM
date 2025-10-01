<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Constructor - solo admin y super admin pueden acceder
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin|Super Admin']);
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,name'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El rol es obligatorio.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        // Asignar rol al usuario
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(User $user)
    {
        $user->load('roles', 'orders', 'payments');
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        // No permitir editar al super admin por otro admin
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes permiso para editar este usuario.');
        }

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        // No permitir editar al super admin por otro admin
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes permiso para editar este usuario.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo ya está registrado.',
            'role.required' => 'El rol es obligatorio.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Actualizar rol
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function updatePassword(Request $request, User $user)
    {
        // No permitir cambiar contraseña del super admin por otro admin
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes permiso para cambiar la contraseña de este usuario.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // No permitir eliminar al super admin
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No se puede eliminar al Super Administrador.');
        }

        // No permitir que un usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus(User $user)
    {
        // No permitir desactivar al super admin
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No se puede desactivar al Super Administrador.');
        }

        // No permitir que un usuario se desactive a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('users.index')
            ->with('success', "Usuario {$status} exitosamente.");
    }
}

