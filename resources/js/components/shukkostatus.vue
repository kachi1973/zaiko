<template>
    <span class="badge" :class="clsname">{{statusname}}{{suffix}}</span>
</template>

<script>
import {shukko_mixin} from "./shukko_mixin.js";
export default {
    mixins: [shukko_mixin],
    computed: {
    },
}
</script>
