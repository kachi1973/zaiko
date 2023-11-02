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

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/ja';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI, {locale});

Vue.component('datepicker', require('../components/datepicker.vue').default)
Vue.component('haseistatus', require('../components/haseistatus.vue').default)
Vue.component('haseicheck', require('../components/haseicheck.vue').default)
Vue.component('haseibutton', require('../components/haseibutton.vue').default)
Vue.component('dlg', require('../components/dlg.vue').default)
Vue.component('user_dlg', require('../components/user_dlg.vue').default)
Vue.component('zaiko_dlg', require('../components/zaiko_dlg.vue').default)
Vue.component('filelist', require('../components/filelist.vue').default)
Vue.component('tanasel', require('../components/tanasel.vue').default)

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.component('loading', Loading)

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

window.router = new VueRouter({
    mode: 'history',
    routes: [
        { path: root_path, props: true, redirect: root_path + 'hasei/list' },
        { path: root_path + 'hasei/create', name: 'hasei.create',  component: require('./components/edit.vue').default },
        { path: root_path + 'hasei/edit/:id', name: 'hasei.edit',    component: require('./components/edit.vue').default },
        { path: root_path + 'hasei/list', name: 'hasei.list',    component: require('./components/list.vue').default },
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
            url: root_path + "hasei/AjaxInit",
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
