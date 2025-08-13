<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type post } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    post: post;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'posts',
        href: route('posts.index'),
    },
    {
        title: 'Ver',
        href: route('posts.show', props.post.id),
    },
];
</script>

<template>
    <Head :title="`Ver post: $\{props.post.name\}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="w-full">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Ver post</h1>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Detalles del post.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <Link :href="route('posts.index')" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Volver
                                </Link>
                                <Link :href="route('posts.edit', props.post.id)" class="ml-2 block rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                    Editar
                                </Link>
                            </div>
                        </div>

                        <div class="flow-root">
                            <div class="mt-8 overflow-x-auto">
                                <div class="inline-block min-w-full py-2 align-middle">
                                    <div class="mt-6 border-t border-gray-100 dark:border-gray-700">
                                        <div class="divide-y divide-gray-100 dark:divide-gray-700">

											<div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
												<dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">title</dt>
												<dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ props.post.title }}</dd>
											</div>

											<div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
												<dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">content</dt>
												<dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ props.post.content }}</dd>
											</div>


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