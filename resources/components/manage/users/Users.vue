<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'User List'" :data="users" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="user-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveUser()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage User Modal</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="userObj.name" type="text" class="form-control"
                                :class="{ 'is-invalid': userErrObj.name !== null }" placeholder="Enter name">
                            <div class="invalid-feedback">
                                {{ userErrObj.name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input v-model="userObj.username" type="text" class="form-control"
                                :class="{ 'is-invalid': userErrObj.username !== null }" placeholder="Enter username">
                            <div class="invalid-feedback">
                                {{ userErrObj.username }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="userObj.password" type="password" class="form-control"
                                :class="{ 'is-invalid': userErrObj.password !== null }" placeholder="Enter password"
                                autocomplete>
                            <div class="invalid-feedback">
                                {{ userErrObj.password }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account Permissions</label>
                            <div class="row mx-1">
                                <div class="form-check col-6" v-for="permission in user_permissions"
                                    :key="permission.id_permission">
                                    <input :value="permission.id_permission" v-model="userObj.id_permissions"
                                        class="form-check-input" type="checkbox">
                                    <label class="form-check-label">{{ permission.permission }}</label>
                                </div>
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
import { onMounted, ref, reactive, h, computed } from 'vue';
import { useStore } from 'vuex';
import CustomTable from '../../includes/CustomTable.vue';
import CreateBtn from './CreateBtn.vue';
import RowBtn from './RowBtn.vue';
const store = useStore();
const forbadeUser = store.getters.forbadeUser;
const user_permissions = computed(() => store.state.user_permissions);
const users = ref([]);
const columns = [{
    accessorKey: 'name',
    header: 'Name',
},
{
    accessorKey: 'acc_status',
    header: 'Account Status',
    cell: ({
        row
    }) => h('span', {
        class: 'badge ' + (row.original.acc_status === 'ENABLED' ? 'badge-success' : 'badge-danger')
    }, row.original.acc_status),
},
{
    accessorKey: 'act_status',
    header: 'Action Status',
    cell: ({
        row
    }) => h('span', {
        class: 'badge ' + (row.original.act_status === 'ALLOWED' ? 'badge-primary' :
            'badge-warning')
    }, row.original.act_status),
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
        user: users.value[row.index],
        funcs: {
            disableUser,
            enableUser,
            forbidUser,
            permitUser,
            viewUser,
            deleteUser
        }
    }),
    enableSorting: false,
}
];
const userObj = reactive({
    id_user: null,
    name: null,
    username: null,
    password: null,
    id_permissions: [],
});
const userErrObj = reactive({
    name: null,
    username: null,
    password: null,
});
onMounted(async () => {
    await Promise.all([
        generateUsers(),
        store.dispatch('getUserPermissions'),
    ]);
    $('#user-modal').on('hide.bs.modal', function () {
        userObj.id_user = null;
        userObj.name = null;
        userObj.username = null;
        userObj.password = null;
        userObj.id_permissions = [];
        userErrObj.name = null;
        userErrObj.username = null;
        userErrObj.password = null;
    });
});

const onCreateBtnClicked = async () => {
    $('#user-modal').modal('show');
};
const onRowCreated = (user) => {
    users.value.unshift(user);
    users.value = users.value.map(obj => obj);
};
const onRowUpdated = (user) => {
    users.value = users.value.map(obj => obj.id_user !== user.id_user ? obj : user);
};
const onRowDeleted = (user) => {
    users.value = users.value.filter(obj => obj.id_user !== user.id_user);
};

async function generateUsers() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/users');
        users.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveUser() {
    LoadingModal();
    try {
        let res = null;
        if (userObj.id_user === null) {
            res = await createUser(userObj);
            onRowCreated(res.data.user);
        } else {
            res = await updateUser(userObj);
            onRowUpdated(res.data.user);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#user-modal').modal('hide');
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in userErrObj) {
                userErrObj[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
async function createUser(userObj) {
    try {
        const res = await axios.post('/api/manage/users/create', userObj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateUser(userObj) {
    try {
        const res = await axios.put('/api/manage/users/update', userObj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewUser(id_user) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/users/read/' + id_user,
        );
        const user = res.data;
        const keys = ['id_user', 'name'];
        keys.forEach(key => userObj[key] = user[key]);
        userObj.id_permissions = user.permissions.map(obj => obj.id_permission);
        $('#user-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteUser(id_user) {
    $swal.fire({
        title: 'Are you sure you want to delete the user account?',
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
                    '/api/manage/users/delete/' + id_user,
                );
                onRowDeleted(res.data.user);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function disableUser(id_user) {
    $swal.fire({
        title: 'Are you sure you want to disable the user account?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.patch(
                    '/api/manage/users/disable', {
                    id_user: id_user,
                }
                );
                onRowUpdated(res.data.user);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function enableUser(id_user) {
    $swal.fire({
        title: 'Are you sure you want to enable the user account?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.patch(
                    '/api/manage/users/enable', {
                    id_user: id_user,
                }
                );
                onRowUpdated(res.data.user);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function forbidUser(id_user) {
    LoadingModal();
    try {
        const res = await axios.patch(
            '/api/manage/users/forbid', {
            id_user: id_user,
        }
        );
        onRowUpdated(res.data.user);
        MessageModal('success', 'Success', res.data.message);
    } catch (error) {
        ErrorModal(error);
    }
}
async function permitUser(id_user) {
    LoadingModal();
    try {
        const res = await axios.patch(
            '/api/manage/users/permit', {
            id_user: id_user,
        }
        );
        onRowUpdated(res.data.user);
        MessageModal('success', 'Success', res.data.message);
    } catch (error) {
        ErrorModal(error);
    }
}
</script>
