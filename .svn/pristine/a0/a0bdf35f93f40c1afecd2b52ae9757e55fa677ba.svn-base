<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.icon-remove {
    background-position-x: -24px;
}
</style>
<?php $_addon = M('Addons')->where('name="Wechat"')->find(); if($_addon){ $db_config = !empty($_addon['config'])?(array)json_decode($_addon['config'], true):array(); } $w__chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; $w__sukey = substr(str_shuffle($w__chars), 3, 15); if(empty($data['config'][group][options][basicsettings][options][url][value])) $data['config'][group][options][basicsettings][options][url][value] = U(C("DEFAULT_MODULE")."/Addons/execute@".$_SERVER['HTTP_HOST'], array("_addons"=>"Wechat","_controller"=>"Wechat","_action"=>"index","ukey"=>$w__sukey)); if(empty($data['config'][group][options][basicsettings][options][ukey][value])) $data['config'][group][options][basicsettings][options][ukey][value] = $w__sukey; if(empty($data['config'][group][options][basicsettings][options][token][value])) $data['config'][group][options][basicsettings][options][token][value] = substr(str_shuffle($w__chars), 3, 30); $_addon_class = "Addons\\Wechat\\WechatAddon"; if(class_exists($_addon_class)){ $__class = new $_addon_class; $_saveconfig_cache_list = array(); foreach ($__class->saveconfig_cache_list as $_sck => $_scv) { $_saveconfig_cache_list[$_scv] = is_array(S("$_scv"))?json_encode(S("$_scv")):S("$_scv"); } } ?>
<?php if(is_array($data['config'])): foreach($data['config'] as $o_key=>$form): ?><div class="form-item cf <?php if(isset($form["topkey"])): echo ($form["topkey"]); endif; ?> <?php if(isset($form["topval"])): if(($data['config'][$form[topkey]]['value']) != $form[topval]): ?>hidden<?php endif; endif; ?>"  <?php if(isset($form["topval"])): ?>tval="<?php echo ($form["topval"]); ?>"<?php endif; ?> <?php if(($form["type"]) == "hidden"): ?>style="display: none;"<?php endif; ?>>
                	<?php if(isset($form["title"])): ?><label class="item-label">
						<?php echo ((isset($form["title"]) && ($form["title"] !== ""))?($form["title"]):''); ?>
						<?php if(isset($form["tip"])): ?><span class="check-tips"><?php echo ($form["tip"]); ?></span><?php endif; ?>
					</label><?php endif; ?>
						<?php switch($form["type"]): case "text": ?><div class="controls">
								<input type="text" name="config[<?php echo ($o_key); ?>]" class="text input-large" value="<?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?>">
							</div><?php break;?>
							<?php case "password": ?><div class="controls">
								<input type="password" name="config[<?php echo ($o_key); ?>]" class="text input-large" value="<?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?>">
							</div><?php break;?>
							<?php case "hidden": ?><input type="hidden" name="config[<?php echo ($o_key); ?>]" value="<?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?>"><?php break;?>
							<?php case "radio": ?><div class="controls">
								<?php if(is_array($form["options"])): foreach($form["options"] as $opt_k=>$opt): ?><label class="radio">
										<input type="radio" name="config[<?php echo ($o_key); ?>]" value="<?php echo ($opt_k); ?>" <?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): if(($form["value"]) == $opt_k): ?>checked<?php endif; endif; ?>><?php echo ($opt); ?>
									</label><?php endforeach; endif; ?>
							</div><?php break;?>
							<?php case "checkbox": ?><div class="controls">
								<?php if(is_array($form["options"])): foreach($form["options"] as $opt_k=>$opt): ?><label class="checkbox">
										<?php is_null($form["value"]) && $form["value"] = array(); ?>
										<input type="checkbox" name="config[<?php echo ($o_key); ?>][]" value="<?php echo ($opt_k); ?>" <?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): if(in_array(($opt_k), is_array($form["value"])?$form["value"]:explode(',',$form["value"]))): ?>checked<?php endif; endif; ?>><?php echo ($opt); ?>
									</label><?php endforeach; endif; ?>
							</div><?php break;?>
							<?php case "select": ?><div class="controls">
								<select name="config[<?php echo ($o_key); ?>]">
									<?php if(is_array($form["options"])): foreach($form["options"] as $opt_k=>$opt): ?><option value="<?php echo ($opt_k); ?>" <?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): if(($form["value"]) == $opt_k): ?>selected<?php endif; endif; ?>><?php echo ($opt); ?></option><?php endforeach; endif; ?>
								</select>
							</div><?php break;?>
                            <?php case "span": ?><b><a href="<?php echo ($form["value"]); ?>" target="_blank"><?php echo ($form["value"]); ?></a></b>
                                <input type="hidden" name="config[<?php echo ($o_key); ?>]" value="<?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?>"><?php break;?>
							<?php case "textarea": ?><div class="controls">
								<label class="textarea input-large">
									<textarea name="config[<?php echo ($o_key); ?>]"><?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?></textarea>
								</label>
							</div><?php break;?>
							<?php case "picture_union": ?><div class="controls">
								<input type="file" id="upload_picture_<?php echo ($o_key); ?>">
								<input type="hidden" name="config[<?php echo ($o_key); ?>]" id="cover_id_<?php echo ($o_key); ?>" value="<?php if((($form["topkey"] != '') AND ($data[config][$form[topkey]][value] == $form[topval])) OR ( $form["topkey"] == '' AND $form["topval"] == '')): echo ($form["value"]); endif; ?>"/>
								<div class="upload-img-box">
									<?php if(!empty($form['value'])): $mulimages = explode(",", $form["value"]); ?>
									<?php if(is_array($mulimages)): foreach($mulimages as $key=>$one): ?><div class="upload-pre-item" val="<?php echo ($one); ?>">
											<img src="<?php echo (get_cover($one,'path')); ?>"  ondblclick="removePicture<?php echo ($o_key); ?>(this)"/>
										</div><?php endforeach; endif; endif; ?>
								</div>
								</div>
								<script type="text/javascript">
									//上传图片
									/* 初始化上传插件 */
									$("#upload_picture_<?php echo ($o_key); ?>").uploadify({
										"height"          : 30,
										"swf"             : "/Public/static/uploadify/uploadify.swf",
										"fileObjName"     : "download",
										"buttonText"      : "上传图片",
										"uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
										"width"           : 120,
										'removeTimeout'   : 1,
										'fileTypeExts'    : '*.jpg; *.png; *.gif;',
										"onUploadSuccess" : uploadPicture<?php echo ($o_key); ?>,
										'onFallback' : function() {
								            alert('未检测到兼容版本的Flash.');
								        }
									});

									function uploadPicture<?php echo ($o_key); ?>(file, data){
										var data = $.parseJSON(data);
										var src = '';
										if(data.status){
											src = data.url || '' + data.path
											$("#cover_id_<?php echo ($o_key); ?>").parent().find('.upload-img-box').append(
												'<div class="upload-pre-item" val="' + data.id + '"><img src="' + src + '" ondblclick="removePicture<?php echo ($o_key); ?>(this)"/></div>'
											);
											setPictureIds<?php echo ($o_key); ?>();
										} else {
											updateAlert(data.info);
											setTimeout(function(){
												$('#top-alert').find('button').click();
												$(that).removeClass('disabled').prop('disabled',false);
											},1500);
										}
									}
									function removePicture<?php echo ($o_key); ?>(o){
										var p = $(o).parent().parent();
										$(o).parent().remove();
										setPictureIds<?php echo ($o_key); ?>();
									}
									function setPictureIds<?php echo ($o_key); ?>(){
										var ids = [];
										$("#cover_id_<?php echo ($o_key); ?>").parent().find('.upload-img-box').find('.upload-pre-item').each(function(){
											ids.push($(this).attr('val'));
										});
										if(ids.length > 0)
											$("#cover_id_<?php echo ($o_key); ?>").val(ids.join(','));
										else
											$("#cover_id_<?php echo ($o_key); ?>").val('');
									}
								</script><?php break;?>
							<?php case "group": ?><ul class="tab-nav nav">
									<?php if(is_array($form["options"])): $i = 0; $__LIST__ = $form["options"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><li data-tab="tab<?php echo ($i); ?>" <?php if(($i) == "1"): ?>class="current"<?php endif; ?>><a href="javascript:void(0);"><?php echo ($li["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
								<div class="tab-content">
								<?php if(is_array($form["options"])): $i = 0; $__LIST__ = $form["options"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tab): $mod = ($i % 2 );++$i;?><div id="tab<?php echo ($i); ?>" class="tab-pane <?php if(($i) == "1"): ?>in<?php endif; ?> tab<?php echo ($i); ?>">
                                    <?php if(isset($tab['tip'])): ?><div class="alert block alert-success">
                                            <div class="alert-content" style="max-width:inherit;"><?php echo ($tab['tip']); ?></div>
                                        </div><?php endif; ?>
										<?php if(is_array($tab['options'])): foreach($tab['options'] as $o_tab_key=>$tab_form): ?><div class="form-item cf <?php if(isset($tab_form["topkey"])): echo ($tab_form["topkey"]); endif; ?> <?php if(isset($tab_form["topval"])): if(($data['config'][$o_key][options][$key][options][$tab_form[topkey]][value]) != $tab_form[topval]): ?>hidden<?php endif; endif; ?>"  <?php if(isset($tab_form["topval"])): ?>tval="<?php echo ($tab_form["topval"]); ?>"<?php endif; ?> <?php if(($tab_form["type"]) == "hidden"): ?>style="display: none;"<?php endif; ?>>
										<label class="item-label">
											<?php echo ((isset($tab_form["title"]) && ($tab_form["title"] !== ""))?($tab_form["title"]):''); ?>
											<?php if(isset($tab_form["tip"])): ?><span class="check-tips"><?php echo ($tab_form["tip"]); ?></span><?php endif; ?>
										</label>
										<div class="controls">
											<?php switch($tab_form["type"]): case "text": ?><input type="text" name="config[<?php echo ($o_tab_key); ?>]" class="text input-large" value="<?php echo ($tab_form["value"]); ?>"><?php break;?>
												<?php case "password": ?><input type="password" name="config[<?php echo ($o_tab_key); ?>]" class="text input-large" value="<?php echo ($tab_form["value"]); ?>"><?php break;?>
												<?php case "hidden": ?><input type="hidden" name="config[<?php echo ($o_tab_key); ?>]" value="<?php echo ($tab_form["value"]); ?>"><?php break;?>
												<?php case "radio": if(is_array($tab_form["options"])): foreach($tab_form["options"] as $opt_k=>$opt): ?><label class="radio">
															<input type="radio" name="config[<?php echo ($o_tab_key); ?>]" value="<?php echo ($opt_k); ?>" <?php if(($tab_form["value"]) == $opt_k): ?>checked<?php endif; ?>><?php echo ($opt); ?>
														</label><?php endforeach; endif; break;?>
												<?php case "checkbox": if(is_array($tab_form["options"])): foreach($tab_form["options"] as $opt_k=>$opt): ?><label class="checkbox">
															<?php is_null($tab_form["value"]) && $tab_form["value"] = array(); ?>
															<input type="checkbox" name="config[<?php echo ($o_tab_key); ?>][]" value="<?php echo ($opt_k); ?>" <?php if(in_array(($opt_k), is_array($tab_form["value"])?$tab_form["value"]:explode(',',$tab_form["value"]))): ?>checked<?php endif; ?>><?php echo ($opt); ?>
														</label><?php endforeach; endif; break;?>
												<?php case "select": ?><select name="config[<?php echo ($o_tab_key); ?>]">
														<?php if(is_array($tab_form["options"])): foreach($tab_form["options"] as $opt_k=>$opt): ?><option value="<?php echo ($opt_k); ?>" <?php if(($tab_form["value"]) == $opt_k): ?>selected<?php endif; ?>><?php echo ($opt); ?></option><?php endforeach; endif; ?>
													</select><?php break;?>
												<?php case "textarea": ?><label class="textarea input-large">
														<textarea name="config[<?php echo ($o_tab_key); ?>]"><?php echo ($tab_form["value"]); ?></textarea>
													</label><?php break;?>
												<?php case "picture_union": ?><div class="controls">
													<input type="file" id="upload_picture_<?php echo ($o_tab_key); ?>">
													<input type="hidden" name="config[<?php echo ($o_tab_key); ?>]" id="cover_id_<?php echo ($o_tab_key); ?>" value="<?php echo ($tab_form["value"]); ?>"/>
													<div class="upload-img-box">
														<?php if(!empty($tab_form['value'])): $mulimages = explode(",", $tab_form["value"]); ?>
														<?php if(is_array($mulimages)): foreach($mulimages as $key=>$one): ?><div class="upload-pre-item" val="<?php echo ($one); ?>">
																<img src="<?php echo (get_cover($one,'path')); ?>"  ondblclick="removePicture<?php echo ($o_tab_key); ?>(this)"/>
															</div><?php endforeach; endif; endif; ?>
													</div>
													</div>
													<script type="text/javascript">
														//上传图片
														/* 初始化上传插件 */
														$("#upload_picture_<?php echo ($o_tab_key); ?>").uploadify({
															"height"          : 30,
															"swf"             : "/Public/static/uploadify/uploadify.swf",
															"fileObjName"     : "download",
															"buttonText"      : "上传图片",
															"uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
															"width"           : 120,
															'removeTimeout'   : 1,
															'fileTypeExts'    : '*.jpg; *.png; *.gif;',
															"onUploadSuccess" : uploadPicture<?php echo ($o_tab_key); ?>,
															'onFallback' : function() {
													            alert('未检测到兼容版本的Flash.');
													        }
														});

														function uploadPicture<?php echo ($o_tab_key); ?>(file, data){
															var data = $.parseJSON(data);
															var src = '';
															if(data.status){
																src = data.url || '' + data.path
																$("#cover_id_<?php echo ($o_tab_key); ?>").parent().find('.upload-img-box').append(
																	'<div class="upload-pre-item" val="' + data.id + '"><img src="' + src + '" ondblclick="removePicture<?php echo ($o_tab_key); ?>(this)"/></div>'
																);
																setPictureIds<?php echo ($o_tab_key); ?>();
															} else {
																updateAlert(data.info);
																setTimeout(function(){
																	$('#top-alert').find('button').click();
																	$(that).removeClass('disabled').prop('disabled',false);
																},1500);
															}
														}
														function removePicture<?php echo ($o_tab_key); ?>(o){
															var p = $(o).parent().parent();
															$(o).parent().remove();
															setPictureIds<?php echo ($o_tab_key); ?>();
														}
														function setPictureIds<?php echo ($o_tab_key); ?>(){
															var ids = [];
															$("#cover_id_<?php echo ($o_tab_key); ?>").parent().find('.upload-img-box').find('.upload-pre-item').each(function(){
																ids.push($(this).attr('val'));
															});
															if(ids.length > 0)
																$("#cover_id_<?php echo ($o_tab_key); ?>").val(ids.join(','));
															else
																$("#cover_id_<?php echo ($o_tab_key); ?>").val('');
														}
													</script><?php break;?>
                                                <?php case "optiongroup": if(is_array($tab_form["options"])): foreach($tab_form["options"] as $opts_k=>$opts): switch($opts["type"]): case "select": ?><label><?php echo ($opts["title"]); ?>：
                                                                    <select name="config[<?php echo ($key); ?>][<?php echo ($o_tab_key); ?>][<?php echo ($opts_k); ?>]">
                                                                        <?php if(is_array($opts["options"])): foreach($opts["options"] as $opts_o_k=>$opts_o): ?><option value="<?php echo ($opts_o_k); ?>" <?php if(($opts["value"] == $opts_o_k) OR ($db_config[$key][$o_tab_key][$opts_k] == $opts_o_k) ): ?>selected<?php endif; ?>><?php echo ($opts_o); ?></option><?php endforeach; endif; ?>
                                                                    </select>
                                                            </label><?php break;?>
                                                            <?php case "text": ?><label><?php echo ($opts["title"]); ?>：
                                                                    <input type="text" name="config[<?php echo ($key); ?>][<?php echo ($o_tab_key); ?>][<?php echo ($opts_k); ?>]" class="text input-large" value="<?php if(empty($db_config[$key][$o_tab_key][$opts_k])): echo ($opts["value"]); else: echo ($db_config[$key][$o_tab_key][$opts_k]); endif; ?>">
                                                            </label><?php break; endswitch; endforeach; endif; break;?>
                                                <?php case "dynamicgroup": $_key = $key; if(is_array($db_config[$_key][$o_tab_key])){ $tabformoptions = $db_config[$_key][$o_tab_key]; foreach ($tabformoptions as $_ok => $_ov) { $tab_form_options[$_ok] = $tab_form[options][0]; foreach ($_ov[sub_button] as $__ok => $__ov) { $tab_form_options[$_ok][sub_button][$__ok] = $tab_form[options][0][sub_button][0]; } } }else{ $tab_form_options = $tab_form[options] ; } ?>
                                                    <?php if(is_array($tab_form_options)): $opts_k = 0; $__LIST__ = $tab_form_options;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$opts): $mod = ($opts_k % 2 );++$opts_k;?><div class="controls">
                                                      <?php switch($opts["type"]): case "text": ?><label><?php echo ($opts["title"]); ?>：<input name="config[<?php echo ($_key); ?>][<?php echo ($o_tab_key); ?>][<?php echo ($opts_k - 1); ?>][<?php echo ($opts["name"]); ?>]" type="text" class="text input-large" <?php if(isset($opts["maxlength"])): ?>maxlength="<?php echo ($opts["maxlength"]); ?>"<?php endif; ?> style="<?php if(isset($opts["width"])): ?>width:<?php echo ($opts["width"]); ?>;<?php endif; ?>" value="<?php if(empty($db_config[$_key][$o_tab_key][$opts_k - 1][$opts[name]])): echo ($opts["value"]); else: echo ($db_config[$_key][$o_tab_key][$opts_k - 1][$opts[name]]); endif; ?>">
                                                            </label>
                                                            <span class="check-tips">
                                                            <?php if($opts_k == 1): ?><a class="add-button" title="添加一级菜单" href="javascript:;"><i class="icon-add"></i></a>
                                                            <?php else: ?>
                                                            <a class="remove-button" title="删除一级菜单" href="javascript:;"><i class="icon-add icon-remove"></i></a><?php endif; ?></span>
                                                          <?php if(isset($opts["sub_button"])): if(is_array($opts["sub_button"])): $optstk = 0; $__LIST__ = $opts["sub_button"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$optst): $mod = ($optstk % 2 );++$optstk;?><div class="children">
                                                          <span style="background-image: url(/Public/Admin/images/tab_sign.png); background-position: -55px 0;  width: 55px; height: 21px; display: inline-block; margin-left: 15px; vertical-align: middle;"></span>
                                                          <?php if($optstk == 1): ?><a class="add-sub-cate" title="添加二级菜单" href="javascript:;">
                                                                <i class="icon-add"></i>
                                                            </a>
                                                          <?php else: ?>
                                                          <a class="remove-sub-cate" title="删除二级菜单" href="javascript:;">
                                                                <i class="icon-add icon-remove"></i>
                                                            </a><?php endif; ?>
                                                          <?php if(is_array($optst)): $opts_ck = 0; $__LIST__ = $optst;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$opts_c): $mod = ($opts_ck % 2 );++$opts_ck; switch($opts_c["type"]): case "select": ?><label><?php echo ($opts_c["title"]); ?>：
                                                                    <select name="config[<?php echo ($_key); ?>][<?php echo ($o_tab_key); ?>][<?php echo ($opts_k - 1); ?>][sub_button][<?php echo ($optstk - 1); ?>][<?php echo ($key); ?>]">
                                                                        <?php if(is_array($opts_c["options"])): foreach($opts_c["options"] as $opts_co_k=>$opts_co): ?><option value="<?php echo ($opts_co_k); ?>" <?php if(($opts_c["value"] == $opts_co_k) OR ($db_config[$_key][$o_tab_key][$opts_k - 1][sub_button][$optstk - 1][$key] == $opts_co_k) ): ?>selected<?php endif; ?>><?php echo ($opts_co); ?></option><?php endforeach; endif; ?>
                                                                    </select></label><?php break;?>
                                                          <?php case "text": ?><label><?php echo ($opts_c["title"]); ?>：
                                                                    <input type="text" name="config[<?php echo ($_key); ?>][<?php echo ($o_tab_key); ?>][<?php echo ($opts_k - 1); ?>][sub_button][<?php echo ($optstk - 1); ?>][<?php echo ($key); ?>]" class="text input-large"<?php if(isset($opts_c["maxlength"])): ?>maxlength="<?php echo ($opts_c["maxlength"]); ?>"<?php endif; ?> style="<?php if(isset($opts_c["width"])): ?>width:<?php echo ($opts_c["width"]); ?>;<?php endif; ?>" value="<?php if(empty($db_config[$_key][$o_tab_key][$opts_k - 1][sub_button][$optstk - 1][$key])): echo ($opts_c["value"]); else: echo ($db_config[$_key][$o_tab_key][$opts_k - 1][sub_button][$optstk - 1][$key]); endif; ?>"></label><?php break; endswitch; endforeach; endif; else: echo "" ;endif; ?>
                                                          </div><?php endforeach; endif; else: echo "" ;endif; endif; break; endswitch;?>
                                                    </div><?php endforeach; endif; else: echo "" ;endif; break; endswitch;?>
											</div>
                                        </div><?php endforeach; endif; ?>
								<!-- <?php if(isset($tab["type"])): switch($tab["type"]): case "cachehtml": ?><div class="data-table table-striped">
												        <form class="ids">
												            <table>
												                <thead>
												                    <tr>
												                        <th width="30%">缓存名称</th>
												                        <th width="60%">缓存内容</th>
												                        <th width="10%">操作</th>
												                    </tr>
												                </thead>
												                <tbody>
												                    <?php if(!empty($_saveconfig_cache_list)): if(is_array($_saveconfig_cache_list)): $i = 0; $__LIST__ = $_saveconfig_cache_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$_scv): $mod = ($i % 2 );++$i;?><tr>
												                                <td><?php echo ($key); ?></td>
												                                <td style="word-break:break-all;"><?php echo ($_scv); ?></td>
												                                <td>
												                                    <a class="confirm ajax-get" title="清除" href="<?php echo U('delcache?id='.$_sck);?>">清除</a>
												                                </td>
												                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
												                        <?php else: ?>
												                        <td colspan="10" class="text-center">aOh! 暂时还没有内容!</td><?php endif; ?>
												                </tbody>
												            </table>
												        </form>
												    </div><?php break; endswitch; endif; ?> -->
								  </div><?php endforeach; endif; else: echo "" ;endif; ?>
								</div><?php break; endswitch;?>

					</div><?php endforeach; endif; ?>
