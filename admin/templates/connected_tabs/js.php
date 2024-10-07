<?
IncludeModuleLangFile(__FILE__);
?>

<script src="/bitrix/modules/studiointer.easytabs/admin/templates/connected_tabs/ckeditor.js"></script>
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script> -->

<script>
	
	var fields_structure = $.parseJSON ('<?= json_encode($arAdminResult['FIELDS']) ?>');
	var main_table_id = 'admin_button_connected_tab_table';
	var selected_row_count = 0;
	var selected_row_saved_count = 0;
	var edited_row_count = 0;
	
	var property_id = <?=$arAdminResult['PROPERTY_ID']?>;
	
	var has_file_upload = 0;
	
	var first_sort;
	
	function si_visual_editor(object) {
		textarea = object.closest(".connected_tab_textarea").querySelector('textarea');

		if (object.checked === true) {
			ClassicEditor
			.create( textarea, {
				toolbar: [ 'bold', 'italic', 'link']
			} )
			.then( editor => {
				editor.model.document.on( 'change:data', () => {
					textarea.value = editor.getData();
				} );
                                } )
			.catch( error => {
				console.error( error );
			} );	
		} else {
			object.closest(".connected_tab_textarea").querySelector('.ck-editor__editable').ckeditorInstance.destroy()
		}
	}
	
	function after_select ()
	{
		if (selected_row_count > 0)
		{
			document.getElementById('admin_button_connected_tab_edit').innerHTML =  '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_EDIT")?> (alt + e)</span><span class="con-btn-count"><strong>'+ selected_row_saved_count +'</strong></span>';
			document.getElementById('admin_button_connected_tab_delete').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_DELETE")?></span><span class="con-btn-count"><strong>'+ selected_row_count +'</strong></span>';
			document.getElementById('admin_button_connected_tab_reassign').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_REASSIGN")?></span><span class="con-btn-count"><strong>'+ selected_row_saved_count +'</strong></span>';
		}
		else
		{
			document.getElementById('admin_button_connected_tab_edit').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_EDIT")?> (alt + e)</span>';
			document.getElementById('admin_button_connected_tab_delete').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_DELETE")?></span>';
			document.getElementById('admin_button_connected_tab_reassign').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_REASSIGN")?></span>';
		}
	
	
	
	
		if (edited_row_count > 0 || selected_row_saved_count != selected_row_count)
		{
			document.getElementById('admin_button_connected_tab_save').innerHTML =  '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_SAVE")?> (alt + s)</span><span class="con-btn-count"><strong>'+ parseInt (parseInt(edited_row_count) + parseInt (selected_row_count) - parseInt (selected_row_saved_count)) +'</strong></span>';
		}
		else
		{
			document.getElementById('admin_button_connected_tab_save').innerHTML = '<span class="con-btn-wraper"><?=GetMessage("STUDIOINTER_EASY_TABS_SAVE")?> (alt + s)</span>';
		}
	
	
	}
	
	jQuery.fn.outerHTML = function(s) {
		return s
			? this.before(s).remove()
			: jQuery("<p>").append(this.eq(0).clone()).html();
	};

	Array.prototype.in_array = function(p_val) {
		for(var i = 0, l = this.length; i < l; i++)	{
			if(this[i] == p_val) {
				return true;
			}
		}
		return false;
	}

	String.prototype.in_array = function(p_val) {
		if (p_val == this)
		{
		   return true;
		}
		else
		{
		   return false;
		}
	}

	Boolean.prototype.in_array = function(p_val) {
		if (true == this)
		{
		   return true;
		}
		else
		{
		   return false;
		}
	}
	
	/**
	 * �������� ������
	 */
	function select_row (row, class_name)
	{
		if (row.className.search ('connected_tab_selected_row_edit') != -1)
		{
			edited_row_count ++;
		}
	
		if (row.className != undefined && row.className.length > 1)
		{
			row.className = row.className + ' ' + class_name;
		}
		else
		{
			row.className = class_name;
		}
		
		
		selected_row_count ++;
		
		row_search_array = row.id.split ('_');
		if (row_search_array.length == 5)
		{
			selected_row_saved_count ++;
		}
		
		after_select ();
	}
	
	/**
	 * ����� ��������� �� ������
	 */
	function unselect_row (row, class_name, check)
	{
		if (row.className != undefined && row.className.length > 1)
		{
			if (row.className.search ('connected_tab_selected_row_edit') != -1)
			{
				edited_row_count --;
			}
		
			if (row.className != class_name)
			{
				if ( row.className ){
					var arrList = row.className.split(' ');
					var strClassUpper = class_name.toUpperCase();
					for ( var i = 0; i < arrList.length; i++ ){
						if ( arrList[i].toUpperCase() == strClassUpper ){
							arrList.splice(i, 1);
							i--;
						}
					}
				}
					row.className = arrList.join(' ');
			}
			else
			{
				row.className = '';
			}
		}
			selected_row_count--;
		
			row_search_array = row.id.split ('_');
			if (row_search_array.length == 5)
			{
				selected_row_saved_count --;
			}
			
			after_select ();
		}

		/**
		 * ������������� ��������� �����
		 */
		function check_rows (table, add_class)
		{
		
			var main = document.getElementById('admin_button_connected_tab_table');
			var tbody = main.getElementsByTagName('tbody');
			var tr = tbody[0].getElementsByTagName('tr');
		
			for (var x = 0; x < tr.length; x++)
			{
				if (document.getElementById(tr[x].id).style.display != 'none')
				{
				if (document.getElementById(tr[x].id.replace('row', 'check')).checked == true)
				{
					unselect_row (tr[x], add_class);
					document.getElementById(tr[x].id.replace('row', 'check')).checked = false;
				
				}
				else
				{
					select_row (tr[x], add_class);
					document.getElementById(tr[x].id.replace('row', 'check')).checked = true;
				}
			}
			}
		

			after_select ();
		}
	
		/**
		 * ���������� �������� � ���� ��� �������������� ������
		 */
		function show_edit_rows (table, row_id_prefix) {
			send_id_array = new Array ();
			$('#admin_button_connected_tab_table > tbody > tr').each (function (index, item)
			{
				if ($(item).find('td').eq (0).find('input[type="checkbox"]').prop('checked') == true && $(item).hasClass ('connected_tab_selected_row_new') != true)
				{
					send_id_array.push ($(item).find('td').eq (0).find('input[type="checkbox"]').attr ('id').replace (row_id_prefix, ''));
				}
		
			});
	
			if (send_id_array.length > 0)
			{
				$.post ('/bitrix/admin/studiointer_connected_tabs_ajax_edit.php?type=<?=preg_replace("[^A-Za-z0-9]", "",  $_GET['type'])?>', 
				{data: send_id_array, ID: <?= $_GET ['ID'] ?>, property_id: property_id},
				function (data)
				{
					replace_rows_to_edit (data, 'connected_tab_check_id_');
				},
				'json'
			);
		
			}
		}
	
		function get_type_list (field_type, value)
		{
			var result_list = '<div class="connected_tab_list">';
			
			if (field_type.TYPE.LIST_TYPE == 'L')
			{
				if (field_type.TYPE.MULTIPLE == 'Y')
				{
					if (field_type.TYPE.MULTIPLE_CNT > field_type.TYPE.LIST.length)
					{
						field_type.TYPE.MULTIPLE_CNT = field_type.TYPE.LIST.length + 1;
					}
					result_list += '<select class="edited_input" multiple="" size="' + field_type.TYPE.MULTIPLE_CNT + '"><option value="">(<?=GetMessage('STUDIOINTER_EASY_TABS_EMPTY')?>)</option>';
					$(field_type.TYPE.LIST).each(function (list_index, list_value)
					{
						if (value.in_array (list_value.VALUE) == true)
						{
							result_list += '<option selected="selected" value="' + list_value.ID + '">' + list_value.VALUE + '</option>';
						}
						else
						{
							result_list += '<option value="' + list_value.ID + '">' + list_value.VALUE + '</option>';
						}
					})
					result_list += "</select>";
				}
				else
				{
					result_list += '<select class="edited_input"><option value="">(<?=GetMessage('STUDIOINTER_EASY_TABS_EMPTY')?>)</option>';
					$(field_type.TYPE.LIST).each(function (list_index, list_value)
					{
						if (value.in_array (list_value.VALUE) == true)
						{
							result_list += '<option selected="selected" value="' + list_value.ID + '">' + list_value.VALUE + '</option>';
						}
						else
						{
							result_list += '<option value="' + list_value.ID + '">' + list_value.VALUE + '</option>';
						}
					})
					result_list += "</select>";
				}
			}
			else
			{
				var result_list = '<div class="connected_tab_list">';
			
					if (field_type.TYPE.MULTIPLE == 'Y')
					{
						result_list += '<div class="connected_tabs_input_checkbox_wrapper">';
						$(field_type.TYPE.LIST).each(function (list_index, list_value)
						{
							var random_id = 'connected_tabs_checkbox_' + Math.random();
							random_id = random_id.replace ('.', '');
							result_list += '<div style="display:none"><input class="edited_input" id="hide_' + random_id + '" type="checkbox" checked="checked" value=""></div>';
							if (value.in_array (list_value.VALUE) == true)
							{
								result_list += '<div><input class="edited_input" id=' + random_id + ' type="checkbox" checked="checked" value="' + list_value.ID + '"><label for=' + random_id + '>' + list_value.VALUE + '</label></div>';
							}
							else
							{
								result_list += '<div><input class="edited_input" id=' + random_id + ' type="checkbox" value="' + list_value.ID + '"><label for=' + random_id + '>' + list_value.VALUE + '</label></div>';
							}
						})
						result_list += "</div>";
					}
					else
					{
						var random_id = 'connected_tabs_radio_' + Math.random();
						var random_name_postfix = '_connected_tabs_radio_name' + Math.random();
						result_list += '<div class="connected_tabs_input_radio_wrapper"><div><input class="edited_input" id="' + random_id + '" name="connected_tabs_radio_' + field_type.TYPE.ID + random_name_postfix + '" type="radio" checked="checked" value=""><label for=' + random_id + '>(<?=GetMessage ('STUDIOINTER_EASY_TABS_EMPTY')?>)</label></div>';
						$(field_type.TYPE.LIST).each(function (list_index, list_value)
						{
							var random_id = 'connected_tabs_radio_' + Math.random();
							random_id = random_id.replace ('.', '');
							if (value.in_array (list_value.VALUE) == true)
							{
								result_list += '<div><input class="edited_input" id=' + random_id + ' name="connected_tabs_radio_' + field_type.TYPE.ID + random_name_postfix + '" type="radio" checked="checked" value="' + list_value.ID + '"><label for=' + random_id + '>' + list_value.VALUE + '</label></div>';
							}
							else
							{
								result_list += '<div><input class="edited_input" id=' + random_id + ' name="connected_tabs_radio_' + field_type.TYPE.ID  + random_name_postfix + '" type="radio" value="' + list_value.ID + '"><label for=' + random_id + '>' + list_value.VALUE + '</label></div>';
							}
						})
						result_list += "</div>";
					
				}
			}
			result_list += '</div>'
			return result_list;
		}
		
		function recount_sort ()
		{
			$("#admin_button_connected_tab_table > tbody > tr").each(function (row_index, row)
			{
				$("#admin_button_connected_tab_table > tbody > tr").eq (row_index).find ('input[name="connected_tab_SORT"]').val(first_sort-row_index);
			})
		}
		
		/**
		 * ������ � ������ ��� �������������� � ����������� �� ���� ���� 
		 */
		function replace_value (td_object, value, field)
		{
			var field_type;
			if (field == undefined)
			{
				field_type = '';
			}
		
			if (field.TYPE != undefined)
			{
				field_type = field.TYPE;
			}
			
			if (field.TYPE.PROPERTY_TYPE != undefined)
			{
				field_type = field.TYPE.PROPERTY_TYPE;
			}
		
			switch (field_type) {
				case 'L':
					
					$(td_object).html (get_type_list (field, value));
					
					break
				case 'F':
					
					var input_id_file = 'fileupload_' + Math.random();
					
					var inner_file_list = '';
					
					if (value != null)
					{
						if (value.IMAGES != undefined)
						{
							$(value.IMAGES).each(function (image_index, image)
							{

								if (image.IS_IMAGE == 'Y')
								{	
									inner_file_list += '<div class="connected_tab_preview_image">'
									inner_file_list += '<a href="' + image.FILE.SRC + '" target="_blank"><img src="' + image.RESIZE.src + '" /><input class="edited_input field_type_' + field_type + '" type="hidden" value="' + image.FILE.ID + '" ></a>'
								}
								else
								{
									inner_file_list += '<div class="connected_tab_doc_element">'
									file_extension  = image.FILE.SRC.split ('.');

									inner_file_list += '<a href="' + image.FILE.SRC + '" target="_blank">' + '<span class="connected_tab_doc_element_type">' + file_extension[file_extension.length - 1]  + '</span><input class="edited_input field_type_' + field_type + '" type="hidden" value="' + image.FILE.ID + '" >'
									inner_file_list += '<span class="connected_tab_doc_element_name">' + image.FILE.FILE_NAME  + '</span></a>'
								}

								if (field.TYPE.WITH_DESCRIPTION == 'Y' || field.FORM_NAME == 'PREVIEW_PICTURE' || field.FORM_NAME == 'DETAIL_PICTURE' )
								{
									inner_file_list += '<span class="connected_tab_file_description"><input class="field_type_file_description" value="' + image.FILE.DESCRIPTION + '"></span>';
								}

								inner_file_list += '<div class="con-btn connected_tab_preview_image_delete"><span class="con-btn-wraper"><?=GetMessage ('STUDIOINTER_EASY_TABS_DELETE')?></span></div></div>';
							});
						}
					}
					
					inner_file_list = '<div class="connected_tab_file_list">' + inner_file_list + '</div>';
					
					input_id_file = input_id_file.replace ('.', '');
					$(td_object).html ('<div class="fileupload" id="form_' + input_id_file + '" action=\'/bitrix/admin/studiointer_connected_tabs_ajax_file.php?property_id=' + property_id + '\' method="POST" enctype="multipart/form-data"><div class="uploader"><input id="' + input_id_file + '" type="file" name="files[]" data-url="/bitrix/admin/studiointer_connected_tabs_ajax_file.php?property_id=' + property_id + '" multiple><span class="action" style="-moz-user-select: none;"><?=GetMessage('STUDIOINTER_EASY_TABS_ADD_FILE')?></span><span class="filename" style="-moz-user-select: none;"><?=GetMessage('STUDIOINTER_EASY_TABS_ADD_GRAG_FILE')?></span></div>' + inner_file_list + '</div>');
					
					has_file_upload = 1;
					
					if (field.FORM_NAME == 'PREVIEW_PICTURE' || field.FORM_NAME == 'DETAIL_PICTURE' )
					{
						$(td_object).append ('<input class="edited_input field_type_' + field_type + '" type="hidden" value="">');
					}
				
					break
				case 'label':
					$(td_object).html (value);
					break
				 	
				case 'bool':
 						if (value == 'Y')
 						{
							$(td_object).html ('<input name="connected_tab_' + field.FORM_NAME + '" type="checkbox" value="Y" checked="checked" name= class="edited_input field_type_' + field_type + '"/>');
 						}
						else
						{
							$(td_object).html ('<input name="connected_tab_' + field.FORM_NAME + '" type="checkbox" value="Y" class="edited_input field_type_' + field_type + '"/>');
						}
 					break
					
				case 'preview_detail_text':
						var textarea_random_radio_name = Math.random ();
						
						var checked_type_text = 'checked="checked"';
						var checked_type_html;
						
						if (value.TYPE == 'text')
						{
							checked_type_text = 'checked="checked"';
							checked_type_html = ''
						}
					
						if (value.TYPE == 'html')
						{
							checked_type_html = 'checked="checked"';
							
							checked_type_text = '';
						}
						
						
						
						var textarea_value = value.TEXT;
						
						if (textarea_value === undefined)
						{
							textarea_value = '';
						}
					
						$(td_object).html ('<div class="connected_tab_textarea"><input id="connected_tab_textarea_type_id_' + textarea_random_radio_name + 'text" type="radio" ' + checked_type_text + ' value="text" name="connected_tab_textarea_type_name_' + textarea_random_radio_name + '"><label for="connected_tab_textarea_type_id_' + textarea_random_radio_name + 'text">text</label>/<input id="connected_tab_textarea_type_id_' + textarea_random_radio_name + 'html" type="radio" value="html" ' + checked_type_html + ' name="connected_tab_textarea_type_name_' + textarea_random_radio_name + '"><label for="connected_tab_textarea_type_id_' + textarea_random_radio_name + 'html">html</label> | <label><input onchange="si_visual_editor(this)" type="checkbox" /> Visual</label><br /><textarea name="connected_tab_' + field.FORM_NAME + '" class="edited_input field_type_' + field_type + '">' + textarea_value + '</textarea></div>');
 					break
					
				case 'S':
						
						$(td_object).html ('<div class="connected_tab_textarea"><textarea name="connected_tab_' + field.FORM_NAME + '" class="edited_input field_type_' + field_type + '">' + value + '</textarea></div>');
 					break
					
				case 'sort':
					
					if ($("#admin_button_connected_tab_table > tbody").hasClass('ui-selectable') == true)
					{
						$("#admin_button_connected_tab_table > tbody").selectable( "destroy");
					}
					
					$(td_object).html ('<input class="edited_input  field_type_' + field_type + '" value="' + value + '" name="connected_tab_' + field.FORM_NAME + '" />');
					
					if ($("#admin_button_connected_tab_table > tbody").hasClass('ui-sortable') != true)
					{
						recount_sort ();
						$("#admin_button_connected_tab_table > tbody").sortable ({ 
							items: 'tr',
							cancel: '.edited_input, .ck-editor',
							stop: function( event, ui )
							{
								recount_sort ();
							}
						});
						
					}
					
					break
					
				default:
					if (value === null)
					{
						value = "";
					}
					
					$(td_object).html ('<input class="edited_input field_type_' + field_type + '" value="' + value + '"  name="connected_tab_' + field.FORM_NAME + '" />');
				}

			}
	
		/**
		 * �������������� ����� ����������
		 */
		function restore_value (td_object, value, field)
		{
			var field_type;
			if (field == undefined)
			{
				field_type = '';
			}
		
			if (field.TYPE != undefined)
			{
				field_type = field.TYPE;
			}
		
			switch (field_type) {
				case 'label':
					$(td_object).html (value.added_id);
					break
				case 'sort':
					
					$(td_object).html ($(td_object).find('.edited_input').val ());
					
					break
				default:
					$(td_object).html ($(td_object).find('.edited_input').val ());
					
				}

			}
	
			/**
			 * �������� ������� �������� �� ����� 
			 */
			function get_object_by_key (parent_object, object_key)
			{
				var return_object_value;
				$.each(parent_object, function(object_index, object_value)
				{
					if (object_index == object_key)
					{
						return_object_value = object_value;
						return false;
					}
				});
		
				return return_object_value;
			}
			
			/**
			 * �������� ������� �������� �� ��� ������
			 */
			function get_object_by_number (parent_object, object_number)
			{
				var return_object_value;
				var i = 0;
				$.each(parent_object, function(object_index, object_value)
				{
					if (i == object_number)
					{
						return_object_value = object_value;
						return false;
					}
					i++;
				});
		
				return return_object_value;
			}
			
			function activate_file_upload (upload_object)
			{
				$(upload_object).fileupload({
					url: '/bitrix/admin/studiointer_connected_tabs_ajax_file.php?property_id=' + property_id,
					dropZone: $(upload_object),
					dataType: 'json',
					start: function (e)
					{
						$(upload_object).append ('<div class="connected_tabs_upload_progress"></div>');
					},
					recalculateProgress: true,
					progressall: function (e, data)
					{
						var progress;
						progress = Math.round((data.loaded) /  (data.total) * 100);
						$(upload_object).find('.connected_tabs_upload_progress').html ('<?=  GetMessage('STUDIOINTER_EASY_TABS_ADD_UPLOADING')?>: ' + progress + '%');
					},
					done: function (e, image)
					{
						$(upload_object).find('.connected_tabs_upload_progress').remove ();
					
						field_type = 'F';
						var inner_file_list = '';
						if (image.result.IS_IMAGE == 'Y')
						{	
							inner_file_list += '<div class="connected_tab_preview_image">'
							inner_file_list += '<a href="' + image.result.FILE.SRC + '" target="_blank"><img src="' + image.result.RESIZE.src + '" /><input class="edited_input field_type_' + field_type + '" type="hidden" value="' + image.result.image_id + '" ></a>'
						}
						else
						{
							inner_file_list += '<div class="connected_tab_doc_element">'
							file_extension  = image.result.FILE.SRC.split ('.');

							inner_file_list += '<a href="' + image.result.FILE.SRC + '" target="_blank">' + '<span class="connected_tab_doc_element_type">' + file_extension[file_extension.length - 1]  + '</span><input class="edited_input field_type_' + field_type + '" type="hidden" value="' + image.result.FILE.ID + '" >'
							inner_file_list += '<span class="connected_tab_doc_element_name">' + image.result.FILE.FILE_NAME  + '</span></a>'
						}

						inner_file_list += '<span class="connected_tab_file_description"><input class="field_type_file_description" value="' + image.result.FILE.DESCRIPTION + '"></span>';


						inner_file_list += '<div class="con-btn connected_tab_preview_image_delete"><span class="con-btn-wraper">�������</span></div></div>';

						$(upload_object).find ('.connected_tab_file_list').append(inner_file_list);
					}
				});
			}
			
			/**
			 * ���������� ������� � ���� ��, ������� ����� �������������, ������ ��������������
			 */
			function replace_rows_to_edit (data, row_id_prefix)
			{
				$.each(data.ITEMS, function (item_index, item)
				{
					replace_row = $('#' + row_id_prefix + item.item_id).parent('td').eq(0).parent ('tr').eq (0);
			
					if ($(replace_row).hasClass ('connected_tab_selected_row_edit') == false)
					{
					$(replace_row).addClass ('connected_tab_selected_row_edit');
						edited_row_count ++;
						after_select ();
					}
					i = 1;
					$.each(item, function (value_index, value)
					{
						if (value_index != 'item_id')
						{
							object_type = get_object_by_key (data.FIELDS, value_index);
							replace_value ($(replace_row).find ('td').eq (i), value, object_type);
							i++;
						}
					})
				});
				
				if (has_file_upload == 1)
				{
					$('.fileupload').each(function () {
						activate_file_upload ($(this));
					});
				}
			}
	
			function add_ediable_row (after)
			{
				var row = $('<tr/>', {
					class: 'connected_tab_selected_row_new',
					id: 'connected_tab_row_id_0_new'
				});
		
				$(row).append('<td><input type="checkbox" class="connected_tab_checkbox_row" id="connected_tab_check_id_0_new" /></td>')
		
				var field_count = $.map(fields_structure, function(n, i) { return i; }).length;
		
				for (i = 0; i < field_count; i++)
				{
					$(row).append('<td></td>');
				}
		
				if (after == undefined)
				{
					$('#' + main_table_id + ' > tbody').append (row);
				}
				else
				{
					$(after).after(row);
				}
		
				var i = 1;
				$.map (fields_structure, function (index, domElement)
				{
					replace_value ($(row).find ('td').eq (i), '', index);
					i++;
				});
		
				editable_row_innder_html = $('#admin_button_connected_tab_table > tbody tr:last').html ();
				$('#connected_tab_row_id_0_new').hide ();
			}
	
			/**
			 *  ��������� ����������
			 */
			function save_result (table)
			{
				var save_array = [];
				$(table).find ("tbody > tr.connected_tab_selected_row").each (function (index, item)
				{
					if ($(item).hasClass ('connected_tab_selected_row_edit') == true || $(item).hasClass ('connected_tab_selected_row_new') == true)
					{
						var temp_row_array = {};
						
						temp_row_array['row_id'] = $(item).attr ('id');
						
						if ($(item).hasClass ('connected_tab_selected_row_edit') == true)
						{
							temp_row_array['ID'] = $(item).find('td').eq (0).find ('input[type="checkbox"]').attr('id').replace ('connected_tab_check_id_', '');
						}
						else
						{
							temp_row_array['ID'] = 'new';
						}
				
						$(item).find ('td').each (function (index_td, td)
						{	
							if ($(td).find ('.edited_input').val () != undefined)
							{
								temp_input_array = [];
								$(td).find ('.edited_input').each (function (form_input_index, input)
								{
									if ($(input).hasClass ('field_type_F'))
									{
										temp_input_array.push ({value: $(input).val (), description: $(input).parent ('a').eq(0).parent('div').eq(0).find ('.field_type_file_description').val ()});
									}
									else
									{
										if (input.type == 'checkbox' || input.type == 'radio')
										{
											if (input.checked == true)
											{
												temp_input_array.push ($(input).val ());
											}
										}
										else
										{
											switch ($(input).attr ('class').replace ('edited_input', '').replace (' ', '')) {
											  case 'field_type_preview_detail_text':
												temp_input_array.push ({TEXT: $(input).val (), TYPE: $(input).parent('div').find('input[type="radio"]:checked').val()});
												break
											  default:
												temp_input_array.push ($(input).val ());
											}

										}
									}
								});
							
								temp_row_array[get_object_by_number (fields_structure, (index_td-1)).FORM_NAME.replace('connected_tab_', '')] = temp_input_array;
							}
					
						});
						save_array.push (temp_row_array);
					}

				});
		
				$.post ('/bitrix/admin/studiointer_connected_tabs_ajax_save.php?type=<?=preg_replace("[^A-Za-z0-9]", "", $_GET['type'])?>', 
				{ID: <?= $_GET ['ID'] ?>, data: (save_array), fields_structure: fields_structure, property_id: property_id}, function (data)
				{
					$('#admin_button_connected_tab_save').html ('<span class="con-btn-wraper"><?=GetMessage('STUDIOINTER_EASY_TABS_SAVE')?></span>');
					$('#admin_button_connected_tab_save').removeAttr ('disabled');
					
					$(data).each (function (index, value){
						if (value.status == '1')
						{
							$('#' + value.id).html (value.row);
							$('#' + value.id).removeClass ('connected_tab_selected_row_new');
							$('#' + value.id).removeClass ('connected_tab_selected_row_edit');
							
							if (value.added_id != undefined)
							{
								$('#' + value.id).attr ('id', 'connected_tab_row_id_' + value.added_id);
							}
							
							$('#' + value.id).find ('.connected_tab_checkbox_row').prop ('checked', true);
							
							edited_row_count --;
							after_select ();
							
						}
						else
						{
							$('#' + value.id).find ('td').eq(0).remove ('.admin_button_connected_tab_unsuccess_saved_message');
							$('#' + value.id).find ('td').eq(0).removeClass ('admin_button_connected_tab_success_saved');
							$('#' + value.id).find ('td').eq(0).addClass ('admin_button_connected_tab_unsuccess_saved');
							$('#' + value.id).find ('td').eq(0).append ('<span class="admin_button_connected_tab_unsuccess_saved_message">' + value.message + '</span>');
						}
					})
					
				}, 'json');
				
			}
			
			function assign_to_another_offer ()
			{
				$('#connected_tab_wraper_reassign').toggle();
				
				 $( "#connected_tab_wraper_reassign_input" ).autocomplete({
					source: "/bitrix/admin/studiointer_connected_tabs_ajax_reassign_list.php?property_id=" + property_id + "&type=<?=preg_replace("[^A-Za-z0-9]", "", $_GET['type'])?>&IBLOCK_ID=<?=intval ($_GET['IBLOCK_ID'])?>",
					minLength: 2,
					select: function( event, ui ) {
							$('#connected_tab_autocomlete_element_hint').html ('<a href="/bitrix/admin/iblock_element_edit.php?ID=' + ui.item.value + '&type=<?=$_GET['type']?>&lang=<?=$_GET['ru']?>&IBLOCK_ID=<?=$_GET['IBLOCK_ID']?>" target="_blank">' + ui.item.label + '</a>');
						}
					
				  });
			}

			function add_ediable_row_last ()
			{
				var can_add = false;
				if (document.getElementById('connected_tab_row_id_0_new') != null)
				{
					can_add = true;
					if (document.getElementById('connected_tab_row_id_0_new').style.display != 'none')
					{
						can_add = true;
					}
				}
				
				if (can_add != true && document.getElementById('connected_tab_row_id_0_new') != null)
				{
					document.getElementById('connected_tab_row_id_0_new').style.display = ''
				}
				else
				{
					var main = document.getElementById(main_table_id);
					var tbody = main.getElementsByTagName('tbody');
					var tr = tbody[0].getElementsByTagName('tr');
					
					var temp_row_body = editable_row_innder_html;
					
					temp_row_body = temp_row_body.replace ('id="connected_tab_check_id_0_new"', 'checked="checked" id="connected_tab_check_id_0_new"');
					temp_row_body = temp_row_body.replace ('connected_tab_check_id_0', 'connected_tab_check_id_' + tr.length);
					
					temp_row_body = temp_row_body.split('connected_tabs_checkbox_').join('connected_tabs_checkbox_' + tr.length  + '_');
					temp_row_body = temp_row_body.split('connected_tabs_radio_').join('connected_tabs_radio_' + tr.length  + '_');
					temp_row_body = temp_row_body.split('connected_tab_textarea_type_id_').join('connected_tab_textarea_type_id_' + tr.length  + '_');
					temp_row_body = temp_row_body.split('connected_tab_textarea_type_name_').join('connected_tab_textarea_type_name_' + tr.length  + '_');
					temp_row_body = temp_row_body.split('value="" name="connected_tab_SORT"').join('name="connected_tab_SORT" value="' + parseInt(first_sort - parseInt (tr.length) + 1) + '"');
					
					temp_row_body = temp_row_body.split('<input name="connected_tab_ACTIVE" value="Y" class="edited_input field_type_bool" type="checkbox">').join('<input type="checkbox" class="edited_input field_type_bool" value="Y" checked="checked" name="connected_tab_ACTIVE">');
					
					$('#' + main_table_id + ' > tbody').append ('<tr id="connected_tab_row_id_' 
						+ tr.length 
						+ '_new" class="connected_tab_selected_row_new">' 
						+ temp_row_body
						+ '</tr>');
				
					select_row (document.getElementById ('connected_tab_row_id_' + (parseInt (tr.length) - 1) + '_new'), 'connected_tab_selected_row');
				
					if (editable_row_innder_html.search ('fileupload') > 0)
					{
						activate_file_upload ($('#admin_button_connected_tab_table > tbody > tr:last').find ('.fileupload'));
					}
				}
			}
			
			function delete_selected_elements ()
			{
				$('#admin_button_connected_tab_table > tbody').removeClass('connected_tab_will_be_deleted');
				
				selected_array_id = new Array ();
				$('#admin_button_connected_tab_table > tbody > tr.connected_tab_selected_row').each(function (tr_index, tr_item)
				{
					var deleted_id_row = $(tr_item).attr ('id').split ('_');
					
					if (deleted_id_row.length == 6)
					{
						$(tr_item).fadeOut (1000, function ()
						{
							$(tr_item).remove ();
							selected_row_count --;
							after_select ();
						});
					}
					else
					{
						selected_array_id.push (deleted_id_row [4]);
					}
				});
				
				if (selected_array_id.length > 0)
				{
					$.post ('/bitrix/admin/studiointer_connected_tabs_ajax_delete.php', {data: selected_array_id, property_id: property_id}, function (data){
						$(data).each(function (is_deleted_index, is_deleted_item)
						{
							if (is_deleted_item.status == 'ok')
							{
								$('#connected_tab_row_id_' + is_deleted_item.id).fadeOut (1000, function ()
								{
									$('#connected_tab_row_id_' + is_deleted_item.id).remove ();
									
									selected_row_saved_count --;
									edited_row_count --;
									after_select ();
								});
							}
						});
					}, 'json');
					
				}
				
				after_select ();
			}

			$(document).ready (function ()
			{
				$('#connected_tab_wraper_reassign').hide ();
				// �������� ������ �������� ������� �������� ��� ����������
				first_sort = parseInt ($.trim ($("#admin_button_connected_tab_table > tbody > tr .connected_tab_sort_row").eq (0).html ()));

				if (isNaN (first_sort) == true)
				{
					first_sort = 500;
				}
				
				$('#connected_tab_wraper_reassign_move').click (function ()
				{
					
						selected_array_id = new Array ();
						$('#admin_button_connected_tab_table > tbody > tr.connected_tab_selected_row').each(function (tr_index, tr_item)
						{
							tr_id = $(tr_item).attr('id');
							
							tr_temp_array = tr_id.split ('_');
							
							if (tr_temp_array.length == 5)
							{
								selected_array_id.push (tr_temp_array[4]);
							}
						});
						
						if (selected_array_id.length == 0)
						{
							$('#connected_tab_wraper_reassign_input').focus ();
						}
						else
						{
							$.post ('/bitrix/admin/studiointer_connected_tabs_ajax_reassign_assign.php', {id_array: selected_array_id, parent_id: $('#connected_tab_wraper_reassign_input').val (), property_id: property_id}, function (data)
							{
								$(selected_array_id).each (function (selected_array_id_index, selected_array_id_value)
								{
									$('#connected_tab_row_id_' + selected_array_id_value).fadeTo (500, 0, function ()
									{
										$(this).remove ();
									});
								});
							}, 'json');
						}
				});
			
				$('#admin_button_connected_tab_edit').click (function ()
				{
					show_edit_rows ($('#admin_button_connected_tab_table'), 'connected_tab_check_id_');
			
					return false;
				});
				$('#admin_button_connected_tab_reassign').click (function ()
				{
					assign_to_another_offer ();
					
					return false;
				});

				$('#connected_tab_check_all').on ('click', function ()
				{
					check_rows ($('#admin_button_connected_tab_table'), 'connected_tab_selected_row');
				});
	
				$('#admin_button_connected_tab_table > tbody').on ('change', "input.connected_tab_checkbox_row", function (e)
				{
					if ($(this).is(':checked') == true)
					{
						select_row (document.getElementById ($(this).attr('id').replace ('check', 'row')), 'connected_tab_selected_row');
					}
					else
					{
						unselect_row (document.getElementById ($(this).attr('id').replace ('check', 'row')), 'connected_tab_selected_row');
					}
					e.preventDefault();
				});
		
				$('#admin_button_connected_tab_save').click (function ()
				{
					document.getElementById ('admin_button_connected_tab_save').innerHTML = '<span class="con-btn-wraper"><?=  GetMessage('STUDIOINTER_EASY_TABS_SAVING')?>...</span>';
					save_result ($('#' + main_table_id));
					return false;
				});
				
				// �������� ��������
				$('#admin_button_connected_tab_delete').hover (function ()
				{
					$('#admin_button_connected_tab_table > tbody').toggleClass ('connected_tab_will_be_deleted');
				});
				
				// ��������� ��������� ���������
				$('#admin_button_connected_tab_delete').mouseleave (function ()
				{
					$('#admin_button_connected_tab_table > tbody').removeClass ('connected_tab_will_be_deleted');
				});
				
				// �������� ��������
				$('#admin_button_connected_tab_delete').click (function ()
				{
					delete_selected_elements ();
				});
				
				// �������� ��������
				$('#admin_button_connected_tab_table > tbody').on ('click', ".connected_tab_preview_image_delete", function (e)
				{
					$(this).parent ('div').fadeOut (1000, function ()
					{
						$(this).remove ();
					});
				});
				
				/*// ���� ����������� ��������� ��������, �� ����� ������ ������ � �������� (�� ����, ����� ���������, ��� ��� ������)
				$('.adm-detail-tab').click (function ()
				{
					if ($('#admin_button_connected_tab_table').is (':visible') == true)
					{
						$('#save').prop ('disabled', true);
						$('#apply').prop ('disabled', true);
						$('#dontsave').prop ('disabled', true);
						$('#save_and_add').prop ('disabled', true);
					}
					else
					{
						$('#save').prop ('disabled', false);
						$('#apply').prop ('disabled', false);
						$('#dontsave').prop ('disabled', false);
						$('#save_and_add').prop ('disabled', false);
					}
				});*/
		
				// ��������� ���� ������ ������
				add_ediable_row ();
		
				var editable_row_innder_html = $('#admin_button_connected_tab_table > tbody tr:last').html ();
				
				$('#admin_button_connected_tab_add').click (function ()
				{
					add_ediable_row_last ();
				});
				
				// �������� � ������������� �����������
				$(window).keyup(function (e)
				{
					// ���������
					if ((e.keyCode == 83) && e.altKey)
					{
						document.getElementById ('admin_button_connected_tab_save').innerHTML = '<span class="con-btn-wraper"><?=  GetMessage('STUDIOINTER_EASY_TABS_SAVING')?>...</span>';
						save_result ($('#' + main_table_id));
						return false;
					}
				
					// �������������
					if ((e.keyCode == 229 || e.keyCode == 69) && e.altKey)
					{
						show_edit_rows ($('#admin_button_connected_tab_table'), 'connected_tab_check_id_');
			
						return false;
					}
				
					// �������� ����� ������ �������
					if (e.keyCode == 40 && e.altKey)
					{
						add_ediable_row_last ();
					}
				});
	
			});
</script>


