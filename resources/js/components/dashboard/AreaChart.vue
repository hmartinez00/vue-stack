<script setup lang="ts">
// Importa las funciones necesarias de Vue
import { ref } from 'vue';
// Importamos el componente de ApexCharts
import VueApexCharts from 'vue3-apexcharts';

// Define las series del gráfico con los datos de área
// La propiedad 'data' contiene los valores que se graficarán
const series = ref([
  {
    data: [0, -41, 35, -51, 0, 62, -69, 32, -32, 54, 16, -50],
  }
]);

// Define las opciones del gráfico de área
const chartOptions = ref({
  chart: {
    height: 350,
    type: 'area',
    // Deshabilita el zoom en el gráfico
    zoom: {
      enabled: false,
    },
  },
  // Deshabilita las etiquetas de datos en los puntos del gráfico
  dataLabels: {
    enabled: false,
  },
  // Título del gráfico
  title: {
    text: 'Negative color for values less than 0',
    align: 'left',
  },
  // Etiquetas para el eje X (meses)
  xaxis: {
    categories: [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec',
    ],
  },
  // Opciones para la línea del gráfico
  stroke: {
    width: 0,
  },
  // Configura los colores de la línea y el área según el valor
  plotOptions: {
    line: {
      colors: {
        threshold: 0,
        colorAboveThreshold: '#0088ee', // Color para valores mayores a 0
        colorBelowThreshold: '#ff0000', // Color para valores menores a 0
      },
    },
  }
});
</script>

<template>
  <!-- Contenedor principal con estilos de Tailwind -->
  <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg shadow-inner">
    <!-- Título del componente -->
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Gráfico de Área con Colores Negativos</h2>
    <!-- Contenedor del gráfico -->
    <div class="w-full max-w-2xl bg-white p-6 rounded-lg shadow-xl">
      <!-- Componente de ApexCharts -->
      <apexchart
        type="area"
        height="350"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </div>
  </div>
</template>

<style scoped>
/* Estilos específicos para este componente (opcional) */
</style>
