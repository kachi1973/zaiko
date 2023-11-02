<template>
    <div class="input-group input-group-sm btn-group-sm">
        <div v-if="tananame" class="input-group-prepend">
            <div class="input-group-text">{{tananame}}</div>
        </div>
        <span @click="ShowDialog" :class="{disabled: disabled}" class="form-control">{{text}}</span>
        <input type="hidden" :id="name1" :name="name1" v-model="in_value1">
        <input type="hidden" :id="name2" :name="name2" v-model="in_value2">
        <input type="hidden" :id="name3" :name="name3" v-model="in_value3">
        <input type="hidden" :id="name4" :name="name4" v-model="in_value4">
        <button v-if="!disabled" type="button" class="btn btn-primary" @click="ShowDialog">
            <span class="fas fa-search" aria-hidden="true"></span>
        </button>
        <dlg v-if="showModal">
            <h3 slot="header">棚を選択してください</h3>
            <div slot="body">
                <div class="input-group">
                    <div class="input-group-prepend col-2 p-0">
                        <div class="input-group-text w-100">事業所</div>
                    </div>
                    <select class="form-control col" v-model="dlg_value1">
                        <option  v-for="item in items1" :key="item.id" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="input-group" v-show="items2">
                    <div class="input-group-prepend col-2 p-0">
                        <div class="input-group-text w-100">場所名</div>
                    </div>
                    <select class="form-control" v-model="dlg_value2">
                        <option  v-for="item in items2" :key="item.id" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="input-group" v-show="items3">
                    <div class="input-group-prepend col-2 p-0">
                        <div class="input-group-text w-100">場所No.</div>
                    </div>
                    <select class="form-control" v-model="dlg_value3">
                        <option  v-for="item in items3" :key="item.id" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="input-group" v-show="items4">
                    <div class="input-group-prepend col-2 p-0">
                        <div class="input-group-text w-100">棚No.</div>
                    </div>
                    <select class="form-control" v-model="dlg_value4">
                        <option  v-for="item in items4" :key="item.id" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
            </div>
            <div slot="footer" class="btn-group btn-group-ms" role="group">
                <button class="btn btn-primary btn_big"                      @click="apply" type="button" :disabled="!apply_enabled">決定</button>
                <button class="btn btn-primary btn_big"                      @click="clear" type="button">クリア</button>
                <button class="btn btn-primary btn_big modal-default-button" @click="close" type="button">キャンセル</button>
            </div>
        </dlg>
    </div>
</template>

<style>
</style>

<script>
export default {
    props: {
        sys: null,
        value: null,
        visible: null,
        disabled: null,
        name1: {type: String, default: null},
        name2: {type: String, default: null},
        name3: {type: String, default: null},
        name4: {type: String, default: null},
        value1: {type: String, default: null},
        value2: {type: String, default: null},
        value3: {type: String, default: null},
        value4: {type: String, default: null},
        tananame: {type: String, default: null},
        full: false,
	},
    data() {
		return {
            in_value1: null,
            in_value2: null,
            in_value3: null,
            in_value4: null,
            dlg_value1: null,
            dlg_value2: null,
            dlg_value3: null,
            dlg_value4: null,
		    showModal: false,
		}
	},
    mounted() {
        var t = this;
        if(t.value1){
            t.in_value1 = t.value1;
            t.in_value2 = t.value2;
            t.in_value3 = t.value3;
            t.in_value4 = t.value4;
        }else if(t.value){
            t.in_value1 = t.value[0];
            t.in_value2 = t.value[1];
            t.in_value3 = t.value[2];
            t.in_value4 = t.value[3];
        }
    },
	watch: {
        value: {
            handler: function(value){
                var t = this;
                t.in_value1 = t.value[0];
                t.in_value2 = t.value[1];
                t.in_value3 = t.value[2];
                t.in_value4 = t.value[3];
            },
            deep: true,
        },
	},
    computed:{
        items1:function(){
            if(this.sys != null) return this.sys.tanas;
            return null;
        },
        items2:function(){
            const t = this;
            var item = t.itemfind(t.items1, t.dlg_value1)
            if(item != null) return item.items;
            return null;
        },
        items3:function(){
            const t = this;
            var item = t.itemfind(t.items2, t.dlg_value2)
            if(item != null) return item.items;
            return null;
        },
        items4:function(){
            const t = this;
            var item = t.itemfind(t.items3, t.dlg_value3)
            if(item != null) return item.items;
            return null;
        },
        text:function(){
            const t = this;
            if(t.sys != null){
                var str = "";
                const ids = [t.in_value1, t.in_value2, t.in_value3, t.in_value4];
                var items = t.sys.tanas;
                var strs = [];
                ids.forEach(id => {
                    const item = t.itemfind(items, id);
                    if(item !=null){
                        strs.push(item.name);
                        items = item.items;
                    }else{
                        items = null;
                    }
                });
                return strs.join(":");
            }
            return "";
        },
		apply_enabled: function(){
            if(this.full){
                if(this.items1 && !this.dlg_value1){
                    return false;
                }else if(this.items2 && !this.dlg_value2){
                    return false;
                }else if(this.items3 && !this.dlg_value3){
                    return false;
                }else if(this.items4 && !this.dlg_value4){
                    return false;
                }
            }
            return true;
        }
    },
	methods: {
        ShowDialog: function(){
            var t = this;
            t.dlg_value1 = t.in_value1;
            t.dlg_value2 = t.in_value2;
            t.dlg_value3 = t.in_value3;
            t.dlg_value4 = t.in_value4;
		    t.showModal = true;
        },
        itemfind:function(items, id){
            if(items!=null){
                const item = items.find(i => i.id == id);
                if (item != undefined) {
                    return item;
                }
            }
            return null;
        },
		apply: function(){
            var t = this;
            t.in_value1 = t.dlg_value1;
            t.in_value2 = t.dlg_value2;
            t.in_value3 = t.dlg_value3;
            t.in_value4 = t.dlg_value4;
            var tanas = [t.in_value1, t.in_value2, t.in_value3, t.in_value4];
            t.$emit("input", tanas);
            t.$emit("apply");
            t.showModal = false;
        },
        clear: function(){
            var t = this;
            t.dlg_value1 = null;
            t.dlg_value2 = null;
            t.dlg_value3 = null;
            t.dlg_value4 = null;
        },
        close: function(){
            this.showModal = false;
        }
    },
}
</script>

