<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Product } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

// Definimos las props que el componente recibirá.
// El controlador le pasará el objeto 'product' completo.
const props = defineProps<{
    product: Product;
}>();

// Definimos el array para el breadcrumb de la página
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Productos',
        href: route('products.index'),
    },
    {
        title: props.product.name,
        href: route('products.show', props.product.id),
    },
];
</script>

<template>
    <Head :title="`Ver Producto: ${props.product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Contenedor principal que se adapta al diseño del dashboard -->
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Contenedor del panel principal con fondo y bordes adaptativos -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Ver Producto</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Detalles de {{ props.product.name }}.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <!-- Usamos el componente Link de Inertia para el botón de "Volver" -->
                                <Link :href="route('products.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="inline-block min-w-full py-2 align-middle">
                                    <div class="mt-6 border-t border-gray-100 dark:border-gray-700">
                                        <dl class="divide-y divide-gray-100 dark:divide-gray-700">

                                            <!-- Fila para el nombre del producto -->
                                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Nombre</dt>
                                                <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ props.product.name }}</dd>
                                            </div>

                                            <!-- Fila para la descripción del producto -->
                                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Descripción</dt>
                                                <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ props.product.description }}</dd>
                                            </div>

                                        </dl>
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
