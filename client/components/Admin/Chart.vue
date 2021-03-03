<template>
  <client-only>
    <VueApexCharts
      type="line"
      height="350"
      :options="chartOptions"
      :series="series"
    />
  </client-only>
</template>
<script>
export default {
  components: {
    VueApexCharts: () => process.client ? import('vue-apexcharts') : null,
  },
  props: {
    name: {
      type: String,
      default: null
    },
    data: {
      type: Array,
      default: null
    },
    xaxisType: {
      type: String,
      default: 'category'
    }
  },
  computed: {
    series() {
      return [
          {
            name: 'Tổng đơn hàng',
            type: 'column',
            data: this.data.map(a => a.count)
          },
          {
            name: 'Tổng giá trị',
            type: 'line',
            data: this.data.map(a => a.amount)
          }
        ];
    },
    chartOptions() {
      let t = this;
      return {
        chart: {
          height: 350,
          type: 'line',
          fontFamily: 'Roboto, sans-serif',
          locales: [{
            "name": "vi",
            "options": {
              "months": ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
              "shortMonths": ["Thg 1", "Thg 2", "Thg 3", "Thg 4", "Thg 5", "Thg 6", "Thg 7", "Thg 8", "Thg 9", "Thg 10", "Thg 11", "Thg 12"],
              "days": ["Chủ nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"],
              "shortDays": ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
              "toolbar": {
                  "exportToSVG": "Tải file SVG",
                  "exportToPNG": "Tải file PNG",
                  "exportToCSV": "Tải file CSV",
                  "menu": "Menu",
                  "selection": "Chọn khoảng",
                  "selectionZoom": "Phóng to khoảng đã chọn",
                  "zoomIn": "Phóng to",
                  "zoomOut": "Thu nhỏ",
                  "pan": "Panning",
                  "reset": "Reset Zoom"
              }
            }
          }],
          defaultLocale: "vi"
        },
        legend: {
          position: 'top'
        },
        stroke: {
          width: [0, 4]
        },
        title: {
          text: this.name
        },
        dataLabels: {
          enabled: true,
          enabledOnSeries: [1],
          formatter: function(value) {
            return t.moneyFormat(value);
          },
        },
        labels: this.data.map(a => a.date),
        xaxis: {
          type: this.xaxisType
        },
        yaxis: [
          {
            title: {
              text: 'Tổng đơn hàng',
            },
          },
          {
            opposite: true,
            title: {
              text: 'Tổng giá trị'
            },
            labels: {
              formatter: function (value) {
                return t.moneyFormat(value);
              }
            },
          }
        ]
      }
    },
  },
  methods: {
    moneyFormat(number) {
      return (
        new Intl.NumberFormat("vi-VN", {
          maximumFractionDigits: 0
        }).format(number) + " ₫"
      );
    }
  },
}
</script>
