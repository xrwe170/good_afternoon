<template>
	<view class="pb-30">
		<u-navbar title="帮助中心"></u-navbar>
		<view class="m-30">
			<view class="">
				<text class="title">一、什么是金融理财？</text>
				<text>金融理财是一种涉及两种数字货币的浮动收益非保本理财产品。投资者可以用合约中规定的任意一种数字货币买入金融理财产品，最终结算货币种类由到期日的结算价和挂钩参考价决定。</text>
				<text>金融理财提供的收益率是随市场变化的，但投资者在确认买入后收益率是固定的，且一旦申购成功后，不可撤销订单，未到期也不支持提前赎回。买入后，在结算期到来时，投资者可确保获取原投资币种的固定保底收益，但也可能会因为行情波动导致投资币种被转化成另一币种，这也是金融理财吸引投资者的最大特征。</text>
				<text>*请您确保在投资金融理财前已充分了解产品及风险。</text>
			</view>
			<view class="mt-50">
				<text class="title">二、如何计算你的收益？</text>
				<text class="title-2">示例 1 ： 以投资 BTC 购买 BTC_USDT 金融理财产品为例：</text>
				<text>用户 A 购买 1 BTC ，挂钩参考价为20,000USDT的金融理财产品，当 2021 年 2 月 1 日该产品到期交割时：</text>
				<text>情况 1：BTC_USDT 结算价＜20,000 USDT，以 BTC 进行回款结算，回款金额=1 BTC *（1+100%/365*31)=1.0849 BTC</text>
				<text>情况 2：BTC_USDT 结算价≥20,000 USDT，以 USDT 进行回款结算，回款金额=1 BTC * 20,000 *（1+100%/365*31)=21698.63 USDT</text>
				<text class="title-2">以投资 USDT 购买 BTC_USDT 金融理财产品为例：</text>
				<text>用户 B 购买 20,000 USDT 的以上产品，挂钩参考价为20,000USDT，当 2021 年 2 月 1 日该产品到期交割时： 情况 1：BTC_USDT 结算价≤20,000 USDT，以 BTC 进行回款结算，回款金额=20,000USDT/20,000 *（1+100%/365*31)=1.0849 BTC</text>
				<text>情况 2：BTC_USDT 结算价＞20,000 USDT，以 USDT 进行回款结算，回款金额=20,000 USDT *（1+100%/365*31)=21,698.63 USDT</text>
				<text>综上所述，产品到期结算时，用户都可以获得 108.49%的回款，但是由于结算价与挂钩价的数值关系，用户最终收到的回款币种存在差异。</text>
			</view>
			<view class="mt-50">
				<text class="title">三、FAQ/常见问题解答</text>
				<text class="title-2">1、如何查看已购买的产品？</text>
				<text>可通过“金融理财”页面右上角“我的持币”入口查看购买的产品信息列表。</text>
				<text class="title-2">、购买成功后可撤销么？</text>
				<text>购买成功后不支持撤销，产品到期交割时自动进行回款结算。</text>
				<text class="title-2">3、购买成功后支持提前赎回么？</text>
				<text>购买成功后不支持提前赎回，产品到期交割时自动进行回款结算。</text>
				<text class="title-2">4、如何收到回款？</text>
				<text>购买成功后，您将在到期后自动收到本金及利息的回款，回款将发放到您的现货账户，无需手动操作。</text>
				<text class="title-2">5、为什么投资的币种和收到的回款币种不一致？</text>
				<text>以何种币种进行结算取决于产品到期交割时挂钩币种的币价与挂钩价之间的数值对比结果，以投资 BTC 购买 BTC_USDT 金融理财产品为例，当结算价＜挂钩价时，以 BTC 进行本金及收益结算回款；当结算价≥挂钩价时，以 USDT 进行本金及收益结算回款。</text>
				<text class="title-2">6、金融理财的风险是什么？</text>
				<text>根据产品规则，回款结算的币种会由到期交割时挂钩币种的币价与挂钩价的对比结果来确定，当市场波动较大时，难以预测到期时结算价及其与挂钩价之间的关系，对于币本位投资用户，存在投资币种或被转化的投资风险。同时，金融理财产品也为币种转换风险提供了收益率补偿，当被转换概率越高时，金融理财产品收益率越高。</text>
			</view>
			<view class="mt-50">
				<text class="title">四、名词解释</text>
				<text class="title-2">年化收益率</text>
				<text>年化收益率会随着市场实时变动，实际年化收益率需以购买成功时为准。</text>
				<text>年化收益率=收益率/持仓期限* 365*100%</text>
				<text>持仓期限=到期日-购买日</text>
				
				<text class="title-2">挂钩参考价</text>
				<text>挂钩参考价是一个基准价格，在到期时会与挂钩币种价格进行对比，从而确定本金及收益的回款币种及数量，以 BTC 专区为例，与 BTC_USDT 价格挂钩。不同产品的挂钩参考价不同，您可根据自己对市场的预期情况选择适合自己的产品。</text>
				<text class="title-2">到期日</text>
				<text>投资产品结束的日期，产品将在到期日当天16:00进行交割结算，收益以及本金回款将在到期结算后自动发放至现货账户。</text>
				<text class="title-2">投资币种</text>
				<text>投资币种为您实际以何种币种购买产品，以 BTC 专区为例，与 BTC_USDT 价格挂钩，现支持以 BTC 或 USDT 币种进行投资。</text>
				<text class="title-2">结算币种</text>
				<text>结算币种为产品到期交割结算时实际收到的回款币种。以何种币种进行结算取决于产品到期交割时挂钩币种价格与挂钩价之间的数值对比结果。以 BTC 专区，投资 BTC 为例，可能收到的结算币种为 BTC 或 USDT。</text>
				<text class="title-2">结算价格</text>
				<text>结算价格为产品到期日下午 16:00Deribit 期权平台标的结算价。</text>
			</view>	
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {

			};
		}
	}
</script>

<style lang="scss" scoped>
	text {
		display: block;
		margin: 20rpx 0;
	}

	.title {
		font-size: 32rpx;
		font-weight: bold;
	}
	
	.title-2{
		font-weight: bold;
	}
</style>
