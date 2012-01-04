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
		
		$api3 = array();
		$api3['pkgHandle'] = $this->pkgHandle;
		$api3['route'] = 'config/:key';
		$api3['routeName'] = t('Get or Delete a Site Config Entry');
		$api3['class'] = 'config';
		$api3['method'] = 'getConf';
		$api3['via'][] = 'get';
		$api3['via'][] = 'delete';
		
		$api4 = array();
		$api4['pkgHandle'] = $this->pkgHandle;
		$api4['route'] = 'config/:pkg/:key';
		$api4['routeName'] = t('Get or Delete a Package Config Entry');
		$api4['class'] = 'config';
		$api4['method'] = 'getConf';
		$api4['via'][] = 'get';
		$api3['via'][] = 'delete';
		
		$api5 = array();
		$api5['pkgHandle'] = $this->pkgHandle;
		$api5['route'] = 'config/new';
		$api5['routeName'] = t('Add Config Entry');
		$api5['class'] = 'config';
		$api5['method'] = 'add';
		$api5['via'][] = 'post';

		Loader::model('api_register', 'api');
		ApiRegister::add($api);
		ApiRegister::add($api2);
		ApiRegister::add($api3);
		ApiRegister::add($api4);
		ApiRegister::add($api5);

		parent::install(); //install the addon - meh
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}