<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>湘潭市未成年人社会保护信息系统</title>
		<link rel="stylesheet" type="text/css" href="/Public/Admin/css/xtwcn/reset.css"/>
		<link rel="stylesheet" type="text/css" href="/Public/Admin/css/xtwcn/login.css"/>
		<script type="text/javascript" src="/Public/Admin/js/xtwcn/PIE.js" ></script>
	</head>
	<body>
		<div class="login">
			<form action="<?php echo U('login');?>" method="post" class="login-form">
				<h2>湘潭市未成年人社会保护信息系统</h2>
				<div class="form-input"><label class="icon">&#xe601;</label><input type="text" name="username" id="username" class="input" placeholder="请输入账号"></div>
				<div class="form-input"><label class="icon">&#xe600;</label><input type="password" name="password" id="password" class="input" placeholder="请输入密码"></div>
				<div class="form-input">
					<label class="remember"><input type="checkbox" id="saveInfo"/> 记住密码</label>
				</div>
				<!-- <div class="form-input">
					<input type="text" class="captchas" name="verify" placeholder="请输入验证码"/>
					<div class="captchas-img">
						<img class="verifyimg reloadverify" alt="点击切换" src="<?php echo U('Public/verify');?>" style="width:98px;height:32px;" />
					</div>
					<span class="change reloadverify" title="换一张">换一张</span>
				</div> -->
				<!-- <a id="login-button" href="information.html">登 录</a> -->
				<!-- <button type="submit" id="login-button">登 录</button> -->
				<button id="login-button" class="login-btn" type="submit">
                    <span class="on">登 录</span>
                </button>
                <div class="check-tips"></div>
			</form>
		</div>
	</body>
	<!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <!--<![endif]-->
    <script type="text/javascript" src="/Public/static/jquery.cookie.js"></script>
    <script type="text/javascript">
    	//表单提交
    	$(document)
	    	.ajaxStart(function(){
	    		$("button:submit").addClass("log-in").attr("disabled", true);
	    	})
	    	.ajaxStop(function(){
	    		$("button:submit").removeClass("log-in").attr("disabled", false);
	    	});

    	$("form").submit(function(){
    		var self = $(this);
    		$.post(self.attr("action"), self.serialize(), success, "json");
    		return false;

    		function success(data){
    			if(data.status){
    				window.location.href = data.url;
    			} else {
    				self.find(".check-tips").text(data.info);
    				//刷新验证码
    				$(".reloadverify").click();
    			}
    		}
    	});

	$(function(){
		//记住密码
        var $nameObj = $("input[name='username']");
		var $pwdObj = $("input[name='password']"); 
		var issave = document.cookie.split(";")[1].split("=")[1];
		// console.log(document.cookie);
		if(issave == 'true'){
			$nameObj.val(document.cookie.split(";")[2].split("=")[1]);
			$pwdObj.val(document.cookie.split(";")[3].split("=")[1]);
			$("#saveInfo").attr('checked',true);
		}else{
			$nameObj.val('');
			$pwdObj.val('');
			$("#saveInfo").attr('checked',false);
		}

		$(".login-btn").click(function(){
			
			var issave = $("#saveInfo").is(':checked');
			var uname = $nameObj.val();
			var npwd = $pwdObj.val(); 
			if(issave){
				document.cookie="issave="+'true';
				document.cookie="username="+uname;
				document.cookie="userpassword="+npwd;
			}else{
				document.cookie="issave="+'';
				document.cookie="username="+'';
				document.cookie="userpassword="+'';
			}
		});



		//初始化选中用户名输入框
		$("#username").focus();
		//刷新验证码
		var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });


	});
    </script>
</html>