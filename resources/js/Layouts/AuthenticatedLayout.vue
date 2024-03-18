<script setup>
import { ref } from 'vue';
import SideNav from '@/Components/Dashboard/SideNav.vue';
import NavBar from '@/Components/Dashboard/NavBar.vue';
</script>

<template>
  <body class="m-0 font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    <!-- sidenav  -->
    <SideNav/>
    <!-- end sidenav -->

    <main class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl">
      <!-- Navbar -->
      <NavBar/>
      <!-- end Navbar -->
      <!-- cards -->
      <slot></slot>
      <!-- end cards -->
    </main>

  </body>

  <!-- <script src="../assets/js/plugins/chartjs.min.js" async></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js" async></script>-->

</template>