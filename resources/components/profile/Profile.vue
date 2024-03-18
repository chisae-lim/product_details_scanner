<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile text-center">
                                <img class="profile-user-img img-fluid img-circle" :src="default5x5Image"
                                    alt="profile picture">
                                <h3 class="profile-username text-center">{{ user.name }}</h3>
                                <p :class="{
                                    'badge badge-primary': user.act_status === 'ALLOWED',
                                    'badge badge-warning': user.act_status === 'DENIED'
                                }">{{ user.act_status }}</p>
                            </div>
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Account Permissions</h3>
                            </div>
                            <div class="card-body">
                                <h6 v-for="permission in user.permissions" :key="permission.id_permission">
                                    {{ permission.permission }}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#ChangePassword"
                                            data-toggle="tab">Change Password</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="ChangePassword">
                                        <form @submit.prevent="changePassword()" class="form-horizontal">
                                            <div class="form-group row">
                                                <label for="inputCurrentPassword" class="col-sm-3 col-form-label">Current
                                                    Password</label>
                                                <div class="col-sm-9">
                                                    <input v-model="changePassObj.current_pass" type="password"
                                                        class="form-control"
                                                        :class="{ 'is-invalid': changePassErrObj.current_pass !== '' }"
                                                        id="inputCurrentPassword" placeholder="CurrentPassword"
                                                        autocomplete>
                                                    <div class="invalid-feedback">
                                                        {{ changePassErrObj.current_pass }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputNewPassword" class="col-sm-3 col-form-label">New
                                                    Password</label>
                                                <div class="col-sm-9">
                                                    <input v-model="changePassObj.new_pass" type="password"
                                                        class="form-control"
                                                        :class="{ 'is-invalid': changePassErrObj.new_pass !== '' }"
                                                        id="inputNewPassword" placeholder="NewPassword" autocomplete>
                                                    <div class="invalid-feedback">
                                                        {{ changePassErrObj.new_pass }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputConfirmPassword" class="col-sm-3 col-form-label">Confirm
                                                    Password</label>
                                                <div class="col-sm-9">
                                                    <input v-model="changePassObj.confirm_pass" type="password"
                                                        class="form-control"
                                                        :class="{ 'is-invalid': changePassErrObj.confirm_pass !== '' }"
                                                        id="inputConfirmPassword" placeholder="ConfirmPassword"
                                                        autocomplete>
                                                    <div class="invalid-feedback">
                                                        {{ changePassErrObj.confirm_pass }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-3 col-sm-9">
                                                    <button type="submit" class="btn btn-danger">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { changeUserPassword } from '../../js/modules/auth.js';
import default5x5Image from "../../../public/assets/images/default5x5.png";

const router = useRouter();

const store = useStore();
const user = computed(() => store.state.user);

const changePassErrObj = reactive({
    current_pass: '',
    new_pass: '',
    confirm_pass: '',
});
const changePassObj = reactive({
    current_pass: '',
    new_pass: '',
    confirm_pass: '',
});
const changePassword = async () => {
    LoadingModal();
    try {
        const res = await changeUserPassword(changePassObj.current_pass, changePassObj.new_pass, changePassObj.confirm_pass);
        const message = res.data.message;
        MessageModal('success', 'Success', message, () => router.push({ name: 'logout' }));
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in changePassErrObj) {
                changePassErrObj[key] = errors[key] !== undefined ? errors[key][0] : '';
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
</script>