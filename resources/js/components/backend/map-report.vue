<template>
    <div>
     <div id="map"></div>
    </div>
</template>

<script>
    import $ from 'jquery'
    require('jvectormap');
    require('../../jquery-jvectormap-us-aea.js');
    import siteAPI from '../../mixins/siteAPI.js';
    import skillectiveHelper from '../../mixins/skillectiveHelper.js';
    export default {
        mixins : [siteAPI, skillectiveHelper],
		data() {
			return {
                // reportView:'geographic'
			}
		},
		methods: {
            getData() {
                this.apiGet('/api/admin/reports/geographic');
            },
            componentHandleGetResponse(responseData) {

                $('#map').vectorMap({
                    map: 'us_aea',
                    backgroundColor: 'transparent',
                    zoomButtons: false,
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            'fill': '#97d135',
                            "fill-opacity": 0.5,
                            'stroke': 'none',
                            "stroke-width": 0,
                            "stroke-opacity": 1
                        },
                        hover: {
                            'fill-color': '#97d135',
                            "fill-opacity": 1,
                            "stroke-opacity": 1
                        },
                    },
                    onRegionTipShow: function (e, tip, code)  {
                        var current = responseData.data.filter((item) => {
                            return item.state == code.split('-')[1]
                        })
                        tip.html(tip.html() + '<strong>'+current[0].count+'('+current[0].percent+'%)</strong>')
                    }
                });
                responseData.data.map((item) => {
                    if(item.count === 0) {
                        $('#map [data-code="US-'+item.state+'"]').addClass('disable-region');
                    }
                })
            },
		},
        mounted() {
            this.getData();
        }
	}
</script>