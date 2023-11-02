
<template>
    <span class="badge" :class="clsname">{{statusname}}{{suffix}}</span>
</template>

<script>
import {zido_mixin} from "./zido_mixin.js";
export default {
    mixins: [zido_mixin],
    computed: {
    },
}
</script>
