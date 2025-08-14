<script setup lang="ts">
import { defineProps, defineEmits, withDefaults } from 'vue';
import type { Form } from '@inertiajs/vue3';

const props = withDefaults(defineProps<{
    form: Form<{
        migration: string;
        batch: string;
    }>;
    buttonText?: string;
}>(), {
    buttonText: 'Guardar'
});

const emit = defineEmits(['submit']);

const submit = () => {
    emit('submit');
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">

        <!-- Campo para Migration del producto -->
        <div>
            <label for="migration" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Migration
            </label>
            <input
                id="migration"
                v-model="props.form.migration"
                type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 bg-white/50 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                autocomplete="migration"
                placeholder="Migration del producto"
            />
            <!-- Muestra el error de validación para el campo 'migration' -->
            <div v-if="props.form.errors.migration" class="text-sm text-red-600 dark:text-red-400 mt-2">
                {{ props.form.errors.migration }}
            </div>
        </div>

        <!-- Campo para Batch del producto -->
        <div>
            <label for="batch" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Batch
            </label>
            <input
                id="batch"
                v-model="props.form.batch"
                type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 bg-white/50 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                autocomplete="batch"
                placeholder="Batch del producto"
            />
            <!-- Muestra el error de validación para el campo 'batch' -->
            <div v-if="props.form.errors.batch" class="text-sm text-red-600 dark:text-red-400 mt-2">
                {{ props.form.errors.batch }}
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :disabled="props.form.processing"
            >
                {{ props.buttonText }}
            </button>
        </div>
    </form>
</template>