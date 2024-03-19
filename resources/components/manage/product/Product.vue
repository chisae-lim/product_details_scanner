<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Product List'" :data="products" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="product-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveProduct()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Product</h5>
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

const products = ref([]);
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
            product: products.value[row.index],
            funcs: {
                viewProduct,
                deleteProduct
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_product: null,
    name: null,
});
const errObj = reactive({
    name: null,
});
onMounted(async () => {
    await generateProducts();
    $('#product-modal').on('hide.bs.modal', function () {
        obj.id_product = null;
        obj.name = null;

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#product-modal').modal('show');
};
const onRowCreated = (product) => {
    products.value.unshift(product);
    products.value = products.value.map(obj => obj);
};
const onRowUpdated = (product) => {
    products.value = products.value.map(obj => obj.id_product !== product.id_product ? obj : product);
};
const onRowDeleted = (product) => {
    products.value = products.value.filter(obj => obj.id_product !== product.id_product);
};


async function generateProducts() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/products');
        products.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveProduct() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_product === null) {
            res = await createProduct(obj);
            onRowCreated(res.data.product);
        } else {
            res = await updateProduct(obj);
            onRowUpdated(res.data.product);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#product-modal').modal('hide');
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
async function createProduct(obj) {
    try {
        const res = await axios.post('/api/manage/products/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateProduct(obj) {
    try {
        const res = await axios.put('/api/manage/products/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewProduct(id_product) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/products/read/' + id_product,
        );
        const product = res.data;
        const keys = ['id_product', 'name'];
        keys.forEach(key => obj[key] = product[key]);
        $('#product-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteProduct(id_product) {
    $swal.fire({
        title: 'Are you sure you want to delete the product?',
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
                    '/api/manage/products/delete/' + id_product,
                );
                onRowDeleted(res.data.product);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
