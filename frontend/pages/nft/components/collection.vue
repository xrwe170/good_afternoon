<template>
	<view class="mx-30">
		<view class="d-grid-columns-2" style="grid-column-gap:20rpx">
			<block v-for="(item,index) in list">
				<nft-artwork :data="item" @clickCollect="collect"></nft-artwork>
			</block>
		</view>
		<view v-if="!list.length" class="collect-now mt-50" @click="goCollect">
			{{i18n.goCollect}}
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list:[],
			};
		},
		created() {
			this.getCollection()
		},
		methods:{
			getCollection(){
				this.$u.api.nft.getCollection().then(res=>{
					let list = res.message
					if(list.length){
						list.forEach(item => {
							item.show = true
						})
						this.list = list
					}
				})
			},
			collect(code){
				const has = this.list.findIndex(el => el.code == code)
				if(has > -1){
					let item = this.list[has]
					item.show = false
					this.list.splice(has,1,item)
					
					setTimeout(()=>{
						this.list.splice(has,1)
					},200)
				}
			},
			goCollect(){
				this.$emit('jumpTo',1)
			}
		},
		computed:{
			i18n(){
				return this.$t("nft")
			}
		}
	}
</script>

<style lang="scss">
.filter-item{
	height: 80rpx;
	line-height: 80rpx;
	padding: 0 60rpx;
	margin-right: 30rpx;
	border: 2rpx solid #ddd;
	border-radius: 20rpx;
	font-size: 32rpx;
	font-weight: bold;
	color: $uni-color-deepblue;
	&.active{
		border:4rpx solid $uni-color-deepblue; ;
		background-image: linear-gradient(to right, #9795f0 0%, #fbc8d4 100%);background-image: linear-gradient(-225deg, #FFFEFF 0%, #D7FFFE 100%);

	}
}

.filter-button{
	font-size: 32rpx;
	background-color: rgba(13,39,88,.15);
	color: $uni-color-deepblue;
	font-weight: bold;
	&.filter-button-dark{
		background-color: $uni-color-deepblue;
		color: #fff;
	}
	&::after{
		border: none;
	}
}

.filter-input{
	background-color: rgba(13,39,88,.05);
	height: 80rpx;
	border-radius: 40rpx;
	padding-left: 30rpx;
	font-weight: bold;
}

.white-circle{
	width: 80rpx;height: 80rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}

.place-a-bid{
	left: 0;right: 0;bottom: 0;
	background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);
	border-radius: 0 0 20rpx 20rpx;
	position: absolute;
	text-align: center;
	font-size: 38rpx;
	font-weight: bold;
	padding: 30rpx 0;
	color: $uni-color-deepblue;
	&.place-a-bid-2{
		background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
		color: #999;
		font-size: 32rpx;
	}
}

.type-name{
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}

.collect-now{
	width: 80vw;
	margin: 0 auto;
	text-align: center;
	font-size: 38rpx;
	font-weight: bold;
	padding: 30rpx 0;
	background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);
	color: #fff;
	border-radius: 50rpx;
}
</style>
