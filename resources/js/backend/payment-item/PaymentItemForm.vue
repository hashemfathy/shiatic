<template>
  <div class="card" style="border-radius: 20px;">
    <div class="card-header">
      <h3>Payment Item</h3>
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
        name: ""
      }
    };
  },
  props: {
    item: {
      required: false,
      type: Object
    }
  },
  mounted() {
    if (this.item) {
      this.form = this.item;
    }
  },
  methods: {
    send() {
      this.$validator.validateAll().then(res => {
        if (res) {
          if (this.item) {
            axios
              .put(`/payment-items/${this.item.id}`, {
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
                return (window.location.href = `/payment-items/${this.item.id}`);
              })
              .catch(error => console.log(error));
          } else {
            axios
              .post(`/payment-items`, {
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
                return (window.location.href = `/payment-items/${res.data.data.id}`);
              })
              .catch(error => this.addServerSideValidationErrors(error));
          }
        }
      });
    }
  }
};
</script>
