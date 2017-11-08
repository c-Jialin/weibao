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
            <span><a href="<?php echo U('case/index', array('id' => $id));?>">采集</a></span><span class="solid">→</span>
            <span class="<?php if($act == 'chushen'){?>active<?php }?>"><a href="<?php echo U('case/trial', array('id' => $id, 'act' => 'chushen'));?>">初审</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/last_instance', array('id' => $id, 'act' => 'zhongshen'));?>">审批</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/dispatch', array('id' => $id, 'act' => 'diaodu'));?>">调度</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/deal_with', array('id' => $id, 'act' => 'chuzhi'));?>">协同处置</a></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/finish', array('id' => $id, 'act' => 'jiean'));?>">结案</a></span></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/visit', array('id' => $id, 'act' => 'huifang'));?>">回访</a></span></span>
        </div>
    </div>
    <div class="information">
        <div class="title <?php if($act == 'chushen'){?>active<?php }?>" data-toggle="collapse" href="#chushen"
             aria-expanded="<?php if($act == 'chushen'){?>true<?php }else{?>false<?php }?>"
             aria-controls="collapseExample">当前阶段：初审<span class="caret"></span></div>
        <form action="<?php echo U('Case/trial');?>" method="post">
            <div id="chushen" class="submit collapse <?php if($act == 'chushen'){?>in<?php }?>">
                <div style="margin-bottom: 8px;">指挥中心或专业机构评估风险风险等级</div>
                <table class="table_2">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                    <tr>
                        <td class="td_item">成长困境<br>及风险等级</td>
                        <td colspan="6" valign="top" style="padding:0 0;">
                            <table cellpadding="0" cellspacing="0" class="table_3" border="0" width="100%"
                                   style="border:none;">
                                <tbody>
                                <tr>
                                    <td class="td_input">1、孤儿或因法定监护人服刑、重病、重残等原因实际无人监护的未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="1" <?php if(in_array(1,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="2" <?php if(in_array(2,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="3" <?php if(in_array(3,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="4" <?php if(in_array(4,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="5" <?php if(in_array(5,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                       value="6" <?php if(in_array(6,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input">2、因家庭暴力、遭受侵害等原因得不到适当监护的未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="7" <?php if(in_array(7,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="8" <?php if(in_array(8,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="9" <?php if(in_array(9,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="10" <?php if(in_array(10,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="11" <?php if(in_array(11,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                       value="12" <?php if(in_array(12,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input">3、因家庭困境面临辍学和失去基本生活保障的未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="13" <?php if(in_array(13,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="14" <?php if(in_array(14,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="15" <?php if(in_array(15,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="16" <?php if(in_array(16,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="17" <?php if(in_array(17,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                       value="18" <?php if(in_array(18,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input">4、有工读教育、特训学校教育、违法犯罪经历及严重偏差行为的未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="19" <?php if(in_array(19,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="20" <?php if(in_array(20,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="21" <?php if(in_array(21,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="22" <?php if(in_array(22,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="23" <?php if(in_array(23,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                       value="24" <?php if(in_array(24,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input">5、被遗弃、拐卖、肋迫及故意伤害等原因陷入成长受阻碍的未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="25" <?php if(in_array(25,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="26" <?php if(in_array(26,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="27" <?php if(in_array(27,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="28" <?php if(in_array(28,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="29" <?php if(in_array(29,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                       value="30" <?php if(in_array(30,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input">6、父母一方或双方外出，临时监护不到位的留守未成年人</td>
                                    <td class="td_input" style="border-right:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="31" <?php if(in_array(31,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="32" <?php if(in_array(32,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="33" <?php if(in_array(33,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="34" <?php if(in_array(34,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="35" <?php if(in_array(35,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                       value="36" <?php if(in_array(36,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                <tr>
                                    <td class="td_input" style="border-bottom:none;">7、生理、心理和精神状态异常或失去生活自理、学习等能力的未成年人
                                    </td>
                                    <td class="td_input" style="border-right:none; border-bottom:none;">
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="37" <?php if(in_array(37,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】一级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="38" <?php if(in_array(38,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】二级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="39" <?php if(in_array(39,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】三级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="40" <?php if(in_array(40,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】四级</label><br>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="41" <?php if(in_array(41,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】五级</label>
                                        <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                       value="42" <?php if(in_array(42,unserialize($Clist['growth_dilemmas']))){?>
                                            checked<?php }?>>】零级</label></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="li">
                    <div class="left">流程操作</div>
                    <div class="right">
                        <label><input type="radio" class="radio"
                                      name="trial_status" <?php if($Clist['trial_status'] == 1) echo 'checked';?>
                            value="1"> <span
                                    class="radio-text">初审通过，提交终审</span></label>&emsp;
                        <select class="select" name="trial_person">
                            <option value="">选择终审人员</option>
                        </select>
                    </div>
                    <div class="right" style="margin-left: 20%;">
                        <label><input type="radio" class="radio" name="trial_status" value="2"> <span
                                class="radio-text">初审不通过，驳回</span></label>&emsp;
                    </div>
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <div style="clear: both;"></div>
                </div>
                <div class="li">
                    <div class="left">初审意见</div>
                    <div class="right">
                        <textarea name="trial_suggestion"
                                  value=""><?php if(!empty($Clist['trial_suggestion'])) echo $Clist['trial_suggestion']; ?></textarea>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div class="li">
                    <div class="left">初审人</div>
                    <div class="right">
                        <span class="span-bottom"><?php echo $member ?></span>
                        <span class="span-bottom">初审日期</span>
                        <span class="span-bottom">
                            <?php if(!empty($Clist['trial_time'])){ echo $Clist['trial_time']; } else { echo date('Y-m-d', time()); }?>
                        </span>
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
        if (case_status == 'caiji' && is_auth) {
            return true;
        }
        if (case_status == 'bohuiCs' && is_auth) {
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