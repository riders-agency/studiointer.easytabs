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

$arFilter =  Array ( "NAME" => "%" . addslashes($_GET['term']) . "%", 'IBLOCK_ID' => intval ($_GET['IBLOCK_ID']));

$arSelect = Array("ID", "NAME");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);

$send_array = array ();

while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  
  $send_array [] = array ('value' => $arFields['ID'], 'label' => $arFields['NAME'], 'name' => $arFields['NAME']);
}

echo json_encode($send_array);