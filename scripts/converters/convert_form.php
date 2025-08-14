<?php

/**
 * Script de PHP para convertir un formulario Blade en un componente de Vue 3.
 *
 * Este script simula el proceso completo:
 * 1. Define el contenido de un archivo Blade de ejemplo.
 * 2. Utiliza una expresión regular para extraer los IDs de los campos (name, description, etc.).
 * 3. Con los IDs extraídos, genera dinámicamente el código completo de un componente de Vue.
 */

/**
 * Valida que se hayan pasado el nombre del modelo y el nombre de la vista como argumentos.
 */
if ($argc < 3) {
    die("Uso: php convert_blade_to_vue.php <NombreDelModelo> <NombreDeLaVista>\n");
}

/**
 * Convierte los argumentos a los nombres de variables que necesitamos.
 */
$singularLowerModelName = strtolower($argv[1]);
$modelName = ucfirst($singularLowerModelName);
$viewName = ucfirst($argv[2]);
$pluralModelName = strtolower($modelName) . 's';

/**
 * Definimos las rutas de los archivos.
 */
$bladePath = "resources/views/{$singularLowerModelName}/{$argv[2]}.blade.php";
$fullBladePath = "" . $bladePath;

/**
 * Verificamos que el archivo Blade exista antes de intentar leerlo.
 */
if (!file_exists($fullBladePath)) {
    die("Error: El archivo Blade '{$fullBladePath}' no existe.\n");
}

$bladeContent = file_get_contents($fullBladePath);

/**
 * Definimos el directorio de salida para los archivos Vue.
 */
$vueDir = "resources/js/Pages/{$modelName}";
$vuePath = "{$vueDir}/{$viewName}.vue";

// -------------------------------------------------------------------------
// MODIFICACIÓN:
// Verificamos si el directorio de destino existe, si no, lo creamos.
// Esto evita un error de 'file_put_contents' si la ruta no existe.
// -------------------------------------------------------------------------
if (!is_dir($vueDir)) {
    mkdir($vueDir, 0777, true);
    echo "Directorio creado: {$vueDir}\n";
}

/**
 * Función para extraer dinámicamente los IDs de los campos del formulario.
 * En un entorno real, leerías el archivo, pero aquí usamos un string.
 *
 * @param string $formContent El contenido del archivo Blade.
 * @return array Los nombres de los campos (ej: ['name', 'description']).
 */
function getFormFieldsFromBladeContent(string $formContent): array
{
    // Expresión regular para encontrar todos los atributos 'id="..."'
    preg_match_all('/id="([^"]+)"/i', $formContent, $matches);
    return $matches[1] ?? [];
}

// -------------------------------------------------------------------------
// PASO 1: Obtener los campos del formulario desde el contenido Blade.
// -------------------------------------------------------------------------
$formFields = getFormFieldsFromBladeContent($bladeContent);

// -------------------------------------------------------------------------
// PASO 2: Generar el código del componente de Vue dinámicamente.
// -------------------------------------------------------------------------
$vueCode = '';

// Iniciar el bloque <script setup>
$vueCode .= "<script setup lang=\"ts\">\n";
$vueCode .= "import { defineProps, defineEmits, withDefaults } from 'vue';\n";
$vueCode .= "import type { Form } from '@inertiajs/vue3';\n\n";

// Generar la definición de props dinámicamente
$vueCode .= "const props = withDefaults(defineProps<{\n";
$vueCode .= "    form: Form<{\n";
foreach ($formFields as $field) {
    $vueCode .= "        {$field}: string;\n";
}
$vueCode .= "    }>;\n";
$vueCode .= "    buttonText?: string;\n";
$vueCode .= "}>(), {\n";
$vueCode .= "    buttonText: 'Guardar'\n";
$vueCode .= "});\n\n";

// Agregar la definición de emits y la función de envío
$vueCode .= "const emit = defineEmits(['submit']);\n\n";
$vueCode .= "const submit = () => {\n";
$vueCode .= "    emit('submit');\n";
$vueCode .= "};\n";
$vueCode .= "</script>\n\n";

// Iniciar el bloque <template>
$vueCode .= "<template>\n";
$vueCode .= "    <form @submit.prevent=\"submit\" class=\"space-y-6\">\n\n";

// Generar los campos del formulario en un bucle
foreach ($formFields as $field) {
    $label = ucfirst($field); // Capitalizar el nombre del campo para la etiqueta
    $placeholder = "{$label} del producto"; // Placeholder dinámico

    $vueCode .= "        <!-- Campo para {$label} del producto -->\n";
    $vueCode .= "        <div>\n";
    $vueCode .= "            <label for=\"{$field}\" class=\"block font-medium text-sm text-gray-700 dark:text-gray-300\">\n";
    $vueCode .= "                {$label}\n";
    $vueCode .= "            </label>\n";
    $vueCode .= "            <input\n";
    $vueCode .= "                id=\"{$field}\"\n";
    $vueCode .= "                v-model=\"props.form.{$field}\"\n";
    $vueCode .= "                type=\"text\"\n";
    $vueCode .= "                class=\"mt-1 block w-full rounded-lg border-gray-300 bg-white/50 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6\"\n";
    $vueCode .= "                autocomplete=\"{$field}\"\n";
    $vueCode .= "                placeholder=\"{$placeholder}\"\n";
    $vueCode .= "            />\n";
    $vueCode .= "            <!-- Muestra el error de validación para el campo '{$field}' -->\n";
    $vueCode .= "            <div v-if=\"props.form.errors.{$field}\" class=\"text-sm text-red-600 dark:text-red-400 mt-2\">\n";
    $vueCode .= "                {{ props.form.errors.{$field} }}\n";
    $vueCode .= "            </div>\n";
    $vueCode .= "        </div>\n\n";
}

// Agregar el botón de envío
$vueCode .= "        <div class=\"flex items-center gap-4\">\n";
$vueCode .= "            <button\n";
$vueCode .= "                type=\"submit\"\n";
$vueCode .= "                class=\"inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150\"\n";
$vueCode .= "                :disabled=\"props.form.processing\"\n";
$vueCode .= "            >\n";
$vueCode .= "                {{ props.buttonText }}\n";
$vueCode .= "            </button>\n";
$vueCode .= "        </div>\n";

// Cerrar el bloque <template>
$vueCode .= "    </form>\n";
$vueCode .= "</template>";

// -------------------------------------------------------------------------
// PASO 3: Imprimir el código del componente de Vue generado.
// -------------------------------------------------------------------------
file_put_contents($vuePath, $vueCode);

echo "¡Vista Blade convertida a componente Vue con éxito!\n";

?>
