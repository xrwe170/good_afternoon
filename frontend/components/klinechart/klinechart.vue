<template>
	<view>
		<view class="options">
			<view class="item overflow-hidden">
				<scroll-view :scroll-x="true" style="white-space: nowrap;">
					<text class="item-li" v-for="(item,index) in intervals" :class=" index == currentIntervalIndex ? 'active' : '' " @click="changeInterval(index)">{{item.name}}</text>
				</scroll-view>
			</view>
			<!-- <view class="item overflow-hidden">
				<scroll-view :scroll-x="true" style="white-space: nowrap;">
					<text class="mx-10" v-for="(item,index) in chartTypes" :class=" index == currentChartTypeIndex ? 'text-primary font-weight-bold' : '' " @click="changeChartType(index)">{{item.name}}</text>
				</scroll-view>
			</view> -->
			<!-- <view class="item overflow-hidden">
				<scroll-view :scroll-x="true" style="white-space: nowrap;">
					<text class="mx-10" v-for="(item,index) in shapes" @click="createShape(item.value)">{{item.name}}</text>
				</scroll-view>
			</view> -->
		</view>
		<view id="renderKlinechart" :prop="chartData" :change:prop="renderKlinechart.getData" class="content">
			
		</view>
	</view>
</template>

<script>
	import {intervals,chartTypes,shapes} from './params'
	export default {
		name: "klinechart",
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
			//精度
			precision:{
				type:Number,
				default:2
			},
		},
		data(){
			return {
				//分时
				intervals,
				currentIntervalIndex:0,
				//图标类型
				chartTypes,
				currentChartTypeIndex:0,
				//画图的类型
				shapes,
				//传输给renderjs的数据
				chartData:{
					precision:2,
					historyData:[],//历史数据
					updateData:{},//更新的实时数据
					chartType:null,//更新图表类型
					createShapeName:null
				},
				period:'',
				dataList:[]
			}
		},
		mounted() {
			//获取历史数据列表
			this.getHistoryData()
			
		},
		methods:{
			async getHistoryData(){
				let {currentIntervalIndex,intervals,row,symbol} = this
				//获取历史K线数据中的，开始时间时间戳，秒级
				const from = Date.parse(new Date()) / 1000 - intervals[currentIntervalIndex].time * 24 * 60 * 60
				//获取历史K线数据中的，结束时间时间戳，秒级
				const to = Date.parse(new Date()) / 1000
				//当前显示的时间的事件名称
				let period = intervals[currentIntervalIndex].value
				this.period = period
				uni.showLoading()
				//请求接口获取数据
				const res = await this.$u.api.market.getHistoryData(from, to, symbol, period)
				const data = res.data.map(item => {
					return {
						timestamp: Number(item.time),
						open: Number(item.open),
						high:Number(item.high),
						low: Number(item.low),
						close: Number(item.close),
						volume:Number(item.volume),
					}
				})
				//显示历史数据
				setTimeout(()=>{
					this.chartData.historyData = data
					this.chartData.precision = this.precision
					uni.hideLoading()
				},200)
				
				this.dataList = data
				//获取实时数据
				this.startSocket()
			},
			startSocket(){
				const { currency_name, legal_name, period ,currentIntervalIndex,intervals} = this
				//新增下一时间段的数据
				this.$store.state.socket.on('kline', res => {
					if (res.currency_name == currency_name && res.legal_name == legal_name && res.period == period ) {
						const data = {
							timestamp: Number(res.time),
							open: Number(res.open),
							high:Number(res.high),
							low: Number(res.low),
							close: Number(res.close),
							volume:Number(res.volume),
						}
						const lastData = this.dataList[this.dataList.length - 1]
						
						if(data.timestamp > lastData.timestamp){
							this.dataList.push(data)
							this.chartData.updateData = lastData
						}
					}
				});
				
				//更新实时数据
				this.$store.state.socket.on('daymarket', res => {
					this.$emit('getSokcetData',res)
					if (res.currency_name == currency_name && res.legal_name == legal_name ) {
						let lastData = this.dataList[this.dataList.length - 1]
						lastData.close = Number(res.now_price)
						this.chartData.updateData = lastData
					}
				});
			},
			//切换时间
			changeInterval(index){
				//停止监听
				this.$store.state.socket.removeListener('kline')
				this.currentIntervalIndex = index
				this.getHistoryData()
			},
			//修改图标类型
			changeChartType(index){
				this.chartData.chartType = this.chartTypes[index].value
				this.currentChartTypeIndex = index
				
			},
			//创建图形
			createShape(shape){
				this.chartData.createShapeName = shape
			}
		},
		computed: {
			symbol() {
				return this.currency_name + '/' + this.legal_name
			}
		},
	}
