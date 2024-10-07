<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");

CModule :: IncludeModule ('iblock');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule ('studiointer.easytabs');

if (CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'X' && CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'W')
{
	die ();
}

$normalize_id_array = array ();

foreach ($_POST['data'] as $key => $value) {
	array_push($normalize_id_array, intval($value));
}

$arAdminResult = ConnectedTab :: GetOfferList(intval ($_POST['property_id']), $normalize_id_array);
$arAdminResult ['FIELDS'] = array ();
foreach ($arAdminResult ['COLUMNS'] as $key => $value) {
	
	$arAdminResult ['FIELDS'][$key]['NAME'] = $value;
	$arAdminResult ['FIELDS'][$key]['TYPE'] = ConnectedTab :: GetFieldType ($key);
	$arAdminResult ['FIELDS'][$key]['FORM_NAME'] = $key;
}

echo json_encode($arAdminResult);