<template>
    <div class="background-wrapper">
        <div class="background-image" :style="{ backgroundImage: `url(${background})` }"></div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <router-link :to="{ name: 'dashboard' }" class="navbar-brand" href="#">Navbar</router-link>
            <router-link :to="{ name: 'login' }" class="navbar-text ml-auto">
                <i class="fas fa-sign-in-alt"></i>
            </router-link>
        </div>
    </nav>
    <section class="content mt-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="mt-5 col-lg-6 col-md-8 col-sm-10">
                    <div class="card">
                        <div class="d-flex justify-content-center mt-3">
                            <img :src="logoImage" :hidden="scanning" class="brand-image border img-circle"
                                style="width: 200px;" alt="logo image">
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
    <div class="modal fade" id="scanner-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scanner</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
</template>
<style>
/* body {
    background-image: url('/assets/images/background.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
} */

.background-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.background-image {
    /* background-image: url('/assets/images/background.jpg'); */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /* opacity: 0.75; */
    width: 100%;
    height: 100%;
}
</style>

<script setup>
import { useRouter } from 'vue-router';

import { StreamBarcodeReader } from "vue-barcode-reader";
import background from "../../../public/assets/images/background.jpg";
import logoImage from "../../../public/assets/images/logo.jpg";
import { onMounted, ref } from 'vue';

const router = useRouter();

const searchBox = ref(null);
const searchText = ref(null);
const scanning = ref(false);

onMounted(() => {
    searchBox.value.focus();
    $('#scanner-modal').on('hide.bs.modal', function () {
        scanning.value = false;
    });
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

    // $('#scanner-modal').modal('show');
}

const onLoaded = (result) => { console.log(result) }

const onDecode = async (result) => {
    searchText.value = result;
}
async function searchProduct() {
    if (searchText.value !== null) {
        searchBox.value.select();
        try {
            const res = await getProductByCode(searchText.value);
            const product = res.data;
            if (product !== '') {
                return window.open('/login', '_blank');
            }
            return MessageModal('error', 'Oops...', 'Something went wrong!.');
        } catch (error) {
            scanning.value = false;
            MessageModal('error', 'Oops...', 'Something went wrong!.');
        }
    }
}
async function getProductByCode(code) {
    try {
        const res = await axios.get('/api/product/search/code/' + code);
        return res;
    } catch (error) {
        throw error;
    }
}
</script>