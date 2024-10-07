<?
/* 
    Created on : 12.02.2013, 23:11:14
    
	
*/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

CModule :: IncludeModule ('iblock');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/iblock/prolog.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('studiointer.easytabs');

if (CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_GET['property_id']))) != 'X' && CIBlock::GetPermission (ConnectedTab :: GetConnectedIblockID(intval ($_GET['property_id']))) != 'W')
{
	die ();
}

$arrFile = array();
if ($_FILES["files"]) {
	foreach ($_FILES["files"]["name"] as $key => $photo) {
		$tmpFile = Array(
			"name" => $photo,
			"size" => $_FILES["files"]["size"][$key],
			"tmp_name" => $_FILES["files"]["tmp_name"][$key],
			"type" => $_FILES["files"]["type"][$key],
			"old_file" => "",
			"del" => "y",
			"MODULE_ID" => "iblock");

		$fid = CFile::SaveFile($tmpFile, "/upload/iblock/");
		$arrFile[] = $fid;
	}
}


if (intval ($arrFile [0] > 0))
{

	$responce_array = array ();
	// �������� ��� ��������

	$file_array = CFile :: GetFileArray ($arrFile [0]);

	$responce_array['FILE'] = $file_array;

	$responce_array['image_id'] = $arrFile [0];

	if (substr_count($file_array['CONTENT_TYPE'], 'image') == 1)
	{
		$responce_array['RESIZE'] = CFile::ResizeImageGet($arrFile [0], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		$responce_array['IS_IMAGE'] = 'Y';
	}
	else
	{
		$responce_array['IS_IMAGE'] = 'N';
	}

	$responce_array['TYPE'] = 'F';
	
	$resized = CFile::ResizeImageGet($arrFile [0], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	echo json_encode($responce_array);
}