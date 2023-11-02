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
Vue.component('zidostatus', require('../components/zidostatus.vue').default)
Vue.component('zidobutton', require('../components/zidobutton.vue').default)
Vue.component('zidocheck', require('../components/zidocheck.vue').default)
Vue.component('dlg', require('../components/dlg.vue').default)
Vue.component('user_dlg', require('../components/user_dlg.vue').default)
Vue.component('zaiko_dlg', require('../components/zaiko_dlg.vue').default)
Vue.component('szaiko_dlg', require('../components/szaiko_dlg.vue').default)
Vue.component('kzaikotable', require('../components/kzaikotable.vue').default)
Vue.component('tanasel', require('../components/tanasel.vue').default)
Vue.component('stsdatesel', require('../components/stsdatesel.vue').default)

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.component('loading', Loading)

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

window.router = new VueRouter({
    mode: 'history',
    routes: [
        { path: root_path, props: true, redirect: root_path + 'zido/list' },
        { path: root_path + 'zido/create',                          name: 'zido.create',    component: require('./components/edit.vue').default },
        { path: root_path + 'zido/edit/:id',                        name: 'zido.edit',      component: require('./components/edit.vue').default, props: true},
        { path: root_path + 'zido/edit/:id/:parent_kind/:parent_id/:hinmoku_id/:hinmoku_suu',
                                                                    name: 'zido.edit2',     component: require('./components/edit.vue').default, props: true},
        { path: root_path + 'zido/list',                            name: 'zido.list',      component: require('./components/list.vue').default },
        { path: root_path + 'zido/list/:parent_kind/:parent_id',    name: 'zido.list2',     component: require('./components/list.vue').default },
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
            url: root_path + "zido/AjaxInit",
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
