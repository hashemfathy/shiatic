import './polyfill'
import './bootstrap'
import VeeValidate from 'vee-validate'

import { loadVueComponentsFromDirectory } from "./shared/utils";

window.Vue = require('vue');

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// components
import store from './store';

// vue good table
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css'
import '../../sass/backend/vuegoodtable.scss'
import ToggleButton from 'vue-js-toggle-button'
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
Vue.use(ToggleButton)
Vue.use(VueGoodTablePlugin);

// sweet alert
import { default as Swal, Toast, SwalDelete, SwalAccept, SwalReject } from '../../Util/swal';

window.Swal = Swal;
window.Toast = Toast;
window.SwalDelete = SwalDelete;
window.SwalAccept = SwalAccept;
window.SwalReject = SwalReject;

const files = require.context('./containers', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// load all views files
const views_files = require.context("../backend", true, /\.vue$/i);
loadVueComponentsFromDirectory(Vue, views_files);

Vue.use(BootstrapVue)
Vue.use(VeeValidate)
Vue.config.productionTip = false


const app = new Vue({
  el: '#app',
  store,
});
