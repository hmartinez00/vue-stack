<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { defineProps, computed } from 'vue'; // Se añade 'computed' para las propiedades reactivas

// Define las props que el componente recibirá
const props = defineProps({
    myMessage: {
        type: String,
        default: 'Mensaje por defecto', // Opcional: Define un valor por defecto
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// --- NUEVA LÓGICA DE PERMISOS ---
// 1. Accede a las props globales de Inertia
const page = usePage();

// 2. Crea una propiedad computada para acceder a los permisos de forma segura
const permissions = computed<string[]>(() => page.props.permissions as string[] || []);

// 3. Función para verificar si el usuario tiene un permiso
const can = (permission: string): boolean => {
    return permissions.value.includes(permission);
};
// --- FIN DE LA LÓGICA DE PERMISOS ---
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Se usa v-if para mostrar este div solo si el usuario tiene el permiso 'dashboard.view-super' -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-center">
                    <h2 class="text-xl font-bold text-center p-4">{{ props.myMessage }}</h2>

                    <div v-if="can('administrative')">
                        <p>Este mensaje es solo para el superusuario!</p>
                    </div>
                    
                </div>
                <!-- Los otros elementos se muestran sin condición -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
