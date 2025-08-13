<?php

// Agrega esta línea para incluir el autoloader de Composer.
// La ruta '../vendor/autoload.php' permite que el script
// encuentre las librerías instaladas por Laravel.
require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Valida que se hayan pasado el nombre del modelo y el nombre de la vista como argumentos.
 */
if ($argc < 2) {
    // die("Uso: php convert_blade_to_vue.php <NombreDelModelo> <NombreDeLaVista>\n");
    die("Uso: php convert_blade_to_vue.php <NombreDelModelo>\n");
}

$singularLowerModelName = strtolower($argv[1]);
$modelName = ucfirst($singularLowerModelName);
// $viewName = ucfirst($argv[2]);

echo "\n¡Generando archivos!\n";

/**
 * Función para ejecutar un script de PHP con argumentos.
 *
 * @param string $phpScript El nombre del script a ejecutar.
 * @param string $modelName El nombre del modelo.
 * @param string $viewName El nombre de la vista.
 * @return void
 */
function executePhpScript(string $phpScript, string $modelName, string $viewName): void
{
    $phpExecutable = 'php';

    // Aquí es donde se construye el comando completo, incluyendo el ejecutable y los argumentos.
    $command = [$phpExecutable, $phpScript, $modelName, $viewName];


    // Se inicializa la variable $process con el comando.
    $process = new Process($command);

    try {
        $process->run(); // Ejecuta el proceso

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // echo 'El procedimiento ha finalizado exitosamente.';

    } catch (ProcessFailedException $exception) {
        // En caso de error, muestra el mensaje de error del proceso.
        echo 'Error al ejecutar el script: ' . $exception->getMessage();

    } catch (\Exception $e) {
        echo 'Ocurrió un error inesperado durante el proceso: ' . $e->getMessage();
    }
}

/**
 * Lógica de conversión específica para cada tipo de vista.
 */

if ($viewName === 'Form') {
    echo "¡Haz seleccionado " . $viewName . "!\n";

    $phpScript = 'scripts/converters/convert_form.php';
    executePhpScript($phpScript, $modelName, $viewName);

} elseif ($viewName === 'Index') {
    echo "¡Haz seleccionado " . $viewName . "!\n";

    $phpScript = 'scripts/converters/convert_index.php';
    executePhpScript($phpScript, $modelName, $viewName);

} elseif ($viewName === 'Create') {
    echo "¡Haz seleccionado " . $viewName . "!\n";

    $phpScript = 'scripts/converters/convert_create.php';
    executePhpScript($phpScript, $modelName, $viewName);

} elseif ($viewName === 'Edit') {
    echo "¡Haz seleccionado " . $viewName . "!\n";

    $phpScript = 'scripts/converters/convert_edit.php';
    executePhpScript($phpScript, $modelName, $viewName);

} elseif ($viewName === 'Show') {
    echo "¡Haz seleccionado " . $viewName . "!\n";

    $phpScript = 'scripts/converters/convert_show.php';
    executePhpScript($phpScript, $modelName, $viewName);

} else {
    die("Error: No se ha implementado la lógica de conversión para la vista '{$viewName}'.\n");
}

