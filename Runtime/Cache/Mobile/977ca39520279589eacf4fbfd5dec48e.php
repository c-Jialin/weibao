<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>湘潭市未成年人社会保护信息系统</title>
    <link rel="stylesheet" type="text/css" href="./Public/Admin/css/xtwcn/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="./Public/Admin/css/xtwcn/index.css"/>
    <script type="text/javascript" src="/Public/Admin/js/xtwcn/PIE.js"></script>
</head>
<body>
<header class="header">
    <div class="top">
        <img src="/Public/Admin/images/xtwcn/logo.png" class="logo"/>
        <!-- <h2>湘潭市未成年保护系统</h2> -->
        <ul class="right">
            <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a href="<?php echo U('Index/index');?>"><img
                    src="/Public/Admin/images/xtwcn/returnhome.png"><br><span>返回首页</span></a></li>
            <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a
                    href="<?php echo U('User/updatepassword');?>"><img
                    src="/Public/Admin/images/xtwcn/changepass.png"><br><span>修改密码</span></a></li>
            <li><img src="/Public/Admin/images/xtwcn/top-solid.png" class="solid"><a
                    href="<?php echo U('Public/logout');?>"><img
                    src="/Public/Admin/images/xtwcn/reload.png"><br><span>重新登录</span></a></li>
        </ul>
    </div>
    <div class="bottom">
        <ul>
            <li><a href="<?php echo U('Case/suoyou');?>">所有案件</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png"
                                                           class="solid"></li>
            <li><a href="<?php echo U('Case/zaichu');?>">正在处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png"
                                                           class="solid"></li>
            <li><a href="<?php echo U('Case/daichu');?>">待处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png"
                                                          class="solid"></li>
            <li><a href="<?php echo U('Case/wancheng');?>">完成处理</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png"
                                                             class="solid"></li>
            <li><a href="<?php echo U('Case/guidang');?>">归档案件</a><img src="/Public/Admin/images/xtwcn/bottom-solid.png"
                                                            class="solid"></li>
        </ul>
        <form class="form-inline">
            <div class="form-group">
                <label for="search" class="sr-only">Password</label>
                <input type="text" class="form-control" id="search" placeholder="">
            </div>
            <a href=""><img src="/Public/Admin/images/xtwcn/search-btn.png"></a>
        </form>
    </div>
</header>
<?php
 $id = $_GET['id']; $act = $_GET['act']; ?>
