<template>
  <div class="card" style="border-radius: 20px;">
    <div class="card-header">
      <h3>{{visit.client_name}} visits</h3>
    </div>
    <div class="card-body">
      <form @submit.prevent="send">
        <div class="form-group">
          <label for="complaint">Complaint</label>
          <input
            type="text"
            class="form-control"
            v-model="form.complaint"
            aria-describedby="emailHelp"
            placeholder="Enter complaint"
            name="complaint"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('complaint') }}</span>
        </div>
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
        <div class="form-group">
          <label for="price">Price</label>
          <input
            type="number"
            class="form-control"
            v-model="form.price"
            id="price"
            name="price"
            aria-describedby="emailHelp"
            placeholder="Enter price"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('price') }}</span>
        </div>
        <div class="form-group">
          <label for="date">Date</label>
          <input
            type="date"
            class="form-control"
            v-model="form.date"
            id="date"
            name="date"
            aria-describedby="emailHelp"
            placeholder="Enter date"
          />
          <span class="text-danger">{{ errors.first('date') }}</span>
        </div>
        <div class="form-group">
          <label for="hour">Hour</label>
          <input
            type="text"
            class="form-control"
            v-model="form.hour"
            id="hour"
            name="hour"
            aria-describedby="emailHelp"
            placeholder="Enter hour"
          />
          <span class="text-danger">{{ errors.first('hour') }}</span>
        </div>
        <div class="form-group">
          <label for="duration">Duration</label>
          <input
            type="text"
            class="form-control"
            v-model="form.duration"
            id="duration"
            name="duration"
            aria-describedby="emailHelp"
            placeholder="Enter duration"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('duration') }}</span>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>
</template>
<script>
import { errorsMixin } from "../mixins/errorsMixin";
export default {
  mixins: [errorsMixin],
  components: {},
  data() {
    return {
      form: {
        client_name: this.visit.client_name,
        complaint: "",
        price: "",
        date: "",
        hour: "",
        duration: "",
        specialist_id: "",
        client_id: this.visit.client_id
      }
    };
  },
  props: {
    visit: {
      required: false,
      type: Object
    },
    specialists: {
      required: true
    }
  },
  mounted() {
    if (this.visit) {
      this.form = this.visit;
    }
  },
  methods: {
    send() {
      this.$validator.validateAll().then(res => {
        if (res) {
          if (this.visit) {
            axios
              .put(`/visits/${this.visit.id}`, {
                ...this.form
              })
              .then(() => {
                Toast.fire({
                  position: "top-end",
                  icon: "success",
                  width: 200,
                  title: "saved",
                  showConfirmButton: false,
                  timer: 1500
                });
              })
              .then(() => {
                return (window.location.href = "/visits");
              })
              .catch(error => this.addServerSideValidationErrors(error));
          } else {
            axios
              .post(`/visits`, {
                ...this.form
              })
              .then(() => {
                Toast.fire({
                  position: "top-end",
                  icon: "success",
                  width: 200,
                  title: "saved",
                  showConfirmButton: false,
                  timer: 1500
                });
              })
              .then(() => {
                return (window.location.href = "/visits");
              })
              .catch(error => this.addServerSideValidationErrors(error));
          }
        }
      });
    }
  }
};
</script>
