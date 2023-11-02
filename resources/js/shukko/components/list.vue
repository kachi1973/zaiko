<template>
<div>
    <div class="card">
        <loading :active.sync="search_ing" :can-cancel="false" :is-full-page="false"></loading>
        <div class="card-header bg-secondary">部品出庫一覧</div>
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
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">在庫場所</div>
                            </div>
                            <select v-model="search.jigyosyo_id" class="form-control">
                                <option value="0">全て</option>
                                <option v-for="item in sys.jigyosyos" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">所属</div>
                            </div>
                            <select v-model="search.bumon_id" v-on:change="bumon_change" class="form-control">
                                <option value="0">全て</option>
                                <option v-for="item in bumons" :value="item.id" v-bind:key="item.id">{{item.name}}</option>
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
                                <div class="input-group-text">受注番号</div>
                            </div>
                            <input type="text" class="form-control" placeholder="00-00000" v-model="search.seiban">
                        </div>
                        <stsdatesel
                            title="日付"
                            :names="['出庫希望日','申請日','出庫日','返却日','返却承認日','製番投入日','課長承認日','完了日']"
                            :sel="search.date_sel" v-on:input_sel="search.date_sel = $event"
                            :date1="search.date1_str" v-on:input_date1="search.date1_str = $event"
                            :date2="search.date2_str" v-on:input_date2="search.date2_str = $event"
                            >
                        </stsdatesel>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">ソート</div>
                            </div>
                            <select v-model="search.sort" class="form-control">
                                <option value="0">No.昇順</option>
                                <option value="1">No.降順</option>
                                <option value="2">申請日,出庫希望日 昇順</option>
                                <option value="3">申請日,出庫希望日 降順</option>
                                <option value="4">出庫希望日 昇順</option>
                                <option value="5">出庫希望日 降順</option>
                                <option value="6">出庫日 昇順</option>
                                <option value="7">出庫日 降順</option>
                                <option value="8">返却日,出庫日 昇順</option>
                                <option value="9">返却日,出庫日 降順</option>
                                <option value="10">返却承認日,出庫日 昇順</option>
                                <option value="11">返却承認日,出庫日 降順</option>
                                <option value="12">完了日 昇順</option>
                                <option value="13">完了日 降順</option>
                                <option value="14">課長承認日 昇順</option>
                                <option value="15">課長承認日 降順</option>
                                <option value="16">受注番号 昇順</option>
                                <option value="17">受注番号 降順</option>
                                <option value="18">所属 昇順</option>
                                <option value="19">所属 降順</option>
                                <option value="20">担当者 昇順</option>
                                <option value="21">担当者 降順</option>
                                <option value="22">状況 昇順</option>
                                <option value="23">状況 降順</option>
                            </select>
                        </div>
                        <div class="input-group btn-group-sm mr-1">
                            <shukkocheck :stsnum="0" id="stschk00" v-model="search.stschk00"/>
                            <shukkocheck :stsnum="10" id="stschk10" v-model="search.stschk10"/>
                            <shukkocheck :stsnum="20" id="stschk20" v-model="search.stschk20"/>
                            <shukkocheck :stsnum="30" id="stschk30" v-model="search.stschk30"/>
                            <shukkocheck :stsnum="40" id="stschk40" v-model="search.stschk40"/>
                            <shukkocheck :stsnum="50" id="stschk50" v-model="search.stschk50"/>
                            <shukkocheck :stsnum="70" id="stschk70" v-model="search.stschk70"/>
                            <shukkocheck :stsnum="99" id="stschk99" v-model="search.stschk99"/>
                            <button type="button" class="btn btn-primary" v-on:click="sts_all(false)">全解除</button>
                            <button type="button" class="btn btn-primary" v-on:click="sts_all(true)">全選択</button>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-primary" v-on:click="search_func(1)">検索</button>
                            <button type="button" class="btn btn-primary" v-on:click="search_pattern(0)">部品出庫検索</button>
                            <button type="button" class="btn btn-primary" v-on:click="search_pattern(1)">部品入庫検索</button>
                            <button type="button" class="btn btn-primary" v-on:click="search_pattern(2)">製番投入検索</button>
                            <button type="button" class="btn btn-primary" v-on:click="search_clear()">クリア</button>
                            <button type="button" class="btn btn-primary" v-on:click="edit(0)" v-if="!IsMobile">新規</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card col-xl-8 p-0 m-0">
                <div class="card-header text-white bg-primary btn p-0" @click="IsJigyosyoVisible=!IsJigyosyoVisible">
                    拠点毎の件数
                    <i class="fa-solid fa-chevron-up float-right" v-if="IsJigyosyoVisible"></i>
                    <i class="fa-solid fa-chevron-down float-right" v-if="!IsJigyosyoVisible"></i>
                </div>
                <div class="card-body p-0" v-if="IsJigyosyoVisible">
                    <loading :active.sync="IsJigyosyoVisibleIng" :can-cancel="false" :is-full-page="false"></loading>
                    <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr class="thead-light">
                            <th class="status2 p-0 text-center" style="width:3%">事業所</th>
                            <th class="status00 status2 p-0 text-center" style="width:2%">出庫申請待</th>
                            <th class="status10 status2 p-0 text-center" style="width:2%">出庫待</th>
                            <th class="status20 status2 p-0 text-center" style="width:2%">返却待</th>
                            <th class="status30 status2 p-0 text-center" style="width:2%">返却承認待</th>
                            <th class="status40 status2 p-0 text-center" style="width:2%">製番待</th>
                            <th class="status50 status2 p-0 text-center" style="width:2%">課長承認待</th>
                            <th class="status70 status2 p-0 text-center" style="width:2%">完了</th>
                            <th class="status99 status2 p-0 text-center" style="width:2%">キャンセル</th>
                            <th class="status_base status2 p-0 text-center" style="width:2%">全て</th>
                            <th class="status_base status2 p-0 text-center" style="width:2%">本日出庫</th>
                            <th class="status_base status2 p-0 text-center" style="width:2%">出庫日経過</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in jigyosyos" v-bind:key="item.id">
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, -2)">{{ item.name }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 0)">{{ item.shukko00_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 10)">{{ item.shukko10_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 20)">{{ item.shukko20_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 30)">{{ item.shukko30_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 40)">{{ item.shukko40_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 50)">{{ item.shukko50_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 70)">{{ item.shukko70_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, 99)">{{ item.shukko99_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, -1)">{{ item.shukko_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, -3)">{{ item.shukko_date1_cnt }}</a></td>
                            <td scope="row" class="p-0 text-right"><a href="#" @click="StatusListClick(item.id, -4)">{{ item.shukko_date2_cnt }}</a></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <div class="form-inline">
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
                </div>
                <table class="table table-sm table-bordered table-hover table-min">
                    <thead>
                        <tr class="thead-light">
                            <th class="p-0">No.</th>
                            <th class="p-0">事業所</th>
                            <th class="p-0">受注番号</th>
                            <th class="p-0">申請日</th>
                            <th class="p-0" v-if="!IsMobile">出庫希望日</th>
                            <th class="p-0">出庫日</th>
                            <th class="p-0">返却日</th>
                            <th class="p-0" v-if="!IsMobile">返却承認日</th>
                            <th class="p-0" v-if="!IsMobile">製番投入日</th>
                            <th class="p-0" v-if="!IsMobile">課長承認日</th>
                            <th class="p-0">完了日</th>
                            <th class="p-0" v-if="!IsMobile">所属</th>
                            <th class="p-0" v-if="!IsMobile">担当者</th>
                            <th class="p-0">状況</th>
                            <th class="p-0">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rec in items" v-bind:key="rec.id" v-on:dblclick="desc(rec.id)">
                            <td class="p-0"><a href="#" v-on:click="desc(rec.id)">{{rec.id}}</a></td>
                            <td class="p-0">{{rec.jigyosyo_name}}</td>
                            <td class="p-0">{{rec.seiban}}</td>
                            <td class="p-0">{{rec.status10_date | YYYYMMDD}}<br>{{rec.status10_user_name}}</td>
                            <td class="p-0" v-if="!IsMobile">{{rec.shukko_date | YYYYMMDD}}</td>
                            <td class="p-0">{{rec.status20_date | YYYYMMDD}}<br>{{rec.status20_user_name}}</td>
                            <td class="p-0">{{rec.status30_date | YYYYMMDD}}<br>{{rec.status30_user_name}}</td>
                            <td class="p-0" v-if="!IsMobile">{{rec.status40_date | YYYYMMDD}}<br>{{rec.status40_user_name}}<br></td>
                            <td class="p-0" v-if="!IsMobile">{{rec.status50_date | YYYYMMDD}}<br>{{rec.status50_user_name}}</td>
                            <td class="p-0" v-if="!IsMobile">{{rec.status60_date | YYYYMMDD}}<br>{{rec.status60_user_name}}</td>
                            <td class="p-0">{{rec.status70_date | YYYYMMDD}}<br>{{rec.status70_user_name}}</td>
                            <td class="p-0" v-if="!IsMobile">{{rec.bumon_name}}</td>
                            <td class="p-0" v-if="!IsMobile">{{rec.user_name}}</td>
                            <td class="p-0"><h5><shukkostatus :stsnum="rec.status" /></h5></td>
                            <td class="p-0">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="edit(rec.id)" v-if="!IsMobile">編集</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="desc(rec.id)" v-if="!IsMobile">詳細</button>
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
.pagination{
    margin-bottom: 0px;
}
.btn{
    min-width: 60px;
}
.table-min {
    min-width: 800px;
    td{
        vertical-align: middle;
        text-align: center;
    }
}
.status2{
    min-width: unset;
    border: 1px solid #dee2e6 !important;
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
            users: [],
            search_ing: false,
            user_search_ing: false,
            search: this.initialSearch(),
            items: [],
            total_cnt: 0,
            page_cnt: 0,
            page_num: 0,
            IsJigyosyoVisible: false,
            IsJigyosyoVisibleIng: false,
            jigyosyos: null,
		}
    },
    computed: {
        bumons(){
            var t = this;
            if(t.search.jigyosyo_id<1){
                return t.sys.bumons;
            }else{
                var a = t.sys.bumons.filter(bumon => bumon.jigyosyo_id == t.search.jigyosyo_id);
                return t.sys.bumons.filter(bumon => bumon.jigyosyo_id == t.search.jigyosyo_id);
            }
        },
    },
    watch: {
        IsJigyosyoVisible(){
            var t = this;
            if(t.IsJigyosyoVisible){
                t.IsJigyosyoVisibleIng = true;
                $.ajax({
                    type : "POST",
                    url : root_path + "ajax/AjaxJigyosyos",
                    dataType : "json",
                    contentType : 'application/json',
                    data : JSON.stringify({})
                }).done(function(datas) {
                    t.jigyosyos = datas.items;
                    t.IsJigyosyoVisibleIng = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.IsJigyosyoVisibleIng = false;
                });
            }
        },
    },
    mounted() {
        var t = this;
        t.search_users();
        if(t.$route.params.type){
            t.search_pattern(Number(t.$route.params.type));
        }else{
            try{
                var ret = JSON.parse(localStorage.getItem("shukko.list.search"));
                Object.assign(t.search, ret);
            }catch{
            }
            t.search_func(1);
        }
    },
    methods: {
        initialSearch: function() {
            return {
                sort: 1,
                id: null,
                jigyosyo_id: 0,
                bumon_id: 0,
                user_id: 0,
                seiban: null,
                date_sel: 0,
                date1_str: null,
                date2_str: null,
                stschk00: true,
                stschk10: true,
                stschk20: true,
                stschk30: true,
                stschk40: true,
                stschk50: true,
                stschk60: false,
                stschk70: false,
                stschk99: false,
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
//                    async: true,
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
            localStorage.setItem("shukko.list.search", JSON.stringify(t.search));
			if(t.search_ing){
				return;
			}
			t.search_ing = true;
            $.ajax({
                type : "POST",
                url : root_path + "shukko/AjaxGetShukkos",
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
        edit(id){
            router.push({ name: 'shukko.edit', params: { id }});
        },
        desc(id){
            router.push({ name: 'shukko.desc', params: { id }});
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
        },
        search_pattern(type){
			var t = this;
            switch(type){
            case 0:
                t.search.sort = 2;
                t.sts_all(false);
                this.search.stschk10 = true;
                t.search_func(1);
                break;
            case 1:
                t.search.sort = 8;
                t.sts_all(false);
                this.search.stschk30 = true;
                t.search_func(1);
                break;
            case 2:
                t.search.sort = 10;
                t.sts_all(false);
                this.search.stschk40 = true;
                t.search_func(1);
                break;
            case 3:
                t.StatusListClick(t.sys.user.jigyosyo.id, -3);
                break;
            case 4:
                t.StatusListClick(t.sys.user.jigyosyo.id, -4);
                break;
            }
        },
        StatusListClick(id, sts){
            var t = this;
            t.search_clear();
            t.search.jigyosyo_id = id;
            switch(sts){
            case -1: // 全て
                t.sts_all(true);
                break;
            case -2: // 完了、中止以外
                break;
            case -3: // 本日出庫
                t.sts_all(false);
                this.search.stschk10 = true;
                t.search.date_sel = 0;
                t.search.date1_str = moment().format('YYYY/MM/DD');
                t.search.date2_str = moment().format('YYYY/MM/DD');
                t.search.sort = 4;
                break;
            case -4: // 出庫日経過
                t.sts_all(false);
                this.search.stschk10 = true;
                t.search.date_sel = 0;
                t.search.date1_str = null;
                t.search.date2_str = moment().format('YYYY/MM/DD');
                t.search.sort = 4;
                break;
            case 0:
                t.sts_all(false);
                this.search.stschk00 = true;
                t.search.sort = 1;
                break;
            case 10:
                t.sts_all(false);
                this.search.stschk10 = true;
                t.search.sort = 2;
                break;
            case 20:
                t.sts_all(false);
                this.search.stschk20 = true;
                t.search.sort = 6;
                break;
            case 30:
                t.sts_all(false);
                this.search.stschk30 = true;
                t.search.sort = 8;
                break;
            case 40:
                t.sts_all(false);
                this.search.stschk40 = true;
                t.search.sort = 10;
                break;
            case 50:
                t.sts_all(false);
                this.search.stschk50 = true;
                t.search.sort = 12;
                break;
            case 70:
                t.sts_all(false);
                this.search.stschk70 = true;
                break;
            case 99:
                t.sts_all(false);
                this.search.stschk99 = true;
                break;
            }
            t.search_func(1);
        },
	},
}
</script>
