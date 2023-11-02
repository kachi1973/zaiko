//require('../bootstrap');
//require('jquery');

import * as cmn from '../components/common.js';

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VuejsDialog from 'vuejs-dialog';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
Vue.use(VuejsDialog, {
    reverse: true,
})

import moment from 'moment';

Vue.component('datepicker', require('../components/datepicker.vue').default)
Vue.component('jorenstatus', require('../components/jorenstatus.vue').default)
Vue.component('jorenbutton', require('../components/jorenbutton.vue').default)
Vue.component('jorencheck', require('../components/jorencheck.vue').default)
Vue.component('dlg', require('../components/dlg.vue').default)
Vue.component('user_dlg', require('../components/user_dlg.vue').default)
Vue.component('filelist', require('../components/filelist.vue').default)
Vue.component('szaiko_dlg', require('../components/szaiko_dlg.vue').default)

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.component('loading', Loading)

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

window.router = new VueRouter({
    mode: 'history',
    routes: [
        { path: root_path, props: true, redirect: root_path + 'joren/list' },
        { path: root_path + 'joren/create', name: 'joren.create',  component: require('./components/edit.vue').default },
        { path: root_path + 'joren/edit/:id', name: 'joren.edit',    component: require('./components/edit.vue').default },
        { path: root_path + 'joren/list', name: 'joren.list',    component: require('./components/list.vue').default },
    ]
})

cmn.vue_set_cmn(Vue);
cmn.jquery_set_cmn($);

const app = new Vue({
    router,
    el: '#app',
    data() {
		return {
            sys: null,
		}
    },
    created(){
        var t = this;
        $.ajax({
            type : "POST",
            url: root_path + "joren/AjaxInit",
            dataType : "json",
            contentType : 'application/json',
            data : null,
            async:false,
        }).done(function(datas) {
            t.sys = datas;
        }).fail(function(jqXHR, textStatus, errorThrown) {
        });
    },
});
