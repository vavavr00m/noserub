<?php
/* SVN FILE: $Id: auth.php 8004 2009-01-16 20:15:21Z gwoo $ */

/**
 * Authentication component
 *
 * Manages user logins and permissions.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller.components
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision$
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::import(array('Router', 'Security'));

/**
 * Authentication control component class
 *
 * Binds access control with user authentication and session management.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller.components
 */
class AuthComponent extends Object {
/**
 * Maintains current user login state.
 *
 * @var boolean
 * @access private
 */
	var $_loggedIn = false;
/**
 * Other components utilized by AuthComponent
 *
 * @var array
 * @access public
 */
	var $components = array('Session', 'RequestHandler');
/**
 * A reference to the object used for authentication
 *
 * @var object
 * @access public
 */
	var $authenticate = null;
/**
 * The name of the component to use for Authorization or set this to
 * 'controller' will validate against Controller::isAuthorized()
 * 'actions' will validate Controller::action against an AclComponent::check()
 * 'crud' will validate mapActions against an AclComponent::check()
 * array('model'=> 'name'); will validate mapActions against model $name::isAuthorized(user, controller, mapAction)
 * 'object' will validate Controller::action against object::isAuthorized(user, controller, action)
 *
 * @var mixed
 * @access public
 */
	var $authorize = false;
/**
 * The name of an optional view element to render when an Ajax request is made
 * with an invalid or expired session
 *
 * @var string
 * @access public
 */
	var $ajaxLogin = null;
/**
 * The name of the model that represents users which will be authenticated.  Defaults to 'User'.
 *
 * @var string
 * @access public
 */
	var $userModel = 'User';
/**
 * Additional query conditions to use when looking up and authenticating users,
 * i.e. array('User.is_active' => 1).
 *
 * @var array
 * @access public
 */
	var $userScope = array();
/**
 * Allows you to specify non-default login name and password fields used in
 * $userModel, i.e. array('username' => 'login_name', 'password' => 'passwd').
 *
 * @var array
 * @access public
 */
	var $fields = array('username' => 'username', 'password' => 'password');
/**
 * The session key name where the record of the current user is stored.  If
 * unspecified, it will be "Auth.{$userModel name}".
 *
 * @var string
 * @access public
 */
	var $sessionKey = null;
/**
 * If using action-based access control, this defines how the paths to action
 * ACO nodes is computed.  If, for example, all controller nodes are nested
 * under an ACO node named 'Controllers', $actionPath should be set to
 * "Controllers/".
 *
 * @var string
 * @access public
 */
	var $actionPath = null;
/**
 * A URL (defined as a string or array) to the controller action that handles
 * logins.
 *
 * @var mixed
 * @access public
 */
	var $loginAction = null;
/**
 * Normally, if a user is redirected to the $loginAction page, the location they
 * were redirected from will be stored in the session so that they can be
 * redirected back after a successful login.  If this session value is not
 * set, the user will be redirected to the page specified in $loginRedirect.
 *
 * @var mixed
 * @access public
 */
	var $loginRedirect = null;
/**
 * The the default action to redirect to after the user is logged out.  While AuthComponent does
 * not handle post-logout redirection, a redirect URL will be returned from AuthComponent::logout().
 * Defaults to AuthComponent::$loginAction.
 *
 * @var mixed
 * @access public
 * @see AuthComponent::$loginAction
 * @see AuthComponent::logout()
 */
	var $logoutRedirect = null;
/**
 * The name of model or model object, or any other object has an isAuthorized method.
 *
 * @var string
 * @access public
 */
	var $object = null;
/**
 * Error to display when user login fails.  For security purposes, only one error is used for all
 * login failures, so as not to expose information on why the login failed.
 *
 * @var string
 * @access public
 */
	var $loginError = null;
/**
 * Error to display when user attempts to access an object or action to which they do not have
 * acccess.
 *
 * @var string
 * @access public
 */
	var $authError = null;
/**
 * Determines whether AuthComponent will automatically redirect and exit if login is successful.
 *
 * @var boolean
 * @access public
 */
	var $autoRedirect = true;
/**
 * Controller actions for which user validation is not required.
 *
 * @var array
 * @access public
 * @see AuthComponent::allow()
 */
	var $allowedActions = array();
/**
 * Maps actions to CRUD operations.  Used for controller-based validation ($validate = 'controller').
 *
 * @var array
 * @access public
 * @see AuthComponent::mapActions()
 */
	var $actionMap = array(
		'index'		=> 'read',
		'add'		=> 'create',
		'edit'		=> 'update',
		'view'		=> 'read',
		'remove'	=> 'delete'
	);
/**
 * Form data from Controller::$data
 *
 * @var array
 * @access public
 */
	var $data = array();
/**
 * Parameter data from Controller::$params
 *
 * @var array
 * @access public
 */
	var $params = array();
/**
 * Method list for bound controller
 *
 * @var array
 * @access protected
 */
	var $_methods = array();
/**
 * Initializes AuthComponent for use in the controller
 *
 * @param object $controller A reference to the instantiating controller object
 * @return void
 * @access public
 */
	function initialize(&$controller) {
		$this->params = $controller->params;
		$crud = array('create', 'read', 'update', 'delete');
		$this->actionMap = array_merge($this->actionMap, array_combine($crud, $crud));
		$this->_methods = $controller->methods;

		$admin = Configure::read('Routing.admin');
		if (!empty($admin)) {
			$this->actionMap = array_merge($this->actionMap, array(
				$admin . '_index'	=> 'read',
				$admin . '_add'		=> 'create',
				$admin . '_edit'	=> 'update',
				$admin . '_view'	=> 'read',
				$admin . '_remove'	=> 'delete',
				$admin . '_create'	=> 'create',
				$admin . '_read'	=> 'read',
				$admin . '_update'	=> 'update',
				$admin . '_delete'	=> 'delete'
			));
		}
		if (Configure::read() > 0) {
			App::import('Debugger');
			Debugger::checkSessionKey();
		}
	}
/**
 * Main execution method.  Handles redirecting of invalid users, and processing
 * of login form data.
 *
 * @param object $controller A reference to the instantiating controller object
 * @return boolean
 * @access public
 */
	function startup(&$controller) {
		$isErrorOrTests = (
			strtolower($controller->name) == 'cakeerror' ||
			(strtolower($controller->name) == 'tests' && Configure::read() > 0) ||
			!in_array($controller->params['action'], $controller->methods)
		);
		if ($isErrorOrTests) {
			return true;
		}
		if (!$this->__setDefaults()) {
			return false;
		}

		$this->data = $controller->data = $this->hashPasswords($controller->data);
		$url = '';

		if (isset($controller->params['url']['url'])) {
			$url = $controller->params['url']['url'];
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);
		$isAllowed = (
			$this->allowedActions == array('*') ||
			in_array($controller->params['action'], $this->allowedActions)
		);

		if ($loginAction != $url && $isAllowed) {
			return true;
		}

		if ($loginAction == $url) {
			if (empty($controller->data) || !isset($controller->data[$this->userModel])) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
				return false;
			}

			$isValid = !empty($controller->data[$this->userModel][$this->fields['username']]) &&
				!empty($controller->data[$this->userModel][$this->fields['password']]);

			if ($isValid) {
				$username = $controller->data[$this->userModel][$this->fields['username']];
				$password = $controller->data[$this->userModel][$this->fields['password']];

				$data = array(
					$this->userModel . '.' . $this->fields['username'] => $username,
					$this->userModel . '.' . $this->fields['password'] => $password
				);

				if ($this->login($data)) {
					if ($this->autoRedirect) {
						$controller->redirect($this->redirect(), null, true);
					}
					return true;
				}
			}

			$this->Session->setFlash($this->loginError, 'default', array(), 'auth');
			$controller->data[$this->userModel][$this->fields['password']] = null;
			return false;
		} else {
			if (!$this->user()) {
				if (!$this->RequestHandler->isAjax()) {
					$this->Session->setFlash($this->authError, 'default', array(), 'auth');
					$this->Session->write('Auth.redirect', $url);
					$controller->redirect($loginAction);
					return false;
				} elseif (!empty($this->ajaxLogin)) {
					$controller->viewPath = 'elements';
					echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
					$this->_stop();
					return false;
				} else {
					$controller->redirect(null, 403);
				}
			}
		}

