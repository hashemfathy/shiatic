<template>
  <div>
    <div class="card" style="border-radius: 20px;">
      <div class="card-header">
        <h3>{{client.name}}</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-6">
            <label for="name">
              name :
              <b>{{client.name}}</b>
            </label>
          </div>
          <div class="col-sm-6">
            <label for="code">
              Code:
              <b>{{client.code}}</b>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <label for="phone">
              Phone:
              <b>{{client.phone}}</b>
            </label>
          </div>
          <div class="col-sm-6">
            <label for="gender">
              Gender:
              <b>{{client.gender}}</b>
            </label>
          </div>
        </div>
      </div>
    </div>
    <hr />
    <a class="btn btn-warning" href @click.prevent v-b-modal.modal-center>add visit</a>
    <hr />
    <div class="card">
      <div class="card-header justify-content-between d-flex align-items-center">
        <h2 class="text-muted">Visits</h2>
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
              <a class="text-success" title="edit" :href="`/visits/${props.row.id}/edit`">
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
    <b-modal id="modal-center" @ok="submit" centered title="Add new visit">
      <input
        type="text"
        class="form-control"
        id="complaint"
        aria-describedby="emailHelp"
        placeholder="Enter complaint"
        name="visit_complaint"
        v-model="form.complaint"
        v-validate="'required'"
      />
      <span class="text-danger">{{ errors.first('visit_complaint') }}</span>
      <br />
      <div class="form-group">
        <label for="exampleFormControlSelect2">Select specialist</label>
        <select
          class="form-control"
          id="exampleFormControlSelect2"
          name="specialist"
          v-model="form.specialist_id"
          v-validate="'required'"
        >
          <option
            v-for="(specialist,i) in specialists"
            :value="specialist.id"
            :key="i"
          >{{specialist.name}}</option>
        </select>
      </div>
      <span class="text-danger">{{ errors.first('specialist') }}</span>
      <br />
      <input
        type="number"
        class="form-control"
        id="price"
        aria-describedby="emailHelp"
        placeholder="Enter price"
        name="visit_price"
        v-model="form.price"
        v-validate="'required'"
      />
      <span class="text-danger">{{ errors.first('visit_price') }}</span>
      <br />
      <input
        type="date"
        class="form-control"
        id="date"
        aria-describedby="emailHelp"
        placeholder="Enter date"
        name="visit_date"
        v-model="form.date"
      />
      <span class="text-danger">{{ errors.first('visit_date') }}</span>
      <br />
      <input
        type="text"
        class="form-control"
        id="hour"
        aria-describedby="emailHelp"
        placeholder="Enter hour"
        name="visit_hour"
        v-model="form.hour"
      />
      <span class="text-danger">{{ errors.first('visit_hour') }}</span>
      <br />
      <input
        type="text"
        class="form-control"
        id="duration"
        aria-describedby="emailHelp"
        placeholder="Enter duration"
        name="visit_duration"
        v-model="form.duration"
        v-validate="'required'"
      />
      <span class="text-danger">{{ errors.first('visit_duration') }}</span>
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
        client_name: this.client.name,
        complaint: "",
        price: "",
        date: "",
        hour: "",
        duration: "",
        specialist_id: "",
        client_id: this.client.id
      },
      columns: [
        {
          label: "Complaint",
          field: "complaint",
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
          label: "Specialist",
          field: "specialist.name",
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
          label: "Price",
          field: "price",
          html: true,
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Date",
          field: "date",
          html: true,
          sortable: true,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Hour",
          field: "hour",
          html: true,
          sortable: false,
          tdClass: "text-center",
          thClass: "text-center"
        },
        {
          label: "Duration",
          field: "duration",
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
  props: {
    client: {
      required: false,
      type: Object
    },
    specialists: {
      required: true
    }
  },
  mounted() {
    this.loadItems(
      `/visits/json?filter[client]=${this.client.id}&page=1&per_page=20`
    );
  },
  methods: {
    submit() {
      this.$validator.validateAll().then(res => {
        if (!res) {
          return;
        }
        if (res) {
          axios
            .post(`/visits`, {
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
              return (window.location.href = `/clients/${res.data.data.client_id}`);
            })
            .catch(error => this.addServerSideValidationErrors(error));
        }
      });
    }
  }
};
</script>
