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
	
		$baseRoute = 'users';
		
		$api = array();
		$api['pkgHandle'] = $this->pkgHandle;
		$api['route'] = $baseRoute;
		$api['routeName'] = t('List Users');
		$api['class'] = 'User';
		$api['method'] = 'listUsers';
		$api['via'][] = 'get';
		
		$api2 = array();
		$api2['pkgHandle'] = $this->pkgHandle;
		$api2['route'] = $baseRoute.'/:id';
		$api2['routeName'] = t('User Info By ID');
		$api2['class'] = 'User';
		$api2['method'] = 'info';
		$api2['filters']['id'] = '(\d+)';//:id can only be numerical
		$api2['via'][] = 'get';

		$api3 = array();
		$api3['pkgHandle'] = $this->pkgHandle;
		$api3['route'] = $baseRoute.'/add';
		$api3['routeName'] = t('Add User');
		$api3['class'] = 'User';
		$api3['method'] = 'addUser';
		$api3['via'][] = 'post';
		
		$api4 = array();
		$api4['pkgHandle'] = $this->pkgHandle;
		$api4['route'] = $baseRoute.'/change_password';
		$api4['routeName'] = t('Change Password');
		$api4['class'] = 'User';
		$api4['method'] = 'changePassword';
		$api4['via'][] = 'post';

		$attr1 = array();
		$attr1['pkgHandle'] = $this->pkgHandle;
		$attr1['route'] = $baseRoute.'/-/attributes';
		$attr1['routeName'] = t('User Attributes List');
		$attr1['class'] = 'UserAttributes';
		$attr1['method'] = 'attributes';
		$attr1['via'][] = 'get';
		
		$attr2 = array();
		$attr2['pkgHandle'] = $this->pkgHandle;
		$attr2['route'] = $baseRoute.'/-/attributes/info/:id';
		$attr2['routeName'] = t('User Attributes List');
		$attr2['class'] = 'UserAttributes';
		$attr2['method'] = 'attributesInfo';
		$attr2['filters']['id'] = '(\d+)';//:id can only be numerical
		$attr2['via'][] = 'get';

// 		$api4 = array();
// 		$api4['pkgHandle'] = $this->pkgHandle;
// 		$api4['route'] = $baseRoute.'/:id/attributes/-/update';
// 		$api4['routeName'] = t('Update User Attributes By ID');
// 		$api4['class'] = 'User';
// 		$api4['method'] = 'attributesUpdate';
// 		$api4['filters']['id'] = '(\d+)';//:id can only be numerical
// 		$api4['via'][] = 'get';
//		$api4['via'][] = 'post';
		
		Loader::model('api_register', 'api');
		ApiRegister::add($api);
		ApiRegister::add($api2);
		ApiRegister::add($api3);
		ApiRegister::add($api4);
		
		ApiRegister::add($attr1);
		ApiRegister::add($attr2);
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}