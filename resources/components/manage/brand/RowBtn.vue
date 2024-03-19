<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.deleteBrand(props.brand.id_brand)"
            class="mx-1 btn btn-sm btn-outline-danger" role="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
        <button @click="props.funcs.viewBrand(props.brand.id_brand)" class="mx-1 btn btn-sm btn-secondary" role="button"><i
                class="fa fa-pencil-alt" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    brand: Object,
    funcs: Object,
});
</script>