
<template>
	<div class="col-md-10">
		<div class="card">
			<loading :active.sync="ing" :can-cancel="false" :is-full-page="false"></loading>
			<div class="card-header bg-secondary">
				発生品入庫 (発行No.[<span v-if="item.id > 0" class="badge badge-primary">{{item.id}}</span>
				<span v-else class="badge badge-primary">新規</span>
				]
			</div>
			<div class="card-body">
				<form class="form-inline">
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<div class="input-group-text">現在の状態</div>
						</div>
						<haseistatus :stsnum="item.status" class="form-control"/>
                        <button type="button" class="btn btn-primary btn-sm" v-on:click="save(-1)" :disabled="!cancel_enabled" v-if="!IsMobile">
                            <i class="fas fa-times"></i>
                        </button>
					</div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary" v-on:click="save(0)" :disabled="!item.items.length" v-if="!IsMobile">
                            保存
                        </button>
                        <haseibutton :stsnum="item.status" class="btn btn-primary" v-on:click="save(1)" :texttype="2" suffix="" v-bind:sys="sys" :disabled="!save_enabled"  v-if="!IsMobile"/>
                        <a :href="'../descprint/' + item.id" class="btn btn-primary" style="min-width: unset" target="_blank" v-if="0 < item.id">印刷</a>
                        <button type="button" class="btn btn-primary" v-on:click="list()">
                            一覧
                        </button>
                    </div>
				</form>
			</div>
			<div class="card-body">
                <div style="overflow-x:auto;">
                    <table class="table table-sm table-bordered table-hover main_table table-min">
                        <tbody>
                            <tr class="fixed_header">
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
                            <tr>
                                <th>発行No.</th>
                                <td>
                                    <span v-if="item.id > 0">{{ item.id }}</span>
                                    <span v-else>新規</span>
                                </td>
                                <th>入庫日</th>
                                <td colspan="6">{{ item.status10_date }}</td>
                            </tr>
                            <tr>
                                <th colspan="1">品目コード</th>
                                <th colspan="2">品名</th>
                                <th colspan="2">型式(Ver,種別)</th>
                                <th colspan="1">数量</th>
                                <th colspan="3">入庫先</th>
                            </tr>
                            <tr v-for="rec in item.items" v-bind:key="rec.id">
                                <template v-if="rec.zaiko">
                                    <td colspan="1">{{ rec.zaiko.hinmoku_id }}</td>
                                    <td colspan="2">
                                        {{ rec.zaiko.hinmoku.name }}
                                    </td>
                                    <td colspan="2">
                                        {{ rec.zaiko.hinmoku.model }}
                                        {{ rec.zaiko.model_v }}
                                        {{ rec.zaiko.model_kind }}
                                    </td>
                                </template>
                                <template v-else>
                                    <td colspan="1"></td>
                                    <td colspan="2"></td>
                                    <td colspan="2"></td>
                                </template>
                                <td colspan="1" class="bg-warning">
                                    <input
                                        :disabled="!edit_editable"
                                        type="number"
                                        class="form-control text-right"
                                        placeholder="数量"
                                        v-model.number="rec.suu"
                                        min="0"
                                        @change="change"
                                    />
                                </td>
                                <td colspan="3">
                                    <button
                                        :disabled="!edit_editable"
                                        type="button"
                                        class="btn btn-primary btn-sm"
                                        v-on:click="hinmoku_search(rec)"
                                    >
                                        <i class="fas fa-search"></i>
                                    </button>
                                    {{ rec.zaiko ? rec.zaiko.tana : null }}
                                </td>
                            </tr>
                            <tr>
                                <th colspan="9">備考</th>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    <textarea
                                        :disabled="!edit_editable"
                                        class="form-control"
                                        cols="20"
                                        rows="3"
                                        placeholder="備考"
                                        v-model="item.biko"
                                    ></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="3"></th>
                                <th colspan="2">所属長(部長)</th>
                                <th colspan="2">所属長(課長)</th>
                                <th colspan="2">担当者</th>
                            </tr>
                            <tr>
                                <td colspan="3" style="height: 80px"></td>
                                <td colspan="2" class="text-center">
                                    <img
                                        v-if="status70_hanko_url != null"
                                        border="0"
                                        :src="status70_hanko_url"
                                        width="64"
                                        height="64"
                                    />
                                </td>
                                <td colspan="2" class="text-center">
                                    <img
                                        v-if="status20_hanko_url != null"
                                        border="0"
                                        :src="status20_hanko_url"
                                        width="64"
                                        height="64"
                                    />
                                </td>
                                <td colspan="2" class="text-center">
                                    <img
                                        v-if="status10_hanko_url != null"
                                        border="0"
                                        :src="status10_hanko_url"
                                        width="64"
                                        height="64"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <filelist
                    title="添付資料"
                    :value="item.file_urls"
                    :id="item.id"
                    :url="url"
                />
                <div class="card mt-2">
                    <div class="card-body">
      					<h5 class="bg-light">申請</h5>
                        <p>修理品の入庫先を選択して「申請」をクリックしてください。</p>
                        <h5 class="bg-light">課長承認</h5>
                        <p>
                            内容に問題ないようでしたら「課長承認」をクリックしてください。<br>
                            部長承認も自動承認されます。<br>
                            入庫先の在庫数が追加されます。<br>
                            個別在庫の場合は在庫マスタで個別在庫IDを発行してください。<br>
                        </p>
                        <h5 class="bg-light">完了後</h5>
                        <p>
                            部品出庫にて修理(出庫)して修理完了後の入庫、または破棄(使用済み)を行ってください。
                        </p>
                    </div>
				</div>
			</div>
		</div>
		<zaiko_dlg
			:sys="sys"
			:show="showZaikoModal"
			:hinmoku_id="zaiko_dlg_id"
			@close="zaikoDlgClose"
		></zaiko_dlg>
	</div>
</template>

<style scoped lang="scss">
.main_table {
	border-collapse: collapse;
	table-layout: fixed;
	th {
		border: solid 2px black;
		background-color: #e9ecef;
		vertical-align: middle;
	}
	td {
		border: solid 2px black;
		vertical-align: middle;
	}
	.fixed_header {
		th {
			border: none;
			padding: 0px;
			margin: 0px;
			width: 16.6%;
		}
	}
}
.table_input {
	padding: 0px;
	margin: 0px;
	&.F1 {
		width: 100%;
	}
}
.pagination {
	margin-bottom: 0px;
}
</style>

<script>
import * as cmn from "../../components/common.js";
import {cmn_mixin} from '../../components/cmn_mixin.js';
export default {
    mixins: [cmn_mixin],
	props: {
		sys: null,
	},
	data() {
		return {
			url: root_path + "hasei",
			item: {
				id: null,
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
				biko: "無し",
				status: 0,
				editable: false,
				items: [],
				file_urls: [],
			},
			ing: false,
			cur_item: null,
			showZaikoModal: false,
			zaiko_dlg_id: null,
			save_enabled_change: 0, // save_enabled()で変更を検出するため
		};
	},
	computed: {
		tenpu() {
			var t = this;
			return 0 < t.item.file_urls.length ? "有り" : "無し";
		},
		status10_hanko_url() {
			var t = this;
			return t.get_hanko_url(
				t.item.status10_user_name,
				t.item.status10_date
			);
		},
		status20_hanko_url() {
			var t = this;
			return t.get_hanko_url(
				t.item.status20_user_name,
				t.item.status20_date
			);
		},
		status70_hanko_url() {
			var t = this;
			return t.get_hanko_url(
				t.item.status70_user_name,
				t.item.status70_date
			);
		},
		save_enabled() {
			var t = this;
			var a = t.save_enabled_change;
			if (!t.item.items.length) {
				return false;
			}
			if (
				t.item.items.some((rec) => {
					if (!rec.zaiko) {
						return true;
					}
					if (!rec.suu) {
						return true;
					}
					return false;
				})
			) {
				return false;
			}
			if (t.sys != null) {
				switch (t.item.status) {
					case 10: // 課長承認待ち
						return t.sys.user.kengen12;
					case 20: // 部長承認待ち
						return t.sys.user.kengen12;
					case 30:
					case 40:
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
		cancel_enabled() {
			var t = this;
			if (t.sys != null) {
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
					default:
						// 申請待ち
						return true;
				}
			}
			return false;
		},
		zido_url() {
			var t = this;
			return `${root_path}zido/list/1/${t.item.id}`;
		},
	},
	mounted() {
		var t = this;
		var hit = false;
		var now = new Date();
		if ("id" in t.$route.params) {
			t.item.id = t.$route.params.id;
			if (0 < t.item.id) {
				t.ing = true;
				hit = true;
				$.ajax({
					type: "POST",
					url: t.url + "/AjaxGetHasei",
					dataType: "json",
					contentType: "application/json",
					async: true,
					data: JSON.stringify({
						id: t.item.id,
					}),
				})
					.done(function (datas) {
						t.ing = false;
						t.set_data(datas);
					})
					.fail(function (jqXHR, textStatus, errorThrown) {
						t.ing = false;
					});
			} else {
				t.set_data(null);
			}
		}
		if (!hit) {
			t.item.user_id = user_id;
		}
		if (t.item.items.length == 0) {
			t.item_add();
		}
	},
	methods: {
		change() {
			this.save_enabled_change++;
		},
		get_hanko_url(name, date) {
			if (name != null && date != null) {
				return root_path + `ajax/GetHanko?name=${name}&date=${date}`;
			} else if (name != null) {
				return root_path + `ajax/GetHanko?name=${name}`;
			} else {
				return null;
			}
		},
		item_add() {
			var t = this;
			var new_id = t.item.id * 100;
			if (0 < t.item.items.length) {
				new_id = t.item.items[t.item.items.length - 1].id + 1;
			}
			t.item.items.push({
				id: new_id,
				hasei_id: null,
				hinmoku: {
					name: null,
					model: null,
				},
				suu: null,
				tanka: null,
			});
		},
		item_del(rec) {
			var t = this;
			t.$dialog
				.confirm(
					{
						title: "確認",
						body: "削除してもよろしいですか？",
					},
					{
						okText: "はい",
						cancelText: "キャンセル",
					}
				)
				.then(function () {
					t.item.items.splice(
						t.item.items.findIndex((i) => i.id == rec.id),
						1
					);
				});
		},
		set_data(datas) {
			var t = this;
			if (datas == null || datas.item == null) {
				t.item.id = 0;
			} else {
				Object.assign(t.item, datas.item);
				t.item.status10_date = t.$options.filters.YYYYMMDD(
					datas.item.status10_date
				);
				t.item.status20_date = t.$options.filters.YYYYMMDD(
					datas.item.status20_date
				);
				t.item.status30_date = t.$options.filters.YYYYMMDD(
					datas.item.status30_date
				);
				t.item.status40_date = t.$options.filters.YYYYMMDD(
					datas.item.status40_date
				);
				t.item.status50_date = t.$options.filters.YYYYMMDD(
					datas.item.status50_date
				);
				t.item.status60_date = t.$options.filters.YYYYMMDD(
					datas.item.status60_date
				);
				t.item.status70_date = t.$options.filters.YYYYMMDD(
					datas.item.status70_date
				);
			}
		},
		list() {
			router.push({ name: "hasei.list" });
		},
		save(type) {
			var t = this;
			t.$dialog
				.confirm(
					{
						title: "確認",
						body: {"-1":"ひとつ前に戻してもよろしいですか？", "0":"保存してもよろしいですか？", "1":"申請してもよろしいですか？"}[type],
					},
					{
						okText: "はい",
						cancelText: "キャンセル",
					}
				)
				.then(function () {
					t.ing = true;
					t.item.editable = t.edit_editable;
					switch (type) {
						case -1:
							t.item.command = 2;
							break;
						case 1:
							t.item.command = 1;
							break;
					}
					$.ajax({
						type: "POST",
						url: t.url + "/AjaxPutHasei",
						dataType: "json",
						contentType: "application/json",
						async: true,
						data: JSON.stringify({
							item: t.item,
						}),
					})
						.done(function (datas) {
							t.set_data(datas);
							t.list();
							t.ing = false;
						})
						.fail(function (jqXHR, textStatus, errorThrown) {
							t.ing = false;
						});
				});
		},
		item_delete(idx) {
			var t = this;
			t.$dialog
				.confirm(
					{
						title: "確認",
						body: "削除してもよろしいですか？",
					},
					{
						okText: "はい",
						cancelText: "キャンセル",
					}
				)
				.then(function () {
					t.item.items.splice(idx, 1);
				});
		},
		hinmoku_search(r) {
			var t = this;
			t.showZaikoModal = true;
			t.cur_item = r;
		},
		zaikoDlgClose: function (zaiko) {
			var t = this;
			t.showZaikoModal = false;
			if (zaiko != null) {
				t.cur_item.suu = 1;
				t.cur_item.zaiko_id = zaiko.id;
				t.cur_item.zaiko = zaiko;
				t.cur_item.zaiko.tana = cmn.get_tana_name(zaiko);
				t.change();
			}
		},
	},
};
</script>
