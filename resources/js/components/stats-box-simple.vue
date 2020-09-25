<template>
    <div class="stats-box-simple">
        <div class="stats-box-simple__title text-muted">
            <slot v-if="hasSlot('title')" name="title"></slot>
            <template v-else>
                {{title}}
            </template>
        </div>
        <div class="stats-box-simple__info">
            <slot v-if="hasSlot('info')" name="info"></slot>
            <template v-else>
                {{info}}
            </template>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'

// Define the props by using Vue's canonical way.
const StatsProps = Vue.extend({
    props: {
        title: String,
        info: String
    }
})

@Component
export default class StatsBoxSimple extends StatsProps {
    hasSlot(name = 'default') {
        return !!this.$slots[name] || !!this.$scopedSlots[name];
    }
}
</script>
