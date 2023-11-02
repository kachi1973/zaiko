<template>
    <div>
        <loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
        <table class="table table-sm table-bordered table-hover mt-2">
            <tbody>
                <tr class="thead-light">
                    <th>{{title}}</th>
                    <th v-if="!IsMobile">
                        操作
                        <button type="button" class="btn btn-primary btn-sm float-right" v-on:click="upload()" style="min-width: unset;" :disabled="!(0<id)"><i class="fas fa-upload"></i></button>
                    </th>
                </tr>
                <tr v-for="url in file_urls" v-bind:key="url.name">
                    <td><a :href="url.path" target="_blank">{{url.name}}</a></td>
                    <td v-if="!IsMobile"><button type="button" class="btn btn-primary btn-sm float-right" v-on:click="delete_pict(url)" style="min-width: unset;"><i class="fas fa-trash-alt"></i></button></td>
                </tr>
            </tbody>
        </table>
        <div v-if="!(0<id)" class="alert alert-warning" role="alert">
            新規状態ではファイルを登録出来ません。一旦保存して下さい。
        </div>
        <input v-show="false" id="FileInput" type="file" v-on:change="fileSelected">
    </div>
</template>

<script>
import {cmn_mixin} from '../components/cmn_mixin.js';
export default {
    mixins: [cmn_mixin],
	props: {
        title: null,
        value: null,
        id: null,
        url: null,
        disabled: null,
    },
    data() {
		return {
            file_urls: this.value,
            ing: false,
		}
    },
    mounted() {
    },
	watch: {
	    value: function(val){
	    	this.file_urls = this.value;
	    },
		file_urls: function (num) {
			this.$emit('input', this.file_urls);
		},
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
            t.ing = true;
            $.ajax({
                type : "POST",
                url : t.url + `/AjaxPutFile/${t.id}`,
                dataType : "json",
                processData: false,
                contentType: false,
                data : formData,
            }).done(function(datas) {
                t.file_urls.splice(0, t.file_urls.length);
                t.file_urls.push(...datas.items);
                t.ing = false;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                t.ing = false;
            });
        },
        delete_pict(url){
            var t = this;
            t.$dialog.confirm({
                title: '確認',
                body: '削除してもよろしいですか？'
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                t.ing = true;
                $.ajax({
                    type : "POST",
                    url : t.url + `/AjaxDeleteFile/${t.id}/${url.name}`,
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : null,
                }).done(function(datas) {
                    t.file_urls.splice(0, t.file_urls.length);
                    t.file_urls.push(...datas.items);
                    t.ing = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.ing = false;
                });
            });
        },
	},
}
</script>

<style scoped>
.modal-container {
	width:100%;
	height:100%;
}
</style>
