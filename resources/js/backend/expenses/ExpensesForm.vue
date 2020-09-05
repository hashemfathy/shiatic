<template>
  <div class="card" style="border-radius: 20px;">
    <div class="card-header">
      <h3>Expenses</h3>
    </div>
    <div class="card-body">
      <form @submit.prevent="send">
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
        <br />
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
        value: "",
        payment_item_id: ""
      }
    };
  },
  props: {
    payment_value: {
      required: false,
      type: Object
    },
    payment_items: {
      required: true
    }
  },
  mounted() {
    if (this.payment_value) {
      this.form = this.payment_value;
    }
  },
  methods: {
    send() {
      this.$validator.validateAll().then(res => {
        if (res) {
          if (this.payment_value) {
            axios
              .put(`/expenses/${this.payment_value.id}`, {
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
                return (window.location.href = `/expenses`);
              })
              .catch(error => console.log(error));
          } else {
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
        }
      });
    }
  }
};
</script>
