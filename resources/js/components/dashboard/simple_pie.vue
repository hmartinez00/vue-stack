<script setup lang="ts">
// Importa las librerías necesarias
import { ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Define las propiedades reactivas del gráfico
const series = ref<number[]>([44, 55, 13, 43, 22]);

// ** Cambio aquí: Se crea una propiedad reactiva para las opciones del gráfico **
const chartOptions = ref({
  chart: {
    width: 380,
    type: 'pie',
  },
  labels: ['Equipo A', 'Equipo B', 'Equipo C', 'Equipo D', 'Equipo E'],
  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: {
          width: 200,
        },
        legend: {
          position: 'bottom',
        },
      },
    },
  ],
});
</script>

<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4 text-center">Gráfico de Equipos</h2>
    <div class="bg-white p-4 rounded-lg shadow-md max-w-sm mx-auto">
      <!-- ** Cambio aquí: Se pasa el objeto de opciones directamente ** -->
      <apexchart
        type="pie"
        width="100%"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </div>
  </div>
</template>

<style scoped>
/* Estilos específicos para este componente */
</style>
