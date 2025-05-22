<template>
	<view class="mx-30 pt-30">
		<u-navbar title="地区选择"></u-navbar>
		<u-search :placeholder="$t('common.search')"  v-model="keyword" :show-action="false"
			input-align="center" @change="change"></u-search>
			
		<view class="mt-20">
			<view class="d-flex-between-center py-16 u-border-bottom" v-for="item in areas" :key="item.country_id" @click="selectArea(item)">
				<view class="">
					<text class="font-size-28 d-block font-weight-bold">{{item.name_en}}</text>
					<text class="font-size-22 d-block opacity-50">{{item.name_cn}}</text>
				</view>
				<text class="font-size-28">{{item.area_code}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	import areas from "common/areas.js"
	export default {
		data() {
			return {
				areas:[],
				keyword:''
			};
		},
		onShow() {
			this.areas = areas
		},
		methods:{
			change(e){
				this.areas = areas.filter(item => {
					if(item.area_code.indexOf(e) > -1 || item.name_cn.indexOf(e) > -1 || item.name_en.indexOf(e) > -1){
						return true
					}else{
						return false
					}
				})
			},
			selectArea(item){
				uni.setStorageSync('area_code',item.area_code)
				uni.navigateBack({
					delta:1
				})
			}
		}
	}
</script>

<style lang="scss">

</style>
