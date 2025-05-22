<template>
	<view class="wrap">
		<view class="container">
			<text class="line-1" :style="{width:style1.length,transform:`rotate(${style1.angle}deg)`}"></text>
			<text class="line-2" :style="{width:style2.length,transform:`rotate(${style2.angle}deg)`}"></text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				angle:120,
				width:250,
				style1:{
					length:0,
					angle:0
				},
				style2:{
					length:0,
					angle:0
				},
				index:0,
			};
		},
		onLoad(options) {
			
		},
		onShow() {
			// setInterval(()=>{
			// 	if(this.index == 100){
			// 		this.index = 0
			// 	}else{
			// 		this.index++
			// 		this.move()
			// 	}
			// },200)
			this.index = 20
			this.move()
	    },
		methods:{
			move(){
				const per = this.index;
				const yuanxinjiao_1 = per * this.angle / 100 
				const yuanxinjiao_2 = (100 - per) * this.angle / 100 
								
				const dijiao_1 = (180 - yuanxinjiao_1) / 2
				const dijiao_2 = (180 - yuanxinjiao_2) / 2
					
				const angle_1 = dijiao_1 - (180 - this.angle) / 2
				const angle_2 = (dijiao_2 - (180 - this.angle) / 2) * -1
								
				const d_1 = Math.cos( yuanxinjiao_1 * Math.PI / 180) * this.R
				const length_1 = 2 *  Math.sqrt( Math.pow(this.R,2) -  Math.pow(d_1,2) )
				
				const d_2 = Math.cos( yuanxinjiao_2 * Math.PI / 180) * this.R
				const length_2 = 2 *  Math.sqrt( Math.pow(this.R,2) -  Math.pow(d_2,2) )
				
				
				this.style1 = {
					length:length_1 + 'px',
					angle:angle_1
				}
				this.style2 = {
					length:length_2 + 'px',
					angle:angle_2
				}
				
			}
		},
		computed:{
			R(){
				const {width,angle} = this
				return width / ( 2 * Math.sin( angle * Math.PI / 180) )
			}
		}
		
	}
</script>

<style lang="scss" scoped>
.wrap{
	width: 100vw;
	height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
	.container{
		position: relative;
		width: 250px;
		height: 2rpx;
		background-color: #eee;
		.line-1{
			display: block;
			height: 2rpx;
			background-color: #000000;
			transform-origin: left top;
		}
		.line-2{
			position: absolute;
			right: 0;
			top: 0;
			display: block;
			height: 2rpx;
			background-color: #000000;
			transform-origin: right top;
		}
	}
}
</style>
