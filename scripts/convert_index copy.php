<?php


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

