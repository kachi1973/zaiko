//require('../../bootstrap');
//require('jquery');

import * as cmn from '../../components/common.js';

import Vue from 'vue';

Vue.component('dlg', require('../../components/dlg.vue').default)
Vue.component('tanasel', require('../../components/tanasel.vue').default)

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.component('loading', Loading)

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/ja';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI, {locale});

Vue.component('datepicker', require('../../components/datepicker.vue').default)

cmn.vue_set_cmn(Vue);
cmn.jquery_set_cmn($);

const app = new Vue({
    el: '#app',
    props: {
    },
    data() {
		return {
            sys: null,
            ing: false,
            search:{
                tana: [],
                id: $('#search_id').val(),
                zaiko_id: $('#search_zaiko_id').val(),
                status_flg: $('#search_status_flg').val(),
                create_date1: def_search_create_date1,
                create_date2: def_search_create_date2,
            },
		}
    },
    mounted(){
        var t = this;
        var url = new URL(window.location.href);
        if(searched){
            t.ing = true;
	        $.ajax({
	            type : "POST",
	            url: root_path + "admin/zaiko/AjaxInit",
	            dataType : "json",
	            contentType : 'application/json',
	            data : null,
	            //async:false,
	        }).done(function(datas) {
	            t.sys = datas;
                t.ing = false;
	        }).fail(function(jqXHR, textStatus, errorThrown) {
                t.ing = false;
	        });
        }else{
            try{
                var ret = JSON.parse(localStorage.getItem("admin.kzaiko.list.search"));
                Object.assign(t.search, ret);
                t.$nextTick(() =>{
                    document.searchForm.submit();
                });
            }catch{
            }
		}
    },
	methods: {
		clear: function(event){
            var t = this;
            t.search.tana = [];
            t.search.id = null;
            t.search.zaiko_id = null;
            t.search.status_flg = 0;
            t.search.create_date1 = null;
            t.search.create_date2 = null;
        },
        onSearch(){
			var t = this;
            localStorage.setItem("admin.kzaiko.list.search", JSON.stringify(t.search));
        },
    },
});
