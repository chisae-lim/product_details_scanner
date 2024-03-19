<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dimension Scales</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Dimension Scales</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Scale List'" :data="scales" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scale-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveScale()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Scale</h5>
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
                            <label>Label</label>
                            <input v-model="obj.label" type="text" class="form-control"
                                :class="{ 'is-invalid': errObj.label !== null }" placeholder="Enter label">
                            <div class="invalid-feedback">
                                {{ errObj.label }}
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

const scales = ref([]);
const columns = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'label',
        header: 'Label',
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
            scale: scales.value[row.index],
            funcs: {
                viewScale,
                deleteScale
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_scale: null,
    name: null,
    label: null,
});
const errObj = reactive({
    name: null,
    label: null,
});
onMounted(async () => {
    await generateScales();
    $('#scale-modal').on('hide.bs.modal', function () {
        for (var key in obj) {
            obj[key] = null;
        }

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#scale-modal').modal('show');
};
const onRowCreated = (scale) => {
    scales.value.unshift(scale);
    scales.value = scales.value.map(obj => obj);
};
const onRowUpdated = (scale) => {
    scales.value = scales.value.map(obj => obj.id_scale !== scale.id_scale ? obj : scale);
};
const onRowDeleted = (scale) => {
    scales.value = scales.value.filter(obj => obj.id_scale !== scale.id_scale);
};


async function generateScales() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/scales');
        scales.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveScale() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_scale === null) {
            res = await createScale(obj);
            onRowCreated(res.data.scale);
        } else {
            res = await updateScale(obj);
            onRowUpdated(res.data.scale);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#scale-modal').modal('hide');
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
async function createScale(obj) {
    try {
        const res = await axios.post('/api/manage/scales/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateScale(obj) {
    try {
        const res = await axios.put('/api/manage/scales/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewScale(id_scale) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/scales/read/' + id_scale,
        );
        const scale = res.data;
        const keys = ['id_scale', 'name', 'label'];
        keys.forEach(key => obj[key] = scale[key]);
        $('#scale-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteScale(id_scale) {
    $swal.fire({
        title: 'Are you sure you want to delete the scale?',
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
                    '/api/manage/scales/delete/' + id_scale,
                );
                onRowDeleted(res.data.scale);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
