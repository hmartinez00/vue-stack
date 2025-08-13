<?php

// -------------------------------------------------------------------------
// PASO 1: Obtener los campos del formulario desde el contenido Blade.
// -------------------------------------------------------------------------
// Validamos que se haya pasado el nombre del controlador como argumento.
if ($argc < 2) {
    die("Uso: php convert_controller_v3.php <NombreDelModelo>\n");
}

/**
 * Convierte los argumentos a los nombres de variables que necesitamos.
 */
$singularLowerModelName = strtolower($argv[1]);
$modelName = ucfirst($singularLowerModelName);
$pluralModelName = strtolower($modelName) . 's';

$controllerName = $argv[1] . 'Controller';
$controllerPath = "../app/Http/Controllers/{$controllerName}.php";

// Asegúrate de que el archivo del controlador existe.
if (!file_exists($controllerPath)) {
    die("Error: El archivo del controlador '{$controllerPath}' no existe.\n");
}

echo "Procesando el controlador: {$controllerPath}\n";

// -------------------------------------------------------------------------
// PASO 2: Generar el código del componente de Vue dinámicamente.
// -------------------------------------------------------------------------
$content = <<<VUE
<?php

namespace App\Http\Controllers;

use App\Models\\{$modelName};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\\{$modelName}Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class {$modelName}Controller extends Controller
{
    /**
     * Muestra la lista de {$pluralModelName}.
     */
    public function index(Request \$request)
    {
        \${$pluralModelName} = {$modelName}::paginate(10); // Ajustamos la paginación

        return Inertia::render('{$modelName}/Index', [
            '{$pluralModelName}' => \${$pluralModelName},
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo {$singularLowerModelName}.
     */
    public function create()
    {
        return Inertia::render('{$modelName}/Create');
    }

    /**
     * Almacena un nuevo {$singularLowerModelName}.
     */
    public function store({$modelName}Request \$request): RedirectResponse
    {
        {$modelName}::create(\$request->validated());

        return Redirect::route('{$pluralModelName}.index')
            ->with('success', '{$modelName} created successfully.');
    }

    /**
     * Muestra un {$singularLowerModelName} específico.
     */
    public function show({$modelName} \${$singularLowerModelName})
    {
        return Inertia::render('{$modelName}/Show', [
            '{$singularLowerModelName}' => \${$singularLowerModelName},
        ]);
    }

    /**
     * Muestra el formulario para editar un {$singularLowerModelName}.
     */
    public function edit({$modelName} \${$singularLowerModelName})
    {
        return Inertia::render('{$modelName}/Edit', [
            '{$singularLowerModelName}' => \${$singularLowerModelName},
        ]);
    }

    /**
     * Actualiza un {$singularLowerModelName}.
     */
    public function update({$modelName}Request \$request, {$modelName} \${$singularLowerModelName}): RedirectResponse
    {
        \${$singularLowerModelName}->update(\$request->validated());

        return Redirect::route('{$pluralModelName}.index')
            ->with('success', '{$modelName} updated successfully');
    }

    /**
     * Elimina un {$singularLowerModelName}.
     */
    public function destroy({$modelName} \${$singularLowerModelName}): RedirectResponse
    {
        \${$singularLowerModelName}->delete();

        return Redirect::route('{$pluralModelName}.index')
            ->with('success', '{$modelName} deleted successfully');
    }
}

VUE;


// -------------------------------------------------------------------------
// PASO 3: Imprimir el código del componente de Vue generado.
// -------------------------------------------------------------------------

file_put_contents($controllerPath, $content);

echo "¡Controlador convertido a Inertia.js con éxito!\n";
