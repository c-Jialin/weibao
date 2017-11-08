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
            <span><a href="<?php echo U('case/finish', array('id' => $id, 'act' => 'jiean'));?>">结案</a></span></span><span class="solid">→</span>
            <span><a href="<?php echo U('case/visit', array('id' => $id, 'act' => 'huifang'));?>">回访</a></span></span>
        </div>
    </div>
    <div class="information">
        <div class="title <?php if($act == ''){?> active <?php }?>" data-toggle="collapse" href="#xinxicaiji"
             aria-expanded="<?php if($act == ''){?>true<?php }else{?>false<?php }?>" aria-controls="collapseExample">
            当前阶段：信息采集<span class="caret"></span></div>
        <form action="<?php echo U('Case/index');?>" method="post" enctype="multipart/form-data">
            <div class="collapse <?php if($act == ''){?>in<?php }?>" id="xinxicaiji">
                <table cellpadding="0" cellspacing="0" border="0" class="table_1" style="margin-bottom:10px; width:863px;margin: auto;margin-top: 15px;margin-bottom: 15px;font-size: 14px;">
                    <input type="hidden" name="xId" value="<?php echo $_GET['id'];?>"/>
                    <tbody>
                    <tr>
                        <td class="tips" colspan="8"><span>流程采集人</span> <span><?php echo ($member); ?></span></td>
                    </tr>
                    <tr>
                        <td class="td_input" align="left">县/市/区：</td>
                        <td class="td_input" align="left">
                            <select class="select left" name="area_code" id="City2" style="float:left;" onChange="nav2(this,3)">
                                <?php if (empty($Clist['area_code'])) echo "<option value=''>请选择</option>"; foreach ($list_top as $v) { if ($Clist['area_code']['k'] == $v['region_id']) { echo "<option selected='selected' value=".$v['region_id'].">".$v['region_name']."</option>"; } else { echo "<option value=".$v['region_id'].">".$v['region_name']."</option>"; } }?>
                            </select>
                        </td>
                        <td class="td_item">街/镇：</td>
                        <td>
                            <select class="select left" name="street_code" id="Town2" style="float:left;" onChange="nav2(this,4)">
                                <?php if (empty($Clist['street_code'])) { echo "<option value=''>请选择街/镇</option>"; } else { echo "<option value=".$Clist['street_code']['k'].">".$Clist['street_code']['v']."</option>"; } ?>
                            </select>
                        </td>
                        <td class="td_item">村/社区：</td>
                        <td>
                            <select class="select left" name="community_code" id="Country2" style="float:left;">
                                <?php if (empty($Clist['community_code'])) { echo "<option value=''>请选择村/社区</option>"; } else { echo "<option value=".$Clist['community_code']['k'].">".$Clist['community_code']['v']."</option>"; } ?>
                            </select>
                        </td>
                        <td align="right">编号：</td>
                        <td class="td_input">
                            <input type="text" class="input" name="case_number" style="width:150px;"
                                   value="<?php echo empty($Clist['case_number']) ? '后台自动生成' : $Clist['case_number'];?>">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="relative" style=" width:855px;">
                    <div id="upload1" class="absolute upload1" style="display:none;"></div>
                    <table cellpadding="0" cellspacing="0" border="0" class="table_2" style=" width:855px;">
                        <tbody>
                        <tr>
                            <td class="td_item" style="width:100px;">姓名</td>
                            <td class="td_input" style="width:110px;"><input type="text" class="input" name="name"
                                                                             value="<?php echo $Clist['name'];?>"></td>
                            <td class="td_item" style="width:80px;">性别</td>
                            <td class="td_input" style="width:120px;">
                                <label>【<input type="radio" class="radio" name="sex"
                                               value="2" <?php if($Clist['sex'] == 2) echo 'checked'; ?>>】男</label>
                                <label>【<input type="radio" class="radio" name="sex"
                                               value="1" <?php if($Clist['sex'] == 1) echo 'checked'; ?>>】女</label>
                            </td>
                            <td class="td_item" style="width:100px; ">民族</td>
                            <td class="td_input" style="width:80px;"><input type="text" class="input" name="nation" value="<?php echo $Clist['nation'];?>"></td>
                            <td rowspan="5" style="width:164px; line-height:22px; padding:0;" id="file">
                                <label class="label" style="color:#000000; position:relative; width:188px; height:238px;">
                                    <?php if (empty($Clist['photo'])) : ?>
                                    <input type="file" name="photo" style="width:80px; height:238px; line-height:238px;" accept="image/jpeg, image/png"/>
                                    <?php else: ?>
                                    <img name="photo" src="/Uploads/case/user/<?php echo $Clist['photo'];?>" style="width:188px;height:238px;">
                                    <?php endif ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">出生年月</td>
                            <td class="td_input" colspan="5">
                                <select name="year" class="select" style="width:55px;">
                                    <?php
 $birthday = unserialize($Clist['birthday']); for ($i=1980; $i<=2011; $i++) { if ($birthday[0] == $i) {?>
                                    <option value="<?php echo $i?>" selected='selected'><?php echo $i?></option>
                                    <?php }else{ ?>
                                    <option value="<?php echo $i?>"><?php echo $i?></option>
                                    ;
                                    <?php } } ?>
                                </select>年&emsp;
                                <select name="month" class="select" style="width:55px;">
                                    <?php
 $birthday = unserialize($Clist['birthday']); for ($i=1; $i<=12; $i++) { if ($birthday[1] == $i) {?>
                                    <option value="<?php echo $i?>" selected='selected'><?php echo $i?></option>
                                    <?php }else{ ?>
                                    <option value="<?php echo $i?>"><?php echo $i?></option>
                                    ;
                                    <?php } } ?>
                                </select>月
                        </tr>
                        <tr>
                            <td class="td_item">户籍所在地</td>
                            <td class="td_input" colspan="5">
                                <select class="select left" name="household_pro_code" id="City1" style="float:left;"
                                        onChange="nav(this,1)">
                                    <?php echo "<option value=".$Clist['household_pro_code']['k'].">
                                    ".$Clist['household_pro_code']['v']."</option>"; ?>

                                    <?php foreach ($list as $v) {?>
                                    <option value="<?php echo $v['region_id']?>"><?php echo $v['region_name']?></option>
                                    <?php }?>
                                </select>
                                <select class="select left" name="household_city_code" id="Town1" style="float:left;"
                                        onChange="nav(this,2)">
                                    <?php echo "<option value=".$Clist['household_city_code']['k'].">
                                    ".$Clist['household_city_code']['v']."</option>"; ?>
                                </select>
                                <select class="select left" name="household_area_code" id="Country1"
                                        style="float:left;">
                                    <?php echo "<option value=".$Clist['household_area_code']['k'].">
                                    ".$Clist['household_area_code']['v']."</option>"; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">家庭住址</td>
                            <td class="td_input" colspan="5" style="line-height:26px;">
                                <select class="select left" name="home_pro_code" id="sheng1" style="float:left;"
                                        onChange="nav1(this,1)">
                                    <?php echo "<option value=".$Clist['home_pro_code']['k'].">
                                    ".$Clist['home_pro_code']['v']."</option>"; ?>
                                    <?php foreach ($list as $v) { ?>
                                    <option value="<?php echo $v['region_id']?>"><?php echo $v['region_name']?></option>
                                    <?php }?>
                                </select>
                                <select class="select left" name="home_city_code" id="shi1"
                                        style="float:left;height:26px;" onChange="nav1(this,2)">
                                    <?php echo "<option value=".$Clist['home_city_code']['k'].">
                                    ".$Clist['home_city_code']['v']."</option>"; ?>
                                </select>
                                <select class="select left" name="home_area_code" id="qu1"
                                        style="float:left;height:26px;">
                                    <?php echo "<option value=".$Clist['home_area_code']['k'].">
                                    ".$Clist['home_area_code']['v']."</option>"; ?>
                                </select>
                                <input type="text" name="home_address" value="<?php echo $Clist['home_address'];?>"
                                       style="display:inline-block;height:26px;width:240px;">
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">健康状况</td>
                            <td class="td_input" colspan="5">
                                <label>【<input type="radio" class="radio healthradio" name="health"
                                               value="1"<?php if($Clist['health'] == 1){?>checked<?php }?>>】健康</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="2"<?php if(in_array(2,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】肢体残疾</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="3"<?php if(in_array(3,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】智力缺陷</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="4"<?php if(in_array(4,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】精神疾病</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="5"<?php if(in_array(5,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】传染病</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="6"<?php if(in_array(6,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】盲人</label><br>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="7"<?php if(in_array(7,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】聋哑</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="8"<?php if(in_array(8,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】吸毒史</label>
                                <label>【<input type="checkbox" class="radio healthbox" name="health"
                                               value="9"<?php if(in_array(9,unserialize($Clist['health']))){?>
                                    checked<?php }?>>】其它重病</label>
                                <input type="text" name="health_other"
                                       value="<?php if($Clist['health_other']){ echo $Clist['health_other']; }?>"
                                       style="display:inline-block;height:26px;width:240px;line-height:26px;">
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">性格状况</td>
                            <td class="td_input" colspan="3">
                                <label>【<input type="radio" class="radio" name="character"
                                               value="1"<?php if($Clist['character'] == 1){?>checked<?php }?>
                                    >】内向</label>
                                <label>【<input type="radio" class="radio" name="character"
                                               value="2"<?php if($Clist['character'] == 2){?>checked<?php }?>
                                    >】外向</label>
                                <label>【<input type="radio" class="radio" name="character"
                                               value="3"<?php if($Clist['character'] == 3){?>checked<?php }?>
                                    >】中性</label>
                                <label>【<input type="radio" class="radio" name="character"
                                               value="4"<?php if($Clist['character'] == 4){?>checked<?php }?>
                                    >】孤僻</label>
                            </td>
                            <td class="td_item">入学状况</td>
                            <td class="td_input" colspan="2">
                                <label>【<input type="radio" class="radio" name="admission_status"
                                               value="1"<?php if($Clist['admission_status'] == 1){?>checked<?php }?>
                                    >】未</label>　
                                <label>【<input type="radio" class="radio" name="admission_status"
                                               value="2"<?php if($Clist['admission_status'] == 2){?>checked<?php }?>
                                    >】在校</label>
                                <label><input type="text" class="input xuexiao" style="width:100px;" name="entrance"
                                              value="<?php echo $Clist['entrance']?>"></label></td>
                        </tr>
                        <tr>
                            <td class="td_item">家庭主要成员<br>（监护人）</td>
                            <td colspan="6" valign="top" style="padding:0 0;">
                                <table cellpadding="0" cellspacing="0" class="table_3" border="0" width="100%"
                                       style="border:none;">
                                    <tbody>
                                    <tr>
                                        <td style="width:40px;">监护人</td>
                                        <td style="width:60px;">姓名</td>
                                        <td style="width:60px;">关系</td>
                                        <td style="width:40px;">年龄</td>
                                        <td style="width:60px;">文化程度</td>
                                        <td style="width:60px;">健康状况</td>
                                        <td style="width:100px;">工作单位</td>
                                        <td style="width:80px;">联系电话</td>
                                        <td style="border-right:none; width:60px;">月收入</td>
                                    </tr>
                                    <tr><?php $family_members = unserialize($Clist['family_members']);?>
                                        <td><input type="checkbox" class="input" name="family_jianhuren[]"
                                                   value="1" <?php if(in_array(1,unserialize($Clist['family_jianhuren']))){?>
                                            checked<?php }?>>
                                        </td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[0]?>"></td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[1]?>"></td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[2]?>"></td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[3]?>"></td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[4]?>"></td>
                                        <td><input type="text" class="input" name="family_members[]" value="<?php echo $family_members[5]?>"></td>
                                        <td><input type="text" class="input shouji" name="family_members[]" value="<?php echo $family_members[6]?>"></td>
                                        <td style="border-right:none;"><input type="text" class="input " name="family_members[]" value="<?php echo $family_members[7]?>"></td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom:none;"><input type="checkbox" class="input" name="family_jianhuren[]" value="2" <?php if(in_array(2,unserialize($Clist['family_jianhuren']))){?>checked<?php }?>></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[8]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[9]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[10]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[11]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[12]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[13]?>"></td>
                                        <td style="border-bottom:none;"><input type="text" class="input shouji" name="family_members1[]" value="<?php echo $family_members[14]?>"></td>
                                        <td style="border-bottom:none; border-right:none;"><input type="text" class="input" name="family_members1[]" value="<?php echo $family_members[15]?>"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">家庭结构</td>
                            <td class="td_input" colspan="6">
                                <label>【<input type="radio" class="radio" name="family_structure"
                                               value="1" <?php if($Clist['family_structure'] == 1){?>checked<?php }?>
                                    >】双亲健在</label>　
                                <label>【<input type="radio" class="radio" name="family_structure"
                                               value="2" <?php if($Clist['family_structure'] == 2){?>checked<?php }?>
                                    >】单亲家庭</label>　
                                <label>【<input type="radio" class="radio" name="family_structure"
                                               value="3" <?php if($Clist['family_structure'] == 3){?>checked<?php }?>
                                    >】孤儿</label>
                                <label>【<input type="radio" class="radio jiating" name="family_structure"
                                               value="4">】离异家庭</label>
                                <label>【<input type="radio" class="radio" id="qitajiating" name="family_structure"
                                               value="5">】其他</label>
                                <input type="text" name="family_other"
                                       value="<?php if($Clist['family_other']){ echo $Clist['family_other']; }?>"
                                       id="qitajiainput"
                                       style="display:inline-block;height:26px;width:240px;line-height:26px;"></td>
                        </tr>
                        <tr>
                            <td class="td_item">监护状况</td>
                            <td class="td_input" colspan="6">
                                <label>【<input type="radio" class="radio" name="guardianship"
                                               value="1" <?php if($Clist['guardianship'] == 1){?>checked<?php }?>
                                    >】父母监护</label>　
                                <label>【<input type="radio" class="radio" name="guardianship"
                                               value="2" <?php if($Clist['guardianship'] == 2){?>checked<?php }?>
                                    >】其他亲属抚养</label>　
                                <label>【<input type="radio" class="radio" name="guardianship"
                                               value="3" <?php if($Clist['guardianship'] == 3){?>checked<?php }?>
                                    >】机构抚养</label>　
                                <label>【<input type="radio" class="radio" name="guardianship"
                                               value="4" <?php if($Clist['guardianship'] == 4){?>checked<?php }?>
                                    >】无人监护</label></td>
                        </tr>
                        <tr>
                            <td class="td_item">生活情境</td>
                            <td class="td_input" colspan="3">
                                <label>【<input type="radio" class="radio" name="life_status"
                                               value="1" <?php if($Clist['life_status'] == 1){?>checked<?php }?>
                                    >】留守</label>　
                                <label>【<input type="radio" class="radio" name="life_status"
                                               value="2" <?php if($Clist['life_status'] == 2){?>checked<?php }?>
                                    >】流动</label>　
                                <label>【<input type="radio" class="radio" name="life_status"
                                               value="3" <?php if($Clist['life_status'] == 3){?>checked<?php }?>
                                    >】正常</label>
                            </td>
                            <td class="td_item">享受救助</td>
                            <td class="td_input" colspan="2">
                                <label>【<input type="checkbox" class="radio" name="enjoy_relief_type[]"
                                               value="1" <?php if(in_array(1,unserialize($Clist['enjoy_relief_type']))){?>
                                    checked<?php }?>>】低保</label>　
                                <label>【<input type="checkbox" class="radio" name="enjoy_relief_type[]"
                                               value="2" <?php if(in_array(2,unserialize($Clist['enjoy_relief_type']))){?>
                                    checked<?php }?>>】医保</label>　
                                <label>【<input type="checkbox" class="radio" name="enjoy_relief_type[]"
                                               value="3" <?php if(in_array(3,unserialize($Clist['enjoy_relief_type']))){?>
                                    checked<?php }?>>】孤补</label></td>
                        </tr>
                        <tr>
                            <td class="td_item">住房类型</td>
                            <td class="td_input" colspan="6">
                                <label>【<input type="radio" class="radio" name="housing_type"
                                               value="1" <?php if($Clist['housing_type'] == 1){?>checked<?php }?>
                                    >】固定住所</label>　
                                <label>【<input type="radio" class="radio" name="housing_type"
                                               value="2" <?php if($Clist['housing_type'] == 2){?>checked<?php }?>
                                    >】临时租住</label>　
                                <label>【<input type="radio" class="radio" name="housing_type"
                                               value="3" <?php if($Clist['housing_type'] == 3){?>checked<?php }?>
                                    >】借住</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">内心困境</td>
                            <td class="td_input" colspan="6">
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="1" <?php if(in_array(1,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】父母关系不好</label>　
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="2" <?php if(in_array(2,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】缺少亲友陪伴</label>　
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="3" <?php if(in_array(3,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】朋辈关系不好</label>　
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="4" <?php if(in_array(4,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】学习成绩不佳</label>　
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="5" <?php if(in_array(5,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】无人理解自己</label><br>
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="6" <?php if(in_array(6,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】感觉自己不如别人</label>　
                                <label>【<input type="checkbox" class="radio" name="inner_predicament[]"
                                               value="7" <?php if(in_array(7,unserialize($Clist['inner_predicament']))){?>
                                    checked<?php }?>>】其他</label>
                                <label><input type="text" class="input" style="width:300px;" name="Heart_other"
                                              value="<?php echo $Clist['Heart_other']?>"></label></td>
                        </tr>
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
                                                           value="1" <?php if(in_array(1,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                           value="2" <?php if(in_array(2,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                           value="3" <?php if(in_array(3,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                           value="4" <?php if(in_array(4,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                           value="5" <?php if(in_array(5,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma1"
                                                           value="6" <?php if(in_array(6,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input">2、因家庭暴力、遭受侵害等原因得不到适当监护的未成年人</td>
                                        <td class="td_input" style="border-right:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="7" <?php if(in_array(7,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="8" <?php if(in_array(8,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="9" <?php if(in_array(9,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="10" <?php if(in_array(10,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="11" <?php if(in_array(11,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma2"
                                                           value="12" <?php if(in_array(12,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input">3、因家庭困境面临辍学和失去基本生活保障的未成年人</td>
                                        <td class="td_input" style="border-right:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="13" <?php if(in_array(13,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="14" <?php if(in_array(14,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="15" <?php if(in_array(15,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="16" <?php if(in_array(16,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="17" <?php if(in_array(17,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma3"
                                                           value="18" <?php if(in_array(18,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input">4、有工读教育、特训学校教育、违法犯罪经历及严重偏差行为的未成年人</td>
                                        <td class="td_input" style="border-right:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="19" <?php if(in_array(19,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="20" <?php if(in_array(20,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="21" <?php if(in_array(21,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="22" <?php if(in_array(22,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="23" <?php if(in_array(23,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma4"
                                                           value="24" <?php if(in_array(24,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input">5、被遗弃、拐卖、肋迫及故意伤害等原因陷入成长受阻碍的未成年人</td>
                                        <td class="td_input" style="border-right:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="25" <?php if(in_array(25,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="26" <?php if(in_array(26,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="27" <?php if(in_array(27,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="28" <?php if(in_array(28,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="29" <?php if(in_array(29,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma5"
                                                           value="30" <?php if(in_array(30,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input">6、父母一方或双方外出，临时监护不到位的留守未成年人</td>
                                        <td class="td_input" style="border-right:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="31" <?php if(in_array(31,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="32" <?php if(in_array(32,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="33" <?php if(in_array(33,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="34" <?php if(in_array(34,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="35" <?php if(in_array(35,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma6"
                                                           value="36" <?php if(in_array(36,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>
                                    <tr>
                                        <td class="td_input" style="border-bottom:none;">
                                            7、生理、心理和精神状态异常或失去生活自理、学习等能力的未成年人
                                        </td>
                                        <td class="td_input" style="border-right:none; border-bottom:none;">
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="37" <?php if(in_array(37,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】一级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="38" <?php if(in_array(38,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】二级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="39" <?php if(in_array(39,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】三级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="40" <?php if(in_array(40,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】四级</label><br>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="41" <?php if(in_array(41,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】五级</label>
                                            <label>【<input type="radio" class="radio" name="growth_dilemma7"
                                                           value="42" <?php if(in_array(42,unserialize($Clist['growth_dilemma']))){?>
                                                checked<?php }?>>】零级</label></td>
                                    </tr>

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_item">主要困境描述</td>
                            <td colspan="6" style="padding:0;">
                                <textarea name="main_dilemma" id="main_dilemma"><?php echo $Clist['main_dilemma']?></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="width: 855px;margin: auto;margin-top: 10px;border-bottom:1px solid rgb(65,199,214);padding-bottom: 10px;">
                    <label>填表人：<input type="text" class="input" style="width:300px;" name="fill_in_person"
                                      value="<?php echo $Clist['fill_in_person']?>"></label>
                    <label style="float: right;"></label>
                    <div id="auth_status" style="clear:both;" case_status="<?php echo $Clist['case_status']; ?>" is_auth="<?php echo IS_AUTH?>"></div>
                </div>
                <div align="center">
                    <button onclick="return auth()" type="submit" style="height: 30px;width: 10%;margin: auto;line-height: 30px;background-color: rgb(242,249,253);text-align: center;color: #000000;cursor: pointer;margin-top: 10px;display:inline-block;margin-right: 20px;">确认登记</button>
                    <button onclick="return auth()" type="submit" style="height: 30px;width: 10%;margin: auto;line-height: 30px;background-color: rgb(242,249,253);text-align: center;color: #000000;cursor: pointer;margin-top: 10px;display:inline-block">清空重置</button>
                </div>
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
        if (case_status == '' && is_auth) {
            return true;
        }
        if (case_status == 'bohuiC' && is_auth) {
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