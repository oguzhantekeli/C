import Vue from 'vue';
import { BootstrapVue } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import store from './store'
require('./bootstrap');

//use declerations
Vue.use(BootstrapVue)

//components
// Vue.component('spinner-comp',require('./components/Spinner.vue'));
Vue.component('standing-comp',require('./components/Standings.vue').default);
Vue.component('buttons-comp',require('./components/Buttons.vue').default);
Vue.component('result-comp',require('./components/Results.vue').default);

const app = new Vue({
    el:'#app',
    store:store,
});
