<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

CModule :: IncludeModule ('iblock');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/iblock/prolog.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('studiointer.easytabs');

if (CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'X' && CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_POST['property_id']))) != 'W')
{
	die;
}
if (is_array ($_POST['id_array']) && intval ($_POST ['parent_id']) > 0)
{
	foreach ($_POST['id_array'] as $key => $value)
	{
		if (intval ($value > 0))
		{
			
			CIBlockElement::SetPropertyValuesEx(
			intval ($value),
			false,
			array(
					intval ($_POST['property_id']) => intval ($_POST ['parent_id'])
				)
			);
		}
	}
	echo json_encode(array ('status' => 'ok'));
}
else
{
	echo json_encode(array ('status' => 'bad'));
}