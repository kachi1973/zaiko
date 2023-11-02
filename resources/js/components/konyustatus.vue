
<template>
    <span class="badge" :class="clsname">{{statusname}}{{suffix}}</span>
</template>

<script>
import {konyu_mixin} from "./konyu_mixin.js";
export default {
    mixins: [konyu_mixin],
    computed: {
    },
}
</script>
