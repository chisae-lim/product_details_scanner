<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="card card-secondary mt-5">
                        <router-link :to="{ name: 'dashboard' }" class="navbar-brand" href="#">
                            <div class="d-flex justify-content-center">
                                <img :src="logoImage" class="brand-image img-circle w-25" alt="logo image">
                            </div>
                        </router-link>
                        <div class="card-header d-flex justify-content-center">
                            <h3 class="card-title">Login to your account</h3>
                        </div>

                        <form @submit.prevent="login()" id="quickForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input v-model="loginObj.username" type="text" class="form-control"
                                        :class="{ 'is-invalid': loginErrObj.username !== null }"
                                        placeholder="Enter username">
                                    <div class="invalid-feedback">
                                        {{ loginErrObj.username }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input v-model="loginObj.password" type="password" class="form-control"
                                        :class="{ 'is-invalid': loginErrObj.password !== null }" placeholder="Password"
                                        autocomplete>
                                    <div class="invalid-feedback">
                                        {{ loginErrObj.password }}
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="custom-control custom-checkbox">
                                        <input v-model="loginObj.remember_me" type="checkbox"
                                            class="custom-control-input" id="RememberMe">
                                        <label class="custom-control-label" for="RememberMe">Remember-me</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { logUserIn } from '../../js/modules/auth.js';
import logoImage from "../../../public/assets/images/logo.jpg";

const router = useRouter();

const loginObj = reactive({
    username: null,
    password: null,
    remember_me: false,
});
const loginErrObj = reactive({
    username: null,
    password: null,
});

const login = async () => {
    LoadingModal();
    try {
        const res = await logUserIn(loginObj.username, loginObj.password, loginObj.remember_me);
        router.push({ name: 'dashboard' });
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in loginErrObj) {
                loginErrObj[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
</script>