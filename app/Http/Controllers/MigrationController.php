<?php

namespace App\Http\Controllers;

use App\Models\Migration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MigrationRequest;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MigrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:migrations.index');
    }

    /**
     * Muestra la lista de migrations.
     */
    public function index(Request $request)
    {
        $migrations = Migration::paginate(10); // Ajustamos la paginación

        return Inertia::render('Migration/Index', [
            'migrations' => $migrations,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo migration.
     */
    public function create()
    {
        return Inertia::render('Migration/Create');
    }

    /**
     * Almacena un nuevo migration.
     */
    public function store(MigrationRequest $request): RedirectResponse
    {
        Migration::create($request->validated());

        return Redirect::route('migrations.index')
            ->with('success', 'Migration created successfully.');
    }

    /**
     * Muestra un migration específico.
     */
    public function show(Migration $migration)
    {
        return Inertia::render('Migration/Show', [
            'migration' => $migration,
        ]);
    }

    /**
     * Muestra el formulario para editar un migration.
     */
    public function edit(Migration $migration)
    {
        return Inertia::render('Migration/Edit', [
            'migration' => $migration,
        ]);
    }

    /**
     * Actualiza un migration.
     */
    public function update(MigrationRequest $request, Migration $migration): RedirectResponse
    {
        $migration->update($request->validated());

        return Redirect::route('migrations.index')
            ->with('success', 'Migration updated successfully');
    }

    /**
     * Elimina un migration.
     */
    public function destroy(Migration $migration): RedirectResponse
    {
        $migration->delete();

        return Redirect::route('migrations.index')
            ->with('success', 'Migration deleted successfully');
    }
}
