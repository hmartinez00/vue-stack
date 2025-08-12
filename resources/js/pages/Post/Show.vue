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
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Detalles del producto.</p>
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
                                <div class="max-w-xl py-2 align-middle">
                                    <div class="p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">									<div class="form-group mb-4">
										<label class="text-gray-500 dark:text-gray-400 font-bold">title</label>
										<p class="text-gray-900 dark:text-gray-100">{{ props.post.title }}</p>
									</div>
									<div class="form-group mb-4">
										<label class="text-gray-500 dark:text-gray-400 font-bold">content</label>
										<p class="text-gray-900 dark:text-gray-100">{{ props.post.content }}</p>
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
