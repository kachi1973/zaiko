<template>
    <div class="input-group input-group-sm">
        <div v-if="title" class="input-group-prepend">
            <div class="input-group-text">{{title}}</div>
        </div>
        <select class="form-control" v-model="in_sel">
            <option  v-for="(nam, index) in names" :key="nam" :value="index">{{nam}}</option>
        </select>
        <datepicker class="form-control" v-model="in_date1"></datepicker>
        <datepicker class="form-control" v-model="in_date2"></datepicker>
    </div>
</template>

<style>
</style>

<script>
export default {
    props: {
        title: null,
        names: null,
        sel: null,
        date1: null,
        date2: null,
        visible: null,
        disabled: null,
	},
    data() {
		return {
            in_sel: 0,
            in_date1: null,
            in_date2: null,
		}
	},
    mounted() {
        this.in_date1 = this.date1;
        this.in_date2 = this.date2;
    },
	watch: {
        sel: function(value){
            this.in_sel = this.sel;
        },
        date1: function(value){
            this.in_date1 = this.date1;
        },
        date2: function(value){
            this.in_date2 = this.date2;
        },
        in_sel: function(value){
            this.$emit("input_sel", this.in_sel);
        },
        in_date1: function(value){
            this.$emit("input_date1", this.in_date1);
        },
        in_date2: function(value){
            this.$emit("input_date2", this.in_date2);
        },
    },
}
</script>

