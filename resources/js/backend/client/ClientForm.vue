<template>
  <div class="card" style="border-radius: 20px;">
    <div class="card-header">
      <h3>Client</h3>
    </div>
    <div class="card-body">
      <form @submit.prevent="send">
        <div class="form-group">
          <label for="name">name</label>
          <input
            type="text"
            class="form-control"
            v-model="form.name"
            aria-describedby="emailHelp"
            placeholder="Enter name"
            name="name"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('name') }}</span>
        </div>
        <div class="form-group">
          <label for="phone">phone</label>
          <input
            type="number"
            class="form-control"
            v-model="form.phone"
            id="phone"
            name="phone"
            aria-describedby="emailHelp"
            placeholder="Enter phone"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('phone') }}</span>
        </div>
        <div class="form-group">
          <label for="code">code</label>
          <input
            type="number"
            class="form-control"
            v-model="form.code"
            id="code"
            name="code"
            aria-describedby="emailHelp"
            placeholder="Enter code"
            v-validate="'required'"
          />
          <span class="text-danger">{{ errors.first('code') }}</span>
        </div>
        <div class="form-group">
          <label for="exampleFormControlSelect1">Select Gender</label>
          <select class="form-control" id="exampleFormControlSelect1" v-model="form.gender">
            <option v-for="(gender,i) in genders" :value="gender" :key="i">{{gender}}</option>
          </select>
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
      genders: ["male", "female"],
      form: {
        name: "",
        phone: "",
        code: "",
        gender: ""
      }
    };
  },
  props: {
    client: {
      required: false,
      type: Object
    }
  },
  mounted() {
    if (this.client) {
      this.form = this.client;
    }
  },
  methods: {
    send() {
      this.$validator.validateAll().then(res => {
        if (res) {
          if (this.client) {
            axios
              .put(`/clients/${this.client.id}`, {
                ...this.form
              })
              .then(() => {
                console.log(res);
                Toast.fire({
                  position: "top-end",
                  icon: "success",
                  width: 200,
                  title: "saved",
                  showConfirmButton: false,
                  timer: 1500
                });
                return (window.location.href = `/clients/${this.client.id}`);
              })
              .catch(error => console.log(error));
          } else {
            axios
              .post(`/clients`, {
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
                return (window.location.href = `/clients/${res.data.data.id}`);
              })
              .catch(error => this.addServerSideValidationErrors(error));
          }
        }
      });
    }
  }
};
</script>
