<?php
/**
 * AppController - application wide controller
 *
 * Base class for all controllers.
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package    profiles
 * @subpackage controllers
 */
class AppController extends Controller {
    public $helpers = array('noserub', 'javascript', 'html', 'form', 'vcf');
    public $components = array('Cookie', 'RequestHandler', 'ContentNegotiation');
    public $view = 'Theme';
    public $theme = 'default';
    
    /**
     * Never ever "use" something here, or the migrations will fail.
     * See Issue #127
     * http://code.google.com/p/noserub/issues/detail?id=127
     *
     * If you need a specific model in AppController, use "App::import('Model', 'ModelName');"
     *
     * (Basically no models that make use of a database table may be 'used' here.
     *  Because if you do so, Cake will complain and you have no chance of doing
     *  the migrations in /system/update.)
     */
    public $uses = array(); 
    
    public function flashMessage($type, $message) {
        $flash_messages = $this->Session->read('FlashMessages');
        $flash_messages[$type][] = $message;
        $this->Session->write('FlashMessages', $flash_messages);
    }
    
    /**
     * Makes sure we redirect to the https url,
     * when context.network.use_ssl is used and we're not
     * on a secure page
     */
    public function checkSecure() {
        if(defined('SHELL_DISPATCHER')) {
            return;
        }
        
        if(Context::read('network.use_ssl')) {
            $server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 0;
            if($server_port != 443) {
                $this->redirect(str_replace('http://', 'https://', FULL_BASE_URL) . $this->here);
            }
        }
    }
    
    /**
     * Makes sure we redirect to the http url,
     * when we're not on a secure page
     */
    public function checkUnsecure() {
        if(defined('SHELL_DISPATCHER')) {
            return;
        }
        
        $server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 0;
        if($server_port != 80) {
            $this->redirect(str_replace('https://', 'http://', FULL_BASE_URL) . $this->here);
        }
    }

    public function beforeFilter() {
        if(!$this->isSystemUpdatePage()) {
            # check for auto-login
            if(!$this->Session->check('Identity.id')) {
                $this->autoLogin();
            }
        }
        
        # Localization
        App::import('Core', 'l10n');
        $this->L10n = new L10n();
        # if language is already set in session, get that
        $language = $this->Session->read('Config.language');
        if(!$language) {
            # if not, get NoseRub default language and save it
            # in the session
            $language = Context::read('network.default_language');
            $this->Session->write('Config.language', $language);
        }
        # now set the language
        $this->L10n->get($language);

        setlocale(LC_ALL, 
            substr($this->L10n->locale, 0, 3) .
            strtoupper(substr($this->L10n->locale, 3, 2)) . 
            '.' . $this->L10n->charset
        );
                
        /**
         * 	Don't you EVER remove this line else you will make the whole 
         * 	application a swiss cheese for XSS!
         *  We often call echo $this->here in our form actions and this would
         *  be exactly where the injection would take place.
         */
        $this->here = preg_replace('/("|\')/', '”', addslashes(strip_tags($this->here)));
        
        header('X-FRAME-OPTIONS: SAMEORIGIN');
        header('X-XSS-Protection: 0');
        
        if(defined('SHELL_DISPATCHER')) {
            $this->layout = 'shell';
            if($this->action != '' &&
               ($this->name . '.' . $this->action) != 'Mails.send' &&
               strpos($this->action, 'shell_') !== 0) {
                echo __('You may not call this route from the shell!', true) . "\n";
                echo $this->name . '.' . $this->action . "\n\n";
                exit;
            }
        } else {
            if(strpos($this->action, 'shell_') === 0) {
                echo '<h1>' . __('Error', true) . '</h1><p>' . __('This route is only accessible from the shell!', true) . '</p>';
                exit;
            }
        }
        
        if(!$this->isSystemUpdatePage()) {
            $this->updateContext();
            
            // make sure the cache is loaded
            $this->loadModel('Service');
            $this->Service->getAllServices();
            
            // load this model always, as we cannot do chaining any more...
            $this->loadModel('ServiceType');
        }
    }
    
    public function ensureSecurityToken() {
        if(!isset($this->Identity)) {
            $this->loadModel('Identity');
        }
        if(isset($this->params['form']['security_token'])) {
            # POST
            $security_token = $this->params['form']['security_token'];
        } else {
            # GET
            $security_token = isset($this->params['security_token']) ? $this->params['security_token'] : '';
            if(!$security_token) {
                $security_token = isset($this->params['named']['_t']) ? $this->params['named']['_t'] : '';
            }
        }
        
        if(!$this->Identity->isCorrectSecurityToken($security_token)) {
            $this->redirect('/pages/security_check/');
        }
    }
    
