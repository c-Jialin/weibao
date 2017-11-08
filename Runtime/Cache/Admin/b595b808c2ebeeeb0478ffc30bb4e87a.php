<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|Wode管理平台</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
    <style>
        body{padding: 0}
    </style>

    <style>
    .bottom ul li{overflow: hidden;}
    </style>
</head>
<body>
    <!-- 头部 -->
   <!--  <div class="header">
       Logo
       <span class="logo">湘潭市未成年后台管理系统</span>
       /Logo
   
       主导航
       <ul class="main-nav">
           <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
       </ul>
       /主导航
   
       用户栏
       <div class="user-bar">
           <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
           <ul class="nav-list user-menu hidden">
               <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
               <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
               <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
               <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
           </ul>
       </div>
   </div> -->
   <header class="header">
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
		    		<!-- <li><a href="">案件调度</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid"></li>
		            <li><a href="">信息采集</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid"></li>
 -->		         
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
                    <li><a href="<?php echo U('User/index');?>">系统管理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png" class="solid">
                    </li>
		    	</ul>

				<?php if(ACTION_NAME != 'index'): ?><form class="form-inline" id="form1" action="<?php echo U('Case/'.ACTION_NAME);?>" method="post">
				  <div class="form-group" style="display:inline-block;margin-top:-11px">
                      <?php if(ACTION_NAME == suoyou): ?><select name="shequ" id="shequ">
                              <option value="0">请选择社区...</option>
                              <?php if(is_array($shequ)): $i = 0; $__LIST__ = $shequ;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["region_id"]); ?>"><?php echo ($vo["region_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                          </select><?php endif; ?>
				    <input type="text" class="form-control" name="search" id="search" placeholder="">
				  </div>
				  <span id="searchbtn"><img src="/Public/Admin/images/xtwcn/search-btn.png"></span>
				</form><?php endif; ?>
		    		
		    </div>

		</header>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
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
        <div id="main" class="main">
            
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
            

            
	<div style="margin:-20px">
		<img src="/Public/Admin/images/xtwcn/welcome.png" style="display:block;float:left;height:100%"/>
		<div class="index-right" style="margin-left:200px">
			<div class="index-title" style="text-align:right;height:30px;line-height:30px;color:#999;padding-right:1em;border-bottom:1px solid #ccc;font-size:12px">
			<span>用户名：<?php echo ($user_info['username']); ?></span>
			&emsp;&emsp;
			<span>上次登录时间：<?php echo (date('Y-m-d h:i',$user_info['last_login_time'])); ?></span>
			<div>
			<div class="index-content" style="width:781px;height:296px;margin:auto;margin-top:50px">
				<div class="left" style="float:left;width:231px">
					<?php if($uid == 3 || UID == 1){?>
                    <a href="index.php?s=/Case/index.html" style="display:block;margin-bottom:10px;text-decoration:none;"><img src="/Public/Admin/images/xtwcn/informationcaiji.png"  alt=""></a>
                    <?php }?>
					<a href="index.php?s=/Case/daichu.html" style="display:block;text-decoration:none;"><img src="/Public/Admin/images/xtwcn/casedeal.png"  alt=""></a>
					<ul style="padding:0;margin:0;list-style:none;text-align:left;text-indent:1em;font-weight:500;font-size:16px">
						<li><a style="text-decoration:none;color:#323232" href="<?php echo U('Case/zaichu');?>">正在处理的案件 (<?php echo $zaichu;?>)</a></li>
						<li><a style="text-decoration:none;color:#323232" href="<?php echo U('Case/zaichu');?>">待处理的案件&emsp; (<?php echo $zaichu;?>)</a></li>
						<li><a style="text-decoration:none;color:#323232" href="">即将超时的案件 (<?php echo $jijiang;?>)</a></li>
						<li><a style="text-decoration:none;color:#323232" href="">已经超时的案件 (<?php echo $chaoshi;?>)</a></li>
					</ul>
				</div>
				<div style="width:450px;height:296px;float:right;border:1px solid #149be7;position:relative;">
					<div style="background-color:#149be7;color:#fff;text-align:left;text-indent:1em;">
							消息中心
					</div>
					<div style="height:267px;overflow-y:scroll">
					<table style="width:100%;text-align:left;text-indent:1em" cellspacing="0" cellpadding="0" >
						<tbody style="font-size:13px">
						<?php foreach ($xiaoxiList as $v) {?>
							<tr>
								<td style="border-bottom:1px dashed #999">
								<a href="index.php?s=/Case/daichu.html" style="color:#323232;text-decoration:none"><?php echo $v['name']; ?>案件需要处理。
								</a>
								</td>
							</tr>
						<?php }?>
						</tbody>
					</table>
					</div>
        		  </div>
            	</div>
            </div>
        </div>  
    </div>
    <div style="clear:both"></div>
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
    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
    <script type="text/javascript">

//        $('#searchbtn').click(function(){
//            var action = "<?php echo ACTION_NAME;?>"
//            var shequ  = $("#shequ").val();
//            var name   = $("#search").val();
//            document.location.href='index.php?s=/Case/'+action+'/shequ/'+shequ+'/name/'+name+'.html';
////            alert('index.php?s=/Case/'+action+'/shequ/'+shequ+'/name/'+name+'.html');
//        });

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
        $("#searchbtn").click(function(){
            $("#form1").submit();
        })
    </script>
    
<script type="text/javascript">
    $(function(){
        $('.copyright').html('<div class="copyright"> ©2015 版权所有</div>');
        $('.sidebar').remove();
    })
</script>

</body>
</html>