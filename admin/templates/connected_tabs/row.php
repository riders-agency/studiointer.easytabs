<?

IncludeModuleLangFile(__FILE__);
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/
foreach ($arAdminResult['ITEMS'] as $item_key => $item): ?>
	
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
												<?if (!is_array($sub_item)):?>
													<?if (!empty($sub_item)):?>
														<?=$sub_item?>
													<?endif?>
												<?else:?>
													<?foreach ($sub_item as $sub_item_s_key_item => $sub_item_s_item):?>
														<div class="connected_tab_row_string_item"><?=$sub_item_s_item['TEXT']?></div>
													<?endforeach;?>
												<?endif?>
											</div>
										</td>
									<? break;?>
									<? case 'F' ?>
										<td>
											<div class="connected_tab_row_file_wrapper">
												<?  foreach ($sub_item['IMAGES'] as $key => $val):?>
													
												<div class="connected_tab_row_file_item">
													<?if ($val['IS_IMAGE'] == 'Y'):?>
														<div class="connected_tab_row_file_item_image">
															<a href="<?=$val ['FILE']['SRC']?>"><img src="<?=$val ['RESIZE']['src']?>" /></a>
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
	
<? endforeach; ?>