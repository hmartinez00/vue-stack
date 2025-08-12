<?php

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


