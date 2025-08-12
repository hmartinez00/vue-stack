<?php

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
$fullBladePath = "../" . $bladePath;

/**
 * Verificamos que el archivo Blade exista antes de intentar leerlo.
 */
if (!file_exists($fullBladePath)) {
    die("Error: El archivo Blade '{$fullBladePath}' no existe.\n");
}

echo "Procesando el archivo Blade: {$fullBladePath}\n";

$content = file_get_contents($fullBladePath);

$vueDir = "../resources/js/Pages/{$modelName}";
$vuePath = "{$vueDir}/{$viewName}.vue";

/**
 * Creamos el directorio si no existe.
 */
if (!is_dir($vueDir)) {
    mkdir($vueDir, 0777, true);
}

/**
 * Nueva función para extraer dinámicamente los campos del formulario.
 */
function getFormFieldsFromBlade($modelName) {
    $formBladePath = "../resources/views/{$modelName}/form.blade.php";
    if (!file_exists($formBladePath)) {
        return [];
    }

    $formContent = file_get_contents($formBladePath);
    preg_match_all('/id="([^"]+)"/i', $formContent, $matches);
    return $matches[1] ?? [];
}

/**
 * Extrae los campos de datos de una vista 'show' de Blade.
 * Busca las variables de Blade del modelo dentro de los tags HTML.
 */
function extractDataFields($htmlContent, $singularLowerModelName) {
    $fields = [];
    $pattern = '/<label[^>]*>(.*?)<\/label>.*?{{\s*\\\$' . $singularLowerModelName . '->(.*?)\s*}}/s';
    preg_match_all($pattern, $htmlContent, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $label = trim(strip_tags($match[1])); // Extraemos el texto de la etiqueta
        $field = trim($match[2]); // Extraemos el nombre del campo
        $fields[] = ['label' => $label, 'field' => $field];
    }
    return $fields;
}

$vueContent = "";

/**
 * Lógica de conversión específica para cada tipo de vista.
 */
