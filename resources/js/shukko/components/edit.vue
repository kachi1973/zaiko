<template>
<div>
    <div class="card">
        <loading :active.sync="item.ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">カート (部品出庫No.[
            <span v-if='item.id>0' class="badge badge-primary">{{item.id}}</span>
            <span v-else class="badge badge-primary">新規</span>
            ]
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-body">
					<form class="form-inline">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">在庫場所</div>
                            </div>
                            <select v-model="item.jigyosyo_id" class="form-control">
                                <option v-for="item in sys.jigyosyos" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
                            </select>
                        </div>
                        <user_dlg :disabled="!editable1" v-model="item.user_id" />
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">在庫担当</div>
                            </div>
                            <span class="form-control">{{item.status20_user_name}}</span>
                        </div>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div class="input-group-text">受注番号</div>
							</div>
							<input type="text" class="form-control" placeholder="00-00000" v-model="item.seiban">
						</div>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div class="input-group-text">出庫希望日</div>
							</div>
                            <datepicker :disabled="!editable1" class="form-control" v-model="item.shukko_date" style="padding: 2px;"></datepicker>
						</div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">出庫日</div>
                            </div>
                            <span class="form-control">{{item.status20_date}}</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">状態</div>
                            </div>
                            <shukkostatus :stsnum="item.status" class="form-control"/>
                        </div>
                        <div class="btn-group btn-group-sm ml-2" role="group">
                            <button type="button" class="btn btn-primary" v-on:click="save(0)" v-bind:disabled="!save_enabled" v-if="!IsMobile">保存</button>
                            <button type="button" class="btn btn-primary" v-on:click="save(1)" v-bind:disabled="!save_enabled" v-if="!IsMobile">出庫申請</button>
							<button type="button" class="btn btn-primary" v-on:click="list()">一覧</button>
						</div>
					</form>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="table table-sm table-bordered table-hover table-min">
                    <thead>
                        <tr class="autorow">
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 2%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 3%" v-if="!IsMobile"></th>
                            <th style="width: 0.5%" v-if="!IsMobile"></th>
                        </tr>
                        <tr class="thead-light">
                            <th rowspan="2">ID</th>
                            <th rowspan="2">在庫場所</th>
                            <th rowspan="2">在庫区分</th>
                            <th colspan="3">場所</th>
                            <th rowspan="2">品目<br/>コード</th>
                            <th colspan="3">型式</th>
                            <th rowspan="2">名称</th>
                            <th rowspan="2">メーカ</th>
                            <th rowspan="2">部品備考</th>
                            <th colspan="3">数量</th>
                            <th :colspan="IsMobile ? 1 : 2">依頼者</th>
                            <th rowspan="2" v-if="!IsMobile">操作</th>
                        </tr>
                        <tr class="thead-light">
                            <th>場所名</th>
                            <th>場所No.</th>
                            <th>棚No.</th>
                            <th>型式</th>
                            <th>Ver</th>
                            <th>種別</th>
                            <th>在庫</th>
                            <th>申請中</th>
                            <th>貸出中</th>
                            <th>申請数</th>
                            <th v-if="!IsMobile">備考</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rec, idx) in item.items" v-bind:key="rec.id">
                            <td class='text-center'>{{rec.zaiko.id}}</td>
                            <td>{{rec.zaiko.jigyosyo_name}}</td>
                            <td><h5><span class="badge" :class="[{ 'badge-primary': rec.zaiko.scaw_flg==0 }, { 'badge-warning': rec.zaiko.scaw_flg==1 }]">{{rec.zaiko.scaw_flg_str}}</span></h5></td>
                            <td>{{rec.zaiko.basho}}</td>
                            <td>{{rec.zaiko.basho_no}}</td>
                            <td>{{rec.zaiko.basho_tana}}</td>
                            <td>{{rec.zaiko.hinmoku_id}}</td>
                            <td>{{rec.zaiko.hinmoku != null ? rec.zaiko.hinmoku.model : null}}</td>
                            <td>{{rec.zaiko.model_v}}</td>
                            <td>{{rec.zaiko.model_kind}}</td>
                            <td>{{rec.zaiko.hinmoku != null ? rec.zaiko.hinmoku.name : null}}</td>
                            <td>{{rec.zaiko.hinmoku != null ? rec.zaiko.hinmoku.maker : null}}</td>
                            <td>{{rec.zaiko.biko}}</td>
                            <td class='text-right'>{{rec.zaiko.zaiko_suu}}</td>
                            <td class='text-right'>{{rec.zaiko.sinsei_suu}}</td>
                            <td class='text-right'>{{rec.zaiko.kashi_suu}}</td>
                            <td>
                                <input type="number" class="table_input F1 text-right" v-model.number="rec.req_suu" :disabled="!editable1" min="1" max="9999">
                            </td>
                            <td v-if="!IsMobile">
                                <textbox v-model="rec.biko" title="備考"></textbox>
                            </td>
                            <td v-if="!IsMobile">
                                <div class='text-center'>
                                    <button type="button" :disabled="!editable1" class="btn btn-primary btn-sm" v-on:click="item_delete(idx)"><i class="fas fa-minus"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card" v-if="!IsMobile">
        <div class="card-header bg-secondary">在庫</div>
        <div class="card-body">
            <loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
            <div class="card">
                <div class="card-body">
                    <form class="form-inline">
                        <tanasel :sys="sys" tananame="棚" v-model="search.tana" />
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">在庫区分</div>
                            </div>
                            <select v-model="search.scaw_flg">
                                <option value="-1">全て</option>
                                <option value="0">貯蔵品</option>
                                <option value="1">SCAW品</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">品目コード</div>
                            </div>
                            <input type="text" class="form-control" placeholder="貯蔵品コード" v-model="search.hinmoku_id">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">型式</div>
                            </div>
                            <input type="text" class="form-control" placeholder="型式" v-model="search.model">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">名称</div>
                            </div>
                            <input type="text" class="form-control" placeholder="名称" v-model="search.name">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">メーカー</div>
                            </div>
                            <input type="text" class="form-control" placeholder="メーカー" v-model="search.maker">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">備考</div>
                            </div>
                            <input type="text" class="form-control" placeholder="備考" v-model="search.biko">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">ソート</div>
                            </div>
                            <select v-model="search.sort" class="form-control">
                                <option value="0">ID 昇順</option>
                                <option value="1">ID 降順</option>
                                <option value="2">在庫区分 昇順</option>
                                <option value="3">在庫区分 降順</option>
                                <option value="4">場所 昇順</option>
                                <option value="5">場所 降順</option>
                                <option value="6">品目コード 昇順</option>
                                <option value="7">品目コード 降順</option>
                                <!--
                                <option value="8">型式 昇順</option>
                                <option value="9">型式 降順</option>
                                <option value="10">名称 昇順</option>
                                <option value="11">名称 降順</option>
                                <option value="12">メーカ 昇順</option>
                                <option value="13">メーカ 降順</option>
                                -->
                                <option value="14">製造年月 昇順</option>
                                <option value="15">製造年月 降順</option>
                            </select>
                        </div>
						<div class="btn-group btn-group-sm" role="group">
							<button type="button" class="btn btn-primary" v-on:click="search_func(1)">検索</button>
							<button type="button" class="btn btn-primary" v-on:click="search_clear()">クリア</button>
						</div>
                        <paginate
                            :page-count="zaiko.page_cnt"
                            :page-range="3"
                            :margin-pages="2"
                            :click-handler="zaiko_page"
                            :prev-text="'＜'"
                            :next-text="'＞'"
                            :container-class="'pagination pagination-sm'"
                            :page-class="'page-item'"
                            :page-link-class="'page-link'"
                            :next-class="'page-link'"
                            :prev-class="'page-link'">
                        </paginate>
						<span v-if="0<zaiko.total_cnt">{{zaiko.page_num}}/{{zaiko.page_cnt}}({{zaiko.total_cnt}}件)</span>
                    </form>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl2 table table-sm table-bordered table-hover table-min">
                    <thead>
                        <tr class="autorow">
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 0.5%"></th>
                        </tr>
                        <tr class="thead-light">
                            <th rowspan="2">ID</th>
                            <th rowspan="2">事業所</th>
                            <th rowspan="2">在庫区分</th>
                            <th colspan="3">場所</th>
                            <th rowspan="2">品目<br/>コード</th>
                            <th colspan="3">型式</th>
                            <th rowspan="2">名称</th>
                            <th rowspan="2">メーカ</th>
                            <th rowspan="2">製造年月</th>
                            <th rowspan="2">備考</th>
                            <th colspan="3">数量</th>
                            <th rowspan="2">操作</th>
                        </tr>
                        <tr class="thead-light">
                            <th>場所名</th>
                            <th>場所No.</th>
                            <th>棚No.</th>
                            <th>型式</th>
                            <th>Ｖer</th>
                            <th>種別</th>
                            <th>在庫</th>
                            <th>申請中</th>
                            <th>貸出中</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rec in zaiko.items" v-bind:key="rec.id" v-on:dblclick="zaiko_click(rec)">
                            <td class='text-center'>{{rec.id}}</td>
                            <td>{{rec.jigyosyo_name}}</td>
                            <td><h5><span class="badge" :class="[{ 'badge-primary': rec.scaw_flg==0 }, { 'badge-warning': rec.scaw_flg==1 }]">{{rec.scaw_flg_str}}</span></h5></td>
                            <td>{{rec.basho}}</td>
                            <td>{{rec.basho_no}}</td>
                            <td>{{rec.basho_tana}}</td>
                            <td>{{rec.hinmoku_id}}</td>
                            <td>{{rec.hinmoku != null ? rec.hinmoku.model : null}}</td>
                            <td>{{rec.model_v}}</td>
                            <td>{{rec.model_kind}}</td>
                            <td>{{rec.hinmoku != null ? rec.hinmoku.name : null}}</td>
                            <td>{{rec.hinmoku != null ? rec.hinmoku.maker : null}}</td>
                            <td>{{rec.seizo_date}}</td>
                            <td>{{rec.biko}}</td>
                            <td class='text-right'>{{rec.zaiko_suu}}</td>
                            <td class='text-right' v-bind:class="{'bg-warning':(0<rec.sinsei_suu)}">{{rec.sinsei_suu}}</td>
                            <td class='text-right' v-bind:class="{'bg-warning':(0<rec.kashi_suu)}">{{rec.kashi_suu}}</td>
                            <td>
                                <div class='text-center'>
                                    <button :disabled="!editable1" type="button" class="btn btn-primary btn-sm" v-on:click="zaiko_click(rec)"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
}
.pagination{
    margin-bottom: 0px;
}
.form-control-el-cascader{
    padding: 0px;
    width: 400px;
    border: none;
}

