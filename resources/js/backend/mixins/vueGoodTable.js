import queryString from "query-string";
export const vueGoodTable = () => ({
  data() {
    return {
      indexUrl: '',
      isLoading: false,
      totalRecords: 0,
      serverParams: {
        page: 1, // what page I want to show
        per_page: 20, // how many items I'm showing per page
        mode: this.mode
      },
      selectOptions: {
        enabled: true,
        selectOnCheckboxOnly: true
      },
      paginationOptions: {
        enabled: true,
        mode: "pages",
        perPage: 20,
        perPageDropdown: [10, 20, 30, 40, 50],
        dropdownAllowAll: false
      },
      rows: []
    }
  },
  methods: {
    updateParams(newProps) {
      this.serverParams = Object.assign({}, this.serverParams, newProps);
    },

    onPageChange(params) {
      this.updateParams({ page: params.currentPage });
      this.loadItems(this.indexUrl);
    },

    onPerPageChange(params) {
      this.updateParams({
        per_page: params.currentPerPage,
        page: 1
      });
      this.loadItems(this.indexUrl);
    },

    onSortChange(params) {
      const field = params[0].field;
      const type = params[0].type === "asc" ? "" : "-";
      this.updateParams({
        sort: type + field
      });
      this.loadItems(this.indexUrl);
    },

    onColumnFilter(params) {
      if (params.columnFilters) {
        for (let filter in params.columnFilters) {
          this.updateParams({
            [`filter[${filter}]`]: params.columnFilters[filter]
          });
          this.loadItems(this.indexUrl);
        }
      }
    },
    // load items is what brings back the rows from server
    loadItems(url) {
      this.indexUrl = url;
      this.getFromServer(url, this.serverParams).then(response => {
        this.totalRecords = response.meta.total;
        this.rows = response.data;
      });
    },
    getFromServer(url, serverParams) {
      return new Promise((resolve, reject) => {
        axios
          .get(
            url + '?' +
            queryString.stringify(serverParams)
          )
          .then(response => {
            resolve(response.data);
          })
          .catch(error => {
            console.log(error);
          });
      });
    }
  }
});
