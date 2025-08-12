<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits, withDefaults } from 'vue';
import type { Form } from '@inertiajs/vue3';

// Definimos las props que este componente recibirá.
// La prop 'form' contendrá el objeto del formulario de Inertia.
// Usamos withDefaults para darle a 'buttonText' un valor por defecto.
const props = withDefaults(defineProps<{
    form: Form<{
        name: string;
        description: string;
    }>;
    buttonText?: string;
}>(), {
    buttonText: 'Guardar'
});

// Definimos los eventos que este componente puede emitir.
// En este caso, emitiremos un evento 'submit' cuando se envíe el formulario.
const emit = defineEmits(['submit']);

const submit = () => {
    // Emitimos el evento 'submit' para que el componente padre (Create.vue o Edit.vue)
    // pueda manejar la lógica de la petición (post, put, etc.).
    emit('submit');
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">

        <!-- Campo para el nombre del producto -->
        <div>
            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Nombre
            </label>
            <input
                id="name"
                v-model="props.form.name"
                type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 bg-white/50 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                autocomplete="name"
                placeholder="Nombre del producto"
            />
            <!-- Muestra el error de validación para el campo 'name' -->
            <div v-if="props.form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-2">
                {{ props.form.errors.name }}
            </div>
        </div>

        <!-- Campo para la descripción del producto -->
        <div>
            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Descripción
            </label>
            <input
                id="description"
                v-model="props.form.description"
                type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 bg-white/50 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                autocomplete="description"
                placeholder="Descripción del producto"
            />
            <!-- Muestra el error de validación para el campo 'description' -->
            <div v-if="props.form.errors.description" class="text-sm text-red-600 dark:text-red-400 mt-2">
                {{ props.form.errors.description }}
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- El texto del botón ahora se define con la prop 'buttonText' -->
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
