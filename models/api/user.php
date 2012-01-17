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
	
	public function addUser() {
		Loader::model('user_info');
		$resp = ApiResponse::getInstance();
		if(is_object(UserInfo::getByUserName($_POST['uName']))) {
			$resp->setError(true);
			$resp->setCode(409);
			$resp->setMessage('ERROR_ALREADY_EXISTS_NAME');
			$resp->send();
		}
		
		if(is_object(UserInfo::getByEmail($_POST['uEmail']))) {
			$resp->setError(true);
			$resp->setCode(409);
			$resp->setMessage('ERROR_ALREADY_EXISTS_EMAIL');
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

	public function changePassword() {
		Loader::model('user_info');
		$resp = ApiResponse::getInstance();
		$ui = UserInfo::getByUserID($_POST['uID']);
		if(!is_object($ui)) {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_INVALID_USER');
			$resp->send();
		}
		$cvh = Loader::helper('concrete/validation');
		$password = $_POST['password'];
		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			$resp->setError(true);
			$resp->setCode(406);
			$resp->setMessage('ERROR_INVALID_LENGTH');
			$resp->send();
		}		
		if (strlen($password) >= USER_PASSWORD_MINIMUM && !$cvh->password($password)) {
			$resp->setError(true);
			$resp->setCode(406);
			$resp->setMessage('ERROR_INVALID_CHARACTERS');
			$resp->send();
		}
		if(is_object($ui)) {
			$data = array();
			$data['uPasswordConfirm'] = $password;
			$data['uPassword'] = $password;
			return $ui->update($data);
		} else {
			throw new Exception('ERROR_INTERNAL_ERROR', 500);
		}
	}
}