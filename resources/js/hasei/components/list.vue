<template>
<div>
    <div class="card">
        <loading :active.sync="search_ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">発生品入庫一覧</div>
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                        <form class="form-inline">
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
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">ソート</div>
                                </div>
                                <select v-model="search.sort" class="form-control">
                                    <option value="0">No. 昇順</option>
                                    <option value="1">No. 降順</option>
                                    <option value="2">予定工場完 昇順</option>
                                    <option value="3">予定工場完 降順</option>
                                    <option value="4">状態 昇順</option>
                                    <option value="5">状態 降順</option>
                                </select>
                            </div>
                            <div class="input-group btn-group-sm mr-1">
                                <haseicheck :stsnum="0" id="stschk00" v-model="search.stschk00"/>
                                <haseicheck :stsnum="10" id="stschk10" v-model="search.stschk10"/>
                                <haseicheck :stsnum="20" id="stschk20" v-model="search.stschk20"/>
                                <haseicheck :stsnum="70" id="stschk70" v-model="search.stschk70"/>
                                <haseicheck :stsnum="99" id="stschk99" v-model="search.stschk99"/>
                                <button type="button" class="btn btn-primary" v-on:click="sts_all(false)">全解除</button>
                                <button type="button" class="btn btn-primary" v-on:click="sts_all(true)">全選択</button>
                            </div>
                            <div class="btn-group btn-group-sm mr-2" role="group">
                                <button type="button" class="btn btn-primary" v-on:click="search_func(1)">検索</button>
                                <button type="button" class="btn btn-primary" v-on:click="search_clear()">クリア</button>
                                <button type="button" class="btn btn-primary" v-on:click="edit(0)" v-if="!IsMobile">新規</button>
                                <button type="button" class="btn btn-primary" v-on:click="download()">XLS</button>
                            </div>
                        </form>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <form class="form-inline">
                    <paginate
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
                            <th>入庫日</th>
                            <th>品目コード</th>
                            <th>品名</th>
                            <th>型式(Ver,種別)</th>
                            <th>数量</th>
                            <th>入庫先</th>
                            <th>担当</th>
                            <th>承認状況</th>
                            <th v-if="!IsMobile">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="rec in items">
                            <tr v-for="(r, r_idx) in rec.items" v-bind:key="r.id" v-on:dblclick="edit(rec.id)">
                                <td v-if="r_idx==0" :rowspan="rec.items.length"><a href="#" v-on:click="edit(rec.id)">{{rec.id}}</a></td>
                                <td v-if="r_idx==0" :rowspan="rec.items.length">{{$options.filters.YYYYMMDD(rec.status10_date)}}</td>
                                <template v-if="r.zaiko != null">
                                    <td>{{r.zaiko.hinmoku.id}}</td>
                                    <td>{{r.zaiko.hinmoku.name}}</td>
                                    <td>{{r.zaiko.hinmoku.model}} {{r.zaiko.model_v}} {{r.zaiko.model_kind}}</td>
                                    <td>{{r.suu | addComma}}</td>
                                    <td><a :href="get_zeiko_url(r.zaiko.id)" target="_blank">{{r.zaiko.tana_str}}</a></td>
                                </template>
                                <template v-else>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{r.suu | addComma}}</td>
                                    <td></td>
                                </template>
                                <td v-if="r_idx==0" :rowspan="rec.items.length">{{rec.status10_user_name}}</td>
                                <td v-if="r_idx==0" :rowspan="rec.items.length"><h5><haseistatus :stsnum="rec.status" /></h5></td>
                                <td v-if="r_idx==0 && !IsMobile" :rowspan="rec.items.length">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="edit(rec.id)">編集</button>
                                    </div>
                                </td>
                            </tr>
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
    min-width: 1200px;
    td{
        vertical-align: middle;
        text-align: center;
    }
}
</style>

<script>
import * as cmn from '../../components/common.js';
import {cmn_mixin} from '../../components/cmn_mixin.js';
function initialSearch() {
    return {
        sort: 1,
        bumon_id: 0,
        user_id: 0,
        seiban: null,
        date2_str: null,
        stschk00: true,
        stschk10: true,
        stschk20: true,
        stschk30: false,
        stschk40: false,
        stschk50: false,
        stschk60: false,
        stschk70: false,
        stschk99: false,
    }
}
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
            search: initialSearch(),
            items: [],
            total_cnt: 0,
            page_cnt: 0,
            page_num: 0,
		}
    },
    mounted() {
        var t = this;
        try{
            var ret = JSON.parse(localStorage.getItem("hasei.list.search"));
            Object.assign(t.search, ret);
        }catch{
        }
        t.search_users();
        t.search_func(1);
    },
    methods: {
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
            Object.assign(t.search, initialSearch());
            t.items = [];
        },
        search_func(pageNum){
			var t = this;
            localStorage.setItem("hasei.list.search", JSON.stringify(t.search));
			if(t.search_ing){
				return;
			}
			t.search_ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "hasei/AjaxGetHaseis",
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
            localStorage.setItem("hasei.list.search", JSON.stringify(t.search));
			if(t.search_ing){
				return;
			}
			t.search_ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "hasei/AjaxGetHaseis",
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
                    download: "発生品入庫一覧.xlsx"
                }).appendTo(document.body)[0].click();
            }).fail(function(jqXHR, textStatus, errorThrown) {
				t.search_ing = false;
			});
        },
        edit(id){
            router.push({ name: 'hasei.edit', params: { id }});
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
