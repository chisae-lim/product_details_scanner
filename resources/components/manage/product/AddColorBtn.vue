<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.addColor(props.color.id_color)"
            class="mx-1 btn btn-sm btn-outline-success" role="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    color: Object,
    funcs: Object,
});
</script>