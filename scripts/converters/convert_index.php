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
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    {$pluralModelName}: {
        type: Object,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '{$modelName}s',
        href: route('{$pluralModelName}.index'),
    },
];

const destroy = (id: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
        router.delete(route('{$pluralModelName}.destroy', id));
    }
};
</script>

<template>
    <Head title="{$modelName}s" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-950 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">{$pluralModelName}</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Una lista de todos los {$pluralModelName}.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('{$pluralModelName}.create')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Añadir nuevo
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="inline-block min-w-full py-2 align-middle">
                                    <table class="w-full divide-y divide-gray-300 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">No</th>\n
VUE;

foreach ($formFields as $field) {
    $vueCode .= "                                               <th scope=\"col\" class=\"py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400\">{$field}</th>\n";
}

$vueCode .= "                                             </tr>\n";
$vueCode .= "                                       </thead>\n";
$vueCode .= "                                       <tbody class=\"divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800\">\n\n";
$vueCode .= "                                           <tr v-for=\"({$singularLowerModelName}, index) in props.{$pluralModelName}.data\" :key=\"{$singularLowerModelName}.id\" class=\"even:bg-gray-50 dark:even:bg-gray-700\">\n";
$vueCode .= "                                               <td class=\"whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900 dark:text-gray-100\">\n";
$vueCode .= "                                                   <template v-if=\"props.{$pluralModelName}.meta\">\n";
$vueCode .= "                                                       {{ props.{$pluralModelName}.meta.from + index }}\n";
$vueCode .= "                                                   </template>\n";
$vueCode .= "                                                   <template v-else>\n";
$vueCode .= "                                                       {{ index + 1 }}\n";
$vueCode .= "                                                   </template>\n";
$vueCode .= "                                               </td>\n";

foreach ($formFields as $field) {
    $vueCode .= "                                               <td class=\"whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900 dark:text-gray-100\">{{ {$singularLowerModelName}.{$field} }}</td>\n";
}

$vueCode = $vueCode . "\n" . <<<VUE
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <Link :href="route('{$pluralModelName}.show', {$singularLowerModelName}.id)" class="text-gray-600 font-bold hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-2">Ver</Link>
                                                    <Link :href="route('{$pluralModelName}.edit', {$singularLowerModelName}.id)" class="text-indigo-600 font-bold hover:text-indigo-900 mr-2">Editar</Link>
                                                    <Link @click.prevent="destroy({$singularLowerModelName}.id)" class="text-red-600 font-bold hover:text-red-900">Eliminar</Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mt-4 px-4">
                                        <!-- Paginación con lógica para modo oscuro -->
                                        <div v-if="props.{$pluralModelName}.links && props.{$pluralModelName}.links.length > 3" class="flex justify-between items-center text-sm">
                                            <Component
                                                :is="Link"
                                                v-for="link in props.{$pluralModelName}.links"
                                                :key="link.label"
                                                :href="link.url"
                                                v-html="link.label"
                                                class="px-3 py-2 rounded-md transition duration-150 ease-in-out border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600"
                                                :class="{
                                                    'bg-indigo-500 text-white font-bold': link.active,
                                                    'text-gray-700 dark:text-gray-300': !link.active,
                                                    'cursor-not-allowed text-gray-400 dark:text-gray-600': !link.url
                                                }"
                                                :aria-disabled="!link.url"
                                                v-if="link.url"
                                            />
                                        </div>
                                    </div>
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
