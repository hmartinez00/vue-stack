<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Database, BarChart3, Package } from 'lucide-vue-next'; // Importé un ícono de ejemplo para Migrations
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const permissions = computed<string[]>(() => page.props.permissions as string[] || []);

/**
 * Función para verificar si el usuario tiene un permiso específico.
 */
const can = (permission: string): boolean => {
    return permissions.value.includes(permission);
};

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Productos',
        href: route('products.index'),
        icon: Package,
    },
    {
        title: 'Posts',
        href: route('posts.index'),
        icon: Folder,
    },
    {
        title: 'Mi Nueva Página',
        href: route('my-new-page'),
        icon: BarChart3,
    },
    // El enlace de Migrations se añade al array solo si el usuario tiene el permiso
    ...(can('migrations.index') ? [
        {
            title: 'Migrations',
            href: route('migrations.index'),
            icon: Database,
        },
    ] : []),
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />

    <!-- Sección de depuración para mostrar los permisos -->
    <div v-if="permissions.length > 0" class="fixed bottom-0 right-0 p-4 bg-gray-800 text-white rounded-tl-lg shadow-lg">
        <h3 class="font-bold text-sm mb-2">Permisos del Usuario:</h3>
        <ul class="text-xs">
            <li v-for="permission in permissions" :key="permission" class="list-disc ml-4">{{ permission }}</li>
        </ul>
    </div>
</template>
