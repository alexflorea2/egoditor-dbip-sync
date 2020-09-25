import "./bootstrap"

import Vue from 'vue'
import Vuelidate from 'vuelidate';
Vue.use(Vuelidate)

import App from './App.vue'
import FrontRouter from "./router/front"

import BaseLayout from "./layouts/Base.vue";

import i18n from './transalations'
import FlagIcon from 'vue-flag-icon';

Vue.use(FlagIcon);

Vue.component('default-layout', BaseLayout);
new Vue({
    i18n,
    el: "#app",
    router: FrontRouter,
    render: (h) => h(App)
});
