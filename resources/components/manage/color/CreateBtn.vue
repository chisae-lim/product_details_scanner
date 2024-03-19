<template>
    Action <button @click="props.funcs.onCreateBtnClicked()" :disabled="forbadeUser" class="btn btn-sm btn-success ml-3"><i
            class="fas fa-plus"></i></button>
</template>
<script setup>
import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const props = defineProps({
    funcs: Object,
});
</script>