.table-min{
min-width: 1200px;
}

</style>

<script>
import moment from 'moment';
import {cmn_mixin} from '../../components/cmn_mixin.js';
import {edit_mixin} from "./edit_mixin.js";
export default {
    mixins: [cmn_mixin, edit_mixin],
    data() {
		return {
            zaiko:{
                total_cnt: 0,
				page_cnt: 0,
				page_num: 0,
                items: [],
            },
            ing: false,
            search: this.initialSearch(),
		}
    },
    computed:{
        save_enabled(){
            var t = this;
            return 0<t.item.items.length && t.item.status<10;
        },
        getPageCount: function() {
            var t = this;
            return Math.ceil(t.zaiko.items.length / this.parPage);
        },
        editable1(){
            var t = this;
            return t.item.status < 10;
        },
    },
	mounted() {
        var t = this;
        try{
            var ret = JSON.parse(localStorage.getItem("shukko.edit.search"));
            Object.assign(t.search, ret);
        }catch{
        }
        t.search_func(1);
    },
    methods: {
        initialSearch: function() {
            return {
                sort: 0,
                scaw_flg: -1,
                hinmoku_id: null,
                model: null,
                name: null,
                maker: null,
                biko: null,
                tana: [],
            }
        },
        mounted_complete(){
        },
        zaiko_page: function (pageNum) {
            var t = this;
            t.search_func(pageNum);
        },
        list(){
            router.push({ name: 'shukko.list' });
        },
        save(type){
            var t = this;
            if(!t.chk_data(1)){
                return;
            }
            t.$dialog.confirm({
                title: '確認',
                body: {"-1":"ひとつ前に戻してもよろしいですか？", "0":"保存してもよろしいですか？", "1":"申請してもよろしいですか？"}[type],
            },{
                okText: 'はい',
                cancelText: 'キャンセル',
            }).then(function() {
                if(type==1){
                    t.item.command = 1;
                }else{
                    t.item.command = 0;
                }
                t.item.items.forEach(r => {
                    r.sinsei_date = t.item.sinsei_date;
                    r.shukko_date = t.item.shukko_date;
                });
                $.ajax({
                    type : "POST",
                    url : root_path + "shukko/AjaxPutShukko",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        shukko: t.item,
                    })
                }).done(function(datas) {
                    t.item.id = datas.item.id;
                    t.list();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                });
            });
        },
        search_func(pageNum){
			var t = this;
			t.zaiko.items = null;
			if(t.ing){
				return;
            }
            localStorage.setItem("shukko.edit.search", JSON.stringify(t.search));
			t.ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "shukko/AjaxGetZaikos",
                dataType : "json",
                contentType : 'application/json',
                async: true,
                data : JSON.stringify({
                    search: t.search,
                    pageNum: pageNum,
                })
            }).done(function(datas) {
				t.ing = false;
                if(datas.items!=null){
                    t.zaiko.total_cnt = datas.total_cnt;
                    t.zaiko.page_cnt = datas.page_cnt;
                    t.zaiko.page_num = datas.page_num;
                    t.zaiko.items = datas.items;
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
				t.ing = false;
			});
        },
        search_clear(){
			var t = this;
            Object.assign(t.search, t.initialSearch());
		},
		zaiko_click(zaiko){
            var hit = false;
            var t = this;
            t.item.items.forEach(r => {
                if(r.zaiko.id == zaiko.id){
                    ++r.req_suu;
                    hit = true;
                }
            });
            if(!hit){
                t.item.items.push({
                    zaiko: zaiko,
                    req_suu: 1,
                    biko: null,
                });
            }
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
	},
}
</script>
