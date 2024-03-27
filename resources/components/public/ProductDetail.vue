<template>
    <section class="content">
        <div class="container">
            <div class="card">
                <div v-if="product === null" class="card-body">
                    <h5 class="card-title">Not Found!.</h5>
                    <p class="card-text">Sorry, The product with barcode({{ bar_code }}) not found.</p>
                    <router-link :to="{ name: 'dashboard' }" class="btn btn-primary">Go to home page</router-link>
                </div>
                <div v-else class="container-fliud">
                    <div class="wrapper row">
                        <div class="mb-3 col-md-12 col-lg-6">
                            <div class="d-flex justify-content-center">
                                <img class="main-image" :src="main_image">
                            </div>
                            <div class="horizontal-scroll d-flex">
                                <img v-for="(image, i) in product.images" class="thumbnail m-1"
                                    :class="{ 'clicked': image.image_url === main_image }"
                                    @click="onThumbNailClicked(i)" :src="image.thumbnail_url">

                            </div>
                        </div>
                        <div class="details col-md-12 col-lg-6">
                            <h3 class="product-title">{{ product.name_en }}</h3>
                            <h3 class="product-title">{{ product.name_ch }}</h3>
                            <h5 class="product-code">code: {{ product.p_code }}</h5>
                            <h5 class="barcode">barcode: {{ product.bar_code }}</h5>
                            <!-- <div class="rating">
                                <div class="stars">
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </div>
                                <span class="review-no">41 reviews</span>
                            </div> -->
                            <!-- <p class="product-description">{{ product.description }}</p> -->
                            <h4 class="price">price: <span>{{ `${product.price}$ / ${product.unit.name}`
                                    }}</span>
                            </h4>
                            <!-- <p class="vote"><strong>91%</strong> of buyers enjoyed this product! <strong>(87
                                    votes)</strong></p> -->
                            <!-- <h5 class="sizes">sizes:
                                <span class="size" data-toggle="tooltip" title="small">s</span>
                                <span class="size" data-toggle="tooltip" title="medium">m</span>
                                <span class="size" data-toggle="tooltip" title="large">l</span>
                                <span class="size" data-toggle="tooltip" title="xtra large">xl</span>
                            </h5> -->

                            <h5 class="brand">brand: {{ product.brand?.name }}</h5>
                            <h5 class="category">category: {{ product.category?.name }}</h5>
                            <h5 class="colors">colors:
                                <span v-for="color in product.colors" :key="color.id_color" class="color"
                                    :style="{ background: color.code }"></span>
                            </h5>
                            <!-- <div class="action">
                                <button class="add-to-cart btn btn-default" type="button">add to cart</button>
                                <button class="like btn btn-default" type="button"><span
                                        class="fa fa-heart"></span></button>
                            </div> -->
                        </div>
                    </div>
                    <div class="details">
                        <h5><label>Descriptions</label></h5>
                        <h5 class="product-description">{{ product.description }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<script setup>

import { RouterLink, useRoute } from 'vue-router'
import { onMounted, ref } from 'vue';
import Navbar from './Navbar.vue';
import logoImage from "../../../public/assets/images/logo.jpg";

const bar_code = ref(null);
const route = useRoute();
const product = ref(null)
const main_image = ref(null);
onMounted(async () => {
    bar_code.value = route.params.bar_code;
    const res = await getProductByBarcode(route.params.bar_code);
    product.value = res.data;
    main_image.value = product.value.images[0].image_url;
});

async function getProductByBarcode(code) {
    try {
        const res = await axios.get('/api/product/bar_code/' + code);
        return res;
    } catch (error) {
        throw error;
    }
}
async function onThumbNailClicked(index) {
    main_image.value = product.value.images[index].image_url;
}
</script>

<style scoped>
/*****************globals*************/
body {
    font-family: 'open sans';
    overflow-x: hidden;
}

.main-image {
    max-width: 100%;
    border-radius: 5px;
    border: 1px solid gray;
    box-shadow: -3px -3px rgb(185, 185, 185);
}

.thumbnail {
    max-width: 100%;
    border-radius: 5px;
    border: 1px solid gray;
    cursor: pointer;
}

.thumbnail.clicked {
    max-width: 100%;
    border-radius: 5px;
    border: 3px inset rgb(146, 58, 58);
    cursor: pointer;
}


.card {
    margin-top: 50px;
    min-height: calc(100vh - 200px);
    background: #eee;
    padding: 3em;
    line-height: 1.5em;
}

@media screen and (min-width: 997px) {
    .wrapper {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
    }
}

.details {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
}

.colors {
    -webkit-box-flex: 1;
    -webkit-flex-grow: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
}

.product-title,
.product-code,
.barcode,
.brand,
.category,
.price,
.colors,
.sizes {
    text-transform: UPPERCASE;
    font-weight: bold;
}

.checked,
.price span {
    color: #ff9f1a;
}

.product-title,
.product-code,
.barcode,
.brand,
.category,
.product-description,
.price,
.colors,
.sizes,
.vote,
.rating {
    margin-bottom: 30px;
}

.product-title {
    margin-top: 0;
}

.size {
    margin-right: 10px;
}

.size:first-of-type {
    margin-left: 40px;
}

.color {
    display: inline-block;
    vertical-align: middle;
    margin: 7px;
    height: 2em;
    width: 2em;
    border-radius: 2px;
    border: 1px rgb(200, 200, 200) solid;
}

.color:first-of-type {
    margin-left: 33px;
}

.add-to-cart,
.like {
    background: #ff9f1a;
    padding: 1.2em 1.5em;
    border: none;
    text-transform: UPPERCASE;
    font-weight: bold;
    color: #fff;
    -webkit-transition: background .3s ease;
    transition: background .3s ease;
}

.add-to-cart:hover,
.like:hover {
    background: #b36800;
    color: #fff;
}

.not-available {
    text-align: center;
    line-height: 2em;
}

.not-available:before {
    font-family: fontawesome;
    content: "\f00d";
    color: #fff;
}

.tooltip-inner {
    padding: 1.3em;
}

@-webkit-keyframes opacity {
    0% {
        opacity: 0;
        -webkit-transform: scale(3);
        transform: scale(3);
    }

    100% {
        opacity: 1;
        -webkit-transform: scale(1);
        transform: scale(1);
    }
}

@keyframes opacity {
    0% {
        opacity: 0;
        -webkit-transform: scale(3);
        transform: scale(3);
    }

    100% {
        opacity: 1;
        -webkit-transform: scale(1);
        transform: scale(1);
    }
}

.horizontal-scroll {
    margin-top: 5px;
    width: 100%;
    overflow-x: scroll;
    overflow: auto;
    white-space: nowrap;
    border-radius: 5px;
    border: 1px inset gray;
}

.horizontal-scroll::-webkit-scrollbar {
    width: 7px;
    height: 7px;
    /* width of the entire scrollbar */
}

.horizontal-scroll::-webkit-scrollbar-track {
    background: rgb(173, 173, 173);
    border-radius: 20px;

    /* color of the tracking area */
}

.horizontal-scroll::-webkit-scrollbar-thumb {
    background-color: rgb(83, 83, 223);
    /* color of the scroll thumb */
    border-radius: 20px;
    /* roundness of the scroll thumb */
    /* border: 3px solid orange; */
    /* creates padding around scroll thumb */
}

/*# sourceMappingURL=style.css.map */
</style>