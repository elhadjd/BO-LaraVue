<script setup lang="ts">
import DataStatsOne from '@/Components/DataStats/DataStatsOne.vue'
import ChartOne from '@/Components/Charts/ChartOne.vue'
import ChartThree from '@/Components/Charts/ChartThree.vue'
import ChartTwo from '@/Components/Charts/ChartTwo.vue'
import MapOne from '@/Components/Maps/MapOne.vue'
import DefaultLayout from '@/Layouts/DefaultLayout.vue'
</script>

<template>
  <DefaultLayout>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
      <DataStatsOne />
    </div>

    <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
      <!-- ====== Chart One Start -->
      <ChartOne />
      <!-- ====== Chart One End -->

      <!-- ====== Chart Two Start -->
      <ChartTwo />
      <!-- ====== Chart Two End -->

      <!-- ====== Chart Three Start -->
      <ChartThree />
      <!-- ====== Chart Three End -->

      <!-- ====== Map One Start -->
      <MapOne />
      <!-- ====== Map One End -->

    </div>
  </DefaultLayout>
</template>
