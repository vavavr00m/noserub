<?php
class PagesController extends AppController {
    var $uses = array();
    
    function display() {
        $path = func_get_args();
        if(isset($path[0]) && !empty($path[0])) {
            if($path[0] == 'home' && $this->Session->check('Identity.id')) {
                $this->redirect('/' . $this->Session->read('Identity.local_username') . '/', null, true);
            }
            
            $this->render($path[0]);
        }
    }
}