</script>
<script module="renderKlinechart" lang="renderjs">
	import {init,dispose} from './klinecharts.min.js'
	import theme from './theme.js' 
	import {rect,circle} from './params'
	export default {
		data(){
			return{
				chart:null,
				macd:null
			}
		},
		mounted(){
			this.initChart()
		},
		methods:{
			//初始化图表
			initChart(){
				dispose('renderKlinechart')
				this.chart = init('renderKlinechart')
				//设置黑色主题
				this.chart.setStyleOptions(theme('dark'))
				//在主图上显示MA折线
				this.chart.createTechnicalIndicator({
					name: 'MA',
					calcParams:[5, 10,30],
				}, false, {
					id: 'candle_pane'
				})
				//显示总量图
				this.chart.createTechnicalIndicator({
					name: 'VOL',
					calcParams:[20, 30],
				}, false, {
					height: 100,
					dragEnabled: false
				})
				//显示MACD图
				this.macd = this.chart.createTechnicalIndicator('MACD', false, {
					height: 100,
					dragEnabled: false
				})
				 this.chart.overrideTechnicalIndicator({
					name:'MACD',
				})
				
			},
			getData(newValue, oldValue){

				//设置历史数据
				if(newValue.historyData){
					this.chart.clearData()
					this.chart.applyNewData(newValue.historyData)
				}
				//更新实时数据
				if(newValue.updateData){
					this.chart.updateData(newValue.updateData)
				}
				//修改图表类型
				if(newValue.chartType){
					this.chart.setStyleOptions({
						candle: {
							type:newValue.chartType
						}
					})
				}
				//设置精度
				this.chart.setPriceVolumePrecision(newValue.precision,newValue.precision)
			}
		},
		beforeDestroy() {
			// if(this.chart){
				
			// 	dispose('renderKlinechart')
			// }
		}
		
		
	}
</script>


<style lang="scss">
	.content {
		height: 1100rpx;
	}

	body {
		margin: 0;
		font-family: Avenir, Helvetica, Arial, sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		background-color: #F2F3F5;
	}

	div,
	p {
		box-sizing: border-box;
	}

	p {
		margin: 0;
	}

	.app {
		display: flex;
		flex-direction: row;
		justify-content: center;
		flex-wrap: wrap;
		padding: 15px;
	}

	.k-line-chart-container {
		display: flex;
		flex-direction: column;
		margin: 15px;
		border-radius: 2px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, .3);
		background-color: #FFFFFF;
		width: 620px;
		height: 440px;
		padding: 16px 6px 16px 16px;
	}

	.k-line-chart-title {
		margin: 0;
		color: #252525;
		padding-bottom: 10px;
	}

	.k-line-chart {
		display: flex;
		flex: 1;
	}

	.k-line-chart-menu-container {
		display: flex;
		flex-direction: row;
		align-items: center;
		margin-top: 10px;
		font-size: 12px;
		color: #606060;
	}

	.k-line-chart-menu-container button {
		cursor: pointer;
		background-color: #2196F3;
		border-radius: 2px;
		margin-right: 8px;
		height: 24px;
		line-height: 26px;
		padding: 0 6px;
		font-size: 12px;
		color: #fff;
		border: none;
		outline: none;
	}
	
	// .options{
	// 	border-top: 2rpx solid #333;
	// 	border-bottom: 2rpx solid #333;
	// 	display: grid;
	// 	grid-template-columns:repeat(2,1fr);
	// 	color: #999;
	// 	.item{
	// 		position: relative;
	// 		padding:16rpx 0;
	// 		line-height: 40rpx;
	// 		&::after{
	// 			display: block;
	// 			content: "";
	// 			position: absolute;
	// 			right: 0;
	// 			top: 0;
	// 			bottom: 0;
	// 			width: 2rpx;
	// 			background: #333;
	// 		}
			
	// 	}
	// }
	
	.options{
		border-bottom: 1px solid #e1e4e9;
		color: #999;
		.item {
			position: relative;
			padding: 8px 0px 8px 22px;
			line-height: 22px;
			font-size: 14px;
			font-family: Roboto;
			font-weight: 400;
			color: #5c6269;
			.item-li:not(:last-child)::after {
			    content: "";
			    display: inline-block;
			    width: 1px;
			    height: 23px;
			    background: #e1e4e9;
			    margin: 0 13px;
			}
			.active{
			    color: #000;
			}
		}
	}
	
	
</style>
