<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiUserAttributes extends ApiController {
	
	public function attributes() {
		//@TODO possibly overhaul
		$resp = ApiResponse::getInstance();
		Loader::model('attribute/categories/user');
		$ua = UserAttributeKey::getList();
		$uaa = array();
		foreach($ua as $attr) {
			$uaa[$attr->akID] = $attr->akHandle;
		}
		$resp->setData($uaa);
		$resp->send();
	}
	
	public function attributesInfo($id) {
		//@TODO possibly overhaul
		Loader::model('attribute/categories/user');
		$ak = UserAttributeKey::getByID($id);
		$resp = ApiResponse::getInstance();
		if(!is_object($ak) || $ak->error) {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_INVALID_ATTRIBUTE');
			$resp->send();
		} else {
			unset($ak->error);
			$resp->setData($ak);
			$resp->send();
		}
	}
}