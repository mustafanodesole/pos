<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="bg-white text-black">
      <q-toolbar>
        <q-btn
          color="black"
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title> AMS </q-toolbar-title>

        <!-- <div class="">
          <q-input dense rounded outlined placeholder="Search Your Product">
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
</q-input>

</div> -->
        <div class="q-px-md">
          <q-btn
            v-if="route.path == '/'"
            class="text-capitalize text-subtitle2"
            label="Add Products"
            color="blue-9"
            to="/add"
          />
          <q-btn
            v-if="route.path == '/add'"
            class="text-capitalize text-subtitle2"
            label="View Products"
            color="blue-9"
            to="/"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-primary text-white"
    >
      <q-list>
        <q-item-label header class="text-white"> Point of Sale </q-item-label>
        <q-separator horizontal />
        <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from "vue";
import EssentialLink from "components/EssentialLink.vue";
import { useRoute } from "vue-router";
const route = useRoute();
console.log(route.path);

const linksList = [
  {
    title: "Home",
    icon: "home",
    link: "/",
  },

  {
    title: "Add Product",
    icon: "inventory_2",
    link: "/add",
  },
];

const leftDrawerOpen = ref(false);

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value;
}
</script>