    public function beforeRender() {
        if($this->name != 'Widgets') {
            # only update it once for a page rendering
            if($this->viewPath != 'errors' && !$this->isSystemUpdatePage()) {
    	        if(!isset($this->Identity)) {
    	            $this->loadModel('Identity');
    	        }
    	        # update security_token
    	        $this->Identity->updateSecurityToken();
    	        
    	        if(Context::isPage('profile.home') && $this->ContentNegotiation->isApplicationRdfXml()) {
    	            $this->layout = 'rdf_xml';
                }
            }
        }
    }
    
    public function afterFilter() {
        $this->Session->write('FlashMessages', array());
    }
    
    protected function updateContext() {
        if($this->name == 'Widgets') {
            # don't update the context for the requestActions
            # of the widgets
            return;
        }
        
        if($this->isSystemUpdatePage()) {
            return;
        }
        
        if(!isset($this->Network)) {
            $this->loadModel('Network');
        }
        
        # get the network data. right now, always
        # for network_id 1
        $data = $this->Network->find('first', array(
            'contain' => false,
            'conditions' => array('id' => 1)
        ));
        
        if(!$data['Network']['url']) {
            # when no URL is found, we try to guess it
            $http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $data['Network']['url'] = 'http://' . $http_host . $this->webroot;
        }
        
        Context::write('network', $data['Network']);

        # set the user agent here, as we didn't know about the network.url before
        Configure::write('noserub.user_agent', 'NoseRub bot from ' . Context::read('network.url') . ' (http://noserub.com/)');
        ini_set('user_agent', Configure::read('noserub.user_agent'));

        $logged_in_identity = $this->Session->read('Identity');
        Context::write('logged_in_identity', $logged_in_identity);
        if($logged_in_identity) {
            if(!isset($this->Identity)) {
                $this->loadModel('Identity');
            }
            $this->Identity->id = $logged_in_identity['id'];
            Context::write('logged_in_identity.message_count', $this->Identity->field('message_count'));
        }
        
        if($this->Session->read('Admin.id')) {
            Context::write('admin_id', $this->Session->read('Admin.id'));
        } else {
            Context::write('admin_id', 0);
        }
        
        // if we're looking on the page of someone else, get the identity
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        if($username) {
            if(!isset($this->Identity)) {
                $this->loadModel('Identity');
            }
            
            $splitted = $this->Identity->splitUsername($username);
            $username = $splitted['username'];
            
            $this->Identity->contain();
            $data = $this->Identity->findByUsername($username);
            Context::write('identity', $data['Identity']);
            
            // check, if logged_in_identity is contact
            Context::write('is_contact', false);
            if(Context::isLoggedInIdentity()) {
                if($this->Identity->Contact->find('first', array(
                    'contain' => false,
                    'conditions' => array(
                        'identity_id' => Context::loggedInIdentityId(),
                        'with_identity_id' => $data['Identity']['id']
                    )
                   ))) {
                    Context::write('is_contact', true);
                };
            }
            
            // check, if the other one has me as contact
            Context::write('allowed_sending', false);
            if(Context::isLoggedInIdentity()) {
                if($this->Identity->Contact->find('first', array(
                    'contain' => false,
                    'conditions' => array(
                        'with_identity_id' => Context::loggedInIdentityId(),
                        'identity_id' => $data['Identity']['id']
                    )
                   ))) {
                    Context::write('allowed_sending', true);
                };
            }
        } else {
            Context::write('identity', false);
        }

        if(Context::read('identity') && Context::isLoggedInIdentity()) {
            # logged in user is the same as the user that is currently displayed
            Context::write('is_self', Context::loggedInIdentityId() == Context::read('identity.id'));
        } else if(Context::read('identity') === false && Context::isLoggedInIdentity()) {
            # if no username is given, the logged in user is always "is_self"
            Context::write('is_self', true);
        }
        
        if(Context::isLoggedInIdentity()) {
            $logged_in_identity = Context::isLoggedInIdentity();
            if(!isset($logged_in_identity['network_id'])) {
                # this can only happen when people are still
                # logged in after a /system/update
                $logged_in_identity['network_id'] = 1;
            }
            Context::write('is_guest', $logged_in_identity['network_id'] == 0 ? true : false);
        }
        
    	if(Configure::read('NoseRub.use_cdn')) {
            $avatar_base_url = 'http://s3.amazonaws.com/' . Configure::read('NoseRub.cdn_s3_bucket') . '/avatars/';
        } else {
            $avatar_base_url = Router::url('/static/avatars/', true);
        }
        Context::write('avatar_base_url', $avatar_base_url);
        
        Context::write('language', $this->Session->read('Config.language'));
        
        Context::write('params', $this->params);
        
        Context::entryAddModus($this->Session->read('entry_add_modus'));
        Context::entryGroupAddModus($this->Session->read('entry_group_add_modus'));
        
        Context::contactFilter($this->Session->read('contact_filter'));
        Context::entryFilter($this->Session->read('entry_filter'));
    }
    
