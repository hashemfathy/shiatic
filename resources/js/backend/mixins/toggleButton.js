export const toggleButton = {
  methods: {
    toggleStatus(url) {
      axios
        .put(url)
        .then(res => { })
        .catch(error => {
          console.log(error);
        });
    }
  }
}
