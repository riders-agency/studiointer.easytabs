<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	Author  : Inozemtsev Konstantin, +7 905 715-99-54, admin@studiointer.net
*/

IncludeModuleLangFile(__FILE__);

global $DB;

global $MESS;

class ConnectedTab {

	/**
	 * ������ ������ �������� �����������
	 * @param type $form
	 */
	static function MakeForm(&$form) {
		
		
		CModule::IncludeModule('iblock');
		
		// ��� �������������� ��������
		if ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/iblock_element_edit.php")
		{

			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "LINK_IBLOCK_ID"=>$_GET ['IBLOCK_ID']));

			$property_array_new = array ();
			$property_array = array ();
			
			while ($temp_array = $properties -> Fetch ())
			{
				$property_array_new[] = $temp_array;
			}
			
			if (!isset ($_GET ['si_connected_iblock']))
			{
				$property_array = $property_array_new[0];
			}
			else
			{
				foreach ($property_array_new as $property_key => $property)
				{
					if ($property['ID'] == intval ($_GET ['si_connected_iblock']))
					{
						$property_array = $property_array_new[$property_key];
					}
				}
			}
			
			if (!empty($property_array) && count ($property_array) == 0)
			{
				$property_array = $property_array_new[0];
			}
			
			$rsPropertyIBLOCK = CIBlock::GetByID ($property_array['IBLOCK_ID']);

			$arPropertyIBLOCK = $rsPropertyIBLOCK -> Fetch ();
			
			$has_content = false;

			/*if ($_GET['s'] == 1) {
				echo '<pre>';
				echo json_encode($form->tabs);
				exit;
			}*/

			/*<?if($_GET['s'] == '1'):?> <?endif?>*/
			foreach ($form->tabs as $tabs_key => $tabs)
			{
				if (array_key_exists ('DIV', $tabs))
				{
					$has_content = true;
				}
			}
			
