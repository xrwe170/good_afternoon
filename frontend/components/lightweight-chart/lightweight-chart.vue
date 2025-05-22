<template>
	<view class="">
		<view class="times" >
			<text v-for="(item,index) in intervals" class="item font-size-22" :class="{active:index == intervalIndex}"
				@click="clickInterval(item,index)">{{item.name}}</text>
		</view>
		<view :chartInfo="chartInfo" :change:chartInfo="lightweightCharts.refreshData" class="overflow-hidden">
			<view class="d-flex font-size-22 px-30 py-16">
				<view style="color: rgba(255, 0, 0, 1);">MA5: {{total5}}</view>
				<view class="ml-16" style="color: rgba(25, 117, 0, 1);">MA10: {{total10}}</view>
			</view>
			<view id="chart" class="position-relative">
			</view>
			
			<view class="d-flex font-size-22 px-30 py-16">
				<view style="color: rgba(255, 0, 0, 1);">24H:{{volume}}</view>
			</view>
			<view id="gram" class="position-relative" style="border-top: 2rpx solid #122137;">
			</view>
			
			<view class="d-flex font-size-22 px-30 py-16">
				<view style="color: rgba(255, 0, 0, 1);">DIF:{{dif}}</view>
				<view class="ml-16" style="color: rgba(25, 117, 0, 1);">DEA:{{dea}}</view>
				<view class="ml-16 text-white">MACD:{{macd}}</view>
			</view>
			<view id="macd" class="position-relative" style="border-top: 2rpx solid #122137;">
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "lightweight-charts",
		props: {
			currency_name: {
				type: String,
				default: "BTC"
			},
			legal_name: {
				type: String,
				default: "USDT"
			},
			row: {
				type: String,
				default: '40'
			},
			showMACD: {
				type: Boolean,
				default: true
			},
			//是否需要
			needMarketDepth:{
				type:Boolean,
				default:false
			},
			//是否需要秒合约列表
			needSecondsList:{
				type:Boolean,
				default:false
			}
		},
		data() {
			return {
				intervals: [{
						name: 'Time',
						value: '1min',
						time: 1 //天
					},
					{
						name: '1min',
						value: '1min',
						time: 1
					},
					{
						name: '5min',
						value: '5min',
						time: 5
					},
					{
						name: '30min',
						value: '30min',
						time: 30
					},
					{
						name: '1h',
						value: '60min',
						time: 60
					},
					{
						name: '1day',
						value: '1D',
						time: 360
					},
					{
						name: '1week',
						value: '1W',
						time: 1800
					},
					{
						name: '1mon',
						value: '1M',
						time: 10800
					},
				],
				chartInfo: {
					candleData:[],
					realTimePrice:0,
					gramData:[],
					ma5:[],
					ma10:[],
					macd:{
						macd:[],
						dif:[],
						dea:[]
					}
				},
				intervalIndex: 0,
				open: '',
				close: '',
				high: '',
				low: '',
				lastData: null,
				total5: '',
				total10: '',
				dif: '',
				dea: '',
				macd: '',
				volume: '',
				period: '',
				socket: null,
				lastHigh:0,
			};
		},
		mounted() {
			this.getData()
		},
		methods: {
			clickInterval(item, index) {
				this.intervalIndex = index
				this.getData()
			},
			getData() {
				let {
					code,
					intervalIndex,
					intervals,
					row,
					symbol
				} = this
				// 当前时间的时间戳
				const to = Date.parse(new Date()) / 1000
				const from = Date.parse(new Date()) / 1000 - intervals[intervalIndex].time * 24 * 60 * 60
				let period = intervals[intervalIndex].value
				this.period = period
				uni.showLoading()
				this.$u.api.market.getHistoryData(from, to, symbol, period).then(res => {
					if(!res.data.length){
						this.startSocket()
						return false
					} 
					const data = res.data
					let candleData = []
					let gramData = []
					let ohlc = []
					let volume = []
					let maset = [5, 10, 20, 30]
					let ma = []
					let dataLength = data.length
					res.data.forEach((item, i) => {
						item.time = item.time
						candleData.push({
							time: item.time / 1000,
							open: item.open,
							high: item.high,
							low: item.low,
							close: item.close,
							value: item.close,
						})
						gramData.push({
							time: item.time / 1000,
							value: item.volume,
							color: item.close - item.open > 0 ? 'rgba(2,194,137,.5)' : 'rgba(232,109,67,.5)'
						})
						maset.forEach((value, index) => {
							if (typeof ma['ma' + value] == "undefined") {
								ma['ma' + value] = [];
							}
							if (typeof ma[value + 'total'] == "undefined") {
								ma[value + 'total'] = 0;
							}
							if (i < value) {
								ma[value + 'total'] += Number(data[i].close);
								ma['ma' + value].push({
									time: Number(data[i].time / 1000),
									value: item.open,
								});
							} else {
								ma[value + 'total'] += (Number(data[i].close) - Number(data[i -
									value].close));
								ma['ma' + value].push({
									time: Number(data[i].time / 1000),
									value: ma[value + 'total'] /
										value
								});
							}
						})

					})

					const macd = this.calcMacd(data)
					this.dif = macd.dif[macd.dif.length - 1].value.toFixed(2)
					this.dea = macd.dea[macd.dea.length - 1].value.toFixed(2)
					this.macd = macd.macd[macd.macd.length - 1].value.toFixed(2)

					this.lastData = candleData[dataLength - 1]

					this.total5 = (ma['5total'] / 5).toFixed(2)
					this.total10 = (ma['10total'] / 10).toFixed(2)

					this.open = candleData[dataLength - 1].open
					this.high = candleData[dataLength - 1].high
					this.low = candleData[dataLength - 1].low
					this.close = candleData[dataLength - 1].close
					this.volume = gramData[gramData.length - 1].value

					setTimeout(() => {
						this.chartInfo = {
							realTimePrice:{},
							candleData,
							gramData,
							ma5: ma['ma5'],
							ma10: ma['ma10'],
							macd,
							timer: intervals[intervalIndex].time
						}
						uni.hideLoading()
						this.startSocket()
					}, 500)
				})
			},
			calcMacd(data) {
				var macd;
				var calcEMA, calcDIF, calcDEA, calcMACD;

				/*
				 * 计算EMA指数平滑移动平均线，用于MACD
				 * @param {number} n 时间窗口
				 * @param {array} data 输入数据
				 * @param {string} field 计算字段配置
				 */
				calcEMA = function(n, data, field) {
					var i, l, ema, a;
					a = 2 / (n + 1);
					if (field) {
						//二维数组
						ema = [data[0][field]];
						for (i = 1, l = data.length; i < l; i++) {
							ema.push(a * data[i][field] + (1 - a) * ema[i - 1]);
						}
					} else {
						//普通一维数组
						ema = [data[0]];
						for (i = 1, l = data.length; i < l; i++) {
							ema.push(a * data[i] + (1 - a) * ema[i - 1]);
						}
					}
					return ema;
				};


				/*
				 * 计算DIF快线，用于MACD
				 * @param {number} short 快速EMA时间窗口
				 * @param {number} long 慢速EMA时间窗口
				 * @param {array} data 输入数据
				 * @param {string} field 计算字段配置
				 */
				calcDIF = function(short, long, data, field) {
					var i, l, dif, emaShort, emaLong;
					dif = [];
					emaShort = calcEMA(short, data, field);
					emaLong = calcEMA(long, data, field);
					for (i = 0, l = data.length; i < l; i++) {
						dif.push(emaShort[i] - emaLong[i]);
					}
					return dif;
				};


				/*
				 * 计算DEA慢线，用于MACD
				 * @param {number} mid 对dif的时间窗口
				 * @param {array} dif 输入数据
				 */
				calcDEA = function(mid, dif) {
					return calcEMA(mid, dif);
				};

				/*
				 * 计算MACD
				 * @param {number} short 快速EMA时间窗口
				 * @param {number} long 慢速EMA时间窗口
				 * @param {number} mid dea时间窗口
				 * @param {array} data 输入数据
				 * @param {string} field 计算字段配置
				 */
				calcMACD = function(short, long, mid, data, field) {
					var i, l, dif, dea, macd, result;
					result = {};
					macd = [];
					dif = calcDIF(short, long, data, field);
					dea = calcDEA(mid, dif);
					for (i = 0, l = data.length; i < l; i++) {
						macd.push((dif[i] - dea[i]) * 2);
					}
					result.dif = dif;
					result.dea = dea;
					result.macd = macd;
					return result;
				};

				macd = calcMACD(12, 26, 9, data, 'close');


				macd.dif = macd.dif.map((item, index) => {
					return {
						time: Number(data[index]['time']) / 1000,
						value: item
					}
				})
				macd.dea = macd.dea.map((item, index) => {
					return {
						time: Number(data[index]['time']) / 1000,
						value: item
					}
				})
				macd.macd = macd.macd.map((item, index) => {
					return {
						time: Number(data[index]['time']) / 1000,
						value: item,
						color: item > 0 ?  'rgba(2,194,137,.5)' : 'rgba(232,109,67,.5)'
					}
				})

				return macd
			},
			startSocket() {
				
				const _this = this
				const {
					currency_name,
					legal_name,
					period
				} = this
				
				this.$store.state.socket.on('daymarket', res => {
					this.$emit('getSokcetData',res)
					
					if (res.currency_name == currency_name && res.legal_name == legal_name ) {
						this.chartInfo.realTimePrice = {
							...this.lastData,
							close:res.close
						}
					}
				});
				
				this.$store.state.socket.on('kline', res => {
					if (res.currency_name == currency_name && res.legal_name == legal_name ) {
						_this.setSocketData(res)
					}
				});
				
				
			},
			setSocketData(data) {
				
				const {
					intervalIndex,
					intervals,
					row,
					period
				} = this
				
				const calcTime = intervals[intervalIndex].time * 60000
				const time = Number(data.time)
				
				const lastData = this.lastData
				
				if (lastData && data.period == period) {
					
					const lastTime = lastData.time < 9999999999 ? lastData.time * 1000 : lastData.time
					if(time - lastTime >= calcTime){
						let newData = {
							...data,
							open:this.lastData.close,
						}
						this.chartInfo.candleData.push(newData)
						this.lastData = newData
						
						this.chartInfo.gramData.push({
							time,
							value: data.volume,
							color: data.close - data.open > 0 ? '#E86D43' : '#02C289'
						})
					}
					
				}else if(!lastData && data.period == period){
										
					this.lastData = data
					this.chartInfo.candleData.push(data)
					this.chartInfo.gramData.push({
						time,
						value: data.volume,
						color: data.close - data.open > 0 ? '#E86D43' : '#02C289'
					})
				}
				
				this.open = data.open
				this.high = data.high
				this.low = data.low
				this.close = data.close
			},
		},
		computed: {
			symbol() {
				return this.currency_name + '/' + this.legal_name
			}
		},
		beforeDestroy() {
			this.$store.state.socket.removeListener('daymarket')
			this.$store.state.socket.removeListener('kline')
		}

	}
