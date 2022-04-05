import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
    extends: Bar,
    mixins: [reactiveProp],
    props: ['options'],
    mounted () {
        this.addPlugin({
            afterDraw: function(chartInstance) {
                if (chartInstance.config.options.showDatapoints) {
                    var helpers = Chart.helpers;
                    var ctx = chartInstance.chart.ctx;
                    var fontColor = helpers.getValueOrDefault(chartInstance.config.options.showDatapoints.fontColor, chartInstance.config.options.defaultFontColor);

                    // render the value of the chart above the bar
                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'center';
                    ctx.fillStyle = fontColor;
        
                    chartInstance.data.datasets.forEach(function (dataset, index) {
                      for (var i = 0; i < dataset.data.length; i++) {
                          if(dataset.label !== "goal") {
                              var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
                              var scaleMax = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
                              var yPos = (scaleMax - model.y) / scaleMax >= 0.93 ? model.y + 20 : model.y - 5;
                              ctx.fillStyle = "#444";

                              if(dataset.data[i] && dataset.data[i] > 0){
                                ctx.fillText('$' + dataset.data[i], model.x, yPos);
                              }
                            
                          } else {
                              if(i < 1) {
                                  if(dataset.data[i] != 0) {
                                      var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
                                      var scaleMax = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
                                      var yPos = (scaleMax - model.y) / scaleMax >= 0.93 ? model.y : model.y - 5;
                                      ctx.fillStyle = '#fff'
                                      ctx.fillRect((ctx.canvas.clientWidth / 2) - 5 ,yPos - 2, 40, 12);
                                      ctx.fillStyle = window.colorCustom;
                                      ctx.fillText('$ '+dataset.data[i], (ctx.canvas.clientWidth / 2) + 15, yPos + 8);
                                  }
                              }
                          }
                      }
                    });
                }
            }
        })
        this.renderChart(this.chartData, this.options)
    }
}