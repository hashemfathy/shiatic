<template>
  <div class="card">
    <div class="card-header justify-content-between d-flex align-items-center">
      <h2 class="text-muted">Clients</h2>
      <b-button class="ml-2" variant="outline-primary" href="/clients/create">Create</b-button>
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
            <a class="text-primary" title="show" :href="`/clients/${props.row.id}`">
              <span class="pcoded-micon">
                <i class="fa fa-eye"></i>
              </span>
            </a>
            <a class="text-success" title="edit" :href="`/clients/${props.row.id}/edit`">
              <span class="pcoded-micon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
            </a>
          </span>
          <span v-else>{{props.formattedRow[props.column.field]}}</span>
        </template>
        <div slot="emptystate">No Data</div>
        <div slot="selected-row-actions"></div>
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
  mixins: [vueGoodTable(), vueGoodTableActions({}), errorsMixin],
  props: {},
  data() {
    return {
      columns: [
        {
          label: "Name",
          field: "name",
          filterOptions: {
            enabled: true, // enable filter for this column
            placeholder: "Search", // placeholder for filter input
            filterDropdownItems: [], // dropdown (with selected values) instead of text input
            filterFn: this.columnFilterFn, //custom filter function that
            trigger: "enter" //only trigger on enter not on keyup
          },
          sortable: true,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Code",
          field: "code",
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
          label: "Gender",
          field: "gender",
          html: true,
          sortable: true,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Phone",
          field: "phone",
          html: true,
          sortable: false,
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
      ]
    };
  },
  mounted() {
    this.loadItems("/clients/json");
  },
  methods: {}
};
</script>  
