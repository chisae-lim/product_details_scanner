<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mt-1">{{ props.title }}</h3>
            <div class="card-tools">
                <div class="input-group input-group">
                    <input v-model="filter" type="text" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="text-nowrap text-center table-valign-middle table table-head-fixed table-bordered table-hover">
                <thead>
                    <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <th v-for="header in headerGroup.headers" :key="header.id"
                            :class="{ 'can-sort': header.column.getCanSort() }"
                            @click="header.column.getToggleSortingHandler()?.($event)">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                            {{
                                { asc: ' ↓', desc: ' ↑' }[header.column.getIsSorted()]
                            }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in table.getRowModel().rows" :key="row.id">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <div class="row align-items-center">
                <div class="col-sm">
                    <span>Page {{ table.getState().pagination.pageIndex
                        + 1 }} of {{ table.getPageCount() }}
                        - {{ table.getFilteredRowModel().rows.length }} results</span>
                </div>

                <div class="col-sm-auto">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-default">
                                        Size
                                    </button>
                                </div>
                                <select class="form-control" @change="table.setPageSize($event.target.value)">
                                    <option value="5">5</option>
                                    <option selected value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option :value="table.getFilteredRowModel().rows.length">Max
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <button @click="table.setPageIndex(0)" type="button" class="btn btn-default">
                                    <i class="fas fa-angle-double-left"></i>
                                </button>
                                <button @click="table.previousPage()" :disabled="!table.getCanPreviousPage()" type="button"
                                    class="btn btn-default">
                                    <i class="fas fa-angle-left"></i>
                                </button>
                                <button @click="table.nextPage()" :disabled="!table.getCanNextPage()" type="button"
                                    class="btn btn-default">
                                    <i class="fas fa-angle-right"></i>
                                </button>
                                <button @click="table.setPageIndex(table.getPageCount() - 1)" type="button"
                                    class="btn btn-default">
                                    <i class="fas fa-angle-double-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.can-sort {
    cursor: pointer;
    user-select: none;
}
</style>
<script setup>
import { ref, computed, onBeforeUpdate } from 'vue';
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    getFilteredRowModel,
} from '@tanstack/vue-table';
const props = defineProps({
    title: String,
    data: Array,
    columns: Array,
})
const sorting = ref([]);
const filter = ref('');
const currentPage = ref(0);
const pageSize = ref(10);
const table = computed(() => useVueTable({
    data: props.data,
    columns: props.columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    state: {
        get sorting() {
            return sorting.value
        },
        get globalFilter() {
            return filter.value
        }
    },
    initialState: {
        pagination: {
            pageIndex: currentPage.value,
            pageSize: pageSize.value,
        }
    },
    onSortingChange: updaterOrValue => {
        sorting.value = typeof updaterOrValue === 'function' ? updaterOrValue(sorting.value) : updaterOrValue
    }
}));
onBeforeUpdate(() => {
    currentPage.value = table.value.getState().pagination.pageIndex;
    pageSize.value = table.value.getState().pagination.pageSize;
});
</script>