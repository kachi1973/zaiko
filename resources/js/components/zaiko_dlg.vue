<template>
    <dlg v-if="show" class="dlg" class-modal-container="modal-container-ex">
        <h5 slot="header">在庫選択</h5>
        <div slot="body">
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
                            <input type="text" class="form-control" placeholder="品目コード" v-model.trim="search.hinmoku_id">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">型式</div>
                            </div>
                            <input type="text" class="form-control" placeholder="型式" v-model.trim="search.model">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">名称</div>
                            </div>
                            <input type="text" class="form-control" placeholder="名称" v-model.trim="search.name">
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">メーカ</div>
                            </div>
                            <input type="text" class="form-control" placeholder="名称" v-model.trim="search.maker">
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
            <table class="tbl2 table table-sm table-bordered table-hover">
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
                        <th style="width: 1%"></th>
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
                    <tr v-for="rec in zaiko.items" v-bind:key="rec.id" v-on:dblclick="close(rec)">
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
                        <td class='text-center'>
                            <button type="button" class="btn btn-primary btn-sm" v-on:click="close(rec)">選択</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div slot="footer" class="btn-group btn-group-ms" role="group">
            <button class="btn btn-primary modal-default-button" @click="close(null)" type="button">キャンセル</button>
        </div>
    </dlg>
</template>

<style lang="scss">
.modal-container-ex {
    max-width: unset !important;
	width:90% !important;
	height:90% !important;
}

.el-popper{
    z-index: 10000 !important;
}
</style>
<style lang="scss" scoped>
.pagination{
    margin-bottom: 0px;
}
.btn{
    min-width: 60px;
}
.form-control-el-cascader{
    padding: 0px;
    width: 400px;
    border: none;
}
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
function initialSearch() {
    return {
        sort: 0,
        scaw_flg: -1,
        hinmoku_id: null,
        model: null,
        name: null,
        maker: null,
        perPage: 10,
        tana: [],
    }
}
export default {
	props: [
		'sys',
        'show',
        'hinmoku_id',
	],
    data() {
		return {
            zaiko:{
                total_cnt: 0,
				page_cnt: 0,
				page_num: 0,
                items: [],
            },
            ing: false,
            showModal: false,
            search: initialSearch(),
		}
    },
	watch: {
	    show: function(val){
            var t = this;
	    	t.showModal = val;
            if(t.showModal){
                t.search_clear();
                t.search.hinmoku_id = t.hinmoku_id;
                t.search_func(1);
            }
	    },
	},
    computed:{
    },
	mounted() {
        var t = this;
        /*
        try{
            var ret = JSON.parse(localStorage.getItem("zaiko_dlg.search"));
            Object.assign(t.search, ret);
        }catch{
        }
        */
    },
    methods: {
        close: function(ev){
            this.$emit("close", ev);
        },
        zaiko_page: function (pageNum) {
            var t = this;
            t.search_func(pageNum);
        },
        search_func(pageNum){
			var t = this;
			t.zaiko.items = null;
			if(t.ing){
				return;
            }
            /*
            localStorage.setItem("zaiko_dlg.search", JSON.stringify(t.search));
            */
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
            Object.assign(t.search, initialSearch());
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
	},
}
</script>
