<template>
    <component :is="layout">
        <router-view :key="$route.path"></router-view>
    </component>
</template>


<script lang="ts">
const DEFAULT_LAYOUT = "default";

import Vue from 'vue'
import Component from 'vue-class-component'


@Component
export default class App extends Vue {


    get layout(): string {
        return `${(this.$route.meta.layout || DEFAULT_LAYOUT)}-layout`;
    }
}
</script>
