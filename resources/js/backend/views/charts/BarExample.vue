<script>
import { Bar } from "vue-chartjs";
import { CustomTooltips } from "@coreui/coreui-plugin-chartjs-custom-tooltips";

export default {
  extends: Bar,
  props: {
    statics: {
      required: true
    },
    title: {
      required: true
    }
  },
  mounted() {
    // Overwriting base render method with actual data.
    this.renderChart(
      {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ],
        datasets: [
          {
            label: this.title,
            backgroundColor: "#f87979",
            data: [
              this.statics.jan,
              this.statics.feb,
              this.statics.mar,
              this.statics.apr,
              this.statics.may,
              this.statics.jun,
              this.statics.jul,
              this.statics.aug,
              this.statics.sep,
              this.statics.oct,
              this.statics.nov,
              this.statics.dec
            ]
          }
        ]
      },
      {
        responsive: true,
        maintainAspectRatio: true,
        tooltips: {
          enabled: false,
          custom: CustomTooltips,
          intersect: true,
          mode: "index",
          position: "nearest",
          callbacks: {
            labelColor: function(tooltipItem, chart) {
              return {
                backgroundColor:
                  chart.data.datasets[tooltipItem.datasetIndex].backgroundColor
              };
            }
          }
        }
      }
    );
  }
};
</script>
