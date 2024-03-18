<template>
    <div class="d-flex justify-content-center">
        <button :disabled="forbadeUser" @click="props.funcs.deleteRoom(props.room.id_room)"
            class="mx-1 btn btn-sm btn-outline-danger" role="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
        <button v-if="props.room.status === 'ENABLED'" :disabled="forbadeUser"
            @click="props.funcs.disableRoom(props.room.id_room)" class="mx-1 btn btn-sm btn-danger" role="button"><i
                class="fa fa-ban" aria-hidden="true"></i></button>
        <button v-if="props.room.status === 'DISABLED'" :disabled="forbadeUser"
            @click="props.funcs.enableRoom(props.room.id_room)" class="mx-1 btn btn-sm btn-success" role="button"><i
                class="fa fa-check-circle" aria-hidden="true"></i></button>
        <button @click="props.funcs.viewRoom(props.room.id_room)" class="mx-1 btn btn-sm btn-secondary" role="button"><i
                class="fa fa-pencil-alt" aria-hidden="true"></i></button>
    </div>
</template>
<script setup>

import { useStore } from 'vuex';

const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const props = defineProps({
    room: Object,
    funcs: Object,
});
</script>