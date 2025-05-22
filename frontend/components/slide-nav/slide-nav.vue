<template>
	<view class="nav font-size-32 font-weight-bold text-secondary position-relative py-20 text-center" :class="`d-grid-columns-${listLength}`">
		<view class="d-flex justify-content-center align-items-center" :class="{'text-deepblue':activeNav == index}" v-for="(item,index) in list" @click="clickNav(index)">
			<text class="">{{item.name}}</text>
			<text v-if="item.num" class="font-size-20 circle-number">{{item.num}}</text>
		</view>
		<text class="nav-line" :style="navLineStyle"></text>
	</view>
</template>

<script>
	export default {
		name:"slide-nav",
		props:{
			current:{
				type:Number,
				default: -1
			},
			list:{
				type:Array,
				default(){
					return {
						
					}
				}
			}
		},
		data() {
			return {
				navLineStyle:{left:0,right:0},
				activeNav:-1,
			};
		},
		created() {
			this.clickNav(0)
		},
		mounted() {
			
		},
		methods:{
			clickNav(index){
				
				if(this.activeNav == index) return
				
				const length = this.listLength
								
				const per = 100 / length
				const slideWidth = 10 //百分比
				//中心值
				const center = index * per + per / 2
				
				const left = center - slideWidth / 2
				const right = 100 - (center + slideWidth / 2)
				
				//如果点右边的
				if(this.activeNav < index){
					this.navLineStyle.right = right + '%'
					setTimeout(()=>{
						this.navLineStyle.left = left + '%'
					},200)
				}else{
					this.navLineStyle.left = left + '%'
					setTimeout(()=>{
						this.navLineStyle.right = right + '%'
					},200)
				}
				
				this.activeNav = index
				
				this.$emit('change',index)
			}
		},
		computed:{
			listLength(){
				return this.list.length
			}
		}
	}
</script>

<style lang="scss">
.nav-line{
		position: absolute;
		bottom: 0;
		height: 6rpx;
		background-color: $uni-color-deepblue;
		border-radius: 6rpx;
		transition: all .3s ease 0s;
	}

.circle-number{
	height: 30rpx;
	width: 30rpx;
	background-color: $uni-color-deepblue;
	border-radius: 50%;
	color: #fff;
	line-height: 30rpx;
	margin-left: 10rpx;
}
</style>
