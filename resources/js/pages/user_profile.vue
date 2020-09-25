<template>
    <div class="row page page_user-profile">
        <template v-if="userProfile===null">
            <div class="loadingOverlay" >
                <div class="spinner"></div>
            </div>
        </template>
        <template v-else>
            <div  class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="section-title text-capitalize">{{ $t('informations') }}</h2>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <stats-box-simple title="CUSTOMER ID" :info="userProfile.customer_id"></stats-box-simple>
                            </div>
                            <div class="col-md-4">
                                <stats-box-simple title="SIGNUP DATE" :info="userProfile.signup_date"></stats-box-simple>
                            </div>
                            <div class="col-md-4">
                                <stats-box-simple title="ACCOUNT" :info="userProfile.account_type"></stats-box-simple>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="bordered-box info-large clearfix">
                                    <div class="float-left">
                                        <div>
                                            <span class="grey-title text-capitalize">{{ $t('login email') }}</span>
                                        </div>
                                        <div>
                                            <span class="dark-blue-title">andu2flo@gmail.com</span>
                                        </div>
                                    </div>
                                    <div class="float-right" style="text-align: right;">
                                        <div class=" text-capitalize">{{ $t('password') }}</div>
                                        <div class=" text-capitalize">{{ $t('change email') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="section-title text-capitalize">{{ $t('primary contact') }}</h2>
                                <p class="text-muted  text-capitalize">{{ $t('please provide your contact information bellow') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form  @submit.prevent="submit" >
                                    <div class="form-group">
                                        <label>{{ $t('first name') }}</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="John"
                                            v-model="form.first_name"
                                            :class="{ 'is-invalid': $v.form.first_name.$error  }"
                                        >
                                        <div class="invalid-feedback" v-if="!$v.form.first_name.required">Field is required</div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ $t('last name') }}</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Doe"
                                            v-model="form.last_name"
                                            :class="{ 'is-invalid': $v.form.last_name.$error  }"
                                        >
                                        <div class="invalid-feedback" v-if="!$v.form.first_name.required">Field is required</div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ $t('phone number') }}</label>
                                        <input type="text" class="form-control" placeholder="0749519936" v-model="form.phone_number">
                                    </div>

                                    <div class="form-group">
                                        <label>{{ $t('company') }}</label>
                                        <input type="text" class="form-control" placeholder="Acme.inc" v-model="form.company">
                                    </div>

                                    <div class="form-group">
                                        <label>{{ $t('street') }}</label>
                                        <input type="text" class="form-control" placeholder="Main" v-model="form.street">
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $t('city') }}</label>
                                                <input type="text" class="form-control" placeholder="New York" v-model="form.city">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $t('zip') }}</label>
                                                <input type="text" class="form-control" placeholder="08963" v-model="form.zip">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="form-group">
                                        <label>{{ $t('country') }}</label>
                                        <v-select label="name" :options="countriesList" v-model="form.country"  :class="{ 'is-invalid': $v.form.country.$error  }"></v-select>
                                        <div class="invalid-feedback" v-if="!$v.form.country.required">Please select a Country.</div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ $t('website') }}</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="www.my-site.com"
                                            v-model="form.website"
                                            :class="{ 'is-invalid': $v.form.website.$error  }"
                                        >
                                        <div class="invalid-feedback" v-if="!$v.form.website.required">Must be a valid URL.</div>
                                    </div>

                                    <button type="submit" class="btn btn-submit float-right">Save</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title">{{ $t('usage') }}</h2>
                    </div>
                </div>
                <div class="row">
                    <template v-for="stat in userProfile.package_quota">
                        <div class="col-md-3">
                            <stats-box-upgrade :title="$t(stat.label)" :used="stat.used" :available="stat.available"></stats-box-upgrade>
                        </div>
                    </template>
                </div>
            </div>
        </template>

    </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'
import {getUserProfile, getListOfCountries} from '../services/Api';
import StatsBoxUpgrade from "../components/stats-box-upgrade.vue";
import StatsBoxSimple from "../components/stats-box-simple.vue";
import { required, url } from 'vuelidate/lib/validators'
import vSelect from 'vue-select'
import Swal, {SweetAlertOptions} from 'sweetalert2';

@Component({
    components:{
        StatsBoxUpgrade,
        StatsBoxSimple,
        vSelect
    },
    validations: {
        form:{
            first_name: {required},
            last_name: {required},
            country: {required},
            website: {url},
        }
    }
})
export default class UserProfilePage extends Vue {
    userProfile : object | null = null;
    countriesList:Array<object> = [];

    form: object = {
        first_name: null,
        last_name: null,
        phone_number: null,
        company: null,
        city: null,
        zip: null,
        country: null,
        website: null,
    };

    mounted(){
        setTimeout(()=>{
            this.getUserData();
        }, 350)
        this.getListOfCountries();
    }

    submit() {
        this.$v.$touch()
        if (this.$v.$invalid) {
            Swal.fire({
                title: 'Error!',
                text: this.$t('please check form'),
                icon: 'error',
            } as SweetAlertOptions)
        } else {
            // do your submit logic here
            Swal.fire({
                title: 'Success!',
                text: "here we would do an ajax request, as the form is valid",
                icon: 'success',
            } as SweetAlertOptions)
        }
    }

    async getUserData(){
         this.userProfile = await getUserProfile();
    }

    async getListOfCountries(){
        this.countriesList = await getListOfCountries();
    }
}
</script>

<style lang="scss">
    @import 'node_modules/vue-select/src/scss/vue-select.scss';
</style>
