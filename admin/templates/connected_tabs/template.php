<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/

IncludeModuleLangFile(__FILE__);

global $MESS;
?><tr>
<td>

	<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
	<style>
		<?=file_get_contents($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/admin/templates/connected_tabs/style.css")?>
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
	
	<script>
		<?=file_get_contents($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/lib/js/upload/js/vendor/jquery.ui.widget.js")?>
	</script>
	<script>
		<?=file_get_contents($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/lib/js/upload/js/jquery.iframe-transport.js")?>
	</script>
	<script>
		<?=file_get_contents($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/lib/js/upload/js/jquery.fileupload.js")?>
	</script>
	
	<?include 'js.php';?>
	
	<?if (count($property_array) > 1):?>
		<div id="connected_tab_choose_iblock_property">
			<h3><?=GetMessage("STUDIOINTER_EASY_TABS_PROPERTY_LIST")?></h3>
			<ul>
				<?foreach ($property_array as $property_key => $property):?>
					<li <?if($property['ID'] == $_GET ['si_connected_iblock'] || $property_id == $property['ID']):?>class="active"<?endif;?>><a href="<?=str_replace('&si_connected_iblock=' . $_GET ['si_connected_iblock'], '', $_SERVER['REQUEST_URI'])?>&si_connected_iblock=<?=$property['ID']?>"><?=$property['NAME']?></a></li>
				<?endforeach;?>
			</ul>
		</div>
	<?endif;?>
	<div class="connected_tab_wraper">
	
		<div id="admin_area_setting_connected_tab"></div>
		<div id="admin_button_connected_tab_add" class="con-btn con-btn-add">
			<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_ADD")?> (alt + &darr;)</span>
		</div>
		<div id="admin_button_connected_tab_edit" class="con-btn">
			<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_EDIT")?> (alt + e)</span>
		</div>
		<div id="admin_button_connected_tab_save" class="con-btn">
			<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_SAVE")?> (alt + s)</span>
		</div>
		<div id="admin_button_connected_tab_delete" class="con-btn con-btn-delete">
			<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_DELETE")?></span>
		</div>
		<div id="admin_button_connected_tab_reassign" class="con-btn">
			<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_REASSIGN")?></span>
		</div>
		
		<div id="connected_tab_wraper_reassign">
			<div class="contab-input-wrap">
				<input class="contab-input" size="47" id="connected_tab_wraper_reassign_input" />
				<span class="connected_tab_autocomlete_element_hint" id="connected_tab_autocomlete_element_hint"></span>
			</div>
			<div class="con-btn" id="connected_tab_wraper_reassign_move"><span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_REASSIGN")?></span></div>
			<!--<div class="con-btn"  id="connected_tab_wraper_reassign_copy"><span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_REASSIGN_COPY")?></span></div>-->
		</div>

		<table class="admin_button_connected_tab_table tLight checkAll check" id="admin_button_connected_tab_table" cellspacing="0" cellpadding="0">
			
			<thead>
				<tr>
					
					<td><input type="checkbox" id="connected_tab_check_all" /></td>
					<? foreach ($arAdminResult['COLUMNS'] as $key => $val): ?>
						<td><?= $val ?></td>
					<? endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?
				foreach ($arAdminResult['ITEMS'] as $item_key => $item): ?>
					<tr id="connected_tab_row_id_<?= $item['item_id'] ?>">
						<td class="center"><input type="checkbox" id="connected_tab_check_id_<?= $item['item_id'] ?>" class="connected_tab_checkbox_row" /></td>
							<? foreach ($item as $sub_item_key => $sub_item): ?>
								
								<?
								
								if (substr_count($sub_item_key, 'PROPERTY') == 1)
								{	
									$field_type = ConnectedTab :: GetFieldType ($sub_item_key);
									$field_type = $field_type['PROPERTY_TYPE'];
								}
								else
								{
									$field_type = ConnectedTab :: GetFieldType ($sub_item_key);
								}
								switch ($field_type):
									case 'bool':?>
										<?if ($sub_item == 'Y'):?>
											<td>
												<?=GetMessage("STUDIOINTER_EASY_TABS_YES")?>
											</td>
										<?else:?>
											<td></td>
										<?endif;?>
										
									<? break;?>
									<? case 'sort' ?>
										<td class="connected_tab_sort_row"><?=$sub_item?></td>
									<? break;?>
									<? case 'preview_detail_text' ?>
										<td><?=$sub_item['TEXT']?></td>
									<? break;?>
									<? case 'L' ?>
										<td>
											<?if (is_array($sub_item)):?>
												<ul class="connected_tab_row_list">
													<?foreach ($sub_item as $sub_item_list_item):?>
														<li><?=$sub_item_list_item?></li>
													<?endforeach;?>
												</ul>
											<?else:?>
												<?=$sub_item?>
											<?endif;?>
										</td>
									<? break;?>
									<? case 'S' ?>
										<td>
											
											<div class="connected_tab_row_string_wrapper">
												<?if (is_array($sub_item)):?>
													<?foreach ($sub_item as $sub_item_s_key_item => $sub_item_s_item):?>
														<div class="connected_tab_row_string_item">
															<?if (!empty($sub_item_s_item) && empty($sub_item_s_item['TEXT'])):?>
																<?=$sub_item_s_item?>
															<?elseif (!empty($sub_item_s_item['TEXT'])):?><?=$sub_item_s_item['TEXT']?><?endif?>
														</div>
													<?endforeach;?>
												<?else:?>
													<?=$sub_item?>
												<?endif?>
											</div>
										</td>
									<? break;?>
									<? case 'F' ?>
										<td>
											<div class="connected_tab_row_file_wrapper" style="max-width: 100px !important;">
												<?if (!empty($sub_item['IMAGES'])):?>
													<?  foreach ($sub_item['IMAGES'] as $key => $val):?>														
													<div class="connected_tab_row_file_item">
														<?if ($val['IS_IMAGE'] == 'Y' && !empty($val ['FILE']['SRC'])):?>
															<div class="connected_tab_row_file_item_image">
																<a href="<?=$val ['FILE']['SRC']?>">
																	<img width="50" height="50" style="width: 50px !important;height: 50px !important;" src="<?=$val ['RESIZE']['src']?>" />
																</a>
															</div>
														<?else:?>
															<div class="connected_tab_row_file_item_file">
															<a href="<?=$val ['FILE']['SRC']?>"><?=$val ['FILE']['FILE_NAME']?></a>
														<?endif;?>

														<?if (!empty ($val ['FILE']['DESCRIPTION'])):?>
															<div class="connected_tab_row_file_item_description">
																<?=$val ['FILE']['DESCRIPTION']?>
															</div>
														<?endif;?>
													</div>
													<?  endforeach;?>
												<?endif?>
											</div>
										</td>
									<? break;?>
									<? default :?>
										<? if ($sub_item_key != 'item_id'): ?>
											<td>
												<?if (is_array ($sub_item)):?>
													<div class="connected_tab_add_images">
														<?  foreach ($sub_item['IMAGES'] as $key => $val):?>
															<img src="<?=$val ['src']?>" />
														<?  endforeach;?>
													</div>
												<?else:?>
													<?=$sub_item?>
												<?endif;?>
											</td>
										<? else: ?>
											<?  //implode('/', $sub_item)?>
										<? endif; ?>
								<?  endswitch;?>
						<? endforeach; ?>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>

	</div>

</td>
</tr>