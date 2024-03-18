<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.deleteUser(props.user.id_user)"
            class="mx-1 btn btn-sm btn-outline-danger" role="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
        <button v-if="props.user.act_status === 'ALLOWED'" :disabled="forbadeUser"
            @click="props.funcs.forbidUser(props.user.id_user)" class="mx-1 btn btn-sm btn-warning" role="button"><i
                class="fa fa-lock" aria-hidden="true"></i></button>
        <button v-if="props.user.act_status === 'DENIED'" :disabled="forbadeUser"
            @click="props.funcs.permitUser(props.user.id_user)" class="mx-1 btn btn-sm btn-primary" role="button"><i
                class="fa fa-unlock" aria-hidden="true"></i></button>
        <button v-if="props.user.acc_status === 'ENABLED'" :disabled="forbadeUser"
            @click="props.funcs.disableUser(props.user.id_user)" class="mx-1 btn btn-sm btn-danger" role="button"><i
                class="fa fa-ban" aria-hidden="true"></i></button>
        <button v-if="props.user.acc_status === 'DISABLED'" :disabled="forbadeUser"
            @click="props.funcs.enableUser(props.user.id_user)" class="mx-1 btn btn-sm btn-success" role="button"><i
                class="fa fa-check-circle" aria-hidden="true"></i></button>
        <button @click="props.funcs.viewUser(props.user.id_user)" class="mx-1 btn btn-sm btn-secondary" role="button"><i
                class="fa fa-pencil-alt" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    user: Object,
    funcs: Object,
});
</script>