<template>
<div>
    <div class="card">
        <loading :active.sync="item.ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">詳細 (部品出庫No.[<span v-if='item.id>0' class="badge badge-primary">{{item.id}}</span>]</div>
        <div class="card-body">
            <div class="form-inline">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">在庫場所</div>
                    </div>
                    <label class="form-control">{{item.jigyosyo_name}}</label>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">依頼者</div>
                    </div>
                    <label class="form-control">{{item.user_name}}</label>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">在庫担当</div>
                    </div>
                    <label class="form-control">{{item.status20_user_name}}</label>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">受注番号</div>
                    </div>
                    <input type="text" class="form-control" placeholder="00-00000" v-model="item.seiban" pattern="\d{2,4}-\d{3,4}-\d{3,4}">
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">出庫希望日</div>
                    </div>
                    <label class="form-control">{{item.shukko_date}}</label>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">出庫日</div>
                    </div>
                    <label class="form-control">{{item.status20_date}}</label>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">状態</div>
                    </div>
                    <shukkostatus :stsnum="item.status" class="form-control"/>
                    <button type="button" class="btn btn-primary btn-sm" style="min-width: unset;" v-on:click="save(0)" :disabled="cancel_disabled" v-if="!IsMobile"><i class="fas fa-times"></i></button>
                </div>
                <div class="btn-group btn-group-sm ml-2" role="group">
                    <button type="button" class="btn btn-primary" v-on:click="save(2)" v-if="!IsMobile" :disabled="hozon_disabled">保存のみ</button>
                    <shukkobutton v-if="!IsMobile" :stsnum="item.status" class="btn btn-primary" v-on:click="save(1)" :texttype="2" suffix=""  v-bind:sys="sys" :disabled="sts_disabled"/>
                    <a :href="'../descprint/' + item.id" class="btn btn-primary"  style="min-width: unset;" target="_blank">印刷</a>
                    <button type="button" class="btn btn-primary" v-on:click="list()">一覧</button>
                </div>
            </div>
            <div class="card" v-if="qr_editable && !IsMobile">
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
            <div style="overflow-x:auto;">
                <table class="table table-sm table-bordered mt-2 table-min">
                    <thead>
                        <tr class="autorow">
                            <th style="width: 4%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 4%"></th>
                            <th style="width: 4%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="cart in carts">
                            <tr v-bind:key="`${cart.scaw_flg}-1`" :class="cart.classes">
                                <th colspan="9">{{cart.name}}</th>
                                <th colspan="3">保管場所</th>
                                <th colspan="3">担当者</th>
                                <th colspan="1">出庫担当者</th>
                                <th rowspan="2">備考</th>
                                <th rowspan="2">在庫<br>ID</th>
                                <th rowspan="2">
                                    操作
                                    <button type="button" class="btn btn-primary btn-sm" @click="desc_open(null)"><i class="fa fa-bars"></i></button>
                                </th>
                            </tr>
                            <tr v-bind:key="`${cart.scaw_flg}-2`" :class="cart.classes">
                                <th>品目コード</th>
                                <th>型式</th>
                                <th>Ver</th>
                                <th>種別</th>
                                <th>名称</th>
                                <th>メーカ</th>
                                <th>部品備考</th>
                                <th>在庫数</th>
                                <th>貸出中</th>
                                <th>場所名</th>
                                <th>場所No.</th>
                                <th>棚No.</th>
                                <th>申請数</th>
                                <th>許可数</th>
                                <th>使用数</th>
                                <th>投入製番</th>
                            </tr>
                            <template v-for="rec in cart.items">
                                <tr v-bind:key="`${cart.scaw_flg}-3-${rec.id}-1`">
                                    <td>{{rec.zaiko.hinmoku_id}}</td>
                                    <td>{{rec.zaiko.hinmoku!=null ? rec.zaiko.hinmoku.model : ""}}</td>
                                    <td>{{rec.zaiko.model_v}}</td>
                                    <td>{{rec.zaiko.model_kind}}</td>
                                    <td>{{rec.zaiko.hinmoku!=null ? rec.zaiko.hinmoku.name : ""}}</td>
                                    <td>{{rec.zaiko.hinmoku!=null ? rec.zaiko.hinmoku.maker : ""}}</td>
                                    <td>{{rec.zaiko.biko}}</td>
                                    <td>{{rec.zaiko.zaiko_suu}}</td>
                                    <td>{{rec.zaiko.kashi_suu}}</td>
                                    <td>{{rec.zaiko.basho}}</td>
                                    <td>{{rec.zaiko.basho_no}}</td>
                                    <td>{{rec.zaiko.basho_tana}}</td>
                                    <td class='text-right'>
                                        {{rec.req_suu}}
                                    </td>
                                    <td class='text-right' :class="{'table-warning': out_suu_enable}">
                                        <input v-if="out_suu_enable && rec.zaiko.kobetu_flg==0" type="number" class="table_input F1 text-right" v-model.number="rec.out_suu" :disabled="sts_disabled" min="0" max="9999">
                                        <span v-else>{{rec.out_suu}}</span>
                                    </td>
                                    <td class='text-right' :class="{'table-warning': used_suu_enable}">
                                        <input v-if="used_suu_enable && rec.zaiko.kobetu_flg==0" type="number" class="table_input F1 text-right" v-model.number="rec.used_suu" min="0" max="9999">
                                        <span v-else>{{rec.used_suu}}</span>
                                    </td>
                                    <td :class="{'table-warning': (seiban_enable && rec.zaiko.scaw_flg==1)}">
                                        <span v-if="rec.zaiko.scaw_flg==0"></span>
                                        <input v-else-if="seiban_enable" type="text" class="table_input F2" v-model="rec.seiban" placeholder="R000000000">
                                        <span v-else>{{rec.seiban}}</span>
                                    </td>
                                    <td>
                                        <template v-if="IsMobile">{{ rec.biko }}</template>
                                        <textbox v-model="rec.biko" title="備考" v-if="!IsMobile"></textbox>
                                    </td>
                                    <td>{{rec.zaiko.id}}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm ml-2" role="group">
                                            <a type="button" class="btn btn-primary btn-sm" :href="get_zeiko_url(rec.zaiko.id)" target="_blank">在</a>
                                            <a type="button" class="btn btn-primary btn-sm" :href="get_kzeiko_url(rec.zaiko.id)" target="_blank">個</a>
                                            <button v-if="0<rec.zaiko.kobetu_flg" type="button" class="btn btn-primary btn-sm" @click="desc_open(rec)"><i class="fa fa-bars"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <transition v-bind:key="`${cart.scaw_flg}-3-${rec.id}-2`" v-if="rec.desc_flg">
                                    <tr>
                                        <td colspan="19" class="bg-secondary text-body pl-5 pt-2 pb-2 pr-2">
                                            <kzaikotable :ikzs="rec.items" @click="kzaiko_click"></kzaikotable>
                                        </td>
                                    </tr>
                                </transition>
                            </template>
                            <tr v-bind:key="`${cart.scaw_flg}-4`" v-if="cart.scaw_flg==1">
                                <td colspan="19">&nbsp;</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <filelist
                title="ファイル名 - BIツール(製番投入品チェックファイル)"
                :value="item.file_urls"
                :id="item.id"
                :url="url"
            />
        </div>
    </div>
</div>
</template>

<style scoped lang="scss">
.table_input{
    padding: 0px;
    margin: 0px;
    &.F1{
        width: 100%;
    }
    &.F2{
        width: 160px;
    }
}
.table-min{
    min-width: 1200px;
}
.v-enter {
    opacity: 0;
}
.v-enter-to {
    opacity: 1;
}
.v-enter-active {
    transition: all 500ms;
}
.v-leave {
    opacity: 1;
}
.v-leave-to {
    opacity: 0;
}
.v-leave-active {
    transition: all 500ms;
}
</style>

<script>
import moment from 'moment';
import {cmn_mixin} from '../../components/cmn_mixin.js';
import {edit_mixin} from "./edit_mixin.js";
import * as cmn from '../../components/common.js';

export default {
    mixins: [cmn_mixin, edit_mixin],
    data() {
		return {
            url: root_path + 'shukko',
            carts: [
                {
                    scaw_flg: 1,
                    classes: "table-warning",
                    name: "SCAW品",
                    items: [],
                },
                {
                    scaw_flg: 0,
                    classes: "table-primary",
                    name: "貯蔵品",
                    items: [],
                },
			],
            kzaiko_dlg: {
                id: null,
                answer: "ＱＲコードを読み取るか、キーボードで個別在庫IDを入力してリターンキーを押してください。",
                focus: false,
            },
			audio_ok: new Audio(root_path + 'sounds/ok.mp3'),
			audio_ng: new Audio(root_path + 'sounds/ng.mp3'),
		}
    },
	mounted() {
    },
	computed: {
        cancel_disabled(){
            var t = this;
            if(t.sys!=null){
                switch(t.item.status){
                    case 0: // 出庫申請待ち
                    case 10: // 出庫待ち
                        return false;
                    case 20: // 返却待ち
                        if (t.sys.user.kengen11) {
                            return false;
                        }
                        break;
                    case 30: // 返却承認待ち
                        return false;
                    case 99: // キャンセル
                        return false;
                    default:
                        break;
                }
            }
            return true;
        },
        sts_disabled(){
            var t = this;
            if(t.sys!=null){
                switch (t.item.status) {
                    case 10: // 出庫待ち
                    case 30: // 返却承認待ち
                        if (t.sys.user.kengen11) {
                            return false;
                        } else {
                            return true;
                        }
                        break;
                    case 50: // 課長承認待ち
                        if (t.sys.user.kengen12) {
                            return false;
                        } else {
                            return true;
                        }
                        break;
                }
                return false;
            }else{
                return true;
            }
        },
        out_suu_enable(){
            var t = this;
            return t.item.status==10;
        },
        used_suu_enable(){
            var t = this;
            return t.item.status==20;
        },
        seiban_enable(){
            var t = this;
            return 20<=t.item.status && t.item.status<=40;
        },
        qr_editable(){
            var t = this;
			if(t.item.status == 10 || t.item.status == 20){
				return t.item.items.some(rec => (0<rec.zaiko?.kobetu_flg));
			}
			return false;
        },
        hozon_disabled(){
            var t = this;
			switch(t.item.status){
                case 10: // 出庫時
                return true;
            }
            return false;
        },
    },
    methods: {
        used_suu_calc() {
            var t = this;
            t.item.items.forEach(item => {
                if(0<item.zaiko.kobetu_flg){
                    item.used_suu = item.items.filter(k => k.status==1).length;
                }else{
                    if(item.used_suu==null){
                        item.used_suu = 0;
                    }
                }
            });
        },
        desc_open(rec){
            var t = this;
            if(rec==null){
                var toggle = null;
                t.item.items.forEach(r => {
                    if(0<r.zaiko.kobetu_flg){
                        if(toggle == null) toggle = !r.desc_flg;
                        r.desc_flg = toggle;
                    }
                });
            }else{
                rec.desc_flg = !rec.desc_flg
            }
        },
		kzaiko_id_input(){
            var t = this;
            var audio = t.audio_ng;
            var id = t.kzaiko_dlg.id;
            switch(t.item.status){
                case 10: // 出庫待ち
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
                                var item = t.item.items.find(item => item.zaiko_id == datas.item.zaiko_id);
                                if(item === undefined){
                                    t.kzaiko_dlg.answer = `個別在庫ID[${id}]は出庫する部品に該当しませんでした。`;
                                }else{
                                    var ikz = item.items.find(item => item.kzaiko_id == datas.item.id);
                                    if(ikz === undefined){
                                        item.items.push({
                                            item_id: item.id,
                                            kzaiko_id: datas.item.id,
                                            status: 1,
                                            kzaiko: datas.item,
                                        });
                                        item.out_suu = item.items.length;
                                        item.desc_flg = true;
                                        t.kzaiko_dlg.answer = `個別在庫ID[${id}]を追加しました。`;
                                        audio = t.audio_ok;
                                    }else{
                                        t.kzaiko_dlg.answer = `個別在庫ID[${id}]は既に追加した個別在庫です。`;
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
                case 20: // 返却待ち
                    var hit = false;
                    t.item.items.forEach(item => {
                        item.items.forEach(ikz => {
                            if(ikz.kzaiko_id == id){
                                hit = true;
                                ikz.status = 2;
                                item.desc_flg = true;
                                t.kzaiko_dlg.answer = `個別在庫ID[${id}]を追加しました。`;
                                audio = t.audio_ok;
                            }
                        });
                    });
                    t.used_suu_calc();
                    if(!hit){
                        t.kzaiko_dlg.answer = `個別在庫ID[${id}]は出庫していません。`;
                    }
                    t.kzaiko_dlg.id = null;
                    t.$nextTick(() => {
                        t.$refs.kzaiko_dlg_id.focus();
                    });
                    audio.play();
                    break;
            }
        },
        kzaiko_click(ikz){
            var t = this;
            switch(t.item.status){
                case 10: // 出庫待ち
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
                                item.out_suu = item.items.length;
                            }
                        });
                    });
                    break;
                case 20: // 返却待ち
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
        list(){
            router.push({ name: 'shukko.list' });
        },
        mounted_complete(){
            var t = this;
            t.carts[0].items = t.item.items.filter(item => item.zaiko.scaw_flg == 1);
            t.carts[1].items = t.item.items.filter(item => item.zaiko.scaw_flg != 1);
            switch(t.item.status){
                case 10: // 出庫待ち
                    t.item.items.forEach(item => {
                        if(item.out_suu==null){
                            item.out_suu = 0;
                        }
                    });
                    break;
                case 20: // 返却待ち
                    t.used_suu_calc();
                    break;
            }
        },
        save(type){
            var t = this;
            if(type==1 && !t.chk_data(2)){
                return;
            }
            var mess = t.warn_chk_data(2);
            if(type == 0){
                switch(t.item.status){
                    case 0: // 出庫申請待ち
                        mess = 'キャンセルしてもよろしいですか？';
                        break;
                    default:
                        mess = 'ひとつ前に戻してもよろしいですか？';
                        break;
                }
            }
            t.$dialog.confirm({
                title: '確認',
                body: mess
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                switch(type){
                    case 0:
                        t.item.command = 2;
                        break;
                    case 1:
                        t.item.command = 1;
                        break;
                }
                t.item.ing = true;
                $.ajax({
                    type : "POST",
                    url : t.url + "/AjaxPutShukko2",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        shukko: t.item,
                    })
                }).done(function(datas) {
                    t.set_data2(datas);
                    t.item.ing = false;
                    t.list();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.item.ing = false;
                });
            });
        },
	},
}
</script>
