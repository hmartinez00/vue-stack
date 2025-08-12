<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Productos',
        href: route('products.index'),
    },
];

const destroy = (id: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        router.delete(route('products.destroy', id));
    }
};
</script>

<template>
    <Head title="Productos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Contenedor principal que se adapta al diseño del dashboard -->
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Contenedor del panel principal con fondo y bordes adaptativos -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Productos</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Una lista de todos los productos.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('products.create')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
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
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">ID</th>
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre</th>
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Descripción</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                            <tr v-for="product in props.products.data" :key="product.id" class="even:bg-gray-50 dark:even:bg-gray-700">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ product.id }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ product.name }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ product.description }}</td>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <Link :href="route('products.show', product.id)" class="text-gray-600 font-bold hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-2">Ver</Link>
                                                    <Link :href="route('products.edit', product.id)" class="text-indigo-600 font-bold hover:text-indigo-900 mr-2">Editar</Link>
                                                    <Link @click.prevent="destroy(product.id)" class="text-red-600 font-bold hover:text-red-900">Eliminar</Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mt-4 px-4">
                                        <!-- Paginación con lógica para modo oscuro -->
                                        <div v-if="props.products.links && props.products.links.length > 3" class="flex justify-between items-center text-sm">
                                            <Component
                                                :is="Link"
                                                v-for="link in props.products.links"
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
