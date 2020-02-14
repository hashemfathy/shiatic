export const vueGoodTableActions = (({ restoreUrl, softDeleteUrl, deleteUrl, acceptUrl, rejectUrl }) => ({
  methods: {
    selectionChanged(params) { },
    bulkDelete() {
      if (this.$refs["my-table"].selectedRows) {
        const selectedRows = this.$refs["my-table"].selectedRows.map(
          el => el.id
        );

        this.delete(selectedRows);
      }
    },
    bulkAccept() {
      if (this.$refs["my-table"].selectedRows) {
        const selectedRows = this.$refs["my-table"].selectedRows.map(
          el => el.id
        );

        this.acceptMulty(selectedRows);
      }
    },
    deleteItem(id) {
      this.delete([id]);
    },
    restoreItem(id) {
      this.restore([id]);
    },
    restore(items) {
      axios
        .post(restoreUrl, {
          ids: items
        })
        .then(() => {
          window.Toast.fire({
            type: "success",
            title: "restored !"
          }).then(() => {
            window.location.reload(true);
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    accept(id) {
      this.acceptMulty([id]);
    },
    acceptMulty(ids) {
      axios
        .post(acceptUrl, {
          ids
        })
        .then(() => {
          window.Toast.fire({
            type: "success",
            title: "accepted"
          }).then(() => {
            window.location.reload(true)
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    reject() {
      this.$validator.validateAll().then(res => {
        if (res) {
          axios
            .post(rejectUrl, {
              id: this.rejectedDirectory,
              rejection_reason: this.rejectionReason
            })
            .then(() => {
              window.Swal.fire({
                position: 'start-end',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.reload(true);
              });

            })
            .catch(err => {
              this.addServerSideValidationErrors(err)
            });
        }
      });
    },
    delete(items) {
      let url = deleteUrl;
      const text = "You won't be able to revert this!"
      window.SwalDelete(text)
        .then(result => {
          if (result.value) {
            axios
              .delete(url, {
                data: {
                  ids: items
                }
              })
              .then(() => {
                window.Toast.fire({
                  icon: "success",
                  title: "deleted !"
                }).then(() => {
                  window.location.reload(true)
                });
              })
              .catch(err => {
                console.log(err);
              });
          }
        });
    },
  }
}))
