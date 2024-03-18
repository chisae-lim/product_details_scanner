<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Rooms</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Rooms</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Room List'" :data="rooms" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="room-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveRoom()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Room</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="roomObj.name" type="text" class="form-control"
                                :class="{ 'is-invalid': roomErrObj.name !== null }" placeholder="Enter name">
                            <div class="invalid-feedback">
                                {{ roomErrObj.name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Capacity</label>
                            <input v-model="roomObj.capacity" type="number" class="form-control"
                                :class="{ 'is-invalid': roomErrObj.capacity !== null }" placeholder="Enter seat number">
                            <div class="invalid-feedback">
                                {{ roomErrObj.capacity }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button :disabled="forbadeUser" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, reactive, h } from 'vue';
import { useStore } from 'vuex';

import CustomTable from '../../includes/CustomTable.vue';
import CreateBtn from './CreateBtn.vue';
import RowBtn from './RowBtn.vue';
const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const rooms = ref([]);
const columns = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'capacity',
        header: 'Capacity',
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({
            row
        }) => h('span', {
            class: 'badge ' + (row.original.status === 'ENABLED' ? 'badge-success' : 'badge-danger')
        }, row.original.status),
    },
    {
        accessorKey: 'action',
        header: () => h(CreateBtn, {
            funcs: {
                onCreateBtnClicked
            }
        }),
        cell: ({
            row
        }) => h(RowBtn, {
            room: rooms.value[row.index],
            funcs: {
                disableRoom,
                enableRoom,
                viewRoom,
                deleteRoom
            }
        }),
        enableSorting: false,
    }
];
const roomObj = reactive({
    id_room: null,
    name: null,
    capacity: 0,
});
const roomErrObj = reactive({
    name: null,
    capacity: null,
});
onMounted(async () => {
    await generateRooms();
    $('#room-modal').on('hide.bs.modal', function () {
        roomObj.id_room = null;
        roomObj.name = null;
        roomObj.capacity = 0;

        for (var key in roomErrObj) {
            roomErrObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#room-modal').modal('show');
};
const onRowCreated = (room) => {
    rooms.value.unshift(room);
    rooms.value = rooms.value.map(obj => obj);
};
const onRowUpdated = (room) => {
    rooms.value = rooms.value.map(obj => obj.id_room !== room.id_room ? obj : room);
};
const onRowDeleted = (room) => {
    rooms.value = rooms.value.filter(obj => obj.id_room !== room.id_room);
};


async function generateRooms() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/rooms');
        rooms.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveRoom() {
    LoadingModal();
    try {
        let res = null;
        if (roomObj.id_room === null) {
            res = await createRoom(roomObj);
            onRowCreated(res.data.room);
        } else {
            res = await updateRoom(roomObj);
            onRowUpdated(res.data.room);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#room-modal').modal('hide');
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in roomErrObj) {
                roomErrObj[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
async function createRoom(roomObj) {
    try {
        const res = await axios.post('/api/manage/rooms/create', roomObj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateRoom(roomObj) {
    try {
        const res = await axios.put('/api/manage/rooms/update', roomObj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewRoom(id_room) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/rooms/read/' + id_room,
        );
        const room = res.data;
        const keys = ['id_room', 'name', 'capacity'];
        keys.forEach(key => roomObj[key] = room[key]);
        $('#room-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteRoom(id_room) {
    $swal.fire({
        title: 'Are you sure you want to delete the room?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Delete it.'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.delete(
                    '/api/manage/rooms/delete/' + id_room,
                );
                onRowDeleted(res.data.room);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function disableRoom(id_room) {
    $swal.fire({
        title: 'Are you sure you want to disable the room?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.patch('/api/manage/rooms/disable',
                    {
                        id_room: id_room,
                    }
                );
                onRowUpdated(res.data.room);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function enableRoom(id_room) {
    $swal.fire({
        title: 'Are you sure you want to enable the room?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.patch('/api/manage/rooms/enable',
                    {
                        id_room: id_room,
                    }
                );
                onRowUpdated(res.data.room);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
