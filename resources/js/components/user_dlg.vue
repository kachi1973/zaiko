<template>
    <div class="input-group input-group-sm btn-group-sm">
        <div class="input-group-prepend">
            <div class="input-group-text">依頼者</div>
        </div>
		<input type="hidden" class="form-control" placeholder="ID" :name="name" v-model="num" style="width:40px; display: inline">
        <span class="form-control" @click="showModal = (true && !disabled)">
            <loading :active.sync="ing" :can-cancel="false" :is-full-page="false" :width="20" :height="20"></loading>
            {{text}}
        </span>
		<button :disabled="disabled" type="button" class="btn btn-primary" @click="showModal = (true && !disabled)">
			<span class="fas fa-search" aria-hidden="true"></span>
		</button>
        <dlg v-if="showModal">
            <h3 slot="header">担当選択</h3>
            <div slot="body">
                <loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">部署</div>
                    </div>
                    <select v-model="selectedLevel1" v-on:change="changeLevel1" class="form-control" :disabled="levels1.length==0">
                        <option v-for="item in levels1" :value="item.id" :key="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">担当</div>
                    </div>
                    <select v-model="selectedLevel2" v-on:change="changeLevel2" class="form-control" :disabled="levels2.length==0">
                        <option v-for="item in levels2" :value="item.id" :key="item.id">{{item.name}}</option>
                    </select>
                </div>
            </div>
            <div slot="footer" class="btn-group btn-group-ms" role="group">
                <button class="btn btn-primary btn_big"                      @click="apply" type="button" :disabled="!(0<selectedLevel2)">決定</button>
                <button class="btn btn-primary btn_big"                      @click="clear" type="button">クリア</button>
                <button class="btn btn-primary btn_big modal-default-button" @click="showModal = false" type="button">キャンセル</button>
            </div>
        </dlg>
    </div>
</template>

<style>
/*
.modal-container {
	width:600px;
	height:auto;
}
*/
</style>

<style lang="scss" scoped>
.userdlg{
    padding: 0px;
    span{
        button{
            padding: 1px 5px;
            span{
                padding: 0px;
            }
        }
    }
}
.userdlg_dsp{
    display:inline-block;
    min-width: 120px;
}
</style>

<script>
export default {
	props: [
		'name',
		'value',
        'visible',
        'disabled',
	],
    data() {
		return {
			num: this.value,
		    showModal: false,
		    text: '',
			selectedLevel1: '',
			levels1: [],
			selectedLevel2: '',
            levels2: [],
            ing: false,
		}
    },
	mounted() {
		var v = this;
		this.get_bumon();
		this.set_num(this.num);
    },
    watch:{
    	value: function(val){
            this.num = val;
    	},
    	num: function(val){
    		this.set_num(val);
    	},
	},
	methods: {
		set_num: function (num) {
    		var v = this;
			if(0<num){
                // 初期値あり
                v.ing = true;
				$.ajax({
					type : "POST",
					url : root_path + "ajax/AjaxGetUserName",
					dataType : "json",
					contentType : 'application/json',
					async: true,
					data : JSON.stringify({
						id : num,
					})
				}).done(function(datas) {
					if(datas.item!=null){
						v.selectedLevel1 = datas.item.bumon_id;
						v.get_users(v.selectedLevel1);
						v.selectedLevel2 = datas.item.id;
					}
					v.text = datas.item.name;
                    v.ing = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    v.ing = false;
                });
			}else{
				// 初期値なし
			}
		},
		get_bumon: function(){
			var v = this;
            v.ing = true;
			$.ajax({
				type : "POST",
				url : root_path + "ajax/AjaxGetBumons",
				dataType : "json",
				contentType : 'application/json',
				async: true,
				data : JSON.stringify({
				})
			}).done(function(datas) {
				v.levels1 = datas.items;
				v.levels2 = [];
                v.ing = false;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                v.ing = false;
            });
		},
		get_users: function (bumon_id) {
			var v = this;
            v.ing = true;
			$.ajax({
				type : "POST",
				url : root_path + "ajax/AjaxGetUsers",
				dataType : "json",
				contentType : 'application/json',
				async: true,
				data : JSON.stringify({
					bumon_id : bumon_id,
				})
			}).done(function(datas) {
				v.levels2 = datas.items;
                v.ing = false;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                v.ing = false;
            });
		},
		changeLevel1: function (evnt) {
			this.get_users(this.selectedLevel1);
		},
		changeLevel2: function (evnt) {
		},
		apply: function (evnt) {
			if(0<this.selectedLevel2){
				this.num=this.selectedLevel2;
				this.$emit('input', this.num)
				this.showModal = false;
			}
		},
		clear: function (evnt) {
			this.selectedLevel1 = 0;
			this.selectedLevel2 = 0;
			this.levels2 = [];
			this.text = '';
		},
	}
}
</script>