if ($viewName === 'Index') {
    $htmlContent = $content;

    // Extraemos el contenido dentro de <x-app-layout>
    preg_match('/<x-app-layout>(.*?)<\/x-app-layout>/s', $htmlContent, $matches);
    $htmlContent = $matches[1];

    // Eliminamos el slot del header para manejarlo en el componente Vue.
    $htmlContent = preg_replace('/<x-slot name="header">(.*?)<\/x-slot>/s', '', $htmlContent);

    // Reemplazar la traducción de Blade por texto plano.
    $htmlContent = preg_replace_callback('/{{ __\((\'|")(.+?)(\'|")\) }}/', function ($matches) {
        $translations = [
            'Create' => 'Crear',
            'Products' => 'Productos',
            'Product' => 'Producto',
            'A list of all the ' => 'Una lista de todos los ',
            'Add new' => 'Añadir nuevo',
            'Edit' => 'Editar',
            'Delete' => 'Eliminar',
            'Show' => 'Ver',
            'Name' => 'Nombre',
            'Description' => 'Descripción',
            'Actions' => 'Acciones',
            'ID' => 'ID',
            'Are you sure to delete?' => '¿Estás seguro de que quieres eliminar este producto?',
        ];
        return $translations[$matches[2]] ?? $matches[2];
    }, $htmlContent);

    // Reemplazar clases de Tailwind para añadir soporte de modo oscuro.
    $htmlContent = str_replace(
        ['bg-white', 'text-gray-900', 'text-gray-700', 'text-gray-500', 'divide-gray-300', 'divide-gray-200'],
        ['bg-white dark:bg-gray-800', 'text-gray-900 dark:text-gray-100', 'text-gray-700 dark:text-gray-400', 'text-gray-500 dark:text-gray-400', 'divide-gray-300 dark:divide-gray-700', 'divide-gray-200 dark:divide-gray-700'],
        $htmlContent
    );

    // Convertir los bucles @foreach a v-for.
    $htmlContent = preg_replace(
        "/@foreach \(\\\${$pluralModelName} as \\\${$singularLowerModelName}\)\s*(.*?)\s*@endforeach/s",
        "<tr v-for=\"{$singularLowerModelName} in props.{$pluralModelName}.data\" :key=\"{$singularLowerModelName}.id\" class=\"even:bg-gray-50 dark:even:bg-gray-700\">\\1</tr>",
        $htmlContent
    );
    // Reemplazar las variables de Blade.
    $htmlContent = preg_replace("/{{\s*\\\${$singularLowerModelName}->(.*?)\s*}}/", "{{ {$singularLowerModelName}.$1 }}", $htmlContent);
    // Reemplazar el contador de Blade
    $htmlContent = preg_replace("/{{\s*\+\+\\\$i\s*}}/", "{{ {$singularLowerModelName}.id }}", $htmlContent);
    // Convertir los links de Blade a componentes <Link> de Inertia.
    $htmlContent = preg_replace(
        '/type="button" href="{{ route\(\''.$pluralModelName.'\.create\'\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.create\')" class="$1">$2</Link>',
        $htmlContent
    );
    $htmlContent = preg_replace(
        '/href="{{ route\(\''.$pluralModelName.'\.show\', \$'.$singularLowerModelName.'->id\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.show\', '.$singularLowerModelName.'.id)" class="$1">$2</Link>',
        $htmlContent
    );
    $htmlContent = preg_replace(
        '/href="{{ route\(\''.$pluralModelName.'\.edit\', \$'.$singularLowerModelName.'->id\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.edit\', '.$singularLowerModelName.'.id)" class="$1">$2</Link>',
        $htmlContent
    );
    // Reemplazar el formulario de eliminación por un link con evento click.
    $htmlContent = preg_replace(
        '/action="{{ route\(\''.$pluralModelName.'\.destroy\', \$'.$singularLowerModelName.'->id\) }}" method="POST">(.*?)<a href="{{ route\(\''.$pluralModelName.'\.destroy\', \$'.$singularLowerModelName.'->id\) }}" class="(.*?)" onclick="event.preventDefault\(\); confirm\(\'Are you sure to delete\?\'\) \? this\.closest\(\'form\'\)\.submit\(\) : false;\">(.*?)<\/a>(.*?)<\/form>/s',
        ' @click.prevent="destroy('.$singularLowerModelName.'.id)" class="$2">$3</Link>',
        $htmlContent
    );
    // Convertir la paginación de Blade a la paginación de Inertia
    $htmlContent = preg_replace(
        '/{!! \$'.$pluralModelName.'->withQueryString\(\)->links\(\) !!}/s',
        '<div v-if="props.'.$pluralModelName.'.links && props.'.$pluralModelName.'.links.length > 3" class="flex justify-between items-center text-sm">
                                                    <Component
                                                        :is="Link"
                                                        v-for="link in props.'.$pluralModelName.'.links"
                                                        :key="link.label"
                                                        :href="link.url"
                                                        v-html="link.label"
                                                        class="px-3 py-2 rounded-md transition duration-150 ease-in-out border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600"
                                                        :class="{
                                                            \'bg-indigo-500 text-white font-bold\': link.active,
                                                            \'text-gray-700 dark:text-gray-300\': !link.active,
                                                            \'cursor-not-allowed text-gray-400 dark:text-gray-600\': !link.url
                                                        }"
                                                        :aria-disabled="!link.url"
                                                        v-if="link.url"
                                                    />
                                                </div>',
        $htmlContent
    );
    // Reemplazar el `h2` del header y el `h1`
    $htmlContent = preg_replace('/<h2 class="(.*?)">\s*{{ __(\'|\")'.$modelName.'s(\'|\") }}\s*<\/h2>/s', '<h2 class="$1">Productos</h2>', $htmlContent);
    $htmlContent = preg_replace('/<h1 class="(.*?)">\s*{{ __(\'|\")'.$modelName.'s(\'|\") }}\s*<\/h1>/s', '<h1 class="$1">Productos</h1>', $htmlContent);
    // Reemplazar la descripción
    $htmlContent = preg_replace('/<p class="(.*?)">A list of all the {{ __(\'|\")'.$modelName.'s(\'|\") }}\.<\/p>/s', '<p class="$1">Una lista de todos los productos.</p>', $htmlContent);

    // Ensamblar el componente de Vue final.
    $vueContent = <<<VUE
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
                {$htmlContent}
            </div>
        </div>
    </AppLayout>
