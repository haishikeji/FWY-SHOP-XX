<template>
	<view>
		<view class="content">
			<view class="user-info">
				<view class="title">
					填写收货地址
				</view>
				<view style="height: 20rpx;"></view>
				<view class="views" >
					<view class="name">收货人</view>
					<view class="input" >
						<input v-model="from.userName" style="margin-left: 55rpx;" type="text" class="inputs" placeholder="填写姓名"/>
					</view>
				</view>
				<view class="views">
					<view class="name">联系电话</view>
					<view class="input">
						<input maxlength="11" v-model="from.userPhone" class="inputs" type="number" placeholder="填写手机号"/>
					</view>
				</view>
				<pick-regions :defaultRegion="defaultRegionCode" @getRegion="handleGetRegion">
				<view class="views">
					<view class="name">所在区域</view>
					<view class="input">
						<input disabled="disabled" v-model="from.address" class="inputs" placeholder="选择收件地区（省、市、区）"/>
					</view>
				</view>
				</pick-regions>
				<view class="views">
					<view class="name">详细地址</view>
					<view class="input">
						<input v-model="from.addDetail" class="inputs" placeholder="例如门牌号、小区、楼层等"/>
					</view>
				</view>

			</view>
				<view class="remark">
					<view class="title">
						预约备注
					</view>
					<textarea v-model="from.remark" placeholder-class="place" class="text-area" placeholder="请填写方便的时间段，以便工作人员到访。"></textarea>
				</view>
				<view class="goods-infos">
					<view class="goods-img">
						<image :src="goodsInfo.cover"></image>
					</view>
					<view class="goods-right">
						<view class="goods-name overflow2">{{goodsInfo.name}} {{goodsInfo.desc}}</view>
						<view class="goods-rule">{{goods.goods_spec}}</view>
					</view>
				</view>
				<view class="pay" v-if="status==1">
					<view class="wx-icon">
						<image src="../../static/index/wxIcon.png"></image>
					</view>
					<view class="texts">微信支付</view>
          <view class="wxIcon">
            <image src="../../static/index/check-icons.png"></image>

          </view>
				</view>
				<view style="height: 200rpx;"></view>

		</view>
		<view class="pay-bottom" v-if="status==1">
			<view class="money">实付款：<span>¥<span style="font-size: 40rpx;">{{goods.sell_price}}</span></span></view>
			<view class="button" @click="$global.clicks(pay)">立即购买</view>
		</view>
		<view class="pay-bottom" v-if="status==0">
			<view class="button" @click="$global.clicks(submit)" style="width: 520rpx;margin-left: 120rpx;">确定预约</view>
		</view>
	</view>

</template>

