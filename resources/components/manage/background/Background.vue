<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Backgrounds</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Backgrounds</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Background List'" :data="backgrounds" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="background-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form @submit.prevent="saveBackground()">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Background</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body scrollable-y text-center">
                        <img ref="imageRef">
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
<style scoped>
img {
    width: 384px;
    height: 216px;
    border-radius: 10px;
    border: 1px inset gray;
}
</style>
<script setup>
import { onMounted, ref, reactive, h, computed } from 'vue';
import { useStore } from 'vuex';

import CustomTable from '../../includes/CustomTable.vue';
import CreateBtn from './CreateBtn.vue';
import RowBtn from './RowBtn.vue';
const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const imageRef = ref(null);
const backgrounds = ref([]);
const columns = [
    {
        accessorKey: 'thumbnail_url',
        header: '',
        cell: (cell) => h('img', {
            style: 'border-radius: 10px;border: 1px inset gray;',
            // class: "profile-user-img img-fluid img-circle",
            src: cell.getValue(),
            // alt: 'student image'
        }),
    },
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'action',
        header: () => h(CreateBtn, {
            funcs: {
                onImageChanged
            }
        }),
        cell: ({
            row
        }) => h(RowBtn, {
            background: backgrounds.value[row.index],
            funcs: {
                deleteBackground,
                activeBackground,
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    image: null,
});
onMounted(async () => {
    await generateBackgrounds();
});

const onRowCreated = (background) => {
    backgrounds.value.unshift(background);
    backgrounds.value = backgrounds.value.map(obj => obj);
};
const onRowUpdated = (background) => {
    backgrounds.value = backgrounds.value.map(obj => obj.id_background !== background.id_background ? obj : background);
};
const onRowDeleted = (background) => {
    backgrounds.value = backgrounds.value.filter(obj => obj.id_background !== background.id_background);
};

const onImageChanged = async (e) => {
    const files = e.target.files;
    if (files && files.length > 0) {
        const fileName = files[0].name;
        const idxDot = fileName.lastIndexOf(".") + 1;
        const extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
        if (!(extFile == "jpg" || extFile == "jpeg" || extFile == "png")) {
            return MessageModal('error', 'Action failed', 'Only jpg/jpeg and png files are allowed!');
        }
        const reader = new FileReader();
        reader.onloadend = function () {
            var image = new Image();
            image.src = reader.result;
            image.onload = function () {
                if (this.width !== 1920 || this.height !== 1080) {
                    return MessageModal('error', 'Action failed', 'Image dimension is prohibited!');
                }
                imageRef.value.src = reader.result;
                obj.image = reader.result;
                $('#background-modal').modal('show');
            };
        }
        reader.readAsDataURL(files[0]);
        e.target.value = null;
    }
};
async function generateBackgrounds() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/backgrounds');
        backgrounds.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveBackground() {
    LoadingModal();
    try {
        let res = await createBackground(obj);
        onRowCreated(res.data.background);
        $('#background-modal').modal('hide');
        MessageModal('success', 'Success', res.data.message);
    } catch (error) {
        if (error.response.status === 422) {
            console.log(error)
        }
        return ErrorModal(error);
    }
}
async function createBackground(obj) {
    try {
        const res = await axios.post('/api/manage/backgrounds/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function deleteBackground(id_background) {
    $swal.fire({
        title: 'Are you sure you want to delete the background?',
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
                    '/api/manage/backgrounds/delete/' + id_background,
                );
                onRowDeleted(res.data.background);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
async function activeBackground(id_background) {
    LoadingModal();
    try {
        const res = await axios.put(
            '/api/manage/backgrounds/active/' + id_background,
        );
        onRowUpdated(res.data.new_background);
        if (res.data.old_background !== null) {
            onRowUpdated(res.data.old_background);

        }
        MessageModal('success', 'Success', res.data.message);
    } catch (error) {
        ErrorModal(error);
    }
}
</script>