		if (!$this->authorize) {
			return true;
		}

		extract($this->__authType());
		switch ($type) {
			case 'controller':
				$this->object =& $controller;
			break;
			case 'crud':
			case 'actions':
				if (isset($controller->Acl)) {
					$this->Acl =& $controller->Acl;
				} else {
					$err = 'Could not find AclComponent. Please include Acl in ';
					$err .= 'Controller::$components.';
					trigger_error(__($err, true), E_USER_WARNING);
				}
			break;
			case 'model':
				if (!isset($object)) {
					$hasModel = (
						isset($controller->{$controller->modelClass}) &&
						is_object($controller->{$controller->modelClass})
					);
					$isUses = (
						!empty($controller->uses) && isset($controller->{$controller->uses[0]}) &&
						is_object($controller->{$controller->uses[0]})
					);

					if ($hasModel) {
						$object = $controller->modelClass;
					} elseif ($isUses) {
						$object = $controller->uses[0];
					}
				}
				$type = array('model' => $object);
			break;
		}

		if ($this->isAuthorized($type)) {
			return true;
		}

		$this->Session->setFlash($this->authError, 'default', array(), 'auth');
		$controller->redirect($controller->referer(), null, true);
		return false;
	}
/**
 * Attempts to introspect the correct values for object properties including
 * $userModel and $sessionKey.
 *
 * @param object $controller A reference to the instantiating controller object
 * @return boolean
 * @access private
 */
	function __setDefaults() {
		if (empty($this->userModel)) {
			trigger_error(__("Could not find \$userModel. Please set AuthComponent::\$userModel in beforeFilter().", true), E_USER_WARNING);
			return false;
		}
		$defaults = array(
			'loginAction' => Router::normalize(array(
				'controller'=> Inflector::underscore(Inflector::pluralize($this->userModel)),
				'action' => 'login'
			)),
			'sessionKey' => 'Auth.' . $this->userModel,
			'logoutRedirect' => $this->loginAction,
			'loginError' => __('Login failed. Invalid username or password.', true),
			'authError' => __('You are not authorized to access that location.', true)
		);
		foreach ($defaults as $key => $value) {
			if (empty($this->{$key})) {
				$this->{$key} = $value;
			}
		}
		return true;
	}
