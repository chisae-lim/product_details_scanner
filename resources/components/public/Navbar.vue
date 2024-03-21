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
</template>

<style scoped>
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
import background from "../../../public/assets/images/background.jpg";
</script>