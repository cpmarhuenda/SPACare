<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Catrecurso;

class CatrecursoPolicy
{
    // Ver el listado de categorías
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('editor') || $user->hasRole('lector');
    }

    // Ver una categoría concreta
    public function view(User $user, Catrecurso $catrecurso): bool
    {
        return $this->viewAny($user); // misma lógica
    }

    // Crear nuevas categorías
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('editor');
    }

    // Editar una categoría existente
    public function update(User $user, Catrecurso $catrecurso): bool
    {
        return $user->hasRole('admin') || $user->hasRole('editor');
    }

    // Eliminar una categoría
    public function delete(User $user, Catrecurso $catrecurso): bool
    {
        return $user->hasRole('admin');
    }
}
