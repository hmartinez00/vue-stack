<script setup lang="ts">
// Importa las funciones necesarias de Vue
import { ref } from 'vue';

// Define las series del gráfico con los datos de embudo
// El nombre de la serie ('Funnel Series') es opcional pero ayuda a la claridad
const series = ref([
  {
    name: "Funnel Series",
    data: [1380, 1100, 990, 880, 740, 548, 330, 200],
  },
]);

// Define las opciones del gráfico, incluyendo el tipo, las etiquetas y el estilo
const chartOptions = ref({
  chart: {
    type: 'bar',
    height: 350,
    // Sombra para el gráfico
    dropShadow: {
      enabled: true,
    },
  },
  // Opciones de la barra, como el radio de las esquinas y la altura
  plotOptions: {
    bar: {
      borderRadius: 0,
      horizontal: true,
      barHeight: '80%',
      isFunnel: true, // Esto convierte el gráfico de barras en un embudo
    },
  },
  // Etiquetas de datos en cada barra
  dataLabels: {
    enabled: true,
    // Función para formatear el texto de las etiquetas
    formatter: function (val: any, opt: any) {
      return opt.w.globals.labels[opt.dataPointIndex] + ':  ' + val;
    },
    dropShadow: {
      enabled: true,
    },
  },
  // Título del gráfico
  title: {
    text: 'Recruitment Funnel',
    align: 'middle',
  },
  // Etiquetas del eje X (eje de categorías)
  xaxis: {
    categories: [
      'Sourced',
      'Screened',
      'Assessed',
      'HR Interview',
      'Technical',
      'Verify',
      'Offered',
      'Hired',
    ],
  },
  // Ocultar la leyenda
  legend: {
    show: false,
  },
});
</script>

<template>
  <!-- Contenedor principal del componente con clases de Tailwind para centrado y espaciado -->
  <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg shadow-inner">
    <!-- Título con estilos de Tailwind -->
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Gráfico de Embudo</h2>
    <!-- Contenedor del gráfico con un diseño limpio y sombreado -->
    <div class="w-full max-w-2xl bg-white p-6 rounded-lg shadow-xl">
      <!-- El componente de ApexCharts con las propiedades del gráfico -->
      <apexchart
        type="bar"
        height="350"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </div>
  </div>
</template>

<style scoped>
/* Estilos específicos si son necesarios.
   Para este componente, todas las clases de estilo son de Tailwind CSS. */
</style>
