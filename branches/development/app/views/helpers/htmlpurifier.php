<?php
/**
 * Helper for all the widget and functions to be used
 */
class HtmlpurifierHelper extends AppHelper {
    
    public function clean($content) {
        App::import('Vendor', 'htmlpurifier', array('file' => 'htmlpurifier'.DS.'HTMLPurifier.standalone.php'));
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', CACHE . 'htmlpurifier');
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.Linkify', true);
        $purifier = new HTMLPurifier($config);
        
        return $purifier->purify($content);
    }
}