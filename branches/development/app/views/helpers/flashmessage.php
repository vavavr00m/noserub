<?php
/**
 * Helper to display flash messages
 */
class FlashMessageHelper extends AppHelper {
    var $helpers = array('Session');
    
    public function render() {
        $flash_messages = $this->Session->read('FlashMessages');
        
        if($flash_messages) {
            foreach($flash_messages as $type => $messages) {
                echo '<div id="message" class="' . $type . '">';
                foreach($messages as $message) {
                    echo '<p>' . $message . '</p>';
                }
                echo '</div>';
            }
        }
    }

}