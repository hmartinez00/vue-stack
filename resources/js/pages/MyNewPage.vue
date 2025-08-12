<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { defineProps } from 'vue';

// Definimos las props que el componente recibirá
const props = defineProps({
  myMessage: {
    type: String,
    default: 'Mensaje por defecto',
  },
  usersList: {
    type: Array, // El tipo de dato para el arreglo de usuarios es 'Array'
    default: () => [], // Un valor por defecto para evitar errores
  },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mi Nueva Página',
        href: '/my-new-page',
    },
];
</script>

<template>
    <Head title="Mi Nueva Página" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-center">
                    <h2 class="text-xl font-bold text-center p-4">{{ props.myMessage }}</h2>
                </div>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
                <h3 class="text-lg font-semibold mb-4">Lista de Usuarios:</h3>
                <ul>
                    <li v-for="user in props.usersList" :key="user.id">
                        {{ user.name }} - {{ user.email }}
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
