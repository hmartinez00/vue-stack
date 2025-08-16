<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { defineProps, computed } from 'vue';

// Importación optimizada de todos los componentes del directorio 'dashboard'
const dashboardComponents = import.meta.glob('../components/dashboard/*.vue', { eager: true });

// Define las props que el componente recibirá
const props = defineProps({
  myMessage: {
    type: String,
    default: 'Mensaje por defecto',
  },
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// --- LÓGICA DE PERMISOS ---
const page = usePage();
const permissions = computed<string[]>(() => page.props.permissions as string[] || []);
const can = (permission: string): boolean => {
  return permissions.value.includes(permission);
};
// --- FIN DE LA LÓGICA DE PERMISOS ---

// Propiedad computada para obtener los componentes listos para usar
const loadedComponents = computed(() => {
  const components = [];
  for (const path in dashboardComponents) {
    const component = dashboardComponents[path].default;
    components.push(component);
  }
  return components;
});

// Propiedad computada para ordenar los componentes en el orden deseado
const orderedComponents = computed(() => {
  const componentOrder = [
    'simple_pie.vue',
    'Funnel.vue',
    'AreaChart.vue',
  ];

  return componentOrder.map(fileName => {
    const path = `../components/dashboard/${fileName}`;
    return dashboardComponents[path].default;
  });
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-center">
          <h2 class="text-xl font-bold text-center p-4">{{ props.myMessage }}</h2>
          <div v-if="can('administrative')">
            <p>Este mensaje es solo para el superusuario!</p>
          </div>
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
        </div>
      </div>
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border flex flex-wrap justify-center gap-6">
        <!-- Renderiza los componentes de forma dinámica, usando la lista ordenada -->
        <component v-for="(comp, index) in orderedComponents" :key="index" :is="comp" />
      </div>
    </div>
  </AppLayout>
</template>
