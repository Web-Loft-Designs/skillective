<template>

    <div class="time-picker-wrapper"  v-click-outside="closeIt">
        <input @click="openIt" type="text" class="form-control" readonly v-model="priceApply" :placeholder="placeholder">
        <input type="hidden" v-model="priceFrom" name="price_from">
        <input type="hidden" v-model="priceTo" name="price_to">
        <div class="time-picker-popup" v-if="showIt">
            <p><strong>Price range</strong>{{inputText}}</p>
            <vue-slider
                    @change="changeTime(price[0], price[1])"
                    :tooltip="'none'"
                    :min="0"
                    :max="1000"
                    :min-range="1"
                    :enable-cross="false"
                    v-model="price"
                    :height="2"
            ></vue-slider>

            <div class="time-picker-footer">
                <a href="#" @click.prevent="clearVal()" class="btn-clear">Clear</a>
                <a href="#" @click.prevent="applyVal()" class="btn-apply">Apply</a>
            </div>
        </div>

    </div>

</template>


<script>
	import _ from 'underscore'
	import $ from 'jquery'
    import ClickOutside from 'vue-click-outside'

	export default {
		name: 'time-range',

		props: ['priceFromProp','priceToProp','hidePlaceholder'],
        directives: {
            ClickOutside
        },

        computed: {
            inputText: function () {
                return '$'+this.price[0]+'  –  $'+this.price[1];
            }
        },

		data () {
            return {
                priceApply: '',
                price: [20, 50],
                priceFrom: '',
                priceTo: '',
                showIt: false,
                placeholder: 'Choose time range'
            }
		},
        watch: {
            priceFromProp: function () {
                console.log(1)
                setTimeout(() => {
                    if (this.priceToProp != ''|| this.priceFromProp != 0) {
                        console.log(this.priceFromProp)
                        this.priceApply = '$'+this.priceFromProp+'  –  $'+this.priceToProp;
                        this.priceFrom = this.priceFromProp;
                        this.priceTo = this.priceToProp;
                        this.price = [this.priceFromProp, this.priceToProp]
                    }
                },10)
            }
        },

		mounted () {
            if(this.priceFromProp != 0 || this.priceFromProp != '') {
                console.log(this.priceFromProp)
                this.priceApply = '$'+this.priceFromProp+'  –  $'+this.priceToProp;
                this.priceFrom = this.priceFromProp;
                this.priceTo = this.priceToProp;
                this.price =  [this.priceFromProp, this.priceToProp]
            }

            if(this.hidePlaceholder) {
                this.placeholder = '';
            }
		},

		methods: {
            clearVal: function () {
                this.showIt = false;
                this.priceApply = '';
                this.priceFrom = 0;
                this.priceTo = 0;
                this.$emit('changeTimeModel', [0, 0]);
            },
            applyVal: function () {
                this.priceApply = '$'+this.price[0]+'  –  $'+this.price[1];
                this.priceFrom = this.price[0];
                this.priceTo = this.price[1];
                this.showIt = false;
                this.$emit('changeTimeModel', [this.priceFrom, this.priceTo]);
            },
            changeTime: function (val1, val2) {
                this.priceFrom = val1;
                this.priceTo = val2;
            },
            closeIt: function () {
                if(this.showIt && this.triggerFlag) {
                    this.showIt = false;
                    this.triggerFlag = false;
                }
            },
            openIt: function () {
                this.showIt = true;
                this.triggerFlag = true;
            },
		}
	}

</script>
