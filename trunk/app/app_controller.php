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
    var $helpers = array('javascript', 'html');
    var $components = array('menu');
    var $uses = array('Identity');
    
    function flashMessage($type, $message) {
        $flash_messages = $this->Session->read('FlashMessages');
        $flash_messages[$type][] = $message;
        $this->Session->write('FlashMessages', $flash_messages);
    }
    
    /**
     * Makes sure we redirect to the https url,
     * when NOSERUB_USE_SSL is used and we're not
     * on a secure page
     *
     * @param  
     * @return 
     * @access 
     */
    public function checkSecure() {
        if(NOSERUB_USE_SSL) {
            $server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 0;
            if($server_port != 443) {
                $this->redirect(str_replace('http://', 'https://', FULL_BASE_URL) . $this->here);
                exit;
            }
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function beforeFilter() {       
        # set menu data
        $this->menu->setViewData($this);  
        
        /**
         * 	Don't you EVER remove this line else you will make the whole 
         * 	application a swiss cheese for XSS!
         *  We often call echo $this->here in our form actions and this would
         *  be exactly where the injection would take place.
         */
        $this->here = preg_replace('/("|\')/', '”', addslashes(strip_tags($this->here)));
        
        if(defined('SHELL_DISPATCHER')) {
            $this->layout = 'shell';
            if($this->action != '' && strpos($this->action, 'shell_') !== 0) {
                echo 'You may not call this route from the shell.' . "\n\n";
                exit;
            }
        } else {
            if(strpos($this->action, 'shell_') === 0) {
                echo '<h1>Error</h1><p>This route is only accessible from the shell!</p>';
                exit;
            }
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function ensureSecurityToken() {
        $session_identity_id = $this->Session->read('Identity.id');
        if(isset($this->params['form']['security_token'])) {
            # POST
            $security_token = $this->params['form']['security_token'];
        } else {
            # GET
            $security_token = isset($this->params['security_token']) ? $this->params['security_token'] : '';
        }
        
        if(!$this->Identity->checkSecurityToken($session_identity_id, $security_token)) {
            $this->redirect('/pages/security_check/', null, true);
        }
    }
    
    public function beforeRender() {
        # set new security_token
        $this->set('security_token', $this->Identity->updateSecurityToken($this->Session->read('Identity.id')));
    }
    
    public function afterFilter() {
        $this->Session->write('FlashMessages', array());
    }
    
}