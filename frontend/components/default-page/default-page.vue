<template>
	<view class="">
		<view>
			<image :src="data" v-if="!total" class="mx-auto d-block my-30" style="width: 320rpx;height: 240rpx;"></image>
			<text v-else="length == total" class="text-center font-size-22 py-20 d-block">{{$t("common.hasNoData")}}</text>
		</view>
		<slot></slot>
	</view>
</template>

<script>
	export default {
		name:"default-page",
		props:{
			name:{
				type:String,
				default:"empty"
			},
			length:{
				type:Number,
				default:0
			},
			total:{
				type:Number,
				default:0
			}
		},
		data() {
			return {
				list:{
					address:require("static/image/svg/address.svg"),
					bill:require("static/image/svg/bill.svg"),
					card:require("static/image/svg/card.svg"),
					collect:require("static/image/svg/collect.svg"),
					comment:require("static/image/svg/comment.svg"),
					content:require("static/image/svg/content.svg"),
					coupon:require("static/image/svg/coupon.svg"),
					data:require("static/image/svg/data.svg"),
					message:require("static/image/svg/message.svg"),
					net:require("static/image/svg/net.svg"),
					order:require("static/image/svg/order.svg"),
					empty:require("static/image/svg/empty.svg"),
					update:require("static/image/svg/update.svg"),
				}
			};
		},
		computed:{
			data(){
				const has = this.list[this.name]
				if(has) return has
				if(!has) return this.list['empty']
			}
		}
	}
</script>

<style lang="scss">

</style>
