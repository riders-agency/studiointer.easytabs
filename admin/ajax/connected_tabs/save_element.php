<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule :: IncludeModule ('iblock');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule ('studiointer.easytabs');

if (CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'X' && CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'W')
{
	die;
}

$response_array = array ();
foreach ($_POST['data'] as $key => $value)
{
	if (intval ($value['ID']) > 0)
	{
		$response_array [] = ConnectedTab :: UpdateIBlockElement ($value, $_POST ['ID']);
		$response_array [count ($response_array) - 1]['id'] = $value ['row_id'];
		$response_array [count ($response_array) - 1]['row'] = ConnectedTab :: GetRowHTMLArray (intval ($_POST['property_id']), array (0 => $value ['ID']));
	}
	
	if ($value['ID'] == 'new')
	{
		$response_array [] = ConnectedTab :: AddIBlockElement ($value, $_POST ['ID'], intval ($_POST['property_id']));
		$response_array [count ($response_array) - 1]['id'] = $value ['row_id'];
		$response_array [count ($response_array) - 1]['row'] = ConnectedTab :: GetRowHTMLArray (intval ($_POST['property_id']), array (0 => $response_array [count ($response_array) - 1] ['added_id']));
	}
}

echo json_encode($response_array);