/**
 * Determines whether the given user is authorized to perform an action.  The type of
 * authorization used is based on the value of AuthComponent::$authorize or the
 * passed $type param.
 *
 * Types:
 * 'controller' will validate against Controller::isAuthorized() if controller instance is
 * 				passed in $object
 * 'actions' will validate Controller::action against an AclComponent::check()
 * 'crud' will validate mapActions against an AclComponent::check()
 * 		array('model'=> 'name'); will validate mapActions against model
 * 		$name::isAuthorized(user, controller, mapAction)
 * 'object' will validate Controller::action against
 * 		object::isAuthorized(user, controller, action)
 *
 * @param string $type Type of authorization
 * @param mixed $object object, model object, or model name
 * @param mixed $user The user to check the authorization of
 * @return boolean True if $user is authorized, otherwise false
 * @access public
 */
	function isAuthorized($type = null, $object = null, $user = null) {
		if (empty($user) && !$this->user()) {
			return false;
		} elseif (empty($user)) {
			$user = $this->user();
		}

		extract($this->__authType($type));

		if (!$object) {
			$object = $this->object;
		}

		$valid = false;
		switch ($type) {
			case 'controller':
				$valid = $object->isAuthorized();
			break;
			case 'actions':
				$valid = $this->Acl->check($user, $this->action());
			break;
			case 'crud':
				$this->mapActions();
				if (!isset($this->actionMap[$this->params['action']])) {
					$err = 'Auth::startup() - Attempted access of un-mapped action "%1$s" in';
					$err .= ' controller "%2$s"';
					trigger_error(
						sprintf(__($err, true), $this->params['action'], $this->params['controller']),
						E_USER_WARNING
					);
				} else {
					$valid = $this->Acl->check(
						$user,
						$this->action(':controller'),
						$this->actionMap[$this->params['action']]
					);
				}
			break;
			case 'model':
				$this->mapActions();
				$action = $this->params['action'];
				if (isset($this->actionMap[$action])) {
					$action = $this->actionMap[$action];
				}
				if (is_string($object)) {
					$object = $this->getModel($object);
				}
			case 'object':
				if (!isset($action)) {
					$action = $this->action(':action');
				}
				if (empty($object)) {
					trigger_error(sprintf(__('Could not find %s. Set AuthComponent::$object in beforeFilter() or pass a valid object', true), get_class($object)), E_USER_WARNING);
					return;
				}
				if (method_exists($object, 'isAuthorized')) {
					$valid = $object->isAuthorized($user, $this->action(':controller'), $action);
				} elseif ($object) {
					trigger_error(sprintf(__('%s::isAuthorized() is not defined.', true), get_class($object)), E_USER_WARNING);
				}
			break;
			case null:
			case false:
				return true;
			break;
			default:
				trigger_error(__('Auth::isAuthorized() - $authorize is set to an incorrect value.  Allowed settings are: "actions", "crud", "model" or null.', true), E_USER_WARNING);
			break;
		}
		return $valid;
	}
