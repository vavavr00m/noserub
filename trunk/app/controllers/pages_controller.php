<?php
class PagesController extends AppController {
    var $uses = array('Identity');
    
    function display() {
        $path = func_get_args();
        if(isset($path[0]) && $path[0] == 'home') {
            if($this->Session->check('Identity.id')) {
                $this->redirect('/' . $this->Session->read('Identity.local_username') . '/', null, true);
            }
            $this->home();
        }
    }
    
    function home() {
        $this->Identity->recursive = 0;
        $this->Identity->expects('Identity');
        $this->set('identities', $this->Identity->findAll(array('is_local' => 1,
                                                                'frontpage_updates' => 1,
                                                                'NOT username LIKE "%@%"'), null, 'created DESC', 10));
        $this->render('home');
    }
}