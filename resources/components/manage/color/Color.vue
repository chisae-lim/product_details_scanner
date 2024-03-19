<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Colors</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Colors</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Color List'" :data="colors" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="color-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveColor()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Color</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="obj.name" type="text" class="form-control"
                                :class="{ 'is-invalid': errObj.name !== null }" placeholder="Enter name">
                            <div class="invalid-feedback">
                                {{ errObj.name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Code</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" :style="{ backgroundColor: obj.code }"></span>
                                <input v-model="obj.code" type="text" class="form-control"
                                    :class="{ 'is-invalid': errObj.code !== null }" placeholder="Enter label">
                                <div class="invalid-feedback">
                                    {{ errObj.code }}
                                </div>
                                <span class="input-group-text" :style="{ backgroundColor: obj.code }"></span>
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

const colors = ref([]);
const columns = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'code',
        header: 'Code',
    },
    {
        accessorKey: 'code',
        header: '#',
        cell: (cell) => h('div', {
            style: { backgroundColor: cell.getValue(), color: cell.getValue() },
        }, cell.getValue()),
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
            color: colors.value[row.index],
            funcs: {
                viewColor,
                deleteColor
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_color: null,
    name: null,
    code: null,
});
const errObj = reactive({
    name: null,
    code: null,
});
onMounted(async () => {
    await generateColors();
    $('#color-modal').on('hide.bs.modal', function () {
        for (var key in obj) {
            obj[key] = null;
        }

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#color-modal').modal('show');
};
const onRowCreated = (color) => {
    colors.value.unshift(color);
    colors.value = colors.value.map(obj => obj);
};
const onRowUpdated = (color) => {
    colors.value = colors.value.map(obj => obj.id_color !== color.id_color ? obj : color);
};
const onRowDeleted = (color) => {
    colors.value = colors.value.filter(obj => obj.id_color !== color.id_color);
};


async function generateColors() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/colors');
        colors.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveColor() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_color === null) {
            res = await createColor(obj);
            onRowCreated(res.data.color);
        } else {
            res = await updateColor(obj);
            onRowUpdated(res.data.color);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#color-modal').modal('hide');
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in errObj) {
                errObj[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
async function createColor(obj) {
    try {
        const res = await axios.post('/api/manage/colors/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateColor(obj) {
    try {
        const res = await axios.put('/api/manage/colors/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewColor(id_color) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/colors/read/' + id_color,
        );
        const color = res.data;
        const keys = ['id_color', 'name', 'code'];
        keys.forEach(key => obj[key] = color[key]);
        $('#color-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteColor(id_color) {
    $swal.fire({
        title: 'Are you sure you want to delete the color?',
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
                    '/api/manage/colors/delete/' + id_color,
                );
                onRowDeleted(res.data.color);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
