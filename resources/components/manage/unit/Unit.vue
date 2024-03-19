<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Item Units</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Item Units</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Unit List'" :data="units" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="unit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveUnit()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Unit</h5>
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

const units = ref([]);
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
            unit: units.value[row.index],
            funcs: {
                viewUnit,
                deleteUnit
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_unit: null,
    name: null,
    label: null,
});
const errObj = reactive({
    name: null,
    label: null,
});
onMounted(async () => {
    await generateUnits();
    $('#unit-modal').on('hide.bs.modal', function () {
        for (var key in obj) {
            obj[key] = null;
        }

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#unit-modal').modal('show');
};
const onRowCreated = (unit) => {
    units.value.unshift(unit);
    units.value = units.value.map(obj => obj);
};
const onRowUpdated = (unit) => {
    units.value = units.value.map(obj => obj.id_unit !== unit.id_unit ? obj : unit);
};
const onRowDeleted = (unit) => {
    units.value = units.value.filter(obj => obj.id_unit !== unit.id_unit);
};


async function generateUnits() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/units');
        units.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveUnit() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_unit === null) {
            res = await createUnit(obj);
            onRowCreated(res.data.unit);
        } else {
            res = await updateUnit(obj);
            onRowUpdated(res.data.unit);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#unit-modal').modal('hide');
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
async function createUnit(obj) {
    try {
        const res = await axios.post('/api/manage/units/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateUnit(obj) {
    try {
        const res = await axios.put('/api/manage/units/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewUnit(id_unit) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/units/read/' + id_unit,
        );
        const unit = res.data;
        const keys = ['id_unit', 'name', 'label'];
        keys.forEach(key => obj[key] = unit[key]);
        $('#unit-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteUnit(id_unit) {
    $swal.fire({
        title: 'Are you sure you want to delete the unit?',
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
                    '/api/manage/units/delete/' + id_unit,
                );
                onRowDeleted(res.data.unit);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
