<?php

// Incluye el autoloader de Composer para cargar las dependencias.
require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Valida que se haya pasado el nombre del modelo como argumento.
 */
if ($argc < 2) {
    die("Uso: php scripts/converters/convert.php <NombreDelModelo>\n");
}

$singularLowerModelName = strtolower($argv[1]);
$modelName = ucfirst($singularLowerModelName);

echo "\n¡Generando archivos para el modelo '{$modelName}'!\n";

/**
 * Función para ejecutar un script de PHP con argumentos.
 *
 * @param string $phpScript El nombre del script a ejecutar.
 * @param string $modelName El nombre del modelo.
 * @param string|null $viewName El nombre de la vista (opcional).
 * @return void
 */
function executePhpScript(string $phpScript, string $modelName, ?string $viewName = null): void
{
    $phpExecutable = 'php';

    // Construye el comando con el ejecutable y los argumentos.
    if ($viewName) {
        $command = [$phpExecutable, $phpScript, $modelName, $viewName];
    } else {
        $command = [$phpExecutable, $phpScript, $modelName];
    }

    $process = new Process($command);

    try {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo "✅ Script '{$phpScript}' ejecutado correctamente.\n";

    } catch (ProcessFailedException $exception) {
        echo '❌ Error al ejecutar el script ' . $phpScript . ': ' . $exception->getMessage();
    } catch (\Exception $e) {
        echo '❌ Ocurrió un error inesperado durante el proceso: ' . $e->getMessage();
    }
}

/**
 * Lógica de conversión para cada tipo de archivo.
 */
executePhpScript('scripts/converters/convert_controller_v3.php', $modelName);
executePhpScript('scripts/converters/convert_form.php', $modelName, 'form');
executePhpScript('scripts/converters/convert_index.php', $modelName, 'index');
executePhpScript('scripts/converters/convert_create.php', $modelName, 'create');
executePhpScript('scripts/converters/convert_edit.php', $modelName, 'edit');
executePhpScript('scripts/converters/convert_show.php', $modelName, 'show');

echo "\nTodos los archivos se han generado exitosamente! 🎉\n";
