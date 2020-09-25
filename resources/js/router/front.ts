import Vue from "vue";
import VueRouter from "vue-router";

import UserProfilePage from "../pages/user_profile.vue";
import NotFound from "../pages/404.vue";

const BASE_LAYOUT = "base";

Vue.use(VueRouter);

const routes = [
   /* {
        path:'/',
        redirect: '/user-profile'
    },*/
    {
        path: "/user-profile",
        name: "user-profile",
        component: UserProfilePage,
    },
    {
        path: '*',
        meta: {
            layout: BASE_LAYOUT
        },
        component: NotFound
    }
];

const router = new VueRouter({
    base: "/",
    mode: "history",
    routes
});

export default router;