</template>
VUE;

} elseif ($viewName === 'Create') {
    // Lógica para la vista Create.
    $formFields = getFormFieldsFromBlade($singularLowerModelName);
    $formFieldsVue = implode(",\n\t", array_map(fn($field) => "$field: ''", $formFields));

    $htmlContent = preg_replace('/<x-slot name="header">(.*?)<\/x-slot>/s', '', $content);
    $htmlContent = preg_replace('/<a type="button" href="{{ route\(\''.$pluralModelName.'\.index\'\) }}" class="(.*?)">(.*?)<\/a>/s', '<Link :href="route(\''.$pluralModelName.'.index\')" class="$1">$2</Link>', $htmlContent);
    $htmlContent = preg_replace(
        '/^.*@include\(\''.$singularLowerModelName.'\.form\'\).*$/m',
        '<Form :form="form" @submit="submit" buttonText="Crear" />',
        $htmlContent
    );
    $vueContent = <<<VUE
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Form from '@/pages/{$modelName}/Form.vue';

const form = useForm({
    {$formFieldsVue},
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '{$modelName}s',
        href: route('{$pluralModelName}.index'),
    },
    {
        title: 'Crear',
        href: route('{$pluralModelName}.create'),
    },
];

const submit = () => {
    form.post(route('{$pluralModelName}.store'));
};
</script>

<template>
    <Head title="Crear {$modelName}" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Crear {$modelName}</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Añade un nuevo {$singularLowerModelName}.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('{$pluralModelName}.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="max-w-xl py-2 align-middle">
                                    <Form :form="form" @submit="submit" buttonText="Crear" />
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

} elseif ($viewName === 'Edit') {
    // Lógica para la vista Edit.
    $formFields = getFormFieldsFromBlade($singularLowerModelName);
    $formFieldsVue = implode(",\n\t", array_map(fn($field) => "$field: props.{$singularLowerModelName}.$field", $formFields));

    $htmlContent = $content;
    preg_match('/<x-app-layout>(.*?)<\/x-app-layout>/s', $htmlContent, $matches);
    $htmlContent = $matches[1];
    $htmlContent = preg_replace('/<x-slot name="header">(.*?)<\/x-slot>/s', '', $htmlContent);

    $htmlContent = preg_replace_callback('/{{ __\((\'|")(.+?)(\'|")\) }}/', function ($matches) {
        $translations = [
            'Update' => 'Actualizar',
            'Product' => 'Producto',
        ];
        return $translations[$matches[2]] ?? $matches[2];
    }, $htmlContent);
    $htmlContent = str_replace(
        ['bg-white', 'text-gray-900', 'text-gray-700'],
        ['bg-white dark:bg-gray-800', 'text-gray-900 dark:text-gray-100', 'text-gray-700 dark:text-gray-400'],
        $htmlContent
    );
    $htmlContent = preg_replace(
        '/href="{{ route\(\''.$pluralModelName.'\.index\'\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.index\')" class="$1">$2</Link>',
        $htmlContent
    );
    $htmlContent = preg_replace(
        '/^.*@include\(\''.$singularLowerModelName.'\.form\'\).*$/m',
        '<Form :form="form" @submit="submit" buttonText="Actualizar" />',
        $htmlContent
    );

    $vueContent = <<<VUE
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type {$modelName} } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Form from '@/pages/{$modelName}/Form.vue';

const props = defineProps<{
    {$singularLowerModelName}: {$modelName};
}>();

const form = useForm({
    {$formFieldsVue},
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '{$modelName}s',
        href: route('{$pluralModelName}.index'),
    },
    {
        title: 'Editar',
        href: route('{$pluralModelName}.edit', props.{$singularLowerModelName}.id),
    },
];

const submit = () => {
    form.patch(route('{$pluralModelName}.update', props.{$singularLowerModelName}.id));
};
</script>

<template>
    <Head :title="`Editar {$modelName}: \${props.{$singularLowerModelName}.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Editar {$modelName}</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Actualiza la información del producto.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('{$pluralModelName}.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="max-w-xl py-2 align-middle">
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

} elseif ($viewName === 'Show') {
    // Lógica para la vista Show.
    $htmlContent = $content;
    preg_match('/<x-app-layout>(.*?)<\/x-app-layout>/s', $htmlContent, $matches);
    $htmlContent = $matches[1];
    $htmlContent = preg_replace('/<x-slot name="header">(.*?)<\/x-slot>/s', '', $htmlContent);

    // Extraemos dinámicamente los campos de la vista 'show'
    $dataFields = extractDataFields($content, $singularLowerModelName);

    // Reemplazar la traducción de Blade por texto plano.
    $htmlContent = preg_replace_callback('/{{ __\((\'|")(.+?)(\'|")\) }}/', function ($matches) {
        $translations = [
            'Show' => 'Ver',
            'Product' => 'Producto',
            'Name' => 'Nombre',
            'Description' => 'Descripción',
        ];
        return $translations[$matches[2]] ?? $matches[2];
    }, $htmlContent);

    $htmlContent = str_replace(
        ['bg-white', 'text-gray-900', 'text-gray-700', 'text-gray-500'],
        ['bg-white dark:bg-gray-800', 'text-gray-900 dark:text-gray-100', 'text-gray-700 dark:text-gray-400', 'text-gray-500 dark:text-gray-400'],
        $htmlContent
    );

    $htmlContent = preg_replace(
        '/href="{{ route\(\''.$pluralModelName.'\.index\'\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.index\')" class="$1">$2</Link>',
        $htmlContent
    );
    $htmlContent = preg_replace(
        '/href="{{ route\(\''.$pluralModelName.'\.edit\', \$'.$singularLowerModelName.'->id\) }}" class="(.*?)">(.*?)<\/a>/s',
        ':href="route(\''.$pluralModelName.'.edit\', props.'.$singularLowerModelName.'.id)" class="$1">$2</Link>',
        $htmlContent
    );

    // Generamos el HTML para los campos de datos de forma dinámica
    $htmlFields = '';
    foreach ($dataFields as $field) {
        $htmlFields .= <<<HTML
                                                <div class="form-group mb-4">
                                                    <label class="text-gray-500 dark:text-gray-400 font-bold">{$field['label']}</label>
                                                    <p class="text-gray-900 dark:text-gray-100">{{ props.{$singularLowerModelName}.{$field['field']} }}</p>
                                                </div>
HTML;
    }

    // Reemplazamos los campos de datos estáticos con los generados dinámicamente
    // Buscamos un patrón que contenga los campos a reemplazar, por ejemplo, un div principal.
    $htmlContent = preg_replace('/<div class="p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">.*?<\/div>/s',
                                "<div class=\"p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg\">\n{$htmlFields}</div>",
                                $htmlContent, 1); // El '1' asegura que solo se reemplace la primera coincidencia

    $vueContent = <<<VUE
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type {$modelName} } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    {$singularLowerModelName}: {$modelName};
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '{$modelName}s',
        href: route('{$pluralModelName}.index'),
    },
    {
        title: 'Ver',
        href: route('{$pluralModelName}.show', props.{$singularLowerModelName}.id),
    },
];
</script>

<template>
    <Head :title="`Ver {$modelName}: \${props.{$singularLowerModelName}.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Ver {$modelName}</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Detalles del producto.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('{$pluralModelName}.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                                <Link :href="route('{$pluralModelName}.edit', props.{$singularLowerModelName}.id)" class="ml-2 block rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                    Editar
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="max-w-xl py-2 align-middle">
                                    <div class="p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                                        <div class="form-group mb-4">
                                            <label class="text-gray-500 dark:text-gray-400 font-bold">Nombre</label>
                                            <p class="text-gray-900 dark:text-gray-100">{{ props.{$singularLowerModelName}.name }}</p>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="text-gray-500 dark:text-gray-400 font-bold">Descripción</label>
                                            <p class="text-gray-900 dark:text-gray-100">{{ props.{$singularLowerModelName}.description }}</p>
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

} else {
    die("Error: No se ha implementado la lógica de conversión para la vista '{$viewName}'.\n");
}

file_put_contents($vuePath, $vueContent);

echo "¡Vista Blade convertida a componente Vue con éxito!\n";

