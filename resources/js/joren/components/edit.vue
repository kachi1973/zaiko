
<template>
    <div class="col-md-10">
        <div class="card">
            <loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
            <div class="card-header bg-secondary">情報連絡票 (発行No.[
                <span v-if='item.id>0' class="badge badge-primary">{{item.id}}</span>
                <span v-else class="badge badge-primary">新規</span>
                ]
            </div>
            <div class="card-body">
                <form class="form-inline">
                    <div class="input-group input-group-sm mr-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">現在の状態</div>
                        </div>
                        <jorenstatus :stsnum="item.status" class="form-control"/>
                        <button type="button" class="btn btn-primary btn-sm" v-on:click="save(-1)" :disabled="!cancel_enabled" v-if="!IsMobile"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary" v-on:click="save(0)" :disabled="!item.items.length" v-if="!IsMobile">保存</button>
                        <jorenbutton :stsnum="item.status" class="btn btn-primary" v-on:click="save(1)" :texttype="2" suffix=""  v-bind:sys="sys" :disabled="!save_enabled" v-if="!IsMobile"/>
                        <a :href="'../descprint/' + item.id" class="btn btn-primary" style="min-width: unset;" target="_blank" v-if="0<item.id">印刷</a>
                        <a :href="zido_url" target="_blank" class="btn btn-primary" style="min-width: unset;" v-if="0<item.id">移動一覧</a>
                        <button type="button" class="btn btn-primary" v-on:click="list()">一覧</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div style="overflow-x:auto;">
                    <table class="table table-sm table-bordered table-hover main_table table-min">
                        <tbody>
                            <tr class="fixed_header">
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                                <th style="width:4%;"></th>
                            </tr>
                            <tr>
                                <th>文章番号</th>
                                <td>
                                    <span v-if='item.id>0'>{{item.no}}</span>
                                    <span v-else>新規</span>
                                </td>
                                <th>発行日</th>
                                <td colspan="4">{{item.status10_date}}</td>
                            </tr>
                            <tr>
                                <th>営業管理部門</th>
                                <td colspan="6">
                                    <select :disabled="!edit_editable" v-model="item.bumon_id" class="form-control">
                                        <option v-for="item in sys.bumons" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>案件番号</th>
                                <td colspan="2">
                                    <input :disabled="!edit_editable" type="text" class="form-control" placeholder="案件番号" v-model="item.anken_id">
                                </td>
                                <th>区分・確度</th>
                                <td colspan="3" class="bg-warning">
                                    <input :disabled="!edit_editable" type="text" class="form-control" placeholder="区分・確度" v-model="item.kubun">
                                </td>
                            </tr>
                            <tr>
                                <th>案件名</th>
                                <td colspan="6" class="bg-warning">
                                    <input :disabled="!edit_editable" type="text" class="form-control" placeholder="案件名" v-model="item.anken">
                                </td>
                            </tr>
                            <tr>
                                <th>顧客名</th>
                                <td colspan="6">
                                    <input :disabled="!edit_editable" type="text" class="form-control" placeholder="顧客名" v-model="item.kokyaku">
                                </td>
                            </tr>
                            <tr>
                                <th>予定工期</th>
                                <td colspan="6" class="bg-warning">
                                    <div class="form-inline">
                                    自）<datepicker :disabled="!edit_editable" class="form-control" v-model="item.kouki_start" style="padding: 2px;"></datepicker>
                                    &emsp;～&emsp;至）<datepicker :disabled="!edit_editable" class="form-control" v-model="item.kouki_end" style="padding: 2px;"></datepicker>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>予定工場完</th>
                                <td colspan="6" class="bg-warning">
                                    <div class="col-md-4">
                                        <datepicker :disabled="!edit_editable" class="form-control" v-model="item.kojyo_end" style="padding: 2px;"></datepicker>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>品目コード</th>
                                <th>品名</th>
                                <th>型式</th>
                                <th>数量</th>
                                <th>単価(&yen;)</th>
                                <th>金額(&yen;)</th>
                                <th>
                                    <button :disabled="!edit_editable" type="button" class="btn btn-outline-primary btn-sm" v-on:click="item_add()" v-if="!IsMobile"><i class="fas fa-plus"></i></button>
                                </th>
                            </tr>
                            <tr v-for="rec in item.items" v-bind:key="rec.id">
                                <td class="bg-warning">
                                    <template v-if="IsMobile">{{rec.hinmoku_id}}</template>
                                    <div class="input-group" v-else>
                                        <input readonly :disabled="!edit_editable" type="text" class="form-control" placeholder="品目コード" v-model.trim="rec.hinmoku_id">
                                        <button :disabled="!edit_editable" type="button" class="btn btn-primary btn-sm" v-on:click="hinmoku_search(rec)"><i class="fas fa-search"></i></button>
                                    </div>
                                </td>
                                <td>{{rec.hinmoku ? rec.hinmoku.fhinrmei : null}}</td>
                                <td>{{rec.hinmoku ? rec.hinmoku.fmekerhincd : null}}</td>
                                <td class="bg-warning"><input :disabled="!edit_editable" type="number" class="form-control text-right" placeholder="数量" v-model.number="rec.suu" min="0" @change="change"></td>
                                <td class="bg-warning"><input :disabled="!edit_editable" type="number" class="form-control text-right" placeholder="単価" v-model.number="rec.tanka" min="0" @change="change"></td>
                                <td class='text-right'>
                                    {{rec.suu * rec.tanka | addComma}}
                                </td>
                                <td>
                                    <button :disabled="!edit_editable_ido || !(rec.ido_cnt < rec.suu)" type="button" class="btn btn-outline-primary" v-on:click="item_ido(rec)" v-if="!IsMobile">
                                        移動<span class="badge" :class="(rec.ido_cnt < rec.suu) ? 'badge-warning' : 'badge-success'">{{rec.ido_cnt}}</span>
                                    </button>
                                    <button :disabled="!edit_editable" type="button" class="btn btn-outline-primary" v-on:click="item_del(rec)" v-if="!IsMobile">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="7">備考</th>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <textarea :disabled="!edit_editable" class="form-control" cols="20" rows="3" placeholder="備考" v-model="item.biko"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>仕様書添付</th>
                                <td colspan="2">
                                    {{tenpu}}
                                </td>
                                <th>機器仕様</th>
                                <td colspan="3" class="bg-warning">
                                    <input :disabled="!edit_editable" type="text" class="form-control" placeholder="機器仕様" v-model="item.kiki">
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4"></th>
                                <th>所属長(部長)</th>
                                <th>所属長(課長)</th>
                                <th>担当者</th>
                            </tr>
                            <tr>
                                <td colspan="4" style="height: 80px;"></td>
                                <td class="text-center"><img v-if="status30_hanko_url!=null" border="0" :src="status30_hanko_url" width="64" height="64"></td>
                                <td class="text-center"><img v-if="status20_hanko_url!=null" border="0" :src="status20_hanko_url" width="64" height="64"></td>
                                <td class="text-center"><img v-if="status10_hanko_url!=null" border="0" :src="status10_hanko_url" width="64" height="64"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <filelist
                    title="仕様書添付"
                    :value="item.file_urls"
                    :id="item.id"
                    :url="url"
                />
            </div>
        </div>
        <szaiko_dlg
            :sys="sys"
            :show="showSZaikoModal"
            @close="szaikoDlgClose"
            ></szaiko_dlg>
    </div>
</template>

<style scoped lang="scss">
.main_table{
    border-collapse: collapse;
    table-layout: fixed;
    th{
        border: solid 2px black;
        background-color: #e9ecef;
        vertical-align: middle;
    }
    td{
        border: solid 2px black;
        vertical-align: middle;
    }
    .fixed_header{
        th{
            border: none;
            padding: 0px;
            margin: 0px;
            width: 16.6%;
        }
    }
}
.table_input{
    padding: 0px;
    margin: 0px;
    &.F1{
        width: 100%;
    }
}
.pagination{
    margin-bottom: 0px;
}
</style>

<script>
import * as cmn from '../../components/common.js';
import {cmn_mixin} from '../../components/cmn_mixin.js';
import moment from 'moment';
export default {
    mixins: [cmn_mixin],
    props: {
        sys: null,
	},
    data() {
		return {
            url: root_path + 'joren',
            item:{
                id: null,
                bumon_id: '4461',
                anken_id: null,
                kubun: '計画・Ａ',
                anken: 'ソリューション在庫不足による補充',
                kokyaku: null,
                status10_date: null,
                status10_user_name: null,
                status20_date: null,
                status20_user_name: null,
                status30_date: null,
                status30_user_name: null,
                status40_date: null,
                status40_user_name: null,
                kouki_start: null,
                kouki_end: null,
                kojyo_end: null,
                biko: 'ソリューション在庫の保有数が無く、障害（クレーム）対応が出来ないため補充する。',
                siyou: 'なし',
                kiki: '決定',
                status: 0,
                editable: false,
                items: [],
                file_urls: [],
            },
            ing: false,
            cur_item: null,
            showSZaikoModal: false,
            save_enabled_change: 0, // save_enabled()で変更を検出するため
		}
    },
    computed:{
        tenpu(){
            var t = this;
            return 0<t.item.file_urls.length ? "有り" : "無し";
        },
        status10_hanko_url(){
            var t = this;
            return t.get_hanko_url(t.item.status10_user_name, t.item.status10_date);
        },
        status20_hanko_url(){
            var t = this;
            return t.get_hanko_url(t.item.status20_user_name, t.item.status20_date);
        },
        status30_hanko_url(){
            var t = this;
            return t.get_hanko_url(t.item.status30_user_name, t.item.status30_date);
        },
        save_enabled(){
            var t = this;
            var a = t.save_enabled_change;
            /*
            if(!t.item.anken_id){
                return false;
            }
            */
            if(!t.item.kubun){
                return false;
            }
            if(!t.item.anken){
                return false;
            }
            /*
            if(!t.item.kokyaku){
                return false;
            }
            */
            if(!t.item.kouki_start){
                return false;
            }
            if(!t.item.kouki_end){
                return false;
            }
            if(!t.item.kojyo_end){
                return false;
            }
            if(!t.item.kiki){
                return false;
            }
            if(!t.item.items.length){
                return false;
            }
            if (t.item.items.some(rec => {
                if(!rec.hinmoku_id){
                    return true;
                }
                if(!rec.suu){
                    return true;
                }
                if(!rec.tanka){
                    return true;
                }
				if(t.item.status==40){
					if(rec.ido_cnt < rec.suu){
						return true;
					}
				}
                return false;
            })) {
                return false;
            }
            if(t.sys!=null){
                switch (t.item.status) {
                    case 10: // 課長承認待ち
                        return t.sys.user.kengen12;
                    case 20: // 部長承認待ち
                        return t.sys.user.kengen12;
                    case 30: // 管理課提出待ち
                        return t.sys.user.kengen11;
                    case 40: // 倉庫移動待ち
                        return t.sys.user.kengen11;
                        break;
                    case 50:
                    case 60:
                    case 70: // 完了
                        return t.sys.user.kengen11;
                    default:
                        return true;
                }
            }
            return false;
        },
        edit_editable(){
            var t = this;
            return (t.item.status < 10) && (!t.IsMobile);
        },
        edit_editable_ido(){
            var t = this;
            return t.item.status == 40;
        },
        cancel_enabled(){
            var t = this;
            if(t.sys!=null){
                switch (t.item.status) {
                    case 10: // 課長承認待ち
                        return true;
                    case 20: // 部長承認待ち
                        return t.sys.user.kengen12;
                    case 30: // 管理課提出待ち
                        return t.sys.user.kengen12;
                    case 40: // 倉庫移動待ち
                        return t.sys.user.kengen11;
                    case 50:
                    case 60:
                    case 70: // 完了
                        return false;
                    case 99: // キャンセル
                    default: // 申請待ち
                        return true;
                }
            }
            return false;
        },
        zido_url(){
            var t = this;
            return `${root_path}zido/list/1/${t.item.id}`;
        }
    },
    watch: {
        'item.kouki_end': function (val) {
            var t = this;
            if(t.item.kojyo_end==null){
                t.item.kojyo_end = val;
            }
        },
    },
    mounted() {
        var t = this;
        var hit = false;
        var now = new Date();
        if('id' in t.$route.params){
            t.item.id = t.$route.params.id;
            if (0 < t.item.id){
                t.ing = true;
                hit = true;
                $.ajax({
                    type : "POST",
                    url: t.url + "/AjaxGetJoren",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        id: t.item.id,
                    })
                }).done(function(datas) {
                    t.ing = false;
                    t.set_data(datas);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.ing = false;
                });
            }else{
                t.set_data(null);
            }
        }
        if (!hit){
            t.item.user_id = user_id;
        }
        if(t.item.items.length==0){
            t.item_add();
        }
    },
    methods: {
        change(){
            this.save_enabled_change++;
        },
        get_hanko_url(name, date){
            if(name!=null && date!=null){
                return root_path + `ajax/GetHanko?name=${name}&date=${date}`;
            }else if(name!=null){
                return root_path + `ajax/GetHanko?name=${name}`;
            }else{
                return null;
            }
        },
        item_add(){
            var t = this;
            var new_id = t.item.id * 100;
            if(0<t.item.items.length){
                new_id = t.item.items[t.item.items.length - 1].id + 1;
            }
            t.item.items.push({
                id: new_id,
                joren_id: null,
                hinmoku: {
                    name: null,
                    model: null,
                },
                suu: null,
                tanka: null,
            });
        },
        item_del(rec){
            var t = this;
            t.$dialog.confirm({
                title: '確認',
                body: '削除してもよろしいですか？'
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                t.item.items.splice(t.item.items.findIndex(i => i.id == rec.id), 1);
            });
        },
        item_ido(rec){
            var t = this;
            var num = rec.suu - rec.ido_cnt;
            if(num<0){
                num = 0;
            }
            window.open(`${root_path}zido/edit/0/1/${t.item.id}/${rec.hinmoku_id}/${num}`, '_blank');
        },
        set_data(datas){
            var t = this;
            if(datas==null || datas.item==null){
                t.item.id = 0;
            }else{
                Object.assign(t.item, datas.item);
                t.item.status10_date = t.$options.filters.YYYYMMDD(datas.item.status10_date);
                t.item.status20_date = t.$options.filters.YYYYMMDD(datas.item.status20_date);
                t.item.status30_date = t.$options.filters.YYYYMMDD(datas.item.status30_date);
                t.item.status40_date = t.$options.filters.YYYYMMDD(datas.item.status40_date);
                t.item.status50_date = t.$options.filters.YYYYMMDD(datas.item.status50_date);
                t.item.status60_date = t.$options.filters.YYYYMMDD(datas.item.status60_date);
                t.item.status70_date = t.$options.filters.YYYYMMDD(datas.item.status70_date);
                t.item.kouki_start = t.$options.filters.YYYYMMDD(datas.item.kouki_start);
                t.item.kouki_end = t.$options.filters.YYYYMMDD(datas.item.kouki_end);
                t.item.kojyo_end = t.$options.filters.YYYYMMDD(datas.item.kojyo_end);
            }
        },
        list(){
            router.push({ name: 'joren.list' });
        },
        save(type){
            var t = this;
            t.$dialog.confirm({
                title: '確認',
                body: {"-1":"ひとつ前に戻してもよろしいでですか？", "0":"保存してもよろしいですか？", "1":"申請してもよろしいですか？"}[type],
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                t.ing = true;
                t.item.editable = t.edit_editable;
                switch(type){
                    case -1:
                        t.item.command = 2;
                        break;
                    case 1:
                        t.item.command = 1;
                        break;
                }
                $.ajax({
                    type : "POST",
                    url : t.url + "/AjaxPutJoren",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        item: t.item,
                    })
                }).done(function(datas) {
                    t.set_data(datas);
                    t.list();
                    t.ing = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.ing = false;
                });
            });
        },
        item_delete(idx){
            var t = this;
            t.$dialog.confirm({
                title: '確認',
                body: '削除してもよろしいですか？'
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                t.item.items.splice(idx, 1);
            });
        },
        hinmoku_search(r){
            var t = this;
            t.showSZaikoModal = true
            t.cur_item = r;
        },
        szaikoDlgClose: function(hinmoku){
            var t = this;
            t.showSZaikoModal = false;
			if(hinmoku){
	            t.cur_item.hinmoku_id = hinmoku.id;
	            t.cur_item.hinmoku = hinmoku;
	            t.change();
			}
        },
	},
}
</script>
