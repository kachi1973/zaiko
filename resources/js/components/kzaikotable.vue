<template>
    <table class="table table-sm table-bordered table-hover table-light">
        <thead class="table-secondary">
            <tr>
                <th style="width: 1%">個別ID</th>
                <th style="width: 2%">貯蔵品コード</th>
                <th style="width: 2%">型式</th>
                <th style="width: 1%">Ver</th>
                <th style="width: 1%">種別</th>
                <th style="width: 2%">備考</th>
                <th style="width: 2%">名称</th>
                <th style="width: 2%">メーカ</th>
                <th style="width: 2%">製造年月</th>
                <th style="width: 1%">状態</th>
                <th v-if="!IsMobile" style="width: 0.5%">操作</th>
            </tr>
        </thead>
        <tbody class="table-light">
            <tr v-for="ikz in ikzs" v-bind:key="ikz.kzaiko.id">
                <td>{{ikz.kzaiko.id}}</td>
                <td>{{ikz.kzaiko.hinmoku_id}}</td>
                <td>{{ikz.kzaiko.hinmoku.model}}</td>
                <td>{{ikz.kzaiko.model_v}}</td>
                <td>{{ikz.kzaiko.model_kind}}</td>
                <td>{{ikz.kzaiko.biko}}</td>
                <td>{{ikz.kzaiko.hinmoku.name}}</td>
                <td>{{ikz.kzaiko.hinmoku.maker}}</td>
                <td>{{ikz.kzaiko.seizo_date}}</td>
                <td v-if="ikz.status==1"         class="text-center bg-warning"  >出庫中</td>
                <td v-else-if="ikz.status==2"    class="text-center bg-success"  >入庫</td>
                <td v-else-if="ikz.status==9999" class="text-center bg-secondary">使用済</td>
                <td v-else                          class="text-center bg-success">保管中</td>
                <td v-if="!IsMobile">
                    <div class='text-center'>
                        <button type="button" :disabled="disabled" class="btn btn-primary btn-sm" v-on:click="kzaiko_click(ikz)"><i class="fas fa-minus"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<style scoped>
</style>

<script>
import {cmn_mixin} from '../components/cmn_mixin.js';
export default {
    mixins: [cmn_mixin],
	props: {
        ikzs: {
            type: Array,
            default: null,
            required: false,
        },
        disabled: {
            type: Boolean,
            default: false,
            required: false,
        },
    },
    data() {
		return {
		}
    },
    mounted() {
    },
	watch: {
	},
	methods: {
		kzaiko_click(ikz){
			this.$emit('click', ikz);
		},
	},
}
</script>
