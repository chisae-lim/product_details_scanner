<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.deleteUnit(props.unit.id_unit)"
            class="mx-1 btn btn-sm btn-outline-danger" role="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
        <button @click="props.funcs.viewUnit(props.unit.id_unit)" class="mx-1 btn btn-sm btn-secondary" role="button"><i
                class="fa fa-pencil-alt" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    unit: Object,
    funcs: Object,
});
</script>