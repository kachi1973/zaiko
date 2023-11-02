
<template>
    <span class="badge" :class="clsname">{{statusname}}{{suffix}}</span>
</template>

<script>
import {hasei_mixin} from "./hasei_mixin.js";
export default {
    mixins: [hasei_mixin],
    computed: {
    },
}
</script>
