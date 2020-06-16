<script>
import { Bar } from "vue-chartjs";
import { CustomTooltips } from "@coreui/coreui-plugin-chartjs-custom-tooltips";

export default {
  extends: Bar,
  props: {
    statics: {
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
            label: "Year visits",
            backgroundColor: "#f87979",
            data: [
              this.statics.janVisits,
              this.statics.febVisits,
              this.statics.marVisits,
              this.statics.aprVisits,
              this.statics.mayVisits,
              this.statics.junVisits,
              this.statics.julVisits,
              this.statics.augVisits,
              this.statics.sepVisits,
              this.statics.octVisits,
              this.statics.novVisits,
              this.statics.decVisits
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
