<template>
    <div class="base--layout">

        <top-navigation></top-navigation>
        <sub-navigation></sub-navigation>

        <div class="container">
            <router-view :key="$route.path"></router-view>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'

import TopNavigation from "../components/top-navigation.vue";
import SubNavigation from "../components/sub-navigation.vue";

@Component({
    components: {
        TopNavigation,
        SubNavigation
    }
})

export default class BaseLayout extends Vue {}
</script>
