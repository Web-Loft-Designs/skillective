<template>
    <div class="chart-component chart-component-admin" :class="{'chart-component-v2':!isDashboard}">
        <div class="chart-component-top"  v-if="isDashboard">
            <select v-model="dashboardView">
                <option value="demographic">User Age</option>
                <option value="geographics">Geographics</option>
                <option value="other">Other</option>
            </select>

            <a href="/backend/reports/demographic">All Reports</a>
        </div>
        <div class="chart-component-body" :class="{'map-admin': dashboardView != 'demographic'}">
            <div class="middle" v-if="datacollection && dashboardView == 'demographic'">
                <bar-chart style="height: 292px;" :chart-data="datacollection" :options="option"></bar-chart>
            </div>
            <div class="middle" v-if="dashboardView == 'geographics'">
                <map-report></map-report>
            </div>
            <div class="middle" v-if="dashboardView == 'other'">
                <backend-other-report
                        :report-data="reportData"
                ></backend-other-report>
            </div>
        </div>
    </div>
</template>


<script>
    import $ from 'jquery'
    import BarChart from '../../BarChart'
    import MagnificPopupModal from './MagnificPopupModal';
    import siteAPI from '../../mixins/siteAPI.js';
    import skillectiveHelper from '../../mixins/skillectiveHelper.js';


    export default {
        components: {
            BarChart,
            MagnificPopupModal
        },
        mixins : [siteAPI, skillectiveHelper],
        props: ['isDashboard'],
        data () {
            return {
                datacollection: null,
                option: {},
                dashboardView: 'demographic',
                reportData: null,
            }
        },
        mounted () {
            this.fillData();
            axios.get('/api/admin/reports/other')
                .then(response => {
                    this.reportData = response.data.data;
                }).catch(error => {
                this.reportData = {}
            });
        },
        methods: {
            convertTo: function (n,d){
                if(n > 999) {
                    var x=(''+n).length,
                        p=Math.pow,
                        d=p(10,d)
                    x-=x%3
                    return Math.round(n*d/p(10,x))/d+" kMGTPE"[x/3]
                } else {
                    return n
                }
            },
            getData() {
                this.apiGet('/api/admin/reports/demographic');
            },
            componentHandleGetResponse(responseData) {
                var data = [];
                var dataMax = 0;
                var labels = [];
                responseData.data.map((item) => {
                    data.push(item.count);
                    labels.push(item.range);
                    if(dataMax < item.count) {
                        dataMax = item.count;
                    }
                });
                if(dataMax < 100) {
                    dataMax = 10;
                } else if(dataMax < 1000) {
                    dataMax = 100;
                } else if(dataMax > 1000) {
                    dataMax = 1000;
                }
                this.option = {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    showDatapoints: true,
                    tooltips: {
                        enabled: false,
                    },
                    scales: {
                        yAxes: [{
                            display: true,
                            drawTicks: false,
                            gridLines: {
                                drawTicks: false,
                                drawBorder: false,
                                zeroLineBorderDashOffset: 29,
                                borderDash: [1, 3],
                            },
                            ticks: {
                                padding: 10,
                                fontColor: '#aaaaaa',
                                fontSize: 14,
                                min: 0,
                                fontFamily: "Hind Vadodara",
                                stepSize: dataMax,
                                callback: (value, index, values) => {
                                    if(value === 0) {
                                        return '';
                                    } else {
                                        return this.convertTo(value,2);
                                    }

                                }
                            },
                        }],
                        xAxes: [{
                            fontColor: '#444',
                            fontSize: 14,
                            fontFamily: "Hind Vadodara",
                            gridLines: {
                                color: "rgba(0, 0, 0, 0)",
                            },
                            ticks: {
                                padding: 1,
                                fontColor: '#444',
                                fontSize: 14,
                                fontFamily: "Hind Vadodara",

                            },
                        }],
                    },
                    onAnimationComplete: function () {
                        var ctx = this.chart.ctx;
                        ctx.font = this.scale.font;
                        ctx.fillStyle = this.scale.textColor
                        ctx.textAlign = "center";
                        ctx.textBaseline = "bottom";
                        this.datasets.forEach(function (dataset) {
                            dataset.bars.forEach(function (bar) {
                                ctx.fillText(bar.value, bar.x, bar.y + 20);
                            });
                        })
                    }
                },
                    this.datacollection = {
                        labels: labels,
                        datasets: [
                            {
                                label: '$',
                                borderColor: '#f5a623',
                                backgroundColor: '#f5a623',
                                data: data
                            },
                        ]
                    }
            },
            fillData () {
                this.getData();
            }
        }
    }

</script>