			if (!empty ($property_array['NAME']) && $has_content == true) {
				
				$form->tabs[] = array(
					"DIV" => "offer_edit_" . $property_array['ID'],
					"TAB" =>  $arPropertyIBLOCK['NAME'] . ' &mdash; ' . $property_array['NAME'],
					"ICON" => "main_user_edit",
					"TITLE" => $arPropertyIBLOCK['NAME'] . ' &mdash; ' . $property_array['NAME'],
					"CONTENT" => ConnectedTab :: GetContent($property_array['ID'], $property_array_new)
				);
			}
		}
	}


	/**
	 * �������� IBLOCK_ID �������� �����������
	 * @return int
	 */
	static function GetConnectedIblockID($property_id) {
		$rsProperty = CIBlockProperty::GetByID ($property_id);
		$arProperty = $rsProperty -> Fetch ();
		return $arProperty['IBLOCK_ID'];
	}

	/**
	 *  �������� ������ �����������
	 * @return type
	 */
	static function GetOfferList ($property_id, $IBlockElementID = NULL)
	{
		$list_column_array = ConnectedTab :: GetListColumn($property_id);
		
		$arAdminResult ['COLUMNS'] = $list_column_array;

		// �������� ������ �����������
		$arSelect = Array("*", "PROPERTY_*");
		
		if ($IBlockElementID == NULL)
		{
			$arFilter = Array(
				"IBLOCK_ID" => ConnectedTab :: GetConnectedIblockID($property_id),
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"PROPERTY_" . $property_id => intval($_REQUEST ['ID'])
			);
		}
		else
		{
			$arFilter = Array(
				"IBLOCK_ID" => ConnectedTab :: GetConnectedIblockID($property_id),
				"ID" => $IBlockElementID,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"PROPERTY_" . $property_id => intval($_REQUEST ['ID'])
			);
		}
		
		$arAdminResult ['FIELDS'] = array ();
		foreach ($arAdminResult ['COLUMNS'] as $key => $value) {

			$arAdminResult ['FIELDS'][$key]['NAME'] = $value;
			$arAdminResult ['FIELDS'][$key]['TYPE'] = ConnectedTab :: GetFieldType ($key);
			$arAdminResult ['FIELDS'][$key]['FORM_NAME'] = $key;
		}

		$res = CIBlockElement::GetList(Array('SORT' => 'DESC'), $arFilter, false, Array("nPageSize" => 50), $arSelect);

		$element_array = array();
		$property_array = array();

		while ($ob = $res->GetNextElement()) {
			$element_array[] = $ob->GetFields();

			$element_array[count($element_array) - 1]['PROPERTIES'] = $ob->GetProperties();
		}
		
		
		// ������������� � ������� �����
		$arAdminResult ['ITEMS'] = array ();
		foreach ($element_array as $key => $value)
		{
			$arAdminResult ['ITEMS'][] = array ();
			
			foreach ($list_column_array as $list_column_key => $list_column_value)
			{
				$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1]['item_id'] = $value['ID'];
				if (substr_count ($list_column_key, 'PROPERTY') == 1)
				{
					foreach ($value['PROPERTIES'] as $property_key => $property_value)
					{
						if ($property_value['ID'] == str_replace('PROPERTY_', '', $list_column_key))
						{
							switch ($property_value['PROPERTY_TYPE'])
							{
								case 'F':
									if (is_array ($property_value['VALUE']))
									{
										foreach ($property_value['VALUE'] as $image_key => $image_id)
										{
											// �������� ��� ��������
											
											$file_array = CFile :: GetFileArray ($image_id);
											
											$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['IMAGES'][$image_key]['FILE'] = $file_array;

											$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['IMAGES'][$image_key]['image_id'] = $image_id;
											
											if (substr_count($file_array['CONTENT_TYPE'], 'image') == 1)
											{
												$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['IMAGES'][$image_key]['RESIZE'] = CFile::ResizeImageGet($image_id, array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
												$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['IMAGES'][$image_key]['IS_IMAGE'] = 'Y';
											}
											else
											{
												$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['IMAGES'][$image_key]['IS_IMAGE'] = 'N';
											}
											
											$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]['TYPE'] = 'F';
										}
									}
									else
									{
										$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = $property_value['VALUE'];
									}
									break;

								default:
									
									$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = $property_value['VALUE'];
									break;
							}
						}
					}
				}
				else
				{
					switch ($list_column_key) {
						case 'DETAIL_PICTURE':
						case 'PREVIEW_PICTURE':
							if (!empty ($value[$list_column_key]))
							{
								$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = array (

									'IMAGES' => array ( 0 =>  array (
										'RESIZE' => CFile::ResizeImageGet($value[$list_column_key], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true),
										'FILE' => CFile::GetFileArray ($value[$list_column_key]),
										'IS_IMAGE' => 'Y'

									)));
							}
							else
							{
								$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = $value[$list_column_key];
							}
//							var_dump ($arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key]);

							break;
						
						
						case 'DETAIL_TEXT':
						case 'PREVIEW_TEXT':
							$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = array ('TEXT' => $value[$list_column_key], 'TYPE' => $value[$list_column_key . '_TYPE']);
							
						break;

						default:
							$arAdminResult ['ITEMS'][count ($arAdminResult ['ITEMS']) - 1][$list_column_key] = $value[$list_column_key];
							break;
					}
				}
			}	
		}
		
		return $arAdminResult;
	}
	
	/**
	 * ���������� ������ ����� �������
	 * 
	 * @param type $ID
	 */	
	static function GetRowHTMLArray ($property_id, $ID = NULL)
	{
		
		foreach ($ID as $key => $value) {
			if (intval ($value) == 0 || $value != intval ($value))
			{
				unset ($ID[$key]);
			}
		}
		
		if (count ($ID) == 0)
		{
			return false;
		}
		
		$arAdminResult = array ();
		
		$arAdminResult = ConnectedTab :: GetOfferList($property_id, $ID);
		
		$arAdminResult['PROPERTY_ID'] = $property_id;
		
		ob_start();

		include ($_SERVER['DOCUMENT_ROOT']
				. DIRECTORY_SEPARATOR
				. 'bitrix'
				. DIRECTORY_SEPARATOR
				. 'modules'
				. DIRECTORY_SEPARATOR
				. 'studiointer.easytabs'
				. DIRECTORY_SEPARATOR
				. 'admin'
				. DIRECTORY_SEPARATOR
				. 'templates'
				. DIRECTORY_SEPARATOR
				. 'connected_tabs'
				. DIRECTORY_SEPARATOR
				. 'row.php'
				);

		$result = ob_get_contents();

		ob_end_clean();

		return $result;
	}
	
	/**
	 * ������� ������ � ��������� �����
	 * 
	 * @return string
	 */
	static function GetContent($property_id, $property_array) {
		
		// �������� ������ ���� �������������� ������, ����� ���� ����������� ������������� ������ ����� � ����
		
		$rsIBLOCKList =  CIBlock::GetList(Array("SORT"=>"ASC"),Array(), false);
		
		$arResult ['iblock_list'] = array ();
		
		while ($tmp_array = $rsIBLOCKList -> Fetch ())
		{
			$arResult ['iblock_list'][] = $tmp_array;
		}
		
		if (intval($_GET ['ID']) <= 0) {
			return GetMessage ('STUDIOINTER_EASY_TABS_MODULE_SAVE_MAIN_FIRST');
		}

		$arAdminResult = ConnectedTab :: GetOfferList($property_id);
		$arAdminResult['PROPERTY_ID'] = $property_id;
		ob_start();

		include_once ($_SERVER['DOCUMENT_ROOT']
				. DIRECTORY_SEPARATOR
				. 'bitrix'
				. DIRECTORY_SEPARATOR
				. 'modules'
				. DIRECTORY_SEPARATOR
				. 'studiointer.easytabs'
				. DIRECTORY_SEPARATOR
				. 'admin'
				. DIRECTORY_SEPARATOR
				. 'templates'
				. DIRECTORY_SEPARATOR
				. 'connected_tabs'
				. DIRECTORY_SEPARATOR
				. 'template.php'
				);

		$result = ob_get_contents();

		ob_end_clean();
		
//		echo ' call ';

		return $result;
	}

	static function GetListColumn($property_id, $type = '') {
		//$aOptions = CUserOptions::GetOption("list", 'b_user_option', array());
		
		$rsIBlock = CIBlock::GetByID (ConnectedTab :: GetConnectedIblockID($property_id));
		
		$arIBlock = $rsIBlock -> Fetch ();
		
		$user_columns = CUserOptions::GetOption("list", "tbl_iblock_list_" . md5($arIBlock['IBLOCK_TYPE_ID'] . '.' . ConnectedTab :: GetConnectedIblockID ($property_id)));
		
		if (!$user_columns)
		{
			$user_columns = CUserOptions::GetOption("list", "tbl_iblock_element_" . md5($arIBlock['IBLOCK_TYPE_ID'] . '.' . ConnectedTab :: GetConnectedIblockID ($property_id)));
			if (!$user_columns) {
				$user_columns = CUserOptions::GetOption("main.interface.grid", "tbl_iblock_element_" . md5($arIBlock['IBLOCK_TYPE_ID'] . '.' . ConnectedTab :: GetConnectedIblockID ($property_id)));
				$user_columns['columns'] = $user_columns['views']['default']['columns'];
			}
			if (!$user_columns)
			{
				$user_columns['columns'] = 'NAME,ACTIVE,SORT,TIMESTAMP_X,ID';
			}
		}
	
		$result_array = array ();
		$list_column_array_match = array(
			'ID' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_ID"),
			'NAME' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_NAME"),
			'SORT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_SORT"),
			'PREVIEW_TEXT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_PREVIEW_TEXT"),
			'DETAIL_TEXT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DETAIL_TEXT"),
			'TIMESTAMP_X' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_TIMESTAMP_X"),
			'ACTIVE' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_ACTIVE"),
			'MODIFIED_BY' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_MODIFIED_BY"),
			'DATE_CREATE' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DATE_CREATE"),
			'EXTERNAL_ID' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_EXTERNAL_ID"),
			'CREATED_BY' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_CREATED_BY"),
			'CREATED_USER_NAME' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_CREATED_USER_NAME"),
			'USER_NAME' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_USER_NAME"),
			'ACTIVE_FROM' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_ACTIVE_FROM"),
			'DATE_ACTIVE_FROM' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DATE_ACTIVE_FROM"),
			'ACTIVE_TO' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DATE_ACTIVE_TO"),
			'DATE_ACTIVE_TO' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DATE_DATE_ACTIVE_TO"),
			'PREVIEW_PICTURE' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_PREVIEW_PICTURE"),
			'PREVIEW_TEXT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_PREVIEW_TEXT"),
			'DETAIL_PICTURE' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DETAIL_PICTURE"),
			'DETAIL_TEXT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_DETAIL_TEXT"),
			'CODE' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_CODE"),
			'TAGS' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_TAGS"),
			'ELEMENT_CNT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_ELEMENT_CNT"),
			'SECTION_CNT' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_SECTION_CNT"),
			'SHOW_COUNTER' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_SHOW_COUNTER"),
			'SHOW_COUNTER_START' => GetMessage("STUDIOINTER_EASY_TABS_FIELD_SHOW_COUNTER_START"),
			);
		
		foreach (explode (',', $user_columns['columns']) as $key => $val)
		{
			if (substr_count($val, 'PROPERTY_') == 0)
			{
				$result_array[$val] = $list_column_array_match [$val];
			}
			else
			{
				if (str_replace ('PROPERTY_', '', $val) != $property_id)
				{
					$rsProperty = CIBlockProperty::GetByID (str_replace ('PROPERTY_', '', $val));
					$arProperty = $rsProperty -> Fetch ();
					$result_array[$val] = $arProperty['NAME'];
				}
			}
		}

		return $result_array;
	}
	
	static function AddIBlockElement ($row, $ID, $property_id)
	{
		global $USER;
		$el = new CIBlockElement;
		
		$main_fields_array = array ();
		
		$property_array = array ($property_id => array ('VALUE' => $ID));
		
		foreach ($row as $key => $value)
		{
			if (substr_count($key, 'PROPERTY_') == 1)
			{
				if (!empty ($_POST ['fields_structure'][$key]['TYPE']['CODE']))
				{
					switch ($_POST ['fields_structure'][$key]['TYPE']['PROPERTY_TYPE'])
					{
						case "F":
							foreach ($value as $file_key => $file_id)
							{
								$property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']][$file_key]['VALUE'] = CFile :: MakeFileArray ($file_id['value']);
								$property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']][$file_key]['DESCRIPTION'] = $file_id['description'];
							}
							
						break;
					
						default:
							$property_array [$_POST ['fields_structure'][$key]['TYPE']['CODE']] = array ();
							foreach ($value as $property_value_key => $property_value) {
								if (is_array($property_value))
								{
									foreach ($property_value as $property_array_sub_key => $property_array_sub_value)
									{
										array_push ($property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']], array ('VALUE' => $property_array_sub_value));
									}
								}
								else
								{
									array_push ($property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']], array ('VALUE' => $property_value));
								}
							}
					}
				}
				
				//$property_array[str_replace('PROPERTY_', '', $key)] = $value;
				//print_r ($property_array);
				unset ($row[$key]);
			}
			else
			{
				switch ($key) {
					case 'PREVIEW_PICTURE':
					case 'DETAIL_PICTURE':
						
						if (!empty ($value [0]['value']))
						{
							$update_file = CFile::UpdateDesc(
							 $value [0]['value'],
							 $value [0]['description']
							);

							$main_fields_array [$key] = CFile::MakeFileArray($value [0]['value']);
						}
						else
						{
							$main_fields_array [$key]['del'] = 'Y';
						}
						
						break;
					
					case 'PREVIEW_TEXT':
					case 'DETAIL_TEXT':
						
						$main_fields_array [$key] = ($value [0]['TEXT']);
						$main_fields_array [$key. '_TYPE'] = ($value [0]['TYPE']);
						
						break;
					default:
						
						$main_fields_array [$key] = $value [0];
						break;
				}
			}
		}

		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
		  "IBLOCK_ID"    => ConnectedTab :: GetConnectedIblockID ($property_id), // ������� ������� ������� �������������
		  );
		
		unset ($main_fields_array['ID']);
		if (!isset($arLoadProductArray ['NAME']) || empty ($arLoadProductArray ['NAME']))
		{
			$arLoadProductArray ['NAME'] = "OFFER";
		}
		
		$id = $el->Add(array_merge ($arLoadProductArray, $main_fields_array));
		
		CIBlockElement::SetPropertyValuesEx($id, false, $property_array);
		if (!$id)
		{
			return array ('status' => '0', 'message' => $el->LAST_ERROR);
		}
		else
		{
			return array ('status' => '1', 'added_id' => $id);
		}
	}
	
	static function UpdateIBlockElement ($row, $ID)
	{
		global $USER;
		$el = new CIBlockElement;
		
		$main_fields_array = array ();
		
		$property_array = array ();
		
		foreach ($row as $key => $value)
		{
			if (substr_count($key, 'PROPERTY_') == 1)
			{
				if (!empty ($_POST ['fields_structure'][$key]['TYPE']['CODE']))
				{
					switch ($_POST ['fields_structure'][$key]['TYPE']['PROPERTY_TYPE'])
					{
						case "F":
							foreach ($value as $file_key => $file_id)
							{
								$property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']][$file_key]['VALUE'] = CFile :: MakeFileArray ($file_id['value']);
								$property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']][$file_key]['DESCRIPTION'] = $file_id['description'];
							}
							
						break;
					
						default:
							$property_array [$_POST ['fields_structure'][$key]['TYPE']['CODE']] = array ();
							foreach ($value as $property_value_key => $property_value) {
								if (is_array($property_value))
								{
									foreach ($property_value as $property_array_sub_key => $property_array_sub_value)
									{
										array_push ($property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']], array ('VALUE' => $property_array_sub_value));
									}
								}
								else
								{
									array_push ($property_array[$_POST ['fields_structure'][$key]['TYPE']['CODE']], array ('VALUE' => $property_value));
								}
							}
					}
				}
				
				//$property_array[str_replace('PROPERTY_', '', $key)] = $value;
				//print_r ($property_array);
				unset ($row[$key]);
			}
			else
			{
				switch ($key) {
					case 'PREVIEW_PICTURE':
					case 'DETAIL_PICTURE':
						
						if (!empty ($value [0]['value']))
						{
							$update_file = CFile::UpdateDesc(
							 $value [0]['value'],
							 $value [0]['description']
							);

							$main_fields_array [$key] = CFile::MakeFileArray($value [0]['value']);
						}
						else
						{
							$main_fields_array [$key]['del'] = 'Y';
						}
						
						break;
					
					case 'PREVIEW_TEXT':
					case 'DETAIL_TEXT':
						
						$main_fields_array [$key] = ($value [0]['TEXT']);
						$main_fields_array [$key. '_TYPE'] = ($value [0]['TYPE']);
						break;

					default:
						
						$main_fields_array [$key] = $value [0];
						break;
				}
			}
		}

		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
		  );
		
		$id = $row['ID'];
		unset ($main_fields_array['ID']);
		
		$res = $el->Update($id, array_merge ($arLoadProductArray, $main_fields_array));
		CIBlockElement::SetPropertyValuesEx($id, false, $property_array);
		
		if (!$res)
		{
			return array ('status' => '0', 'message' => $el->LAST_ERROR);
		}
		else
		{
			return array ('status' => '1');
		}
	}
	
	/**
	 * ���������� ��� ���� �� ��� �����
	 * 
	 * @param string $field
	 * @return string
	 */
	static function GetFieldType ($field)
	{
		if (substr_count($field, 'PROPERTY') == '1')
		{
			$property_id = str_replace('PROPERTY_', '', $field);
			$field = 'property';
		}
		switch ($field) {
			case 'ID':
			case 'TIMESTAMP_X':
			case 'MODIFIED_BY':
			case 'DATE_CREATE':
			case 'CREATED_BY':
			case 'USER_NAME':
			case 'CREATED_USER_NAME':
			case 'ELEMENT_CNT':
			case 'SECTION_CNT':
			case 'SHOW_COUNTER':
			case 'SHOW_COUNTER_START':

				return 'label';
				break;
			case 'ACTIVE':

				return 'bool';
				break;
			case 'SORT':

				return 'sort';
				break;
			case 'PREVIEW_PICTURE':
			case 'DETAIL_PICTURE':

				return 'F';
				break;
			case 'PREVIEW_TEXT':
			case 'DETAIL_TEXT':

				return 'preview_detail_text';
				break;
			case 'property':
				$temp_property_array = array ();
				$property_array = array ();
				
				$rsProperty = CIBlockProperty::GetByID(
				 $property_id
						);
				
				$property_array = $rsProperty -> Fetch ();
				
				if ($property_array ['PROPERTY_TYPE'] == 'L')
				{
					$property_array['LIST'] = array ();
					$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("PROPERTY_ID"=>$property_array['ID']));
					while($enum_fields = $property_enums->GetNext())
					{
						$property_array['LIST'][] = $enum_fields;
					}
				}
				
				return $property_array;
				break;
			default:
				return 'text';
				break;
		}
	}
}