</script>
<script module="lightweightCharts" lang="renderjs">
	let myChart
	export default {
		data() {
			return {
				candleSeries: null,
				lineSeries: null,
				open: 0
			}
		},
		mounted() {
			if (typeof window.lightweightCharts === 'function') {
				this.initEcharts()
			} else {
				// 动态引入较大类库避免影响页面展示
				const script = document.createElement('script')
				// view 层的页面运行在 www 根目录，其相对路径相对于 www 计算
				script.src = 'static/trading-view/lightweight-charts.standalone.production.js'
				script.onload = this.initEcharts.bind(this)
				document.head.appendChild(script)
			}
		},
		methods: {
			initEcharts(symbol, lang) {
				const width = window.innerWidth
				this.$nextTick(function() {
					// candles
					const chart = LightweightCharts.createChart('chart', {
						width,
						height: 300,
						layout:{
							backgroundColor: "#122137",
							textColor: "rgba(255,255,255,.3)",
							fontSize: 12,
						},
						crosshair: {
							mode: LightweightCharts.CrosshairMode.Normal,
						},
						timeScale: {
							visible: false,
						},
						priceScale: {
							borderColor: "rgba(70, 130, 180, 0.1)",
						},
						grid: {
							vertLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							},
							horzLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							}
						}
					});

					this.candleSeries = chart.addCandlestickSeries({
						upColor: "#02C289",
						downColor: "#E86D43",
						borderUpColor: "#02C289",
						borderDownColor: "#E86D43",
					});
					

					this.lineMa5 = chart.addLineSeries({
						color: 'rgba(255, 0, 0, 1)',
						lineWidth: 1,
						priceLineVisible: false,
						lastValueVisible: false
					});

					this.lineMa10 = chart.addLineSeries({
						color: 'rgba(25, 117, 0, 1)',
						lineWidth: 1,
						priceLineVisible: false,
						lastValueVisible: false
					});

					//macd
					const macd = LightweightCharts.createChart('macd', {
						width,
						height: 100,
						layout:{
							backgroundColor: "#122137",
							textColor: "rgba(255,255,255,.3)",
							fontSize: 12,
						},
						crosshair: {
							mode: LightweightCharts.CrosshairMode.Normal,
						},
						timeScale: {
							visible: true,
							tickMarkFormatter(time) {
								const date = new Date(time * 1000);
								return (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date
									.getHours() + ':' + date.getMinutes();
							}
						},
						priceScale: {
							borderColor: "rgba(70, 130, 180, 0.1)",
						},
						grid: {
							vertLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							},
							horzLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							}
						}
					});

					this.macdMacd = macd.addHistogramSeries({
						base: 0
					});
					this.macdDif = macd.addLineSeries({
						color: 'rgba(255, 0, 0, 1)',
						lineWidth: 1,
					});
					this.macdDea = macd.addLineSeries({
						color: 'rgba(25, 117, 0, 1)',
						lineWidth: 1,
					});

					//gram
					const gram = LightweightCharts.createChart('gram', {
						width,
						height: 80,
						layout:{
							backgroundColor: "#122137",
							textColor: "rgba(255,255,255,.3)",
							fontSize: 12,
						},
						crosshair: {
							mode: LightweightCharts.CrosshairMode.Normal,
						},
						timeScale: {
							visible: false,
						},
						priceScale: {
							borderColor: "rgba(70, 130, 180, 0.1)",
						},
						grid: {
							vertLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							},
							horzLines: {
								color: "rgba(70, 130, 180, 0.1)",
								style: 1,
								visible: true
							}
						}
					});

					this.gram = gram.addHistogramSeries({
						base: 0
					})
				})
			},
			refreshData(newValue, oldValue) {
				//console.log(newValue, oldValue);
				
				this.candleSeries.setData(newValue.candleData);
				if(newValue.realTimePrice.time) this.candleSeries.update(newValue.realTimePrice);
				
				this.gram.setData(newValue.gramData)
				this.lineMa5.setData(newValue.ma5);
				this.lineMa10.setData(newValue.ma10);
				

				this.macdMacd.setData(newValue.macd.macd);
				this.macdDif.setData(newValue.macd.dif);
				this.macdDea.setData(newValue.macd.dea);
			}
		}
	}
</script>
<style lang="scss">
	.tv-lightweight-charts {
		margin: 0 auto;
	}

	.times {
		display: grid;
		grid-template-columns: repeat(8, 1fr);
		grid-gap: 10rpx;
		text-align: center;

		.item {
			padding: 20rpx 0;
			color: rgba(255,255,255,.6);
			position: relative;
			&.active {
				&:after{
					position: absolute;
					display: block;
					content: "";
					bottom: 0;
					left: 30%;
					right: 30%;
					height: 4rpx;
					background-color: #FF0000;
					border-radius: 6rpx;
				}
			}
		}
	}
</style>
