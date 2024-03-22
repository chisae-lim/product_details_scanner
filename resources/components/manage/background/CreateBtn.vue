<template>
    Action
    <input :disabled="forbadeUser" @change="props.funcs.onImageChanged($event)" type="file" accept=".png, .jpg, .jpeg"
        id="file-input" style="display: none">
    <label for="file-input">
        <a :class="{ 'disabled': forbadeUser }" class="mx-1 btn btn-primary btn-sm" title="upload product image"><i
                class="fa fa-upload"></i></a>
    </label>
</template>
<script setup>
import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const props = defineProps({
    funcs: Object,
});
</script>