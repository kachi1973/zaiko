<template>
    <button type="button" v-on:click="click()" :disabled="disabled2">{{statusname}}{{suffix}}</button>
</template>

<script>
import {shukko_mixin} from "./shukko_mixin.js";
export default {
    mixins: [shukko_mixin],
    computed: {
        disabled2(){
            var t = this;
            if(t.disabled){
                return true;
            }
            if(70<=t.stsnum){
                return true;
            }
            return false;
        },
    },
    mounted() {
    },
	methods: {
		click: function (num) {
			this.$emit('click');
		},
	},
}
</script>
