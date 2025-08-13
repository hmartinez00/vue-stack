<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Form from '@/pages/Post/Form.vue';

const form = useForm({
	title: '',
	content: '',
});

// Definimos el array para el breadcrumb de la página
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'posts',
        href: route('posts.index'),
    },
    {
        title: 'Create',
        href: route('posts.create'),
    },
];

// Función para enviar el formulario.
const submit = () => {
    // Usamos 'form.post' para enviar los datos y crear un nuevo producto.
    form.post(route('posts.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Create post</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Add a new post.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('posts.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Back
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="max-w-xl py-2 align-middle">
                                    <!-- Aquí usamos el componente Form. Ahora no hace falta pasarle el buttonText ya que tiene un valor por defecto -->
                                    <Form :form="form" @submit="submit" buttonText="Create" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>