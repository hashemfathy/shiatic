export const errorsMixin = {
  methods: {
    errorBags(errors) {
      if (Object.keys(errors).length > 0) {
        for (let error in errors) {
          this.addErrors(error, errors[error][0]);
        }
      }
    },
    addErrors(key, error) {
      this.$validator.errors.add({
        field: key,
        msg: error
      });
    },
    addServerSideValidationErrors(res) {
      if (res.response.status == 422) {
        this.errorBags(res.response.data.errors)
      }
    }
  }
}
