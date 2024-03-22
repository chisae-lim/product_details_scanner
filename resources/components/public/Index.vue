<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10">
                    <div class="card">
                        <div class="d-flex justify-content-center mt-3">
                            <router-link :to="{ name: 'dashboard' }" class="navbar-brand" href="#">
                                <img :src="logoImage" :hidden="scanning" class="brand-image border img-circle"
                                    style="width: 200px;" alt="logo image">
                            </router-link>

                        </div>
                        <div v-if="scanning" class="mx-auto p-3" style="max-width: 400px;">
                            <StreamBarcodeReader @decode="onDecode">
                            </StreamBarcodeReader>
                        </div>
                        <form @submit.prevent="searchProduct()">
                            <div class="card-body">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <button @click="onScanBtnClicked()" class="btn     btn-outline-primary"
                                            type="button">
                                            <i class="bi bi-upc-scan"></i>
                                        </button>
                                    </span>
                                    <input ref="searchBox" v-model="searchText" class="form-control py-2" type="search"
                                        placeholder="Enter code">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-primary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<script setup>
import Navbar from './Navbar.vue';

import { StreamBarcodeReader } from "vue-barcode-reader";
import logoImage from "../../../public/assets/images/logo.jpg";
import { onMounted, ref } from 'vue';

const searchBox = ref(null);
const searchText = ref(null);
const scanning = ref(false);
const decoding = ref(false);

onMounted(() => {
    searchBox.value.focus();
})

const onScanBtnClicked = async () => {
    LoadingModal();
    await navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            scanning.value = !scanning.value;
            CloseModal();
        })
        .catch(error => {
            MessageModal('error', 'Oops...', 'Error accessing camera: Permission denied!.');
        });
}

const onDecode = async (result) => {
    searchText.value = result;
    searchProduct();
}

// 8846015180038
async function searchProduct() {
    if (searchText.value !== null && decoding.value === false) {
        decoding.value = true;
        searchBox.value.select();
        let product = null;
        try {
            const res = await searchProductByCode(searchText.value);
            product = res.data;
            if (product !== '') {
                return window.open(product.link, '_blank').focus();
            }
            return MessageModal('info', 'Not Found', 'Product not found!.');
        } catch (error) {
            if (product !== null) {
                return await $swal.fire({
                    title: 'We are going to send you to a new browser tab for product detail.',
                    html: '<pre>' + "Please make a confirmation." + '</pre>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Yes, Open it.'
                }).then(async (sw) => {
                    if (sw.isConfirmed) {
                        return window.open(product.link, '_blank').focus();
                    }
                });
            }
            scanning.value = false;
            return MessageModal('error', 'Oops...', 'Something went wrong!.');
        } finally {
            decoding.value = false;
        }
    }
}
async function searchProductByCode(code) {
    try {
        const res = await axios.get('/api/product/search/code/' + code);
        return res;
    } catch (error) {
        throw error;
    }
}
</script>