<?php

	class AuthController extends Controller {

		public static function getCurrentUser() {
			$data = SessionStorage::get('user');
			if($data){
				return $data;
			}
			header("Location: " . Application::$settings['url'] . "/signout");
			exit;
		}

		public static function checkCookie() {
			if (isset($_COOKIE['site_auth_token']) && !empty($_COOKIE['site_auth_token'])) {
				$token = $_COOKIE['site_auth_token'];
				$user = new UserModel();
				$userData = $user->getByAuthToken($token);

				if ($userData) {
					$token = Utils::generateToken();

					$user->save(" ,`authtoken` = '" . $token . "' WHERE `id` = '" . $userData['id'] . "'");

					setcookie('site_auth_token', $token, time() + 60*60*24*14, '/');

					SessionStorage::add('user', $userData);
				}
			}
		}
		
		public static function signIn($request, $vars) {
			$status = "Daxil ol";
			if($request->isPost()){
				$formData = LoginForm::getValues();
				if($formData['success']){
					$login = $formData['data']['login'];
					$password = $formData['data']['password'];
					$userModel = new UserModel();
					if($user = $userModel->getByLoginPassword($login, $password)){
						$token = Utils::generateToken();
						setcookie('site_auth_token', $token, time() + 60*60*24*14);
						$user['type'] = 0;
						$user['operator'] = $user;
						SessionStorage::add('user', $user);
						SessionStorage::add('auth', true);
						SessionStorage::add('permissions', PermissionController::getPermissions(['user_id' => $user['id'], 'operator_id' => $user['id']]));
						SessionStorage::add('subject', (new SubjectModel())->getEmptySubject());
						header("Location: " . Application::$settings['url'] . '/subject');
						return true;
					} else if($operator = $userModel->getOperatorByLoginPassword($login, $password)){
						$token = Utils::generateToken();
						setcookie('site_auth_token', $token, time() + 60*60*24*14);
						$user = $userModel->getOneById($operator['user_id']);
						$user['type'] = 1;
						$user['spc'] = 0;
						$user['operator'] = $operator;
						SessionStorage::add('user', $user);
						SessionStorage::add('auth', true);
						SessionStorage::add('permissions', PermissionController::getPermissions(['user_id' => $user['id'], 'operator_id' => $user['operator']['id']]));
						SessionStorage::add('subject', (new SubjectModel())->getEmptySubject());
						header("Location: " . Application::$settings['url'] . '/subject');
						return true;
					} else {
						self::signOut($request, array(), false);
						$status = '<span style="color: red">Login və ya şifrə səhvdir</span>';
						self::renderTemplate("users" . ds . "signin.tpl", [
							'status' => $status
						]);
					}
				} else {
					self::signOut($request, array(), false);
					$status = "Login və ya şifrə səhvdir";
					self::renderTemplate("users" . ds . "signin.tpl", [
						'status' => $status
					]);
				}
			} else {
				self::renderTemplate("users" . ds . "signin.tpl", [
					'status' => $status
				]);
			}
		}
		
		public static function signOut($request, $vars = Array(), $exit = true) {
			SessionStorage::remove('user');
			SubjectController::setNull();
			$token = isset($_COOKIE["site_auth_token"]) ? $_COOKIE["site_auth_token"] : null;
			setcookie('site_auth_token', $token, time() - 60*60*24*2, '/');
			if($exit){
				self::renderTemplate("users" . ds . "signin.tpl");
			}
		}
	}

?>