/**
 * Get authorization type
 *
 * @param string $auth Type of authorization
 * @return array Associative array with: type, object
 * @access private
 */
	function __authType($auth = null) {
		if ($auth == null) {
			$auth = $this->authorize;
		}
		$object = null;
		if (is_array($auth)) {
			$type = key($auth);
			$object = $auth[$type];
		} else {
			$type = $auth;
			return compact('type');
		}
		return compact('type', 'object');
	}
/**
 * Takes a list of actions in the current controller for which authentication is not required, or
 * no parameters to allow all actions.
 *
 * @param string $action Controller action name
 * @param string $action Controller action name
 * @param string ... etc.
 * @return void
 * @access public
 */
	function allow() {
		$args = func_get_args();
		if (empty($args) || $args == array('*')) {
			$this->allowedActions = $this->_methods;
		} else {
			if (isset($args[0]) && is_array($args[0])) {
				$args = $args[0];
			}
			$this->allowedActions = array_merge($this->allowedActions, $args);
		}
	}
/**
 * Removes items from the list of allowed actions.
 *
 * @param string $action Controller action name
 * @param string $action Controller action name
 * @param string ... etc.
 * @return void
 * @see AuthComponent::allow()
 * @access public
 */
	function deny() {
		$args = func_get_args();
		foreach ($args as $arg) {
			$i = array_search($arg, $this->allowedActions);
			if (is_int($i)) {
				unset($this->allowedActions[$i]);
			}
		}
		$this->allowedActions = array_values($this->allowedActions);
	}
/**
 * Maps action names to CRUD operations. Used for controller-based authentication.
 *
 * @param array $map Actions to map
 * @return void
 * @access public
 */
	function mapActions($map = array()) {
		$crud = array('create', 'read', 'update', 'delete');
		foreach ($map as $action => $type) {
			if (in_array($action, $crud) && is_array($type)) {
				foreach ($type as $typedAction) {
					$this->actionMap[$typedAction] = $action;
				}
			} else {
				$this->actionMap[$action] = $type;
			}
		}
	}
