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

// 3. Definimos un array asociativo con los patrones de búsqueda y sus reemplazos.
// Esto centraliza la lógica de sustitución para una mejor legibilidad y mantenimiento.
$replacements = [
    // Reemplaza el 'use' statement de View por Inertia.
    'use Illuminate\View\View;' => 'use Inertia\Inertia;',

    // Elimina la declaración de tipo ': View' en todos los métodos que retornan vistas.
    '/: View/' => '',

    // Reemplaza el return del método index.
    "/return view\('{$singularLowerModelName}.index', compact\('{$pluralModelName}'\)\)\s*->with\(.*?\);/s" => "return Inertia::render('{$inertiaPath}/Index', [\n            '{$pluralModelName}' => \${$pluralModelName},\n        ]);",

    // Reemplaza el return del método create.
    "/return view\('{$singularLowerModelName}.create', compact\('{$singularLowerModelName}'\)\)\s*;/s" => "return Inertia::render('{$inertiaPath}/Create');",

    // Reemplaza el método show completo para aplicar Route Model Binding.
    "/public function show\(\$id\)\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);\s*return view\('{$singularLowerModelName}.show', compact\('{$singularLowerModelName}'\)\);\s*\}/s" => "public function show({$modelName} \${$singularLowerModelName})\n    {\n        return Inertia::render('{$inertiaPath}/Show', [\n            '{$singularLowerModelName}' => \${$singularLowerModelName},\n        ]);\n    }",

    // Reemplaza el método edit completo para aplicar Route Model Binding.
    "/public function edit\(\$id\)\s*\{\s*\$(.*?)\s*=\s*([a-z]+)::find\(\$id\);\s*return view\('{$singularLowerModelName}.edit', compact\('{$singularLowerModelName}'\)\);\s*\}/s" => "public function edit({$modelName} \${$singularLowerModelName})\n    {\n        return Inertia::render('{$inertiaPath}/Edit', [\n            '{$singularLowerModelName}' => \${$singularLowerModelName},\n        ]);\n    }",

    // Reemplaza el método destroy completo para aplicar Route Model Binding.
    "/public function destroy\(\$id\): RedirectResponse\s*\{\s*([a-z]+)::find\(\$id\)->delete\(\);\s*return Redirect::route\('{$pluralModelName}.index'\)\s*->with\('success', '{$modelName} deleted successfully'\);\s*\}/s" => "public function destroy({$modelName} \${$singularLowerModelName}): RedirectResponse\n    {\n        \${$singularLowerModelName}->delete();\n\n        return Redirect::route('{$pluralModelName}.index')\n            ->with('success', '{$modelName} deleted successfully');\n    }",
];

echo "\nPatrones a buscar (con '{$modelName}' y '{$pluralModelName}'):\n";
foreach (array_keys($replacements) as $pattern) {
    echo "- " . $pattern . "\n";
}
echo "\nRealizando sustituciones...\n";


// 4. Recorremos el array y realizamos las sustituciones de código.
foreach ($replacements as $pattern => $replacement) {
    // Si el patrón comienza y termina con '/', lo tratamos como una expresión regular.
    if (str_starts_with($pattern, '/') && str_ends_with($pattern, '/')) {
        echo "Patrón de regex: " . $pattern . "\n";
        echo "Reemplazo de regex: " . $replacement . "\n";
        $content = preg_replace($pattern, $replacement, $content);
    } else {
        // De lo contrario, lo tratamos como una cadena literal.
        echo "Patrón de cadena: " . $pattern . "\n";
        echo "Reemplazo de cadena: " . $replacement . "\n";
        $content = str_replace($pattern, $replacement, $content);
    }
}

echo $content;

// // 5. Escribimos el contenido modificado en el archivo.
// file_put_contents($controllerPath, $content);

// echo "¡Controlador convertido a Inertia.js con éxito!\n";
