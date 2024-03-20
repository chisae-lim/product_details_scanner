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
                        <div class="d-flex justify-content-center m-3">
                            <img :src="logoImage" class="brand-image img-circle" style="width: 200px;" alt="logo image">
                        </div>
                        <form @submit.prevent="searchBox.select()">
                            <div class="card-body">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <button @click="onScanClicked()" class="btn     btn-outline-primary"
                                            type="button">
                                            <i class="bi bi-upc-scan"></i>
                                        </button>
                                    </span>
                                    <input ref="searchBox" @click="searchBox.select()" class="form-control py-2"
                                        type="search" placeholder="Enter code">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                        <div class="mx-auto">
                            <StreamBarcodeReader v-if="scanning" @decode="onDecode" @loaded="onLoaded">
                            </StreamBarcodeReader>
                        </div>
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
import { StreamBarcodeReader } from "vue-barcode-reader";
import background from "../../../public/assets/images/background.jpg";
import logoImage from "../../../public/assets/images/logo.jpg";
import { onMounted, ref } from 'vue';
const searchBox = ref(null);
const scanning = ref(false);

onMounted(() => {
    searchBox.value.focus();
    $('#scanner-modal').on('hide.bs.modal', function () {
        scanning.value = false;
    });
})

const onScanClicked = async () => {
    await navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            scanning.value = !scanning.value;
        })
        .catch(error => {
            console.error('Error accessing camera:', error);
        });

    // $('#scanner-modal').modal('show');
}
const onDecode = (result) => { console.log(result) }
const onLoaded = (result) => { console.log(result) }
const onError = (result) => { console.log(result) }
</script>