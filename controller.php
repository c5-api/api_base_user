<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiUserPackage extends Package {

	protected $pkgHandle = 'api_user';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.0';

	public function getPackageName() {
		return t("Api:User");
	}

	public function getPackageDescription() {
		return t("Provides API user management.");
	}

	public function on_start() {
		//Loader::model('user', 'api_user');
	}

	public function install() {
		$installed = Package::getByHandle('api');
		if(!is_object($installed)) {
			throw new Exception(t('Please install the "API" package before installing %s', $this->getPackageName()));
		}
		$api = array();
		$api['pkgHandle'] = $this->pkgHandle;
		$api['route'] = 'users';
		$api['routeName'] = t('List Users');
		$api['class'] = 'user';
		$api['method'] = 'listUsers';
		$api['via'][] = 'get';
		
		$api2 = array();
		$api2['pkgHandle'] = $this->pkgHandle;
		$api2['route'] = 'users/:id';
		$api2['routeName'] = t('User Info By ID');
		$api2['class'] = 'user';
		$api2['method'] = 'info';
		$api2['filters']['id'] = '(\d+)';//:id can only be numerical
		$api2['via'][] = 'get';

		Loader::model('api_register', 'api');
		ApiRegister::add($api);
		ApiRegister::add($api2);

		parent::install(); //install the addon - meh
	}
	
	public function uninstall() {
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}