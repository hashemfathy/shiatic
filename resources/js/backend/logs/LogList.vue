<template>
  <div class="card">
    <div class="card-header justify-content-between d-flex align-items-center">
      <h2 class="text-muted">Log</h2>
    </div>
    <div class="card-body">
      <vue-good-table
        ref="my-table"
        :columns="columns"
        :rows="rows"
        :select-options="selectOptions"
        mode="remote"
        :line-numbers="true"
        :pagination-options="paginationOptions"
        @on-page-change="onPageChange"
        @on-sort-change="onSortChange"
        @on-column-filter="onColumnFilter"
        @on-per-page-change="onPerPageChange"
        @on-selected-rows-change="selectionChanged"
        :totalRows="totalRecords"
        :isLoading.sync="isLoading"
        styleClass="table table-hover table-bordered vgt-table bordered "
      >
        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'buttons'">
            <a class="text-danger" @click.prevent="deleteLog(props.row.id)">
              <span class="pcoded-micon">
                <i class="icon-trash"></i>
              </span>
            </a>
          </span>
        </template>
        <div slot="emptystate">No Data</div>
        <div slot="selected-row-actions">
          <button type="button" class="btn btn-pinterest" @click="bulkDelete()">
            <span class="cib-pinterest btn-icon mr-2">
              <i class="icon-trash"></i>
            </span>Delete selected
          </button>
        </div>
      </vue-good-table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import queryString from "query-string";
import { vueGoodTable } from "../mixins/vueGoodTable";
import { vueGoodTableActions } from "../mixins/vueGoodTableActions";
import { errorsMixin } from "../mixins/errorsMixin";

export default {
  mixins: [
    vueGoodTable(),
    vueGoodTableActions({
      deleteUrl: "/cms/log/delete-selected"
    }),
    errorsMixin
  ],
  props: {},
  data() {
    return {
      isLoading: false,
      totalRecords: 0,
      serverParams: {
        page: 1, // what page I want to show
        per_page: 20, // how many items I'm showing per page
        mode: this.mode
      },
      selectOptions: {
        enabled: true,
        selectOnCheckboxOnly: true
      },
      paginationOptions: {
        enabled: true,
        mode: "pages",
        perPage: 20,
        perPageDropdown: [10, 20, 30, 40, 50],
        dropdownAllowAll: false
      },
      columns: [
        {
          label: "User",
          field: "user",
          filterOptions: {
            enabled: true, // enable filter for this column
            placeholder: "Search", // placeholder for filter input
            filterDropdownItems: [], // dropdown (with selected values) instead of text input
            filterFn: this.columnFilterFn, //custom filter function that
            trigger: "enter" //only trigger on enter not on keyup
          },
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Action",
          field: "action",
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Date",
          field: "updated_at",
          sortable: true,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Action",
          field: "buttons",
          html: true,
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        }
      ],
      rows: []
    };
  },
  mounted() {
    this.loadItems("/cms/logs/all");
  },
  methods: {
    deleteLog($log) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete"
      }).then(result => {
        if (result.value) {
          axios
            .delete("/cms/log/delete/" + $log, {
              log: $log
            })
            .then(() => {
              Toast.fire({
                position: "top-start",
                icon: "success",
                title: "deleted",
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.href = "/cms/log";
              });
            });
        }
      });
    }
  }
};
</script>   
