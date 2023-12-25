
<template>

<div>
    <div class="card">
        <loading :active.sync="search_ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">倉庫移動伝票一覧</div>
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <div class="form-inline">
                        <div class="input-group input-group-sm col-md-1 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">No.</div>
                            </div>
                            <input type="number" class="form-control" placeholder="000000" v-model="search.id">
                        </div>
                        <div class="input-group input-group-sm col-md-2 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">関連</div>
                            </div>
                            <select v-model.number="search.parent_kind" class="form-control p-0">
                                <option value="0">全て</option>
                                <option value="1">情報連絡票</option>
                                <option value="2">部品購入依頼</option>
                            </select>
                            <input type="number" v-bind:disabled="!(0<search.parent_kind)" class="form-control text-right" placeholder="ID" v-model.number="search.parent_id" min="1">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" v-on:click="kanren_open()" v-bind:disabled="!(0<search.parent_kind && 0<search.parent_id)">開く</button>
                            </div>
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">所属</div>
                            </div>
                            <select v-model="search.bumon_id" v-on:change="bumon_change" class="form-control">
                                <option value="0">全て</option>
                                <option v-for="item in sys.bumons" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">担当者</div>
                            </div>
                            <loading :active.sync="user_search_ing" :can-cancel="false" :is-full-page="false" :width="20" :height="20"></loading>
                            <select v-model="search.user_id" class="form-control">
                                <option value="0">全て</option>
                                <option v-for="item in users" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
                            </select>
                        </div>
                        <stsdatesel
                            title="日付"
                            :names="['発行日','入庫/出庫日']"
                            :sel="search.date_sel" v-on:input_sel="search.date_sel = $event"
                            :date1="search.date1_str" v-on:input_date1="search.date1_str = $event"
                            :date2="search.date2_str" v-on:input_date2="search.date2_str = $event"
                            >
                        </stsdatesel>
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
                                <option value="0">No. 昇順</option>
                                <option value="1">No. 降順</option>
                                <option value="6">申請日 昇順</option>
                                <option value="7">申請日 降順</option>
                                <option value="2">入庫・出庫日 昇順</option>
                                <option value="3">入庫・出庫日 降順</option>
                                <option value="4">状態 昇順</option>
                                <option value="5">状態 降順</option>
                            </select>
                        </div>
                        <div class="input-group btn-group-sm mr-1">
                            <zidocheck :stsnum="0" id="stschk00" v-model="search.stschk00"/>
                            <zidocheck :stsnum="10" id="stschk10" v-model="search.stschk10"/>
                            <zidocheck :stsnum="20" id="stschk20" v-model="search.stschk20"/>
                            <zidocheck :stsnum="30" id="stschk30" v-model="search.stschk30"/>
                            <zidocheck :stsnum="70" id="stschk70" v-model="search.stschk70"/>
                            <zidocheck :stsnum="99" id="stschk99" v-model="search.stschk99"/>
                            <button type="button" class="btn btn-primary" v-on:click="sts_all(false)">全解除</button>
                            <button type="button" class="btn btn-primary" v-on:click="sts_all(true)">全選択</button>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-primary" v-on:click="search_func(1)">検索</button>
                            <button type="button" class="btn btn-primary" v-on:click="search_clear()">クリア</button>
                            <button type="button" class="btn btn-primary" v-on:click="edit(0)" v-if="!IsMobile">新規</button>
                            <button type="button" class="btn btn-primary" v-on:click="download()">XLS</button>
                        </div>
                    </div>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <form class="form-inline">
                    <paginate
                        class="ml-2"
                        :page-count="page_cnt"
                        :page-range="3"
                        :margin-pages="2"
                        :click-handler="list_page"
                        :prev-text="'＜'"
                        :next-text="'＞'"
                        :container-class="'pagination pagination-sm'"
                        :page-class="'page-item'"
                        :page-link-class="'page-link'"
                        :next-class="'page-link'"
                        :prev-class="'page-link'">
                    </paginate>
                    <span v-if="0<total_cnt">{{page_num}}/{{page_cnt}}({{total_cnt}}件)</span>
                </form>
                <table class="table table-sm table-bordered table-hover table-min">
                    <thead>
                        <tr class="thead-light">
                            <th>No.</th>
                            <th>発行日<br>(申請日)</th>
                            <th>入庫<br>出庫日</th>
                            <th>社員</th>
                            <th v-if="!IsMobile">責任部署</th>
                            <th>製番</th>
                            <th>品番</th>
                            <th>品名</th>
                            <th>型式</th>
                            <th>数量</th>
                            <th>出庫理由</th>
                            <th>入庫理由</th>
                            <th v-if="!IsMobile">備考</th>
                            <th>承認状況</th>
                            <th v-if="!IsMobile">関連</th>
                            <th v-if="!IsMobile">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="rec in items">
                            <template v-if="0<rec.items.length">
                                <tr v-for="(r, r_idx) in rec.items" v-bind:key="r.id" v-on:dblclick="edit(rec.id)">
                                    <td v-if="r_idx==0" :rowspan="rec.items.length"><a href="#" v-on:click="edit(rec.id)">{{rec.id}}</a></td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length">{{$options.filters.YYYYMMDD(rec.status10_date)}}</td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length">{{$options.filters.YYYYMMDD(rec.inout_date)}}</td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length">{{rec.status10_user_id}}<br>{{rec.status10_user_name}}</td>
                                    <td v-if="r_idx==0 && !IsMobile" :rowspan="rec.items.length">{{rec.bumon_name}}</td>
                                    <td>{{r.seiban}}</td>
                                    <td>{{r.hinmoku_id}}</td>
                                    <td>{{r.hinmoku ? r.hinmoku.name : null}}</td>
                                    <td>{{r.hinmoku ? r.hinmoku.model : null}}</td>
                                    <td class="text-right">{{r.suu | addComma}}</td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length">{{rec.out_riyuu}}</td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length">{{rec.in_riyuu}}</td>
                                    <td v-if="r_idx==0 && !IsMobile" :rowspan="rec.items.length">{{rec.biko}}</td>
                                    <td v-if="r_idx==0" :rowspan="rec.items.length"><h5><zidostatus :stsnum="rec.status" /></h5></td>
                                    <td v-if="r_idx==0 && !IsMobile" :rowspan="rec.items.length"><a v-if="0<rec.parent_kind" :href="rec.parent_url">{{rec.parent_str}}</a></td>
                                    <td v-if="r_idx==0 && !IsMobile" :rowspan="rec.items.length">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="edit(rec.id)">編集</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-bind:key="rec.id" v-on:dblclick="edit(rec.id)">
                                    <td>{{rec.id}}</td>
                                    <td>{{$options.filters.YYYYMMDD(rec.status10_date)}}</td>
                                    <td>{{$options.filters.YYYYMMDD(rec.inout_date)}}</td>
                                    <td>{{rec.status10_user_id}}<br>{{rec.status10_user_name}}</td>
                                    <td>{{rec.bumon_name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td>{{rec.out_riyuu}}</td>
                                    <td>{{rec.in_riyuu}}</td>
                                    <td>{{rec.biko}}</td>
                                    <td><h5><zidostatus :stsnum="rec.status" /></h5></td>
                                    <td>
                                        <a v-if="0<rec.parent_kind" :href="rec.parent_url">{{rec.parent_str}}</a>
                                    </td>
                                    <td v-if="!IsMobile">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="edit(rec.id)">編集</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped lang="scss">
.pagination{
    margin-bottom: 0px;
}
.table-min{
    min-width: 1000px;
    td{
        vertical-align: middle;
        text-align: center;
    }
}
</style>

<script>
import * as cmn from '../../components/common.js';
import {cmn_mixin} from '../../components/cmn_mixin.js';
export default {
    mixins: [cmn_mixin],
    props: {
        sys: null,
	},
    data() {
		return {
            users: [],
            search_ing: false,
            user_search_ing: false,
            search: this.initialSearch(),
            items: [],
            total_cnt: 0,
            page_cnt: 0,
            page_num: 0,
		}
    },
    mounted() {
        var t = this;
        if('parent_kind' in t.$route.params && 'parent_id' in t.$route.params){
            t.search_clear();
            t.search.parent_kind = t.$route.params.parent_kind;
            t.search.parent_id = t.$route.params.parent_id;
            t.sts_all(true);
        }else{
            try{
                var ret = JSON.parse(localStorage.getItem("zido.list.search"));
                Object.assign(t.search, ret);
            }catch{
            }
        }
        t.search_users();
        t.search_func(1);
    },
    computed:{
    },
    methods: {
        initialSearch: function() {
            return {
                sort: 1,
                id: null,
                bumon_id: 0,
                user_id: 0,
                seiban: null,
                date_sel: 0,
                date1_str: null,
                date2_str: null,
                biko: null,
                stschk00: true,
                stschk10: true,
                stschk20: true,
                stschk30: true,
                stschk40: true,
                stschk50: false,
                stschk60: false,
                stschk70: false,
                stschk99: false,
                parent_kind: 0,
                parent_id: null,
            }
        },
        kanren_open: function(){
            var t = this;
            //localStorage.setItem("zido.list.search", JSON.stringify(t.search));
            switch (t.search.parent_kind) {
                case 1:
                    //window.location.href = `${root_path}joren/edit/${t.search.parent_id}`;
                    window.open(`${root_path}joren/edit/${t.search.parent_id}`, '_blank');
                    break;
                case 2:
                    //window.location.href = `${root_path}konyu/edit/${t.search.parent_id}`;
                    window.open(`${root_path}konyu/edit/${t.search.parent_id}`, '_blank');
                    break;
            }
        },
        list_page: function (pageNum) {
            var t = this;
            t.search_func(pageNum);
        },
		bumon_change(evnt) {
            var t = this;
            t.search.user_id = 0;
            t.search_users();
		},
		search_users() {
            var t = this;
            t.users = [];
            if(0<t.search.bumon_id){
                t.user_search_ing = true;
                $.ajax({
                    type : "POST",
                    url : root_path + "ajax/AjaxGetUsers",
                    dataType : "json",
                    contentType : 'application/json',
                    data : JSON.stringify({
                        bumon_id: t.search.bumon_id,
                    })
                }).done(function(datas) {
                    t.users = datas.items;
                    t.user_search_ing = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.user_search_ing = false;
                });
            }
        },
        search_clear(){
			var t = this;
            Object.assign(t.search, t.initialSearch());
            t.items = [];
        },
        search_func(pageNum){
			var t = this;
            localStorage.setItem("zido.list.search", JSON.stringify(t.search));
			if(t.search_ing){
				return;
			}
			t.search_ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "zido/AjaxGetZidos",
                dataType : "json",
                contentType : 'application/json',
                data : JSON.stringify({
                    search: t.search,
                    pageNum: pageNum,
                })
            }).done(function(datas) {
				t.search_ing = false;
                if(datas.items!=null){
                    t.total_cnt = datas.total_cnt;
                    t.page_cnt = datas.page_cnt;
                    t.page_num = datas.page_num;
                    t.items = datas.items;
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
				t.search_ing = false;
			});
        },
        download(){
			var t = this;
            localStorage.setItem("zido.list.search", JSON.stringify(t.search));
			if(t.search_ing){
				return;
			}
			t.search_ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "zido/AjaxGetZidos",
                xhrFields: { responseType: 'blob' },
                contentType : 'application/json',
                data : JSON.stringify({
                    search: t.search,
                    pageNum: 1,
                    type: 'excel',
                }),
            }).done(function(response, _textStatus, _jqXHR) {
				t.search_ing = false;
                $('<a>', {
                href: URL.createObjectURL(new Blob([response], { type: response.type })),
                    download: "倉庫移動伝票一覧.xlsx"
                }).appendTo(document.body)[0].click();
            }).fail(function(jqXHR, textStatus, errorThrown) {
				t.search_ing = false;
			});
        },
        edit(id){
            router.push({ name: 'zido.edit', params: { id }});
        },
        sts_all(val){
            this.search.stschk00 = val;
            this.search.stschk10 = val;
            this.search.stschk20 = val;
            this.search.stschk30 = val;
            this.search.stschk40 = val;
            this.search.stschk50 = val;
            this.search.stschk60 = val;
            this.search.stschk70 = val;
            this.search.stschk99 = val;
        }
	},
}
</script>
