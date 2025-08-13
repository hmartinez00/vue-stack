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
// $fullBladePath = "../" . $bladePath;
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

// $vueDir = "../resources/js/Pages/{$modelName}";
$vueDir = "resources/js/Pages/{$modelName}";
$vuePath = "{$vueDir}/{$viewName}.vue";


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
$formPath = "resources/views/{$singularLowerModelName}/form.blade.php";
// $fullformPath = "../" . $formPath;
$fullformPath = "" . $formPath;
$formContent = file_get_contents($fullformPath);
$formFields = getFormFieldsFromBladeContent($formContent);

// -------------------------------------------------------------------------
// PASO 2: Generar el código del componente de Vue dinámicamente.
// -------------------------------------------------------------------------
$vueCode = <<<VUE
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type {$modelName} } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Form from '@/pages/{$modelName}/Form.vue';

const props = defineProps<{
    {$singularLowerModelName}: {$modelName};
}>();\n\n
VUE;

$vueCode .= "const form = useForm({\n";

foreach ($formFields as $field) {
    $vueCode .= "\t{$field}: props.{$singularLowerModelName}.{$field},\n";
}

$vueCode .= "});\n\n";

$vueCode = $vueCode . <<<VUE
// Definimos el array para el breadcrumb de la página
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '{$pluralModelName}',
        href: route('{$pluralModelName}.index'),
    },
    {
        title: 'Editar',
        href: route('{$pluralModelName}.edit', props.{$singularLowerModelName}.id),
    },
];

// Función para enviar el formulario de actualización
// `form.patch` hace una petición PATCH a la ruta '{$pluralModelName}.update'
const submit = () => {
    form.patch(route('{$pluralModelName}.update', props.{$singularLowerModelName}.id), {
        // El controlador se encargará de la redirección y el mensaje de éxito
    });
};
</script>

<template>
    <Head :title="`Editar {$singularLowerModelName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Contenedor principal que se adapta al diseño del dashboard -->
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Contenedor del panel principal con fondo y bordes adaptativos -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Editar {$singularLowerModelName}</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Actualiza la información del {$singularLowerModelName}.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <!-- Usamos el componente Link de Inertia para el botón de "Volver" -->
                                <Link :href="route('{$pluralModelName}.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="max-w-xl py-2 align-middle">
                                    <!-- Aquí usamos el nuevo componente Form -->
                                    <Form :form="form" @submit="submit" buttonText="Actualizar" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
VUE;


// -------------------------------------------------------------------------
// PASO 3: Imprimir el código del componente de Vue generado.
// -------------------------------------------------------------------------
// echo $vueCode;

file_put_contents($vuePath, $vueCode);

echo "¡Vista Blade convertida a componente Vue con éxito!\n";
