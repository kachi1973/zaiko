<template>
    <input
        :name="name"
        v-model="value2"
        :id="id"
        type="date"
        class="form-control"
	    placeholder="YYYY/MM/DD"
        :disabled = "disabled"
        >
</template>

<script>
export default {
	props: {
        value: null,
        name: null,
        id: null,
        disabled: null,
    },
    data() {
		return {
            value2: this.replace(this.value),
		}
    },
    mounted() {
    },
	watch: {
		value2: function (val) {
			this.$emit('input', this.value2);
		},
	    value: function(val){
	    	this.value2 = this.replace(this.value);
	    },
	},
	methods: {
        replace : function(val){
            return val ? val.replaceAll('/', '-') : null;
        }
	},
}
</script>

<style lang="scss" scoped>
</style>
