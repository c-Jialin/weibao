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
body{padding: 0};
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
            

            
    <!-- 主体 -->
    <!-- <div id="indexMain" class="index-main"> -->
       <!-- 插件块 -->
       <!-- <div class="container-span"><?php echo hook('AdminIndex');?></div>
    </div> -->
  <!--   <div>
        <div class="bottom-erji" style="top:50px;position:fixed;left:0;right:0">
           <ul>
               <li class="active"><a href="<?php echo U('Index/index');?>">所有案件</a></li>
               <li><a href="<?php echo U('Case/daichu');?>">待处理</a></li>
               <li><a href="<?php echo U('Case/wancheng');?>">完成处理</a></li>
               <li><a href="">正在处理</a></li>
               <li><a href="<?php echo U('Case/guidang');?>">归档案件</a></li>
           </ul>
           <div class="search-form fr cf">
           <div class="sleft">
               <input type="text" name="nickname" class="search-input" value="<?php echo I('nickname');?>" placeholder="请输入用户昵称或者ID">
               <a class="sch-btn" href="javascript:;" id="search" url="<?php echo U('index');?>"><i class="btn-search"></i></a>
           </div>
       </div>
       </div>
    </div>
    <div class="main-title">
    </div> -->
    <!-- <div class="cf"> -->
        <!-- <div class="fl">
            <a class="btn" href="<?php echo U('User/add');?>">新 增</a>
            <button class="btn ajax-post" url="<?php echo U('User/changeStatus',array('method'=>'resumeUser'));?>" target-form="ids">启 用</button>
            <button class="btn ajax-post" url="<?php echo U('User/changeStatus',array('method'=>'forbidUser'));?>" target-form="ids">禁 用</button>
            <button class="btn ajax-post confirm" url="<?php echo U('User/changeStatus',array('method'=>'deleteUser'));?>" target-form="ids">删 除</button>
        </div> -->

        <!-- 高级搜索 -->
        
    <!-- </div> -->
    <!-- 数据列表 -->
    <div style="float: right; margin-bottom: 2px;">
        <span>excel导出</span>
        <select id="select_" >
            <option class="" value="0">请选择导出方式</option>
            <option class="" value="1">全部导出</option>
            <?php if(is_array($shequ)): $i = 0; $__LIST__ = $shequ;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class="" value="<?php echo ($vo["region_id"]); ?>"><?php echo ($vo["region_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
        <form action="<?php echo U('Case/enrollListExcel');?>" method="post" id="myform_excle"></form>
    </div>
    <div class="data-table table-striped" >
    <table class="">
    <thead>
    <tr><th colspan="13" style="text-align:center">社会保护人员基本信息</th></tr>
        <tr>
        <th class="">序号</th>
        <th class="">登记时间</th>
        <th >姓名</th>
        <th >性别</th>
        <th >年龄</th>
        <th >民族</th>
        <th >家庭住址</th>
        <th >健康状况</th> 
        <th class="" >摸底上报风险等级</th>
        <th class="" >机构评估风险等级</th>
        <th class="" >帮扶后最高风险等级</th>
        <th class="" >流程</th>
        <th class="" >操作</th>
        </tr>
    </thead>
    <tbody>
    <?php
 if (isset($CaseList) && !empty($CaseList)) { foreach ((array)$CaseList as $k => $v) { ?>
    <tr>
        <td><?php echo $k+1?></td>
        <td><?php echo date("Y-m-d",$v['fill_in_time'])?></td>
        <td><?php echo $v['name']?></td>
        <td><?php echo getSex($v['sex'])?></td>
        <td><?php echo getAge($v['birthday'])?></td>
        <td><?php echo $v['nation']?></td>
        <td><?php echo $v['home_address']?></td>
        <td><?php  $health = unserialize($v['health']); foreach ($health as $val) {?>
            <?php echo getHealth($val,$v['id']);?><br>
            <?php }?>
        </td>
        <td><?php $fengxian = unserialize($v['growth_dilemma']); foreach ($fengxian as $v1) { $str[] = getFengxian($v1); } echo max($str); ?></td>
        <td><?php $fengxians = unserialize($v['growth_dilemmas']); foreach ($fengxians as $v2) { $strs[] = getFengxian($v2); } echo max($strs); ?></td>
       <td><?php $fengxianss = unserialize($v['growth_dilemmass']); foreach ($fengxianss as $v2) { $strss[] = getFengxian($v2); } echo max($strss); ?></td>

        <?php
 $turn_related = M('case')->where(array('id'=>$v['id']))->getField('turn_related'); ?>

        <td>
            <?php if($v['case_status'] == 'huifang'){?>
            回访完成
            <?php }else if($v['visit_status'] == null && $v['finish_suggestion'] !== ''){?>
            结案完成
            <?php }else if($v['finish_suggestion'] !== ''){?>
            结案完成
            <?php }else if($v['management_status'] == 1){?>
            处置完成
            <?php }else if($v['case_status'] == 'bohuiCz'){?>
            处置驳回
            <?php }else if($v['dispatch_instance'] !== ''){?>
            调度完成
            <?php }else if($v['last_instance_status'] == 1){?>
            审批完成
            <?php }else if($v['case_status'] == 'bohuiCs'){?>
            初审驳回
            <?php }else if($v['trial_status'] == 1){?>
            初审完成
            <?php }else if($v['case_status'] == 'caiji'){?>
            采集完成
            <?php }else if($v['case_status'] == 'bohuiC'){?>
            采集驳回
            <?php }?>
        </td>

        <td>
            <?php if(($v['case_status'] == 'huifang') || ($v['visit_status'] == null && $v['finish_suggestion'] !== '')){?>
            <span style="color:#A0A0A0">完成</span>
            <a href="index.php?s=/Case/visit/id/<?php echo $v['id'];?>/act/huifang.html">查看</a>
            <?php }else if(($v['visit_status'] == 1 && in_array('3', $uid)) || ($v['visit_status'] == 1 && UID == 1)){?>
            <a href="index.php?s=/Case/visit/id/<?php echo $v['id'];?>/act/huifang.html">回访</a>
            <?php }else if($v['visit_status'] == 1 && !in_array('3', $uid)){?>
            <span style="color:#A0A0A0">回访</span>
            <a href="{:U('Case/finish',array('id'=>$v['id']))}">查看</a>
            <?php }else if(($v['management_status'] == 1 && in_array('4', $uid)) || ($v['management_status'] == 1 && UID == 1)){?>
            <a href="index.php?s=/Case/finish/id/<?php echo $v['id'];?>/act/jiean.html">结案</a>
            <?php }else if($v['management_status'] == 1 && !in_array('4', $uid)){?>
            <span style="color:#A0A0A0">结案</span>
            <?php }else if((in_array('1', $uid) || in_array('3', $uid)) || ($v['case_status'] == 'diaodu' && $v['dispatch_instance'] != '' && $turn_related == $uid) || ($v['case_status'] == 'diaodu' && $v['dispatch_instance'] != '' && UID == 1)){?>
            <a href="index.php?s=/Case/deal_with/id/<?php echo $v['id'];?>/act/chuzhi.html">处置</a>
            <?php }else if($v['case_status'] == 'diaodu' && $v['dispatch_instance'] != '' && $turn_related !== $uid){?>
            <span style="color:#A0A0A0">处置</span>
            <?php }else if(($v['last_instance_status'] == 1 && in_array('4', $uid)) || ($v['last_instance_status'] == 1 && UID == 1) || ($v['case_status'] == 'bohuiCz' && in_array('4', $uid)) || ($v['case_status'] == 'bohuiCz' && UID == 1)){?>
            <a href="index.php?s=/Case/dispatch/id/<?php echo $v['id'];?>/act/diaodu.html">调度</a>
            <?php }else if($v['last_instance_status'] == 1 && !in_array('4', $uid)){?>
            <span style="color:#A0A0A0">调度</span>
            <?php }else if(($v['case_status'] == 'chushen' && $v['trial_status'] == 1 && in_array('2', $uid)) || ($v['case_status'] == 'chushen' && $v['trial_status'] == 1 && UID == 1)){?>
            <a href="index.php?s=/Case/last_instance/id/<?php echo $v['id'];?>/act/zhongshen.html">审批</a>
            <?php }else if($v['case_status'] == 'chushen' && $v['trial_status'] == 1 && !in_array('2', $uid)){?>
            <span style="color:#A0A0A0">审批</span>
            <?php }else if(($v['case_status'] == 'caiji' && in_array('4', $uid)) || ($v['case_status'] == 'caiji' && UID == 1) || ($v['case_status'] == 'bohuiCs' && in_array('4', $uid)) || ($v['case_status'] == 'bohuiCs' && UID == 1)){?>
            <a href="index.php?s=/Case/trial/id/<?php echo $v['id'];?>/act/chushen.html">初审</a>
            <?php }else if($v['case_status'] == 'caiji' && !in_array('4', $uid)){?>
            <span style="color:#A0A0A0">初审</span>
            <?php }else if(($v['case_status'] == 'bohuiC' && in_array('3', $uid)) || ($v['case_status'] == 'bohuiC' && UID == 1)){?>
            <a href="index.php?s=/Case/index/id/<?php echo $v['id'];?>.html">采集</a>
            <?php }else if(!in_array('3', $uid)){?>
            <span style="color:#A0A0A0">采集</span>
            <?php }?>
            <?php if (UID == 1) { ?>
            <a href="index.php?s=/Case/suoyou/id/<?php echo $v['id'];?>.html">删除</a>
            <?php } ?>
        </td>
    </tr>
          <?php } } else { ?>
            <tr><td colspan="9" class="text-center">暂无数据</td></tr>
           <?php } ?>
    </tbody>
    </table>
    </div>
    <div class="page">
        <?php echo ($Page); ?>
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
    $('#select_').change(function(){
        var option_value = $(this).find("option:selected").val();
//        alert(option_value);
        switch (option_value){
            case '1' :
                $('#myform_excle').children().remove();
                $('#myform_excle').append('<span>全部:</span><input type="text" name="all" id="all" value="all"/> <input type="hidden" name="leix" value="all"/> <input type="hidden" name="meet_id" value="<?php echo I('meet_id');?>"/> <input type="submit" class="btn" ></a>');
                break;
            case option_value :
                $('#myform_excle').children().remove();
                $('#myform_excle').append('<span></span><input type="hidden" name="shequ" id="shequ" value="<?php echo getShequ('+option_value+')?>"/> <input type="hidden" name="leix" value="'+option_value+'"/> <input type="hidden" name="meet_id" value="<?php echo I('meet_id');?>"/> <input type="submit" class="btn" ></a>');
                break;
//            case '3' :
//                $('#myform_excle').children().remove();
//                $('#myform_excle').append(' <span>编号:</span><input type="text" name="no_start" id="no_start" value=""/> - <input type="text" name="no_end" id="no_end" value=""/> <input type="hidden" name="leix" value="enroll_no"/> <input type="hidden" name="meet_id" value="<?php echo I('meet_id');?>"/> <input type="submit" class="btn" ></a> ');
//                break;
        }
    });
    $(function(){
        var action = "<?php echo ACTION_NAME;?>";
        $("#action").val(action);
    })
    /* 插件块关闭操作 */
    $(".title-opt .wm-slide").each(function(){
        $(this).click(function(){
            $(this).closest(".columns-mod").find(".bd").toggle();
            $(this).find("i").toggleClass("mod-up");
        });
    })
    $(function(){
        // $('#main').attr({'id': 'indexMain','class': 'index-main'});
        $('.copyright').html('<div class="copyright"> ©2013 <a href="http://www.topthink.net" target="_blank">topthink.net</a> 上海顶想信息科技有限公司版权所有</div>');
        $('.sidebar').remove();
    })
</script>

</body>
</html>