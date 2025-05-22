<template>
	<view class="number-box-wrapper" >
		<view class="number-box-content" >
			<view class="icon-box left" @click="iconClick('-')">
				<u-icon name="minus"  :size="size"></u-icon>
			</view>
			<input type="text" v-model="inputValue" class="number-box-input" @input="input" @blur="blur">
			<view class="icon-box right" @click="iconClick('+')">
				<u-icon name="plus" :size="size"></u-icon>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"number-box",
		props:{
			value:{
				type:Number,
				default:1,
			},
			min:{
				type:Number,
				default:0
			},
			max:{
				type:Number,
				default:999999999999999999
			},
			//精度
			precision:{
				type:Number,
				default:2
			},
			size:{
				type:Number,
				default:32
			},
			step:{
				type:Number,
				default:0.01
			}
			
		},
		data() {
			return {
				inputValue:0,
			};
		},
		created() {
			this.inputValue = this.retStringBypPrecision(this.value)
		},
		methods:{
			//返回格式化后的数字，格式为字符串
			retStringBypPrecision(value){
				value = +value
				return value.toFixed(this.precision)
			},
			//点击加减按钮
			iconClick(type){
				this.$u.throttle(()=>{
					//点击加号按钮
					const nextValue = this.$utils.math(+this.inputValue,type,this.step)
					this.compare(nextValue)
				},100)
			},
			compare(nextValue){
				let v1 = nextValue,v2 = this.inputValue,value = 0
				v1 = +v1
				//如果不再取值范围内，则还设置为v2
				if(v1 >= this.min && v1 <= this.max){
					value = v1
				}else if(v1 > this.max){
					value = this.max
				}else if(v1 < this.min){
					value = this.min
				}
				this.$nextTick(() => {
					this.inputValue = this.retStringBypPrecision(value);
				})
			},
			//输入时触发
			input(e){
				let nextValue = e.detail.value
				let value = 0
				if(!+nextValue){
					let exec = /(\d+(\.\d+)?)/.exec(nextValue)
					if(exec){
						value = exec[0]
					}else{
						value = this.value
					}
					this.compare(value)
				}
				
			},
			//input失去焦点时
			blur(e){
				let nextValue = e.detail.value
				this.compare(nextValue)
			}
		},
		watch:{
			inputValue(v1,v2){
				this.$emit('input', Number(this.inputValue));
			}
		}
	}
</script>

<style lang="scss" scoped>
.number-box-wrapper{
	display: inline-block;
	border-radius: 4rpx;
	.number-box-content{
		display: flex;
		padding: 20rpx 0;
		.icon-box{
			background-color: #F2F3F5;
			padding: 6rpx;
			&.left{
				border-radius: 6rpx 0 0 6rpx;
			}
			&.right{
				border-radius: 0 6rpx 6rpx 0;
			}
		}
		.number-box-input{
			background-color: #F2F3F5;
			padding: 2rpx 8rpx;
			text-align: center;
			width: 120rpx;
			margin: 0 10rpx;
		}
	}
	
}
</style>