<div class="page container">
    <div class="jindu">
        <h4 class="title">案件：
            <?php echo $Clist['name'] . ' '; foreach (unserialize($Clist['health']) as $va) { echo getHealth($va) . ' '; } foreach(unserialize($Clist['inner_predicament']) as $vb){ echo getNeixin($vb,$id) . ' '; } foreach (unserialize($Clist['growth_dilemma']) as $kc => $vc) { echo getFxian($vc); echo getFengxian($vc); echo '级 '; } ?>
        </h4>
        <div class="tips">
            <span>案件正在处理阶段：<?php echo getStage($Clist['case_status'])?></span><span>指挥调度责任人：无</span><span>社会责任人：<?php echo $Clist['fill_in_person']?></span>
        </div>
        <div class="stage">
            <?php $id = empty($Clist['id']) ? $_GET['id'] : $Clist['id']; ?>
            <span class="<?php if($act == ''){?>active<?php }?>"><a href="<?php echo U('case/index', array('id' => $id));?>">采集</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/trial', array('id' => $id, 'act' => 'chushen'));?>">初审</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/last_instance', array('id' => $id, 'act' => 'zhongshen'));?>">审批</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/dispatch', array('id' => $id, 'act' => 'diaodu'));?>">调度</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/deal_with', array('id' => $id, 'act' => 'chuzhi'));?>">协同处置</a></span><span class="solid">→</span>
            <span class="<?php if($act == 'jiean'){?>active<?php }?>"><a href="<?php echo U('case/finish', array('id' => $id, 'act' => 'jiean'));?>">结案</a></span></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/visit', array('id' => $id, 'act' => 'huifang'));?>">回访</a></span></span>
        </div>
    </div>
    <div class="information">
        <div class="title <?php if($act == 'jiean'){?>active<?php }?>" data-toggle="collapse" href="#jiean"
             aria-expanded="<?php if($act == 'jiean'){?>true<?php }else{?>false<?php }?>"
             aria-controls="collapseExample">当前阶段：结案 <span class="caret"></span></div>
        <form action="<?php echo U('Case/finish');?>" method="post">
            <div id="jiean" class="submit collapse <?php if($act == 'jiean'){?>in<?php }?>">
                结案表：
                <table cellpadding="0" cellspacing="0" border="0" class="table_1"
                       style="margin-bottom:10px; width:863px;margin: auto;margin-top: 15px;margin-bottom: 15px;font-size: 12px;">
                    <tbody>
                    <tr>
                        <td class="td_input" align="left">县/市/区：</td>
                        <td class="td_input" align="left" style="width:170px;">
                            <select class="select left" name="household_city_code" id="shi3" style="float:left;" onChange="nav3(this,2)">
                                <?php echo "<option value=".$Clist['household_city_code']['k'].">
                                ".$Clist['household_city_code']['v']."</option>"; ?>
                                <?php foreach ($shi as $v) {?>
                                <option value="<?php echo $v['region_id']?>"><?php echo $v['region_name']?></option>
                                <?php }?>
                            </select>
                            <select class="select left" name="household_area_code" id="qu3" style="float:left;">
                                <?php echo "<option value=".$Clist['household_area_code']['k'].">
                                ".$Clist['household_area_code']['v']."</option>"; ?>
                            </select>
                        </td>
                        <td align="right" style="width:100px;">街/镇/村/社区：</td>
                        <td class="td_input" align="left" style="width:250px;">
                            <input type="text" name="home_address" value="<?php echo $Clist['home_address'];?>" style="display:inline-block;height:26px;width:240px;">
                        </td>
                        <td align="right">编号：</td>
                        <td class="td_input"><?php echo $Clist['case_number'];?></td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="0" cellspacing="0" border="0" class="table_2" style=" width:800px;">
                    <tbody>
                    <tr>
                        <td class="td_item" style="width:100px;">姓名</td>
                        <td class="td_input" style="width:110px;"><?php echo $Clist['name']?></td>
                        <td class="td_item" style="width:80px;">性别</td>
                        <td class="td_input" style="width:120px;"><label><?php echo getSex($Clist['sex'])?></label></td>
                        <td class="td_item" style="width:100px; ">出生年月</td>
                        <td class="td_input" style="width:80px;"><?php echo $birthday[0];?>-<?php echo $birthday[1];?></td>
                    </tr>
                    <tr>
                        <td class="td_item">接收日期</td>
                        <td class="td_input" colspan="3"><?php echo date("Y-m-d",$Clist['fill_in_time'])?></td>
                        <td class="td_item">结案日期</td>
                        <td class="td_input"><?php echo date("Y-m-d",time())?></td>
                    </tr>
                    <tr>
                        <td class="td_item">初始机构评估<br>最高风险等级</td>
                        <td class="td_input" colspan="5">
                            <?php $fengxian = unserialize($Clist['growth_dilemma']); foreach ($fengxian as $v1) { $str[] = getFengxian($v1); } echo max($str); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">中期评估<br>最高风险等级</td>
                        <td class="td_input" colspan="5">
                            <?php $fengxian1 = unserialize($Clist['growth_dilemmas']); foreach ($fengxian1 as $v2) { $str[] = getFengxian($v2); } echo max($str); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">困境回顾</td>
                        <td class="td_input" colspan="5">
                            <input type="text" name="dilemma_review" value="<?php echo $Clist['dilemma_review']?>" class="input">
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">提供帮扶情况</td>
                        <td class="td_input" colspan="5">
                            <input type="text" name="help_situation" value="<?php echo $Clist['help_situation']?>" class="input">
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">专业反思</td>
                        <td class="td_input" colspan="5">
                            <input type="text" name="professional_Reflect" value="<?php echo $Clist['professional_Reflect']?>" class="input">
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">目标评估及建议</td>
                        <td class="td_input" colspan="5">
                            <input type="text" name="recommendations" value="<?php echo $Clist['recommendations']?>" class="input">
                        </td>
                    </tr>
                    <tr>
                        <td class="td_item">帮扶等级</td>
                        <td class="td_input" colspan="5">
                            <select name="growth_dilemmass" id="" class="select">
                                <?php
 for ($i=1; $i<=5; $i++) { if ($i == $Clist['growth_dilemmass']) { echo "<option value=".$i." selected='selected'>".$i."</option>"; } else { echo "<option value=".$i.">".$i."</option>"; } }?>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <div class="li" style="margin-bottom: 8px;">
                    <div class="left">流程操作</div>
                    <div class="right">
                        <label>
                            <input type="checkbox" <?php if($Clist['visit_status'] == 1) echo 'checked'?> class="checkbox visit_form" name="visit_status" value="1">
                            <span class="radio-text">回访</span>
                        </label>
                        <input type="radio" class="radio formRadio" name="visit_form" value="1" <?php if($Clist['visit_form'] == 1) echo 'checked'?>><b
                            class="radio-text">指定</b>
                        <input type="text" class="formText" name="visit_form1" value="<?php if($Clist['visit_form'] == 1) echo $Clist['visit_way']; ?>" style="height:28px;width:40px"><b
                            class="radio-text"> 天</b>
                        &emsp;
                        <input type="radio" class="radio formRadio" name="visit_form" value="2" <?php if($Clist['visit_form'] == 2) echo 'checked'?>><b
                            class="radio-text">周期</b>
                        <input type="text" class="formText" name="visit_form2_1" value="<?php if($Clist['visit_form'] == 2){ $str = unserialize($Clist['visit_way']); echo $str[0];}?>" style="height:28px;width:40px"><b
                            class="radio-text"> 天</b>
                        <input type="text" class="formText" name="visit_form2_2" value="<?php if($Clist['visit_form'] == 2){ $str = unserialize($Clist['visit_way']); echo $str[1];}?>" style="height:28px;width:40px"><b
                            class="radio-text"> 次</b>
                        <br>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <input type="hidden" name="id" value="<?php echo $id;?>">
                <div class="li">
                    <div class="left">结案意见</div>
                    <div class="right">
                        <textarea name="finish_suggestion"><?php echo $Clist['finish_suggestion']; ?></textarea>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div class="li">
                    <div class="left">结案人</div>
                    <div class="right">
                        <span class="span-bottom"><?php echo ($member); ?></span>&nbsp
                        <span class="span-bottom">结案日期</span>
                        <span class="span-bottom">
                            <?php if(!empty($Clist['finish_time'])){ echo $Clist['finish_time']; } else { echo date('Y-m-d', time()); }?>
                        </span>
                        &nbsp;
                        <label>手动设置处置日期 <input type="datetime-local" name="finish_time" style="font-size:12px;line-height:12px;"></label>
                    </div>
                    <div id="auth_status" style="clear:both;" case_status="<?php echo $Clist['case_status']; ?>" is_auth="<?php echo IS_AUTH?>"></div>
                </div>
                <button onclick="return auth()" type="submit" style="height: 30px;width: 70%;margin: auto;line-height: 30px;background-color: rgb(161,161,161);text-align: center;color: #000000;cursor: pointer;margin-top: 10px;display:block">提交</button>
            </div>
        </form>
        <br>
        <?php ?>
    </div>
