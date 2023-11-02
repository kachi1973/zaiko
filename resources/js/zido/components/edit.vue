<template>
<div class="col-md-10">
    <div class="card">
        <loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">倉庫移動伝票 (発行No.[
            <span v-if='item.id>0' class="badge badge-primary">{{item.id}}</span>
            <span v-else class="badge badge-primary">新規</span>
            ]
        </div>
        <div class="card-body form-inline">
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">現在の状態</div>
                </div>
                <zidostatus :stsnum="item.status" class="form-control"/>
                <button type="button" class="btn btn-primary btn-sm" v-on:click="save(-1)" :disabled="!cancel_enabled" v-if="!IsMobile"><i class="fas fa-times"></i></button>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-primary" v-on:click="save(0)" :disabled="!save_enabled" v-if="!IsMobile">保存</button>
                <zidobutton :stsnum="item.status" class="btn btn-primary" v-on:click="save(1)" :texttype="2" suffix=""  v-bind:sys="sys" :disabled="!save_enabled" v-if="!IsMobile"/>
                <a :href="'../descprint/' + item.id" class="btn btn-primary"  style="min-width: unset;" target="_blank" v-if="0<item.id">印刷</a>
                <button type="button" class="btn btn-primary" v-on:click="list()">一覧</button>
            </div>
        </div>
        <div class="card ml-2" v-if="qr_editable">
            <div class="card-body" :class="{'bg-success': kzaiko_dlg.focus, 'bg-light': !kzaiko_dlg.focus}">
                <div class="form-inline">
                    <div class="input-group input-group-sm">
                        <div class="btn btn-sm" v-on:click="$refs.kzaiko_dlg_id.focus()"><span class="fas fa-qrcode"></span></div>
                        <input type="number" class="form-control text-right" placeholder="0000000" v-model.number="kzaiko_dlg.id" id="kzaiko_dlg_id" ref="kzaiko_dlg_id" v-on:keydown.enter="kzaiko_id_input()" :disabled="item.ing" aria-describedby="kzaiko_dlg_id" @focus="kzaiko_dlg.focus = true" @blur="kzaiko_dlg.focus = false" >
                        <button type="button" class="btn btn-sm" :class="{'btn-success': kzaiko_dlg.focus, 'btn-primary': !kzaiko_dlg.focus}" v-on:click="$refs.kzaiko_dlg_id.focus()">
                            {{kzaiko_dlg.focus ? 'ＱＲリードＯＫ' : 'ＱＲリードする場合はクリックしてください'}}<i class="fas fa-uncheck"></i>
                        </button>
                        <div class="input-group-append" v-if="kzaiko_dlg.focus">
                            <span class="input-group-text bg-warning" id="kzaiko_dlg_id">
                                {{kzaiko_dlg.answer}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="message!=null" class="alert alert-danger mt-2" role="alert">
            <h5>{{message}}</h5>
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
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                            <th style="width:4%;"></th>
                        </tr>
                        <tr>
                            <th colspan="3">関連</th>
                            <td colspan="23">
                                <div class="form-inline">
                                    <select v-model.number="item.parent_kind" class="form-control" :disabled="IsMobile">
                                        <option value="0">無し</option>
                                        <option value="1">情報連絡票</option>
                                        <option value="2">部品購入依頼</option>
                                    </select>
                                    <input type="number" class="form-control text-right" placeholder="ID" v-model.number="item.parent_id" min="0" :disabled="IsMobile">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">発行No.</th>
                            <td colspan="5">
                                <span v-if='item.id>0'>{{item.id}}</span>
                                <span v-else>新規</span>
                            </td>
                            <th colspan="3">発行日</th>
                            <td colspan="6">{{item.status10_date}}</td>
                            <th colspan="3">区分</th>
                            <td colspan="6">棚移動</td>
                        </tr>
                        <tr>
                            <th colspan="3">依頼担当者</th>
                            <td colspan="5">{{item.status10_user_id}}<br>{{item.status10_user_name}}</td>
                            <th colspan="3">入庫・出庫日</th>
                            <td colspan="6">
                                <datepicker :disabled="!edit_editable" class="form-control" v-model="item.inout_date" style="padding: 2px;"></datepicker>
                            </td>
                            <th colspan="3">資材区分</th>
                            <td colspan="6" :class="{'alert-dark':!isKojyo}">
                                <select :disabled="!edit_editable" v-model="item.sizai" class="form-control" :class="{'alert-dark':!isKojyo}">
                                    <option>材料</option>
                                    <option>商品</option>
                                    <option>事務用品</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">受注No.</th>
                            <td colspan="5" :class="{'alert-dark':!isKojyo}">
                                <input :disabled="!edit_editable" type="text" class="form-control" :class="{'alert-dark':!isKojyo}" placeholder="受注No." v-model="item.juchu_id">
                            </td>
                            <th colspan="3">責任部署</th>
                            <td colspan="15">
                                <select :disabled="!edit_editable" v-model="item.bumon_id" v-on:change="bumon_change" class="form-control">
                                    <option v-for="b in sys.bumons" :value="b.id" v-bind:key="b.id">{{b.name}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">入庫理由</th>
                            <td colspan="10">
                                <select :disabled="!edit_editable" v-model="item.in_riyuu" class="form-control">
                                    <option>新規(情報連絡票完成分入庫)</option>
                                    <option>棚移動(営業所間移動)</option>
                                    <option>新規(購入品移動 工場→出先移動)</option>
                                    <option>棚移動(購入品移動 工場→出先移動)</option>
                                    <option>新規(購入品移動 工場→中部移動)</option>
                                    <option>棚移動(購入品移動 工場→中部移動)</option>
                                    <option>その他</option>
                                </select>
                            </td>
                            <th colspan="3">出庫理由</th>
                            <td colspan="10" :class="{'alert-dark':!isKojyo}">
                                <select :disabled="!edit_editable" v-model="item.out_riyuu" class="form-control" :class="{'alert-dark':!isKojyo}">
                                    <option>自家消費</option>
                                    <option>備品</option>
                                    <option>固定資産</option>
                                    <option>その他</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">移動元倉庫</th>
                            <td colspan="10" class="bg-warning">
                                <div class="form-inline">
                                    <select :disabled="!edit_editable" v-model.number="item.from_type" class="form-control">
                                        <option value="0"  :disabled="!(from_type_enable || isKojyo)">第１工場 １階</option>
                                        <option value="1"  :disabled="!(from_type_enable || isKojyo)">第１工場 ２階</option>
                                        <option value="99" :disabled="!(from_type_enable || !isKojyo)">ソリューション棚</option>
                                    </select>
                                </div>
                            </td>
                            <th colspan="3">移動先倉庫</th>
                            <td colspan="10" class="bg-warning">
                                <div class="form-inline">
                                    <select :disabled="!edit_editable" v-model.number="item.to_type" class="form-control">
                                        <!--
                                        <option value="0">00901 1</option>
                                        <option value="1">02103 A-S1-4</option>
                                        <option value="2">03101 3-3-1</option>
                                        <option value="3">04101 5-1</option>
                                        <option value="4">05101 C-1</option>
                                        <option value="5">06101 A-1</option>
                                        <option value="6">07101 F</option>
                                        <option value="7">08101 2-1</option>
                                        <option value="8">09101 A-4</option>
                                        <option value="9">10101 MB-1</option>
                                        -->
                                        <option value="99">ソリューション棚</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="25">在庫</th>
                            <th colspan="1">
                                <button :disabled="!edit_editable" type="button" class="btn btn-outline-primary btn-sm" v-on:click="item_add()" v-if="!IsMobile"><i class="fas fa-plus"></i></button>
                            </th>
                        </tr>
                        <template v-for="(rec, idx) in item.items">
                            <tr v-bind:key="rec.id * 10 + 1">
                                <th colspan="1" rowspan="6">{{idx + 1}}</th>
                                <th colspan="2">製番</th>
                                <td colspan="5" :class="{'alert-dark':!isKojyo}">
                                    <input :disabled="!edit_editable" type="text" class="form-control" :class="{'alert-dark':!isKojyo}" placeholder="製番" v-model="rec.seiban">
                                </td>
                                <th colspan="2">品名</th>
                                <td colspan="7">{{rec.hinmoku ? rec.hinmoku.name : null}}</td>
                                <th colspan="3">在庫区分</th>
                                <td colspan="5">
                                    <select :disabled="!edit_editable" v-model="rec.kubun" class="form-control">
                                        <option>SCAW品</option>
                                        <option>貯蔵品</option>
                                    </select>
                                </td>
                                <td colspan="1" rowspan="5">
                                    <button :disabled="!edit_editable" type="button" class="btn btn-outline-primary btn-sm" v-on:click="item_del(rec.id)" v-if="!IsMobile"><i class="far fa-trash-alt"></i></button>
                                </td>
                            </tr>
                            <tr v-bind:key="rec.id * 10 + 2">
                                <th colspan="2">
                                    <a v-if="rec.zaiko!=null" :href="get_zeiko_url(rec.zaiko.id)" target="_blank">品番</a>
                                    <template v-else>品番</template>
                                </th>
                                <td colspan="5" class="bg-warning">
                                    <template v-if="IsMobile">{{ rec.hinmoku_id }}</template>
                                    <div v-else class="input-group">
                                        <input readonly :disabled="!edit_editable" type="text" class="form-control" placeholder="品番" v-model.trim="rec.hinmoku_id">
                                        <el-tooltip v-if="edit_editable" class="item" content="移動元の品番を選択" placement="bottom">
                                            <button :disabled="!edit_editable" type="button" class="btn btn-primary btn-sm" v-on:click="hinmoku_search(0, rec)"><i class="fas fa-search"></i></button>
                                        </el-tooltip>
                                    </div>
                                </td>
                                <th colspan="2">型式</th>
                                <td colspan="7">{{rec.hinmoku ? rec.hinmoku.model : null}}</td>
                                <th colspan="3">数量(個)</th>
                                <td colspan="5" class="bg-warning">
                                    <input :disabled="!edit_editable" type="number" class="form-control text-right" placeholder="数量" v-model.number="rec.suu" min="0">
                                </td>
                            </tr>
                            <tr v-bind:key="rec.id * 10 + 3">
                                <th colspan="4" >移動元倉庫</th>
                                <td colspan="20" :class="{'alert-dark':isKojyo}">
                                    {{rec.from_tana_str}}
                                </td>
                            </tr>
                            <tr v-bind:key="rec.id * 10 + 4">
                                <th colspan="2" rowspan="2">移動先倉庫</th>
                                <th colspan="2" :class="{'line-through':rec.to_zaiko_id}">棚指定</th>
                                <td colspan="20" class="bg-warning">
                                    <div class="input-group">
                                        <tanasel :sys="sys" v-model="rec.to_tana" :disabled="!edit_editable" v-on:apply="to_tana_change($event, rec)" :full="true"/>
                                    </div>
                                </td>
                            </tr>
                            <tr v-bind:key="rec.id * 10 + 5">
                                <th colspan="2" :class="{'line-through':!rec.to_zaiko_id}">在庫指定</th>
                                <td colspan="20" class="bg-warning">
                                    <div class="input-group">
                                        <div class="form-control" :class="{disabled: !edit_editable}" v-on:click="hinmoku_search(1, rec)">{{rec.to_zaiko_str}}</div>
                                        <el-tooltip v-if="edit_editable" class="item" content="移動先の在庫を選択" placement="bottom">
                                            <button :disabled="!edit_editable" type="button" class="btn btn-primary btn-sm" v-on:click="hinmoku_search(1, rec)"><i class="fas fa-search"></i></button>
                                        </el-tooltip>
                                    </div>
                                </td>
                            </tr>
                            <tr v-bind:key="rec.id * 10 + 6">
                                <td colspan="26" class="bg-secondary text-body pl-2 pt-2 pb-2 pr-2" v-if="(0<(rec.zaiko==null ? 0 : rec.zaiko.kobetu_flg)) && 20<=item.status">
                                    <kzaikotable :ikzs="rec.items" @click="kzaiko_click" :disabled="!cancel_enabled"></kzaikotable>
                                </td>
                            </tr>
                        </template>
                        <tr>
                            <th colspan="3">備考</th>
                            <td colspan="23">
                                <textarea class="form-control" cols="20" rows="3" placeholder="備考" v-model="item.biko" :disabled="IsMobile"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="1" rowspan="2"></th>
                            <th colspan="5">承認</th>
                            <th colspan="5">投入担当</th>
                            <th colspan="5">出庫担当</th>
                            <th colspan="5">所属長</th>
                            <th colspan="5">依頼者</th>
                        </tr>
                        <tr>
                            <td colspan="5" style="height: 80px;"></td>
                            <td colspan="5"></td>
                            <td colspan="5" class="text-center"><img v-if="status30_hanko_url!=null" border="0" :src="status30_hanko_url" width="64" height="64"></td>
                            <td colspan="5" class="text-center"><img v-if="status20_hanko_url!=null" border="0" :src="status20_hanko_url" width="64" height="64"></td>
                            <td colspan="5" class="text-center"><img v-if="status10_hanko_url!=null" border="0" :src="status10_hanko_url" width="64" height="64"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card mt-2">
                <div class="card-body">
                    <h5 class="bg-light">申請</h5>
                    <p>
                        修理品の入庫先を選択して「申請」をクリックしてください。<br>
                        品目ごとの移動先倉庫の違いについて<br>
                        移動先倉庫(棚指定)＝移動先の棚に新しく在庫を作成して移動します。<br>
                        移動先倉庫(在庫指定)＝移動先に既存の在庫を指定して移動します。<br>
                    </p>
                    <h5 class="bg-light">課長承認</h5>
                    <p>
                        内容に問題なければ「課長承認」をクリックしてください。<br>
                    </p>
                    <h5 class="bg-light">出庫待ち</h5>
                    <p>
                    </p>
                    <h5 class="bg-light">入庫待ち</h5>
                    <p>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <span v-if="errHtml!=null" v-html="errHtml"></span>
    <zaiko_dlg
        :sys="sys"
        :show="showZaikoModal"
        :hinmoku_id="zaiko_dlg_id"
        @close="zaikoDlgClose"
        ></zaiko_dlg>
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
option:disabled {
    color: gray;
    background-color: lightgray;
}
</style>
<style lang="scss">
.el-cascader-menu__wrap{
    height: 300px;
}
.rec_tana_sel{
    padding: 0px;
    margin: 0px;
    width: 90%;
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
        id: null,
        parent_kind: null,
        parent_id: null,
        hinmoku_id: null,
        hinmoku_suu: null,
	},
    data() {
		return {
            item:{
                id: null,
                inout_date: null,
                from_type: 99,
                from_jigyosyo_id: null,
                from_basho: null,
                from_basho_no: null,
                from_basho_tana: null,
                to_type: 99,
                to_jigyosyo_id: null,
                to_basho: null,
                to_basho_no: null,
                to_basho_tana: null,
                to_zaiko_id: null,
                to_zaiko_str: null,
                sizai: '材料',
                juchu_id: null,
                bumon_id: '4461',
                in_riyuu: 'その他',
                out_riyuu: 'その他',
                kubun: 'SCAW品',
                tana: '00901 1',
                biko: null,
                status: 0,
                status10_date: null,
                status10_user_name: null,
                status20_date: null,
                status20_user_name: null,
                status30_date: null,
                status30_user_name: null,
                status40_date: null,
                status40_user_name: null,
                status50_date: null,
                status50_user_name: null,
                status60_date: null,
                status60_user_name: null,
                status70_date: null,
                status70_user_name: null,
                parent_kind: 0,
                parent_id: null,
                editable: false,
                items: [],
            },
            ing: false,
            zaikos: null,
            cur_item: null,
            message: null,
            errHtml: null,
            showZaikoModal: false,
            showZaikoModal_mode: 0,
            zaiko_dlg_id: null,
            showSZaikoModal: false,
            kzaiko_dlg: {
                id: null,
                answer: "個別在庫ID入力エリアにフォーカスがある状態でＱＲコードを読み取るか、キーボードで個別在庫IDを入力してリターンキーを押してください。",
                focus: false,
            },
			audio_ok: new Audio(root_path + 'sounds/ok.mp3'),
			audio_ng: new Audio(root_path + 'sounds/ng.mp3'),
        }
    },
    computed:{
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
        status70_hanko_url(){
            var t = this;
            return t.get_hanko_url(t.item.status70_user_name, t.item.status70_date);
        },
        save_enabled(){
            var t = this;
            if(!t.item.items.length) return false;
            if (t.item.items.some(rec => {
                if(!rec.hinmoku_id) return true;
                if(!rec.suu) return true;
                if(rec.to_tana.length!=4 && !rec.to_zaiko_id) return true;
                return false;
            })){
                return false;
            }
            if(t.sys!=null){
                switch (t.item.status) {
                    case 10: // 所属長認待ち
                        return t.sys.user.kengen12;
                    case 20: // 出庫待ち
                        if (t.item.items.some(rec => (0<rec.zaiko?.kobetu_flg) && (rec.suu != rec.items?.length))) return false;
                        return t.sys.user.kengen11;
                    case 30: // 入庫待ち
                        if (t.item.items.some(rec => rec.items!=null && rec.items.some(r=>r.status!=2))) return false;
                        return t.sys.user.kengen11;
                    case 40: // 倉庫移動待ち
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
            return (t.item.status < 10) && !t.IsMobile;
        },
        cancel_enabled(){
            var t = this;
            if(t.sys!=null){
                switch (t.item.status) {
                    case 0:
                        return true;
                    case 10: // 所属長認待ち
                        return true;
                    case 20: // 出庫待ち
                        return t.sys.user.kengen12;
                    case 30: // 入庫待ち
                        return t.sys.user.kengen11;
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
        from_type_enable(){
            var t = this;
            if (t.item.items.some(rec => {
                if(rec.hinmoku_id){
                    return true;
                }
                return false;
            })) {
                return false;
            }
            return true;
        },
        isKojyo(){
            var t = this;
            if(t.item.from_type==0 || t.item.from_type==1){
                return true;
            }
            return false;
        },
        qr_editable(){
            var t = this;
			if(t.item.status == 20 || t.item.status == 30){
				return t.item.items.some(rec => (0<rec.zaiko?.kobetu_flg));
			}
			return false;
        },
    },
    watch: {
        'item.kouki_end_str': function (val) {
            var t = this;
            if(t.item.kojyo_end_str==null){
                t.item.kojyo_end_str = val;
            }
        },
        'item.kouki_end_str': function (val) {
            var t = this;
            if(t.item.kojyo_end_str==null){
                t.item.kojyo_end_str = val;
            }
        },
    },
    mounted() {
        var t = this;
        var hit = false;
        var now = new Date();
        t.item.id = t.id;
        if (0 < t.item.id){
            t.ing = true;
            hit = true;
            $.ajax({
                type : "POST",
                url: root_path + "zido/AjaxGetZido",
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
        if (!hit){
            t.item.user_id = user_id;
        }
        if(t.item.items.length==0){
            t.item_add();
        }
    },
    methods: {
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
                zido_id: null,
                hinmoku_id: null,
                seiban: null,
                suu: null,
                zaiko_id: null,
                name: null,
                model: null,
                from_tana_str: null,
                to_type: 99,
                to_jigyosyo_id: null,
                to_basho: null,
                to_basho_no: null,
                to_basho_tana: null,
                to_zaiko_id: null,
                to_zaiko_str: null,
				kubun: "SCAW品",
                to_tana: [],
                hinmoku: {
                    name: null,
                    model: null,
                },
            });
        },
        item_del(id){
            var t = this;
            t.$dialog.confirm({
                title: '確認',
                body: '削除してもよろしいですか？'
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                t.item.items.splice(t.item.items.findIndex(i => i.id == id), 1);
            });
        },
		bumon_change(evnt) {
		},
        set_data(datas){
            var t = this;
            if(datas==null){
                if(t.parent_kind != null && t.parent_id != null && t.hinmoku_id != null && t.hinmoku_suu != null){
                    t.item.parent_kind = t.parent_kind;
                    t.item.parent_id = t.parent_id;
                    t.item.from_type = 0;
                    t.item_add();
                    var rec = t.item.items[0];
                    rec.suu = parseInt(t.hinmoku_suu);
                    rec.zaiko_id = null;
                    rec.hinmoku_id = t.hinmoku_id;
                    rec.hinmoku = null;
                    t.set_scaw_item(rec);
                }
            }else{
                Object.assign(t.item, datas.item);
                t.item.inout_date = t.$options.filters.YYYYMMDD(datas.item.inout_date);
                t.item.status10_date = t.$options.filters.YYYYMMDD(datas.item.status10_date);
                t.item.status20_date = t.$options.filters.YYYYMMDD(datas.item.status20_date);
                t.item.status30_date = t.$options.filters.YYYYMMDD(datas.item.status30_date);
                t.item.status40_date = t.$options.filters.YYYYMMDD(datas.item.status40_date);
                t.item.status50_date = t.$options.filters.YYYYMMDD(datas.item.status50_date);
                t.item.status60_date = t.$options.filters.YYYYMMDD(datas.item.status60_date);
                t.item.status70_date = t.$options.filters.YYYYMMDD(datas.item.status70_date);
                t.item.items.forEach(rec => {
                    if(rec.zaiko!=null){
                        rec.from_tana_str = cmn.get_zaiko_name(rec.zaiko);
                    }
                    if(rec.to_zaiko!=null){
                        rec.to_zaiko_str = cmn.get_zaiko_name(rec.to_zaiko);
                    }
                    rec.to_tana =[];
                    if(rec.to_jigyosyo_id && rec.to_basho && rec.to_basho_no && rec.to_basho_tana){
                        rec.to_tana =[rec.to_jigyosyo_id, rec.to_basho, rec.to_basho_no, rec.to_basho_tana];
                    }
                });
            }
        },
        list(){
            router.push({ name: 'zido.list' });
        },
        chk_data(){
            var t = this;
            var err_dlg = (message)=>{
                t.$dialog.alert({
                    title: 'エラー',
                    body: message,
                }, {
                    okText: 'ok',
                    reverse: false,
                });
            };
            switch(t.item.status){
                case 0: // 申請待ち
                    if(t.item.items.some(rec => {
                        if (!Number.isInteger(rec.suu) || rec.suu<1) {
                            err_dlg('数量が不正な部品があります。');
                            return true;
                        }else if(rec.zaiko!=null && rec.zaiko.zaiko_suu - rec.zaiko.sinsei_suu - rec.zaiko.kashi_suu - rec.suu < 0){
                            err_dlg('数量が上限を超えている部品があります。');
                            return true;
                        }
                        return false;
                    })){
                        return false;
                    }
                    break;
            }
            return true;
        },
        save(type){
            var t = this;
            if(!t.chk_data()){
                return;
            }
            t.$dialog.confirm({
                title: '確認',
                body: {"-1":"ひとつ前に戻してもよろしいですか？", "0":"保存してもよろしいですか？", "1":"申請してもよろしいですか？"}[type],
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
                t.item.items.forEach(rec => {
                    rec.to_jigyosyo_id = rec.to_tana[0];
                    rec.to_basho = rec.to_tana[1];
                    rec.to_basho_no = rec.to_tana[2];
                    rec.to_basho_tana = rec.to_tana[3];
                });
                t.errHtml = null;
                $.ajax({
                    type : "POST",
                    url : root_path + "zido/AjaxPutZido",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        item: t.item,
                    })
                }).done(function(datas) {
                    //t.set_data(datas);
                    t.ing = false;
                    if(datas!=null){
                        t.message = datas.message;
                        if(datas.ret){
                            t.list();
                        }
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.ing = false;
                    t.errHtml = jqXHR;
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
        hinmoku_search(mode, r){
            var t = this;
            t.cur_item = r;
            if(t.isKojyo && mode==0){
                t.showSZaikoModal = true
            }else{
                t.showZaikoModal = true;
				t.showZaikoModal_mode = mode;
                t.zaiko_dlg_id = r.hinmoku_id;
            }
        },
        zaikoDlgClose: function(zaiko){
            var t = this;
            t.showZaikoModal = false;
            if(zaiko!=null){
				switch(t.showZaikoModal_mode){
					case 0:
						//t.cur_item.suu = 1;
						t.cur_item.zaiko_id = zaiko.id;
						t.cur_item.hinmoku_id = zaiko.hinmoku_id;
						t.cur_item.hinmoku = zaiko.hinmoku;
						t.cur_item.from_tana_str = cmn.get_zaiko_name(zaiko);
                        t.cur_item.zaiko = zaiko;
						break;
					case 1:
                        t.cur_item.to_zaiko_id = zaiko.id;
                        t.cur_item.to_zaiko_str = cmn.get_zaiko_name(zaiko);
                        t.cur_item.to_tana = [];
                        /*
						t.cur_item.to_tana =[
							zaiko.jigyosyo_id,
							zaiko.basho,
							zaiko.basho_no,
							zaiko.basho_tana,
						];
                        */
						break;
				}
            }
        },
        szaikoDlgClose: function(zaiko){
            var t = this;
            t.showSZaikoModal = false;
            if(zaiko!=null){
                //t.cur_item.suu = 1;
                t.cur_item.zaiko_id = null;
                t.cur_item.hinmoku_id = zaiko.id;
                t.cur_item.hinmoku = zaiko;
                t.cur_item.from_tana_str = null;
            }
        },
		set_scaw_item(rec){
            var t = this;
            $.ajax({
                type : "POST",
                url : root_path + "ajax/AjaxGetHinmoku",
                dataType : "json",
                contentType : 'application/json',
                async: true,
                data : JSON.stringify({
                    type: t.item.from_type,
                    id: rec.hinmoku_id,
                })
            }).done(function(datas) {
                rec.hinmoku = datas.item;
            }).fail(function(jqXHR, textStatus, errorThrown) {
            });
        },
		kzaiko_id_input(){
            var t = this;
            var id = t.kzaiko_dlg.id;
            var audio = t.audio_ng;
            switch(t.item.status){
                case 20: // 出庫待ち
                    t.item.ing = true;
                    $.ajax({
                        type : "POST",
                        url : root_path + "ajax/AjaxGetKzaiko",
                        dataType : "json",
                        contentType : 'application/json',
                        async: true,
                        data : JSON.stringify({
                            id: id,
                        })
                    }).done(function(datas) {
                        if(datas.item==null){
                            t.kzaiko_dlg.answer = `個別在庫ID[${id}]に該当する個別在庫がありませんでした。`;
                        }else{
                            if(0<datas.item.status){
                                    t.kzaiko_dlg.answer = `個別在庫ID[${id}]は出庫できません。`;
                            }else{
                                var hit = false;
                                if(undefined === t.item.items.find(target_item => {
                                    if(target_item.zaiko_id == datas.item.zaiko_id){
                                        hit = true;
                                        var ikz = target_item.items.find(item => item.kzaiko_id == datas.item.id);
                                        if(ikz != undefined){
                                            t.kzaiko_dlg.answer = `個別在庫ID[${id}]は既に追加した個別在庫です。`;
                                            return true;
                                        }
                                        if(target_item.items.length < target_item.suu){
                                            target_item.items.push({
                                                item_id: t.item.id,
                                                kzaiko_id: datas.item.id,
                                                status: 1,
                                                kzaiko: datas.item,
                                            });
                                            audio = t.audio_ok;
                                            target_item.out_suu = target_item.items.length;
                                            t.kzaiko_dlg.answer = `個別在庫ID[${id}]を追加しました。`;
                                            return true;
                                        }
                                        return false;
                                    }
                                })){
                                    if(hit){
                                        t.kzaiko_dlg.answer = `これ以上追加できません。`;
                                    }else{
                                        t.kzaiko_dlg.answer = `個別在庫ID[${id}]は出庫する部品に該当しませんでした。`;
                                    }
                                }
                            }
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        t.kzaiko_dlg.answer = `検索に失敗しました(${textStatus})`;
                    }).always(function(jqXHR, textStatus, errorThrown) {
                        audio.play();
                        t.kzaiko_dlg.id = null;
                        t.item.ing = false;
                        t.$nextTick(() => {
                            t.$refs.kzaiko_dlg_id.focus();
                        });
                    });
                    break;
                case 30: // 入庫待ち
                    var hit = false;
                    t.item.items.forEach(item => {
                        item.items.forEach(ikz => {
                            if(ikz.kzaiko_id == id){
                                audio = t.audio_ok;
                                hit = true;
                                ikz.status = 2;
                                t.kzaiko_dlg.answer = `個別在庫ID[${id}]を追加しました。`;
                            }
                        });
                    });
                    if(!hit){
                        t.kzaiko_dlg.answer = `個別在庫ID[${id}]は出庫していません。`;
                    }
                    audio.play();
                    t.kzaiko_dlg.id = null;
                    t.$nextTick(() => {
                        t.$refs.kzaiko_dlg_id.focus();
                    });
                    break;
            }
        },
        kzaiko_click(ikz){
            var t = this;
            switch(t.item.status){
                case 20: // 出庫待ち
                    t.$dialog.confirm({
                        title: '確認',
                        body: '削除してもよろしいですか？'
                    },{
                        okText: 'はい',
                        cancelText: 'キャンセル',
                    }).then(function() {
                        t.item.items.forEach(item => {
                            var idx = item.items.findIndex(k => k.kzaiko_id == ikz.kzaiko_id);
                            if(0<=idx){
                                item.items.splice(idx, 1);
                            }
                        });
                    });
                    break;
                case 30: // 入庫待ち
                    if(ikz.status==2){
                        t.$dialog.confirm({
                            title: '確認',
                            body: '返却を取り消してもよろしいですか？'
                        },{
                            okText: 'はい',
                            cancelText: 'キャンセル',
                        }).then(function() {
                            ikz.status = 1;
                            t.used_suu_calc();
                        });
                    }
                    break;
            }
        },
        to_tana_change(value, rec){
            if(rec.to_tana.length==4){
                rec.to_zaiko_id = null;
                rec.to_zaiko_str = null;
            }
        },
	},
}
</script>
