<template>
    <span>
        <input type="checkbox" class="btn-check" :id="id" autocomplete="off" v-model="value2">
        <label class="btn" :for="id" :disabled="disabled" :class="clsname">{{statusname}}{{suffix}}</label>
    </span>
</template>
<style lang="scss" scoped>
.btn-check:checked + .btn, :not(.btn-check) + .btn:active, .btn:first-child:active, .btn.active, .btn.show {
    box-shadow: inset 0px 0px 5px 3px blue;
}
label{
    padding: 2px 0px;
}
</style>

<script>
import {zido_mixin} from "./zido_mixin.js";
export default {
    mixins: [zido_mixin],
    data() {
		return {
            value2: this.value,
        }
    },
    computed: {
    },
	watch: {
		value2: function (val) {
			this.$emit('input', this.value2);
		},
	    value: function(val){
	    	this.value2 = this.value;
	    },
	},
	methods: {
	},
}
</script>