<script type="text/javascript" charset="utf-8">
		$(function(){
			$('button[type="submit"].submit-btn.ajax-post').click(function(){
				//清空缓存
				$.get("<?php echo addons_url('Wechat://Wechatclass/update_cache');?>");
		  		return false;
			});
		});
		function bindShow(radio_bind, selectors){
			$(radio_bind).click(function(){
				if($(this).val() == $(selectors).attr("tval")){
					$(selectors).removeClass('hidden');
				}else{
					$(selectors).addClass('hidden');
				}
			})
		}
		$(document).delegate('.add-button',"click",function(){
			var c = '.controls',cn = '.children';
			var t = $(this).parents(c).html();
			var l = $(this).parents(c).parent(c).find(c).length;
			var a = $(this).parents(c).find(cn).first().html();
			console.log(a);
			var s = parseInt($(this).parents(c).parent(c).find(c).last().html().match(/\[button\]\[(\d+)\]/i)[1]);
			
			
			s = (s<1)?0:s;
			t = t.replace(/(<a[\s\S]+?)(add-button)([\s\S]+?)(添加一级菜单)([\s\S]+?)(icon-add)([\s\S]+?<\/a>)/i, "$1remove-button$3删除一级菜单$5$6 icon-remove$7");
			t = t.replace(/(<div[\s\S]+?class="children")([\s\S]+?)(<\/div>)/g, '');
			t = t+'<div class="children">'+a+'</div>';
			t = t.replace(/(\[button\]\[)(\d+)(\])/g, "$1"+parseInt(parseInt(s) + 1).toString()+"$3");
			t = t.replace(/(\[sub_button\]\[)(\d+)(\])/g, "$1"+(0).toString()+"$3");
			t = t.replace(/(<input)(.*?)(value=")(.*?)("([^>]*?)>)/g, "$1$2$3$5");
			if(l < 3){
				$($(this).parents(c).parent(c)).append('<div class="controls">'+t+'</div>');
			}
			return false;
		})
		$(document).delegate('.add-sub-cate',"click",function(){
			var c = '.controls',cn = '.children';
			var t = $(this).parent(cn).html();
			var l = $(this).parent(cn).parent(c).find(cn).length;
			var s = parseInt($(this).parent(cn).parent(c).find(cn).last().html().match(/sub_button\]\[(\d+)\]/i)[1]);
			s = (s<1)?0:s;
			t = t.replace(/(<a[\s\S]+?)(add-sub-cate)([\s\S]+?)(添加二级菜单)([\s\S]+?)(icon-add)([\s\S]+?<\/a>)/i, "$1remove-sub-cate$3删除二级菜单$5$6 icon-remove$7");
			t = t.replace(/(<input)(.*?)(value=")(.*?)("([^>]*?)>)/g, "$1$2$3$5");
			t = t.replace(/(sub_button\]\[)(\d+)(\])/g, "$1"+parseInt(parseInt(s) + 1).toString()+"$3");
			if(l < 5){
			$($(this).parent(cn).parent(c)).append('<div class="children">'+t+'</div>');
			}
			return false;
		})
		$(document).delegate(".remove-sub-cate","click",function(){
		  	$(this).parent('.children').remove();
		});
		$(document).delegate(".remove-button","click",function(){
		  	$(this).parent('.check-tips').parent('.controls').remove();
		});
		
		//配置的动态
		bindShow('[name="config[codelogin]"]','.codelogin');
</script>