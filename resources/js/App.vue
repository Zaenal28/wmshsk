<template>
  <div id="app" class="font-sans antialiased text-slate-800">
    <router-view v-if="isLoginRoute" />
    <AppLayout v-else>
      <router-view />
    </AppLayout>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppLayout from './layouts/AppLayout.vue';

const route = useRoute();
const isLoginRoute = computed(() => route.name === 'login');
</script>
