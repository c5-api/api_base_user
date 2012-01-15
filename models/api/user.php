<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiUser extends ApiController {
	
	public function listUsers() {
		//@TODO make this use the api
		/*Loader::model('user_list');
		$ul = new UserList();
		$ul->setItemsPerPage(-1);
		return $ul->getPage();*/
		$db = Loader::db();
		$r = $db->Execute('SELECT uID, uName FROM Users');
		$u = array();
		while($row = $r->FetchRow()) {
			$u[$row['uID']] = $row['uName'];
		}
		return $u;
	}
	
	public function info($id) {
		Loader::model('user_info');
		$ui = UserInfo::getByID($id);
		$resp = ApiResponse::getInstance();
		if(!is_object($ui)) {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_INVALID_USER');
			$resp->send();
		} else {
			unset($ui->error);
			unset($ui->uPassword);
			$resp->setData($ui);
			$resp->send();
		}
	}
	
	public function attributes($id) {
		//@TODO possibly overhaul
		Loader::model('user_info');
		$ui = UserInfo::getByID($id);
		$resp = ApiResponse::getInstance();
		if(!is_object($ui)) {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_INVALID_USER');
			$resp->send();
		} else {
			Loader::model('attribute/categories/user');
			$ua = UserAttributeKey::getAttributes($id);
			$resp->setData($ua);
			$resp->send();
		}
	}
	
	public function addUser() {
		Loader::model('user_info');
		$resp = ApiResponse::getInstance();
		if(is_object(UserInfo::getByUserName($_POST['uName'])) || is_object(UserInfo::getByEmail($_POST['uEmail']))) {
			$resp->setError(true);
			$resp->setCode(409);
			$resp->setMessage('ERROR_ALREADY_EXISTS');
			$resp->send();
		}
		$data = array();
		$data['uPassword'] = $_POST['uPassword'];
		$data['uName'] = $_POST['uName'];
		$data['uEmail'] = $_POST['uEmail'];
		
		$ui = UserInfo::add($data);
		if(is_object($ui)) {
			$resp->setData($ui);
			$resp->send();
		} else {
			throw new Exception('ERROR_INTERNAL_ERROR', 500);
		}
	}
}