<?
global $MESS;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/studiointer.easytabs/install/index.php");

Class studiointer_easytabs extends CModule
{
	var $MODULE_ID = "studiointer.easytabs";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';
	public $NEED_MAIN_VERSION = '';
	public $NEED_MODULES = array();
	
	function __construct() 
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		
		$this -> MODULE_NAME = GetMessage("STUDIOINTER_EASY_TABS_MODULE_NAME");
		
		$this->PARTNER_NAME = GetMessage("STUDIOINTER_EASY_TABS_PARTNER_NAME");
		$this->PARTNER_URI = 'http://www.studiointer.net/';

	}
	
	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		$step = IntVal($step);
		$errors = false;
		
		$this->errors = false;
		$this->InstallFiles();
		
		RegisterModule("studiointer.easytabs");
		
		RegisterModuleDependences("main", "OnAdminTabControlBegin", "studiointer.easytabs", "ConnectedTab", "MakeForm");
		
		$APPLICATION->IncludeAdminFile("Установка", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/studiointer.easytabs/install/step1.php");
		
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;
		
		$this->errors = false;
		UnRegisterModuleDependences("main", "OnAdminTabControlBegin", "studiointer.easytabs", "ConnectedTab", "MakeForm");
		UnRegisterModule("studiointer.easytabs");
		
		$this -> UnInstallFiles ();
		
		$APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/studiointer.easytabs/install/unstep1.php");

	}
	
	function InstallFiles()
	{
		CopyDirFiles($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/install/admin", $_SERVER ['DOCUMENT_ROOT']."/bitrix/admin");
		//CopyDirFiles($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/install/js", $_SERVER ['DOCUMENT_ROOT']."/bitrix/js");
	}
	
	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/install/admin", $_SERVER ['DOCUMENT_ROOT']."/bitrix/admin");
		//DeleteDirFiles($_SERVER ['DOCUMENT_ROOT'] . "/bitrix/modules/studiointer.easytabs/install/js", $_SERVER ['DOCUMENT_ROOT']."/bitrix/js");
	}
}