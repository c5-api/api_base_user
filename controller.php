<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiBaseUserPackage extends Package {

	protected $pkgHandle = 'api_base_user';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.0';

	public function getPackageName() {
		return t("Api:Base:User");
	}

	public function getPackageDescription() {
		return t("Provides API user management.");
	}

	public function install() {
		$installed = Package::getByHandle('api');
		if(!is_object($installed)) {
			throw new Exception(t('Please install the "API" package before installing %s', $this->getPackageName()));
		}
		
		$this->refreshRoutes();

		parent::install(); //install the addon - meh
	}
	
	public function refreshRoutes() {
		$api = array();
		$api['pkgHandle'] = $this->pkgHandle;
		$api['route'] = 'users';
		$api['routeName'] = t('List Users');
		$api['class'] = 'User';
		$api['method'] = 'listUsers';
		$api['via'][] = 'get';
		
		$api2 = array();
		$api2['pkgHandle'] = $this->pkgHandle;
		$api2['route'] = 'users/:id';
		$api2['routeName'] = t('User Info By ID');
		$api2['class'] = 'User';
		$api2['method'] = 'info';
		$api2['filters']['id'] = '(\d+)';//:id can only be numerical
		$api2['via'][] = 'get';

		$api3 = array();
		$api3['pkgHandle'] = $this->pkgHandle;
		$api3['route'] = 'users/:id/attributes';
		$api3['routeName'] = t('User Attributes By ID');
		$api3['class'] = 'User';
		$api3['method'] = 'attributes';
		$api3['filters']['id'] = '(\d+)';//:id can only be numerical
		$api3['via'][] = 'get';
		
		Loader::model('api_register', 'api');
		ApiRegister::add($api);
		ApiRegister::add($api2);
		ApiRegister::add($api3);
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}