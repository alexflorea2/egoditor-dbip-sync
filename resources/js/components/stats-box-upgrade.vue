<template>
    <div class="stats-box-upgrade">
        <div class="stats-box-upgrade__title text-muted">
            <slot v-if="hasSlot('title')" name="title"></slot>
            <template v-else>
                {{title}}
            </template>
        </div>
        <div class="stats-box-upgrade__used">
            <slot v-if="hasSlot('used')" name="used"></slot>
            <template v-else>
                {{used}}
            </template>
        </div>
        <div class="stats-box-upgrade__available text-muted">
            <slot v-if="hasSlot('available')" name="available"></slot>
            <template v-else>
                {{available}}
            </template>
        </div>

        <button class="btn btn-upgrade text-capitalize">{{ $t('upgrade') }}</button>

    </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'

// Define the props by using Vue's canonical way.
const StatsProps = Vue.extend({
    props: [
        'title',
        'available',
        'used'
    ]
})

@Component
export default class StatsBoxUpgrade extends StatsProps {
    hasSlot(name = 'default') {
        return !!this.$slots[name] || !!this.$scopedSlots[name];
    }
}
</script>
