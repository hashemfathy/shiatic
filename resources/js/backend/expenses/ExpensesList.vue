<template>
  <div>
    <a class="btn btn-warning" href @click.prevent v-b-modal.modal-center>add expenses</a>
    <hr />
    <div class="card">
      <div class="card-header justify-content-between d-flex align-items-center">
        <h2 class="text-muted">expenses</h2>
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
              <a class="text-success" title="edit" :href="`/expenses/${props.row.id}/edit`">
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
    <b-modal id="modal-center" @ok="submit" centered title="Add new expense">
      <div class="form-group">
        <label for="exampleFormControlSelect2">Select payment</label>
        <select
          class="form-control"
          id="exampleFormControlSelect2"
          name="payment_item"
          v-model="form.payment_item_id"
          v-validate="'required'"
        >
          <option
            v-for="(payment_item,i) in payment_items"
            :value="payment_item.id"
            :key="i"
          >{{payment_item.name}}</option>
        </select>
        <span class="text-danger">{{ errors.first('payment_item') }}</span>
      </div>
      <br />
      <input
        type="number"
        class="form-control"
        id="value"
        aria-describedby="emailHelp"
        placeholder="Enter value"
        name="value"
        v-model="form.value"
        v-validate="'required'"
      />
      <span class="text-danger">{{ errors.first('value') }}</span>
    </b-modal>
  </div>
</template>
<script>
import queryString from "query-string";
import { vueGoodTable } from "../mixins/vueGoodTable";
import { vueGoodTableActions } from "../mixins/vueGoodTableActions";
import { errorsMixin } from "../mixins/errorsMixin";
export default {
  mixins: [vueGoodTable(), vueGoodTableActions({}), errorsMixin],
  data() {
    return {
      form: {
        value: "",
        payment_item_id: ""
      },
      columns: [
        {
          label: "Payment Item",
          field: "payment_item.name",
          filterOptions: {
            enabled: false, // enable filter for this column
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
          label: "Value",
          field: "value",
          filterOptions: {
            enabled: false, // enable filter for this column
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
          field: "buttons",
          html: true,
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        }
      ]
    };
  },
  props: {
    payment_items: {
      required: true
    }
  },
  mounted() {
    this.loadItems(`/expenses/jsonToday?page=1&per_page=20`);
  },
  methods: {
    submit() {
      this.$validator.validateAll().then(res => {
        if (!res) {
          return;
        }
        if (res) {
          axios
            .post(`/expenses`, {
              ...this.form
            })
            .then(res => {
              Toast.fire({
                position: "top-end",
                icon: "success",
                width: 200,
                title: "saved",
                showConfirmButton: false,
                timer: 1500
              });
              return (window.location.href = `/expenses`);
            })
            .catch(error => this.addServerSideValidationErrors(error));
        }
      });
    }
  }
};
</script>
