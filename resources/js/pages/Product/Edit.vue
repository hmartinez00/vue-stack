<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Product } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Form from '@/pages/Product/Form.vue';

// Definimos las props que el componente recibirá.
// El controlador le pasará el objeto 'product' completo.
const props = defineProps<{
    product: Product;
}>();

// Definimos el estado del formulario e inicializamos sus campos
// con los datos del producto que recibimos como prop.
const form = useForm({
    name: props.product.name,
    description: props.product.description,
});

// Definimos el array para el breadcrumb de la página
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Productos',
        href: route('products.index'),
    },
    {
        title: 'Editar',
        href: route('products.edit', props.product.id),
    },
];

// Función para enviar el formulario de actualización
// `form.patch` hace una petición PATCH a la ruta 'products.update'
const submit = () => {
    form.patch(route('products.update', props.product.id), {
        // El controlador se encargará de la redirección y el mensaje de éxito
    });
};
</script>

<template>
    <Head :title="`Editar Producto: ${props.product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Contenedor principal que se adapta al diseño del dashboard -->
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Contenedor del panel principal con fondo y bordes adaptativos -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Editar Producto</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Actualiza la información del producto.</p>
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
