<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Brands</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Brands</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Brand List'" :data="brands" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="brand-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveBrand()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Brand</h5>
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

const brands = ref([]);
const columns = [
    {
        accessorKey: 'name',
        header: 'Name',
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
            brand: brands.value[row.index],
            funcs: {
                viewBrand,
                deleteBrand
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_brand: null,
    name: null,
});
const errObj = reactive({
    name: null,
});
onMounted(async () => {
    await generateBrands();
    $('#brand-modal').on('hide.bs.modal', function () {
        obj.id_brand = null;
        obj.name = null;

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#brand-modal').modal('show');
};
const onRowCreated = (brand) => {
    brands.value.unshift(brand);
    brands.value = brands.value.map(obj => obj);
};
const onRowUpdated = (brand) => {
    brands.value = brands.value.map(obj => obj.id_brand !== brand.id_brand ? obj : brand);
};
const onRowDeleted = (brand) => {
    brands.value = brands.value.filter(obj => obj.id_brand !== brand.id_brand);
};


async function generateBrands() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/brands');
        brands.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveBrand() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_brand === null) {
            res = await createBrand(obj);
            onRowCreated(res.data.brand);
        } else {
            res = await updateBrand(obj);
            onRowUpdated(res.data.brand);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#brand-modal').modal('hide');
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
async function createBrand(obj) {
    try {
        const res = await axios.post('/api/manage/brands/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateBrand(obj) {
    try {
        const res = await axios.put('/api/manage/brands/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewBrand(id_brand) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/brands/read/' + id_brand,
        );
        const brand = res.data;
        const keys = ['id_brand', 'name'];
        keys.forEach(key => obj[key] = brand[key]);
        $('#brand-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteBrand(id_brand) {
    $swal.fire({
        title: 'Are you sure you want to delete the brand?',
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
                    '/api/manage/brands/delete/' + id_brand,
                );
                onRowDeleted(res.data.brand);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
