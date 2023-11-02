<template>
<span v-if="visible">
	<span class="breakword" v-html="value">
	</span>
    <button type="button" class="btn btn-primary btn-sm float-right" @click="edit"><i class="fas fa-edit"></i></button>
  <dlg v-if="showModal">
    <h3 slot="header">{{title}}</h3>
    <div slot="body">
    <textarea v-model="value2" cols="60" rows="10"></textarea>
    </div>
	<div slot="footer" class="btn-group btn-group-ms" role="group">
	    <button class="btn btn-default btn_big"                      @click="apply"  type="button" >決定</button>
	    <button class="btn btn-default btn_big modal-default-button" @click="cancel" type="button">キャンセル</button>
	</div>
  </dlg>
</span>
</template>

<style scoped>
.modal-container {
	width:600px;
	height:auto;
}
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
	props: {
		name: String,
		value: String,
        visible:  {
            type: Boolean,
            default: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        title: String,
	},
    data() {
		return {
			value2: this.value,
		    showModal: false,
		}
    },
	mounted() {
    },
    watch:{
    	value: function(val){
            this.value2 = val;
    	},
	},
	methods: {
		edit: function (evnt) {
            var t = this;
            t.showModal = true;
            t.value2 = t.value;
		},
		apply: function (evnt) {
            var t = this;
            t.showModal = false;
            t.$emit('input', t.value2)
		},
		cancel: function (evnt) {
            var t = this;
            t.showModal = false;
		},
	}
}
</script>