<script>
import api from 'utils/api'
import pickRegions from '@/components/pick-regions/pick-regions.vue'
	export default {
		components:{pickRegions},
		data() {
			return {
				 from:{
					 "userName":"",
					 "userPhone":"",
					 "userDetail":"",
					 "proName":"",
					 "cityName":"",
					 "countyName":"",
					 "addDetail":"",
					 "remark":"",
					 "address":""
				 },
				 pickerValueDefault: [0, 0, 0],
				 pickerText: '',
				 status:0,//0代表是预约 1代表下单购买
				 goods:'',
			     region:[],
				 goodsInfo:'',
				 }
		},
		onLoad(options) {
			this.goods=uni.getStorageSync("goods")
			this.goodsInfo=uni.getStorageSync("goodsInfo")
			this.status=uni.getStorageSync("status")
			// let infos=uni.getStorageSync("userInfo")
			api.getAddress().then((res)=>{
				if(res.code==1 && res.data!=''){					
					this.from.userName=res.data.user_name
					this.from.userPhone=res.data.phone
					// this.from.userDetail=infos.userDetail
					this.from.proName=res.data.pro_name
					this.from.cityName=res.data.city_name
					this.from.countyName=res.data.county_name
					this.from.addDetail=res.data.add_detail
					// this.from.remark=infos.remark
					this.from.address=res.data.pro_name+','+res.data.city_name+','+res.data.county_name
				}
			})
			// this.getAddress()
		},
		onUnload() {
			let from=this.from;
			uni.setStorageSync("userInfo",from)
			uni.removeStorageSync("goodsInfo")
			uni.removeStorageSync("status")
			uni.removeStorageSync("goods")
		},
		onShow() {
			this.getUserInfo();
		},
		methods: {
			//获取用户信息
			getUserInfo(){
				api.getUserInfo().then((res)=>{
					console.error(res)
					if(res.code==1){
						this.userInfos=res.data.detail
					}
				})
			},
			 tojson(arr){
				if(!arr.length) return null;

				var i = 0;
				len = arr.length,
				array = [];
				for(;i<len;i++){
					array.push({"projectname":arr[i][0],"projectnumber":arr[i][1]});
				}
				return JSON.stringify(array);
			},
			//下单
			pay(){
				let from=this.from;
				if(from.userName==''){
					uni.showToast({
						icon:"none",
						title:"请输入姓名"
					})
					return;
				}
				if(from.userPhone==''){
					uni.showToast({
						icon:"none",
						title:"请输入手机号"
					})
					return;
				}
				if(!(/^1[3456789]\d{9}$/.test(from.userPhone))){
					uni.showToast({
						icon:"none",
						title:"请输入正确的手机号"
					})
					return;
				}
				if(from.address==''){
					uni.showToast({
						icon:"none",
						title:"请选择区域"
					})
					return;
				}
				if(from.addDetail==''){
					uni.showToast({
						icon:"none",
						title:"请输入详细地址"
					})
					return;
				}
				let data={
					"goods_json":"",
					"pro_name":from.proName,
					"city_name":from.cityName,
					"county_name":from.countyName,
					"add_detail":from.addDetail,
					"phone":from.userPhone,
					"user_name":from.userName,
					"remark":from.remark,
				}
				let data_s={'goods_id':this.goodsInfo.id,"spec_id":this.goods.id,'num':1}
				// let array=[]
				// array.push(data_s)
				let datas=[];
				let data_ss=[];
				data_ss.push(data_s)
				// data_ss.push(datas)
				// console.error(JSON.stringify(data_ss))
				// return
				let arrs=data_ss
				data.goods_json=JSON.stringify(data_ss)



				api.createOrder(data).then((res)=>{
					if(res.code==1){
						api.payOrder({"order_id":res.data.order_id}).then((ret)=>{

							if(ret.code==1){
								let config=ret.data.config.config;
									uni.requestPayment({
										provider: 'wxpay',
										timeStamp: config.timestamp,
										nonceStr: config.nonceStr,
										package: config.package,
										signType: config.signType,
										paySign: config.paySign,
										success: function(res) {
											uni.showToast({
												icon:"none",
												title:"支付成功"
											})
											uni.navigateTo({
												url:"/pages/orderSuccess/index"
											})
										},
										fail: function(res) {
											uni.showToast({
												icon:"none",
												title:"支付失败"
											})
										},
										complete: function(res) {
											uni.showToast({
												icon:"none",
												title:"支付取消"
											})
										}
									});
							}
						})
					}
				})
			},
			 handleGetRegion(e){
                this.region = e
				// e 确认后选中的数据
				this.pickerText = JSON.stringify(e)
				this.from.proName=e[0].name;
				this.from.cityName=e[1].name;
				this.from.countyName=e[2].name;
				this.from.address=e[0].name+','+e[1].name+','+e[2].name
            },
			getAddress(){
				api.getAreaTree({"level":3}).then((res)=>{
					if(res.code==1){
						this.treeData=res.data.list;
						console.error(res.data.list)
					}
				})
			},
			//预约订单
			submit(){
				let from=this.from;
				if(from.userName==''){
					uni.showToast({
						icon:"none",
						title:"请输入姓名"
					})
					return;
				}
				if(from.userPhone==''){
					uni.showToast({
						icon:"none",
						title:"请输入手机号"
					})
					return;
				}
				if(!(/^1[3456789]\d{9}$/.test(from.userPhone))){
					uni.showToast({
						icon:"none",
						title:"请输入正确的手机号"
					})
					return;
				}
				if(from.address==''){
					uni.showToast({
						icon:"none",
						title:"请选择区域"
					})
					return;
				}
				if(from.addDetail==''){
					uni.showToast({
						icon:"none",
						title:"请输入详细地址"
					})
					return;
				}
				let data={
					"goods_id":this.goodsInfo.id,
					"spec_id":this.goods.id,
					"pro_name":from.proName,
					"city_name":from.cityName,
					"county_name":from.countyName,
					"add_detail":from.addDetail,
					"phone":from.userPhone,
					"user_name":from.userName,
					"remark":from.remark,
				}
				api.makeAppointment(data).then((res)=>{
					if(res.code==1){
						uni.showToast({
							icon:"none",
							title:"预约成功",
							success() {
								setTimeout(()=>{
									uni.navigateTo({
										url:"/pages/subscribe/index"
									})
								},2000)
							}
						})
					}
				})
			},

		}
	}
