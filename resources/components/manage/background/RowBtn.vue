<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.deleteBackground(props.background.id_background)"
            class="mx-1 btn btn-sm btn-outline-danger" role="button"><i class="fa fa-trash"
                aria-hidden="true"></i></button>
        <button v-if="props.background.status === 'DISABLED'"
            @click="props.funcs.activeBackground(props.background.id_background)" class="mx-1 btn btn-sm btn-warning"
            role="button"><i class="far fa-star" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    background: Object,
    funcs: Object,
});
</script>