<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|Wode管理平台</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Mobile/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Mobile/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Mobile/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Mobile/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Mobile/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Mobile/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
    <style>
        body{padding: 0}
        .sidebar{display:none;}
    </style>

    <style>
    .bottom ul li{overflow: hidden;}
    body{padding:0;}
    </style>
</head>
<body>
    <!-- 头部 -->
    <div class="header">
      <div class="top">
                <img src="/Public/Admin/images/xtwcn/logo.png" class="logo"/>
                <ul class="right">
                    <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('Index/index');?>"><img src="/Public/Admin/images/xtwcn/returnhome.png"><br><span >返回首页</span></a></li>
                    <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('User/updatepassword');?>"><img src="/Public/Admin/images/xtwcn/changepass.png"><br><span >修改密码</span></a></li>
                    <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('Public/logout');?>"><img src="/Public/Admin/images/xtwcn/reload.png"><br><span >重新登录</span></a></li>
                </ul>
            </div>
       <!-- 主导航 -->
       <div class="bottom">
       <ul>
           <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid"><a href="<?php echo (u($menu["url"])); ?>"></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
       </ul>
       </div>
       <!-- /主导航 -->
       <div class="user-bar">
           <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
           <ul class="nav-list user-menu hidden">
               <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
               <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
               <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
               <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
           </ul>
       </div>
   </div>
   <!-- <header class="header">
               <div class="top">
                   <img src="/Public/Admin/images/xtwcn/logo.png" class="logo"/>
                   <ul class="right">
                       <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('Index/index');?>"><img src="/Public/Admin/images/xtwcn/returnhome.png"><br><span >返回首页</span></a></li>
                       <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('User/updatepassword');?>"><img src="/Public/Admin/images/xtwcn/changepass.png"><br><span >修改密码</span></a></li>
                       <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('Public/logout');?>"><img src="/Public/Admin/images/xtwcn/reload.png"><br><span >重新登录</span></a></li>
                   </ul>
               </div>
               <div class="bottom">
                   <ul>
                       <li><a href="<?php echo U('Index/index');?>">首页</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                       <a href="<?php echo U('Index/index');?>"><?php if(ACTION_NAME == 'index'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>
                       <li><a href="">案件调度</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid"></li>
                       <li><a href="">信息采集</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid"></li>                 
                    <li><a href="<?php echo U('Case/suoyou',array('act','suoyou'));?>">所有案件</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    <a href="<?php echo U('Case/suoyou');?>"><?php if(ACTION_NAME == 'suoyou'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>   
                    <li><a href="<?php echo U('Case/zaichu');?>">正在处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    <a href="<?php echo U('Case/zaichu');?>"><?php if(ACTION_NAME == 'zaichu'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>
                       <li><a href="<?php echo U('Case/daichu');?>">待处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    <a href="<?php echo U('Case/daichu');?>"><?php if(ACTION_NAME == 'daichu'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>
                       <li><a href="<?php echo U('Case/wancheng');?>">完成处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    <a href="<?php echo U('Case/wancheng');?>"><?php if(ACTION_NAME == 'wancheng'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>
                       <li><a href="<?php echo U('Case/guidang');?>">归档案件</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    <a href="<?php echo U('Case/guidang');?>"><?php if(ACTION_NAME == 'guidang'){?><img src="/Public/Admin/images/xtwcn/sanjiao.png" class="sanjiao" /><?php }?></a></li>
                   </ul>
                   <form class="form-inline">
                     <div class="form-group" style="display:inline-block;margin-top:-11px">
                       <input type="text" class="form-control" id="search" placeholder="">
                     </div>
                     <a href=""><img src="/Public/Admin/images/xtwcn/search-btn.png"></a>
                   </form>
                       
               </div>
           </header> -->
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar" style="float:left;position:absolute;">
        <!-- 子导航 -->
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main" style="width:100%;padding:0;float:right;box-sizing:border-box;padding-left:220px;padding-top:20px;padding-right:20px;">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
           <!--  nav -->
            

            
	<!-- 标题栏 -->
		<div class="tab-wrap">
		<ul class="tab-nav nav">
		<li class="current"><a href="<?php echo U('updatenickname');?>">修改昵称</a></li>
		<li ><a href="<?php echo U('updatepassword');?>">修改密码</a></li>
		</ul>
		<div class="tab-content">
	<!-- 修改密码表单 -->
    <form action="<?php echo U('User/submitNickname');?>" method="post" class="form-horizontal" autocomplete="off">
		<div class="form-item">
			<label class="item-label">密码：<span class="check-tips">（请输入密码）</span></label>
			<div class="controls">
				<input type="password" name="password" class="text input-large"  autocomplete="off"/>
			</div>
		</div>
		<div class="form-item">
			<label class="item-label">昵称：<span class="check-tips">（请输入新昵称）</span></label>
			<div class="controls">
				<input type="text" name="nickname" class="text input-large" autocomplete="off" value="<?php echo get_username();?>"/>
			</div>
		</div>
		<div class="form-item">
			<button type="submit" class="btn submit-btn ajax-post" target-form="form-horizontal">确 认</button>
			<button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
		</div>
	</form>
			</div>
	</div>

        </div>
        <!-- <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.onethink.cn" target="_blank">OneThink</a>管理平台</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div> -->
    </div>
    <!-- 内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/index.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>
    <script type="text/javascript" src="/Public/Mobile/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
	<script src="/Public/static/thinkbox/jquery.thinkbox.js"></script>

</body>
</html>