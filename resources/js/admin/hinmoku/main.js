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
		}
    },
    mounted(){
    },
	methods: {
		upload(){
            var t = this;
            var target = $('#FileInput');
            target.val('');
			target.trigger("click");
		},
		fileSelected(event){
			var t = this;
			var file = event.target.files[0];
			const formData = new FormData();
            formData.append('file', file);
            $.ajax({
                type : "POST",
                url : root_path + 'admin/hinmoku/AjaxPutFile',
                dataType : "json",
                processData: false,
                contentType: false,
                data : formData,
            }).done(function(datas) {
                window.alert(`${datas.change_rows}件更新しました。`);
                location.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                window.alert(`更新に失敗しました。`);
            });
        },
    },
});