/**
 * Manually log-in a user with the given parameter data.  The $data provided can be any data
 * structure used to identify a user in AuthComponent::identify().  If $data is empty or not
 * specified, POST data from Controller::$data will be used automatically.
 *
 * After (if) login is successful, the user record is written to the session key specified in
 * AuthComponent::$sessionKey.
 *
 * @param mixed $data User object
 * @return boolean True on login success, false on failure
 * @access public
 */
	function login($data = null) {
		$this->__setDefaults();
		$this->_loggedIn = false;

		if (empty($data)) {
			$data = $this->data;
		}

		if ($user = $this->identify($data)) {
			$this->Session->write($this->sessionKey, $user);
			$this->_loggedIn = true;
		}
		return $this->_loggedIn;
	}
/**
 * Logs a user out, and returns the login action to redirect to.
 *
 * @param mixed $url Optional URL to redirect the user to after logout
 * @return string AuthComponent::$loginAction
 * @see AuthComponent::$loginAction
 * @access public
 */
	function logout() {
		$this->__setDefaults();
		$this->Session->del($this->sessionKey);
		$this->Session->del('Auth.redirect');
		$this->_loggedIn = false;
		return Router::normalize($this->logoutRedirect);
	}
/**
 * Get the current user from the session.
 *
 * @param string $key field to retrive.  Leave null to get entire User record
 * @return mixed User record. or null if no user is logged in.
 * @access public
 */
	function user($key = null) {
		$this->__setDefaults();
		if (!$this->Session->check($this->sessionKey)) {
			return null;
		}

		if ($key == null) {
			return array($this->userModel => $this->Session->read($this->sessionKey));
		} else {
			$user = $this->Session->read($this->sessionKey);
			if (isset($user[$key])) {
				return $user[$key];
			}
			return null;
		}
	}
/**
 * If no parameter is passed, gets the authentication redirect URL.
 *
 * @param mixed $url Optional URL to write as the login redirect URL.
 * @return string Redirect URL
 * @access public
 */
	function redirect($url = null) {
		if (!is_null($url)) {
			$redir = $url;
			$this->Session->write('Auth.redirect', $redir);
		} elseif ($this->Session->check('Auth.redirect')) {
			$redir = $this->Session->read('Auth.redirect');
			$this->Session->delete('Auth.redirect');

			if (Router::normalize($redir) == Router::normalize($this->loginAction)) {
				$redir = $this->loginRedirect;
			}
		} else {
			$redir = $this->loginRedirect;
		}
		return Router::normalize($redir);
	}
/**
 * Validates a user against an abstract object.
 *
 * @param mixed $object  The object to validate the user against.
 * @param mixed $user    Optional.  The identity of the user to be validated.
 *                       Uses the current user session if none specified.  For
 *                       valid forms of identifying users, see
 *                       AuthComponent::identify().
 * @param string $action Optional. The action to validate against.
 * @see AuthComponent::identify()
 * @return boolean True if the user validates, false otherwise.
 * @access public
 */
	function validate($object, $user = null, $action = null) {
		if (empty($user)) {
			$user = $this->user();
		}
		if (empty($user)) {
			return false;
		}
		return $this->Acl->check($user, $object, $action);
	}
/**
 * Returns the path to the ACO node bound to a controller/action.
 *
 * @param string $action  Optional.  The controller/action path to validate the
 *                        user against.  The current request action is used if
 *                        none is specified.
 * @return boolean ACO node path
 * @access public
 */
	function action($action = ':controller/:action') {
		return str_replace(
			array(':controller', ':action'),
			array(Inflector::camelize($this->params['controller']), $this->params['action']),
			$this->actionPath . $action
		);
	}
/**
 * Returns a reference to the model object specified, and attempts
 * to load it if it is not found.
 *
 * @param string $name Model name (defaults to AuthComponent::$userModel)
 * @return object A reference to a model object
 * @access public
 */
	function &getModel($name = null) {
		$model = null;
		if (!$name) {
			$name = $this->userModel;
		}

		if (PHP5) {
			$model = ClassRegistry::init($name);
		} else {
			$model =& ClassRegistry::init($name);
		}

		if (empty($model)) {
			trigger_error(__('Auth::getModel() - Model is not set or could not be found', true), E_USER_WARNING);
			return null;
		}

		return $model;
	}
