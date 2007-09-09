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
}