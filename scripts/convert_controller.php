<?php

// Validamos que se haya pasado el nombre del controlador como argumento.
if ($argc < 2) {
    die("Uso: php convert_controller.php <NombreDelControlador>\n");
}

$controllerName = $argv[1];
$controllerPath = "../app/Http/Controllers/{$controllerName}.php";

// Asegúrate de que el archivo del controlador existe.
if (!file_exists($controllerPath)) {
    die("Error: El archivo del controlador '{$controllerPath}' no existe.\n");
}

echo "Procesando el controlador: {$controllerPath}\n";

// 1. Leer el contenido del archivo.
$content = file_get_contents($controllerPath);

// 2. Extraer el nombre del modelo para hacerlo dinámico.
if (preg_match('/use App\\\\Models\\\\(.*?);/', $content, $matches)) {
    $modelName = $matches[1];
    $pluralModelName = strtolower($modelName) . 's'; // Asume un plural simple.
    $singularLowerModelName = strtolower($modelName); // Nombre del modelo en singular y minúsculas para las vistas.
    $inertiaPath = ucfirst($modelName);
} else {
    die("Error: No se pudo encontrar una declaración de modelo 'use App\\Models\\...'.\n");
}

// 3. Definimos los patrones de búsqueda y los imprimimos para depuración.
echo "\nPatrones a buscar (con '{$modelName}' y '{$pluralModelName}'):\n";

$patterns = [
    // Patrón corregido: Ahora usa el nombre singular de la vista.
    "return view\('{$singularLowerModelName}.index', compact\('{$pluralModelName}'\)\)\s*->with\(.*?\);",
    "return view\('{$singularLowerModelName}.create', compact\('{$singularLowerModelName}'\)\)\s*;",
    "return view\('{$singularLowerModelName}.show', compact\('{$singularLowerModelName}'\)\)\s*;",
    "return view\('{$singularLowerModelName}.edit', compact\('{$singularLowerModelName}'\)\)\s*;",
    // Estos patrones fueron corregidos para abarcar el método completo.
    "public function show\(\$id\): View\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);",
    "public function edit\(\$id\): View\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);",
    "public function destroy\(\$id\): RedirectResponse\s*\{\s*([a-z]+)::find\(\$id\)->delete\(\);",
];

foreach ($patterns as $pattern) {
    echo "- /" . $pattern . "/s\n";
}

echo "\nRealizando sustituciones...\n";

// 4. Realizamos las sustituciones de código.
$content = str_replace('use Illuminate\View\View;', 'use Inertia\Inertia;', $content);

// Eliminamos la declaración de tipo ': View' en todos los métodos que retornan vistas.
$content = preg_replace("/: View/", "", $content);

// Reemplazamos el return del método index.
$content = preg_replace(
    "/return view\('{$singularLowerModelName}.index', compact\('{$pluralModelName}'\)\)\s*->with\(.*?\);/s",
    "return Inertia::render('{$inertiaPath}/Index', [\n            '{$pluralModelName}' => \${$pluralModelName},\n        ]);",
    $content
);

// Reemplazamos el return del método create.
$content = preg_replace(
    "/return view\('{$singularLowerModelName}.create', compact\('{$singularLowerModelName}'\)\)\s*;/s",
    "return Inertia::render('{$inertiaPath}/Create');",
    $content
);

// Reemplazamos el método show completo para aplicar Route Model Binding.
$content = preg_replace(
    "/public function show\(\$id\)\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);\s*return view\('{$singularLowerModelName}.show', compact\('{$singularLowerModelName}'\)\);\s*\}/s",
    "public function show({$modelName} \${$singularLowerModelName})\n    {\n        return Inertia::render('{$inertiaPath}/Show', [\n            '{$singularLowerModelName}' => \${$singularLowerModelName},\n        ]);\n    }",
    $content
);

// Reemplazamos el método edit completo para aplicar Route Model Binding.
$content = preg_replace(
    "/public function edit\(\$id\)\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);\s*return view\('{$singularLowerModelName}.edit', compact\('{$singularLowerModelName}'\)\);\s*\}/s",
    "public function edit({$modelName} \${$singularLowerModelName})\n    {\n        return Inertia::render('{$inertiaPath}/Edit', [\n            '{$singularLowerModelName}' => \${$singularLowerModelName},\n        ]);\n    }",
    $content
);

// Reemplazamos el método destroy completo para aplicar Route Model Binding.
$content = preg_replace(
    "/public function destroy\(\$id\): RedirectResponse\s*\{\s*([a-z]+)::find\(\$id\)->delete\(\);\s*return Redirect::route\('{$pluralModelName}.index'\)\s*->with\('success', '{$modelName} deleted successfully'\);\s*\}/s",
    "public function destroy({$modelName} \${$singularLowerModelName}): RedirectResponse\n    {\n        \${$singularLowerModelName}->delete();\n\n        return Redirect::route('{$pluralModelName}.index')\n            ->with('success', '{$modelName} deleted successfully');\n    }",
    $content
);

// 4. Escribimos el contenido modificado en el archivo.
file_put_contents($controllerPath, $content);

echo "¡Controlador convertido a Inertia.js con éxito!\n";