</div>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.js"></script>
<script type="text/javascript" src="/Public/Admin/js/xtwcn/bootstrap.min.js"></script>
<!-- 新增编辑器引入文件 -->
<link rel="stylesheet" href="/Public/static/kindeditor/default/default.css"/>
<script src="/Public/static/kindeditor/kindeditor-min.js"></script>
<script src="/Public/static/kindeditor/zh_CN.js"></script>
<!--图片上传-->
<script>
    window.URL = window.URL || window.webkitURL;
    var fileElem = document.getElementById("mypic"),
        fileList = document.getElementById("imgList");
    function handleFiles(obj) {
        var files = obj.files,
            img = new Image();
        if (window.URL) {
            //File API
            fileList.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
            fileList.width = 160;
            fileList.onload = function (e) {
                window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
            }
        } else if (window.FileReader) {
            //opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = function (e) {
                fileList.src = this.result;
                fileList.width = 160;
            }
        } else {
            //ie
            obj.select();
            obj.blur();
            var nfile = document.selection.createRange().text;
            document.selection.empty();
            fileList.src = nfile;
            fileList.width = 160;
            img.onload = function () {
            }
            //fileList.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src='"+nfile+"')";
        }
    }
</script>
<script>
    /* 表单禁用 */
    KindEditor.ready(function (K) {

        var ed = K.create('textarea[name="main_dilemma"]', {
            resizeType: 1,
            // uploadJson : "<?php echo U('Base/uploadImg',array('path'=>'goods'));?>",
            allowPreviewEmoticons: false,
            allowImageUpload: true,
            items: [
                'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'emoticons', 'image', 'multiimage', 'link'],
            afterBlur: function () {
                this.sync();
            }
        });
    });
    $(function () {
        $('.huifang').on('click', '.huifangsave', function () {
            var str = $(this).siblings('textarea').val();
            var id = '<?php echo $id;?>';
            var va = $('.huifangsave').index(this);
            $.post('index.php?s=/Case/tihui', {id: id, str: str, va: va}, function (res) {
                if (res == 1) {
                    alert('提交成功');
                }
            })
        });
        // $('.huifangsave').on('click',function(){
        // 	alert($(this).siblings('textarea').val());
        // });
        $('.healthradio').click(function () {
            $('.healthbox').prop('checked', false);
        })
        $('.healthbox').click(function () {
            $('.healthradio').prop('checked', false);
        })
    })
    var uid = '<?php echo $group_id;?>';
    if (uid == 1) {
        $(function () {
            $('#chushen input').prop('disabled', 'disabled');
            $('#chushen select').prop('disabled', 'disabled');
            $('#chushen textarea').prop('disabled', 'disabled');
            $('#zhongshen input').prop('disabled', 'disabled');
            $('#zhongshen select').prop('disabled', 'disabled');
            $('#zhongshen textarea').prop('disabled', 'disabled');
            $('#diaodu input').prop('disabled', 'disabled');
            $('#diaodu select').prop('disabled', 'disabled');
            $('#diaodu textarea').prop('disabled', 'disabled');
            $('#xinxicaiji input').prop('disabled', 'disabled');
            $('#xinxicaiji select').prop('disabled', 'disabled');
            $('#xinxicaiji textarea').prop('disabled', 'disabled');
        })
    } else if (uid == 2) {
        $(function () {
            $('#jiean input').prop('disabled', 'disabled');
            $('#jiean select').prop('disabled', 'disabled');
            $('#jiean textarea').prop('disabled', 'disabled');
            $('#xinxicaiji input').prop('disabled', 'disabled');
            $('#xinxicaiji select').prop('disabled', 'disabled');
            $('#xinxicaiji textarea').prop('disabled', 'disabled');
            $('#diaodu input').prop('disabled', 'disabled');
            $('#diaodu select').prop('disabled', 'disabled');
            $('#diaodu textarea').prop('disabled', 'disabled');
            $('#chuzhi input').prop('disabled', 'disabled');
            $('#chuzhi select').prop('disabled', 'disabled');
            $('#chuzhi textarea').prop('disabled', 'disabled');
        })
    } else if (uid == 3) {
        $(function () {
            $('#zhongshen input').prop('disabled', 'disabled');
            $('#zhongshen select').prop('disabled', 'disabled');
            $('#zhongshen textarea').prop('disabled', 'disabled');
            $('#diaodu input').prop('disabled', 'disabled');
            $('#diaodu select').prop('disabled', 'disabled');
            $('#diaodu textarea').prop('disabled', 'disabled');
            $('#chushen input').prop('disabled', 'disabled');
            $('#chushen select').prop('disabled', 'disabled');
            $('#chushen textarea').prop('disabled', 'disabled');
        })
    } else if (uid == 4) {
        $(function () {
            $('#zhongshen input').prop('disabled', 'disabled');
            $('#zhongshen select').prop('disabled', 'disabled');
            $('#zhongshen textarea').prop('disabled', 'disabled');
            $('#xinxicaiji input').prop('disabled', 'disabled');
            $('#xinxicaiji select').prop('disabled', 'disabled');
            $('#xinxicaiji textarea').prop('disabled', 'disabled');
            $('#chuzhi input').prop('disabled', 'disabled');
            $('#chuzhi select').prop('disabled', 'disabled');
            $('#chuzhi textarea').prop('disabled', 'disabled');
        })
    }
    $(function () {
        var text = '<div class="li zhuchibeizhu"><div class="left">处置备注 </div><div class="right"> <textarea></textarea></div><div style="clear: both;"></div></div>';
        var strVar = '<div class="li huifangjilu"><div class="left">回访记录 </div><div class="right"> <textarea></textarea></div><div style="clear: both;"></div></div>';
        $('.title').click(function () {
            $(this).children('.caret').toggleClass('caret-active');
        })
        $('#addchuzhi').click(function () {
            $('.zhuchibeizhu:last').after(text);
            $('.zhuchibeizhu:last').children('.right').children('textarea').attr('name', 'management_record' + ($('.zhuchibeizhu').length - 1));
        })
        $('#addhuifang').click(function () {
            $('.huifangjilu:last').after(strVar);
            $('.huifangjilu:last').children('.right').children('textarea').attr('name', 'visit_suggestion' + ($('.huifangjilu').length - 1));
        })
    })
</script>
<script type="text/javascript">
    function auth() {
        var case_status = $("#auth_status").attr('case_status');
        var is_auth = $("#auth_status").attr('is_auth');
        if (case_status == 'chuzhi' && is_auth) {
            return true;
        }
        return false;
    }
    function nav(obj, type) {
        $.post("<?php echo U('Case/Linkage');?>", {"id": obj.value, "type": type}, function (res) {
            if (type == 1) {
                if (obj.value > 0) {
                    $("#Town1").html(res);
                } else {
                    $("#Town1").html("<option>请选择市</option>");
                }
                $("#Country1").html("<option>请选择区</option>");
            } else if (type == 2) {
                if (obj.value > 0) {
                    $("#Country1").html(res)
                } else {
                    $("#Country1").html("<option>请选择区</option>");
                }
            }
        })
    }
    function nav1(obj, type) {
        $.post("<?php echo U('Case/Linkage');?>", {"id": obj.value, "type": type}, function (res) {
            if (type == 1) {
                if (obj.value > 0) {
                    $("#shi1").html(res);
                } else {
                    $("#shi1").html("<option>请选择市</option>");
                }
                $("#qu1").html("<option>请选择区</option>");
            } else if (type == 2) {
                if (obj.value > 0) {
                    $("#qu1").html(res)
                } else {
                    $("#qu1").html("<option>请选择区</option>");
                }
            }
        })
    }
    function nav2(obj, type) {
        $.post("<?php echo U('Case/Linkage_top');?>", {"id": obj.value, "type": type}, function (res) {
            if (type == 3) {
                if (obj.value > 0) {
                    $("#Town2").html(res);
                } else {
                    $("#Town2").html("<option>请选择街/镇</option>");
                }
                $("#Country2").html("<option>请选择村/社区</option>");
            } else if (type == 4) {
                if (obj.value > 0) {
                    $("#Country2").html(res)
                } else {
                    $("#Country2").html("<option>请选择村/社区</option>");
                }
            }
        })
    }
    function nav3(obj, type) {
        $.post("<?php echo U('Case/Linkage');?>", {"id": obj.value, "type": type}, function (res) {
            if (type == 1) {
                if (obj.value > 0) {
                    $("#shi3").html(res);
                } else {
                    $("#shi3").html("<option>请选择市</option>");
                }
                $("#qu3").html("<option>请选择区</option>");
            } else if (type == 2) {
                if (obj.value > 0) {
                    $("#qu3").html(res)
                } else {
                    $("#qu3").html("<option>请选择区</option>");
                }
            }
        })
    }
</script>
</body>
</html>