/**
 * Identifies a user based on specific criteria.
 *
 * @param mixed $user Optional. The identity of the user to be validated.
 *              Uses the current user session if none specified.
 * @param array $conditions Optional. Additional conditions to a find.
 * @return array User record data, or null, if the user could not be identified.
 * @access public
 */
	function identify($user = null, $conditions = null) {
		if ($conditions === false) {
			$conditions = null;
		} elseif (is_array($conditions)) {
			$conditions = array_merge((array)$this->userScope, $conditions);
		} else {
			$conditions = $this->userScope;
		}
		if (empty($user)) {
			$user = $this->user();
			if (empty($user)) {
				return null;
			}
		} elseif (is_object($user) && is_a($user, 'Model')) {
			if (!$user->exists()) {
				return null;
			}
			$user = $user->read();
			$user = $user[$this->userModel];
		} elseif (is_array($user) && isset($user[$this->userModel])) {
			$user = $user[$this->userModel];
		}

		if (is_array($user) && (isset($user[$this->fields['username']]) || isset($user[$this->userModel . '.' . $this->fields['username']]))) {

			if (isset($user[$this->fields['username']]) && !empty($user[$this->fields['username']])  && !empty($user[$this->fields['password']])) {
				if (trim($user[$this->fields['username']]) == '=' || trim($user[$this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$this->userModel.'.'.$this->fields['username'] => $user[$this->fields['username']],
					$this->userModel.'.'.$this->fields['password'] => $user[$this->fields['password']]
				);
			} elseif (isset($user[$this->userModel . '.' . $this->fields['username']]) && !empty($user[$this->userModel . '.' . $this->fields['username']])) {
				if (trim($user[$this->userModel . '.' . $this->fields['username']]) == '=' || trim($user[$this->userModel . '.' . $this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$this->userModel.'.'.$this->fields['username'] => $user[$this->userModel . '.' . $this->fields['username']],
					$this->userModel.'.'.$this->fields['password'] => $user[$this->userModel . '.' . $this->fields['password']]
				);
			} else {
				return false;
			}
			$model =& $this->getModel();
			$data = $model->find(array_merge($find, $conditions), null, null, 0);
			if (empty($data) || empty($data[$this->userModel])) {
				return null;
			}
		} elseif (!empty($user) && is_string($user)) {
			$model =& $this->getModel();
			$data = $model->find(array_merge(array($model->escapeField() => $user), $conditions));

			if (empty($data) || empty($data[$this->userModel])) {
				return null;
			}
		}

		if (!empty($data)) {
			if (!empty($data[$this->userModel][$this->fields['password']])) {
				unset($data[$this->userModel][$this->fields['password']]);
			}
			return $data[$this->userModel];
		}
		return null;
	}
/**
 * Hash any passwords found in $data using $userModel and $fields['password']
 *
 * @param array $data Set of data to look for passwords
 * @return array Data with passwords hashed
 * @access public
 */
	function hashPasswords($data) {
		if (is_object($this->authenticate) && method_exists($this->authenticate, 'hashPasswords')) {
			return $this->authenticate->hashPasswords($data);
		}

		if (is_array($data) && isset($data[$this->userModel])) {
			if (isset($data[$this->userModel][$this->fields['username']]) && isset($data[$this->userModel][$this->fields['password']])) {
				$data[$this->userModel][$this->fields['password']] = $this->password($data[$this->userModel][$this->fields['password']]);
			}
		}
		return $data;
	}
/**
 * Hash a password with the application's salt value (as defined with Configure::write('Security.salt');
 *
 * @param string $password Password to hash
 * @return string Hashed password
 * @access public
 */
	function password($password) {
		return Security::hash($password, null, true);
	}
/**
 * Component shutdown.  If user is logged in, wipe out redirect.
 *
 * @param object $controller Instantiating controller
 * @access public
 */
	function shutdown(&$controller) {
		if ($this->_loggedIn) {
			$this->Session->del('Auth.redirect');
		}
	}
}
?>