	private function autoLogin() {
        $login_id = $this->Cookie->read('li'); # login id
            
        if($login_id) {
            if(!isset($this->Identity)) {
                $this->loadModel('Identity');
            }
            
            $this->Identity->contain();
            $identity = $this->Identity->findById($login_id);

            if(!$identity) {
                # not found. delete the cookie.
                $this->Cookie->del('li');
            } else {
                $this->Identity->id = $identity['Identity']['id'];
                $this->Identity->saveField('last_login', date('Y-m-d H:i:s'));
                
                $this->Session->write('Identity', $identity['Identity']);
                # refresh auto login cookie
                $this->Cookie->write('li', $login_id, true, '4 weeks');
            }
        }
    }
    
    /**
     * stores the validation errors and the actual data that was
     * submitted to the session. this is needed, because of our
     * widget system and doing redirects in it.
     *
     * you can see how it is used in AdminsController::settings()
     *
     * also see AppController::retrieveFormErrors()
     */
    public function storeFormErrors($modelName, $data, $validationErrors) {
        $formErrors = $this->Session->read('FormErrors');
        if(!$formErrors) {
            $formErrors = array();
        }
        $formErrors[$modelName] = array(
            'data' => $data,
            'validationErrors' => $validationErrors
        );
        $this->Session->write('FormErrors', $formErrors);
    }
    
    /**
     * retrieving form errors for a given model from the session.
     * $this->data is set to the previous data, so we can show
     * the user what was wrong and also retsore the validationErrors,
     * so that the form helpers can display them.
     *
     * you can see how it is used in WidgetsController::form_admin_settings()
     *
     * also see AppController::storeFormErrors()
     */
    public function retrieveFormErrors($modelName) {
        $formErrors = $this->Session->read('FormErrors');
        if(is_array($formErrors)) {
            if(isset($formErrors[$modelName])) {
                $this->data = $formErrors[$modelName]['data'];
                $this->{$modelName}->validationErrors = $formErrors[$modelName]['validationErrors'];
                unset($formErrors[$modelName]);
                $this->Session->write('FormErrors', $formErrors);
            }
        }
    }
    
    /**
     * who may access this controller method. each role
     * inherits from the other.
     *
     * roles: visitor -> guest -> user -> admin
     *
     * will be extended later with pseudo roles
     * like 'group admin', etc.. They then will
     * have params, eg:
     *   $this->grantAccess('group_admin' => $this->Group->id);
     *
     * other access tokens:
     * - self: only if the logged in user looks at it's own
     *         page, he/she may see it
     */
    public function grantAccess($access) {
        if(!is_array($access)) {
            $access = array($access);
        }
        
        foreach($access as $allow) {
            if($allow == 'all') {
                return;
            }
            # no need to test for 'visitor', as
            # this is the default behaviour
            if($allow == 'guest') {
                if(Context::isGuest() ||
                   Context::isLoggedInIdentity() ||
                   Context::isAdmin()) {
                    return;
                } else {
                    $this->info_route('not_allowed_for_visitors');
                }
            } else if($allow == 'user') {
                if(Context::isLoggedInIdentity() ||
                   Context::isAdmin()) {
                    return;
                } else {
                    $this->info_route('not_allowed_for_guests');
                }
            } else if($allow == 'admin') {
                if(Context::isAdmin()) {
                    return;
                } else {
                    $this->info_route('not_allowed_for_users');
                }
            } else if($allow == 'self') {
                if(Context::isSelf()) {
                    return;
                } else {
                    $this->info_route('only_allowed_for_self');
                }
            } else {
                $this->error_route('unknown_access');
            }
        }
        
        $this->error_route('unknown_access');
    }
    
    /**
     * denies access for specific roles. for instance
     * the login page for already logged in users.
     * 
     * roles *do not* inherit the behaviour!
     */
    public function denyAccess($access) {
        if(!is_array($access)) {
            $access = array($access);
        }
        
        foreach($access as $deny) {
            if($allow == 'none') {
                return;
            }
            # no need to test for 'visitor', as
            # this is the default behaviour
            if($deny == 'guest') {
                if(Context::isGuest()) {
                    $this->info_route('not_allowed_for_guests');
                }
            } else if($deny == 'user') {
                if(Context::isLoggedInIdentity())  {
                    $this->info_route('not_allowed_for_users');
                }
            } else if($deny == 'admin') {
                if(Context::isAdmin()) {
                    $this->info_route('not_allowed_for_admins');
                }
            } else {
                $this->error_route('unknown_access');
            }
        }
        
        $this->error_route('unknown_access');
    }
    
    protected function error_route($action) {
        $this->redirect('/pages/error/' . $action);
    }
    
    protected function info_route($action) {
        $this->redirect('/pages/info/' . $action);
    }
    
    private function isSystemUpdatePage() {
    	return (strpos($this->here, '/system/update') !== false) ? true : false;
    }
}