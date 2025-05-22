@extends('manages.layadmin')

@section('title', '内容管理系统')

@section('page-head')

@endsection

@section('page-content')
  <div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
      <div class="layui-header">
        <!-- 头部区域 -->
        <ul class="layui-nav layui-layout-left">
          <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
              <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
          </li>
          
          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="刷新">
              <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
          </li>
        </ul>

        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right" style="padding-right: 20px">
          
          
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="theme">
              <i class="layui-icon layui-icon-theme"></i>
            </a>
          </li>
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="note">
              <i class="layui-icon layui-icon-note"></i>
            </a>
          </li>
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="fullscreen">
              <i class="layui-icon layui-icon-screen-full"></i>
            </a>
          </li>
          <li class="layui-nav-item" lay-unselect >
            <a href="javascript:;">
              <cite>{{@$username}}</cite>
            </a>
            <dl class="layui-nav-child">
{{--              <dd><a lay-href="/agent/set_info">基本资料</a></dd>--}}
{{--              <dd><a lay-href="/agent/set_password">修改密码</a></dd>--}}
{{--              <hr>--}}
              <dd layadmin-event="logout" style="text-align: center;"><a>退出</a></dd>
            </dl>
          </li>
        </ul>
      </div>

      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          <div class="layui-logo">
            <span>内容管理系统</span>
          </div>
          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">

            @foreach($menus as $vo)
                <li data-name="{{@$vo['module']}}" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="{{@$vo['module']}}" lay-direction="2">
                    <i class="layui-icon {{@$vo['icon']}}"></i>
                    <cite>{{@$vo['name']}}</cite>
                  </a>
                  @if(isset($vo['children']))
                    <dl class="layui-nav-child">
                      @foreach($vo['children'] as $v)
                          <dd data-name="console">
                            <a lay-href="/{{@$v['action']}}">{{@$v['name']}}</a>
                          </dd>
                      @endforeach
                    </dl>
                  @endif
                </li>
            @endforeach

          </ul>
        </div>
      </div>

      <!-- 页面标签 -->
      <div class="layadmin-pagetabs" id="LAY_app_tabs">
        <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-down">
          <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
              <a href="javascript:;"></a>
              <dl class="layui-nav-child layui-anim-fadein">
                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
              </dl>
            </li>
          </ul>
        </div>
        <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
          <ul class="layui-tab-title" id="LAY_app_tabsheader">
            <li lay-id="" lay-attr="" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
          </ul>
        </div>
      </div>
      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
          <iframe src="/manages/console" frameborder="0" class="layadmin-iframe"></iframe>
        </div>
      </div>
      
      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
  </div>
  @endsection

@section('scripts')

  <script src="{{URL("winadmin/lib/layui/layui.js")}}"></script>
  <script>
    var tixian = '{{URL("static/audio/tixian.mp3")}}';
    var chuzhi = '{{URL("static/audio/chuzhi.mp3")}}';

    var tips_url = '{{url('admin/tips/tips')}}';

    // 订单轮询
    setInterval(function () {
      $.ajax({
        url:tips_url + '?type=1',
        type:'GET',
        data:{},
        dataType:'JSON',
        success:function(res){
          if (res.code == 100){
            layer.msg("您有新的提现申请,请及时处理", {time: 3000, shift: 5,offset:"tc"});
            var audio = new Audio(tixian);
            audio.play();
          }
        }
      });
      setTimeout(function (){
        $.ajax({
          url:tips_url + '?type=2',
          type:'GET',
          data:{},
          dataType:'JSON',
          success:function(res){
            if (res.code == 100){
              layer.msg("您有新的充值订单,请及时处理", {time: 3000, shift: 5,offset:"tc"});
              var audio = new Audio(chuzhi);
              audio.play();
            }
          }
        });
      }, 15000);

    },30000);




  </script>
       
@endsection