</script>

<style lang="scss">
.wxIcon{
  margin-right: 32rpx;
  image{
    width: 32rpx;
    height: 32rpx;
  }
}
	.pay-bottom{
    justify-content: space-between;
		width: 100%;
		height: 150rpx;
		background: #FFFFFF;
		position: fixed;
		bottom: 0;
		display: flex;
		padding-top: 25rpx;
		.button{
			text-align: center;
			color: #fff;
			font-size: 32rpx;
			line-height: 80rpx;
			width: 320rpx;
			height: 80rpx;
			background: #222222;
			margin-right: 50rpx;
		}
		.money{
			//margin-left: 80rpx;
			color: #222;
			font-size: 24rpx;
			margin-top: 15rpx;
      margin-right: 20rpx;
      text-align: right;
      flex: 1;
			span{
				font-size: 28rpx;
				font-weight: bold;
			}
		}
	}
	.content{
		padding: 30rpx;

		.pay{
			height: 90rpx;
			background: #FFFFFF;
			box-shadow: 0px 4rpx 12rpx 0px rgba(231,231,231,0.41);
			border-radius: 16rpx;
			display: flex;
      justify-content: space-between;
			margin: 25rpx 0;
			line-height: 90rpx;
			.texts{
				color: #222;
				font-size: 28rpx;
				margin-left: 15rpx;
        flex: 1;
			}
			.wx-icon{
				image{
					margin: 20rpx 0 0 30rpx;
					width: 48rpx;
					height: 48rpx;
				}
			}
		}
		.goods-infos{
			height: 124rpx;
			background: #FFFFFF;
			box-shadow: 0px 4rpx 12rpx 0px rgba(231,231,231,0.41);
			border-radius: 16rpx;
			padding: 30rpx;
			display: flex;
			.goods-img{
				image{
					width: 120rpx;
					height: 120rpx;
				}
			}
			.goods-right{
				margin-left: 20rpx;
				.goods-name{
					color: #222;
					font-size: 24rpx;
				}
				.goods-rule{
					color: #666;
					font-size: 20rpx;
					margin-top: 15rpx;
				}
			}
		}
		.remark{
			height: 252rpx;
			background: #FFFFFF;
			box-shadow: 0px 4rpx 12rpx 0px rgba(231,231,231,0.41);
			border-radius: 16rpx;
			margin:25rpx 0;
			padding: 30rpx;
			.title{
				font-size: 28rpx;
				color: #222;
			}
			.place{
				color: #999;
			}
			.text-area{
				font-size: 24rpx;
				color: #333;
				margin-top: 25rpx;
			}
		}
		.user-info{
			height:400rpx;
			background: #FFFFFF;
			box-shadow: 0px 4px 12rpx 0px rgba(231,231,231,0.41);
			border-radius: 16rpx;
			width: 630rpx;
			background-color: #fff;
			padding: 30rpx;

			.views{
				display: flex;
				// margin-top: 30rpx;
				height: 80rpx;
				line-height: 80rpx;
				font-size: 24rpx;
				.input{
					.inputs{

						height: 80rpx;
						line-height: 80rpx;
						border-bottom: 1rpx solid #f5f5f5;
						width: 400rpx;
						margin-left: 30rpx;
					}
				}
			}
			.title{
				font-size: 28rpx;
				color: #222;

			}
		}
	}
</style>
