<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	Author	   : Inozemtsev Konstantin, +7 905 715-99-54, admin@studiointer.net
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

$id_array = array ();

foreach ($_POST['data'] as $key => $value) {
	
	if(!CIBlockElement::Delete($value))
	{
		$id_array [] = array ('id' => $value, 'status' => 'error');
	}
	else
	{
		$id_array [] = array ('id' => $value, 'status' => 'ok');	
	}
}

echo json_encode($id_array);