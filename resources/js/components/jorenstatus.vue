
<template>
    <span class="badge" :class="clsname">{{statusname}}{{suffix}}</span>
</template>

<script>
import {joren_mixin} from "./joren_mixin.js";
export default {
    mixins: [joren_mixin],
    computed: {
    },
}
</script>
