<template>
    <button type="button" v-on:click="click()" :disabled="disabled2">{{statusname}}{{suffix}}</button>
</template>

<script>
import {zido_mixin} from "./zido_mixin.js";
export default {
    mixins: [zido_mixin],
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
