<template>
    <div class="jumbotron">
        <div class="container">
            <h2 class="large--title">Egoditor GmbH</h2>

            <div>
                <button v-for="entry in languages" :key="entry.title" @click="changeLocale(entry.language)">
                    <flag :iso="entry.flag" v-bind:squared=false></flag>
                    {{ entry.title }}
                </button>
            </div>

            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">User management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">White label</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">API keys</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Billing</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'
import i18n from '../transalations'

@Component
export default class SubNavigation extends Vue {
    languages = [
        {flag: 'us', language: 'en', title: 'English'},
        {flag: 'es', language: 'es', title: 'Español'}
    ]

    changeLocale(locale: string) {
        i18n.locale = locale;
    }
}
</script>
