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
                scaw_flg: $('#search_scaw_flg').val(),
                id: $('#search_id').val(),
                hinmoku_id: $('#search_hinmoku_id').val(),
                model: $('#search_model').val(),
                name: $('#search_name').val(),
                maker: $('#search_maker').val(),
                sort: $('#search_sort').val(),
                type: $('#search_type').val(),
                checked01: $('#search_checked01').prop('checked'),
                date1: $('#search_date1').val(),
                date2: $('#search_date2').val(),
                biko: $('#search_biko').val(),
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
                //async: false,
            }).done(function(datas) {
                t.sys = datas;
                t.ing = false;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                t.ing = false;
            });
        }else{
            try{
                var ret = JSON.parse(localStorage.getItem("admin.zaiko.list.search"));
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
            t.search.scaw_flg = -1;
            t.search.id = null;
            t.search.hinmoku_id = null;
            t.search.model = null;
            t.search.name = null;
            t.search.maker = null;
            t.search.id = null;
            t.search.sort = 0;
            t.search.type = 0;
            t.search.checked01 = false;
            t.search.date1 = null;
            t.search.date2 = null;
            t.search.biko = null;
        },
		upload(){
            var t = this;
            var target = $('#FileInput');
            target.val('');
			target.trigger("click");
		},
		fileSelected(event){
			var t = this;
			t.ing = true;
			var file = event.target.files[0];
			const formData = new FormData();
            formData.append('file', file);
            $.ajax({
                type : "POST",
                url : root_path + 'admin/zaiko/AjaxPutFile',
                dataType : "json",
                processData: false,
                contentType: false,
                data : formData,
            }).done(function(datas) {
                t.ing = false;
                window.alert(`${datas.change_rows}件更新しました。`);
                location.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                t.ing = false;
                window.alert(`更新に失敗しました。`);
            });
        },
        onSearch(){
			var t = this;
            localStorage.setItem("admin.zaiko.list.search", JSON.stringify(t.search));
        },
        onKcreate(url){
            if(window.confirm('個別在庫データを作成しますか？')){
                window.location.href = url;
                //window.open(url, '_blank');
                //location.reload();
            }
        },
    },
});
