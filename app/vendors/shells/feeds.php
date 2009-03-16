<?php

class FeedsShell extends Shell {
     public function main() {
         $out  = __('Available commands:', true) . "\n";
         $out .= "\t - upload (uploads feeds to CDN, if defined)\n\n";
         
         $this->out($out);
     }
     
     public function upload() {
         if(!Configure::read('NoseRub.use_cdn')) {
             # we don't need to do any upload
             $this->out(__('no ffed uploaded - no CDN defined in noserub.php', true));
             return;
         }

         $this->Syndication = Classregistry::init('Syndication');
         
         # I do this like this and not through a LIMIT of 250, because
         # then more than one task could run at once, without doing any harm
         for($i=0; $i<250; $i++) {
             # no two refresh's within 14 minutes
             $last_upload = date('Y-m-d H:i:s', strtotime('-15 minutes'));

             $this->Syndication->contain();
             $data = $this->Syndication->find(
                 'first', 
                 array(
                     'conditions' => array(
                         'Syndication.last_upload <' => $last_upload
                     ),
             		'order' => array(
             		    'Syndication.last_upload ASC', 
             		    'Syndication.modified DESC'
             		)
             	)
             );
             if($data) {
                 # save the old last_update timestamp
                 $datetime_last_upload = $data['Syndication']['last_upload'];

                 # set the last_upload right now, so a parallel running task
                 # would not get it, while we are uploading the data
                 $this->Syndication->id = $data['Syndication']['id'];
                 $this->Syndication->saveField('last_upload', date('Y-m-d H:i:s'));

                 # call the internal method
                 # it's not important which feed_type we want, as all
                 # available will be created and being uploaded
                 if($this->feed($data['Syndication']['hash'].'.rss', true, $datetime_last_upload)) {
                     $$this->out($data['Syndication']['hash']);
                 } else {
                     # in this case, we need to set the old timestamp again. because it could
                     # happen, that a rss feed is updated some time after now, but with an item
                     # older than now.
                     $this->Syndication->id = $data['Syndication']['id'];
                     $this->Syndication->saveField('last_upload', $datetime_last_upload);
                 }
             }

             # also upload a generic feed
             $this->Syndication->Identity->contain();
             $data = $this->Syndication->Identity->find(
                 'first',
                 array(
                     'conditions' => array(
                         'Identity.generic_feed' => 1,
                         'Identity.last_generic_feed_upload <' => $last_upload
                     ),
                     'order' => 'Identity.last_generic_feed_upload ASC'
                 )
             );
             if($data) {
                 # save the old last_update timestamp
                 $datetime_last_upload = $data['Identity']['last_generic_feed_upload'];

                 # set the last_upload right now, so a parallel running task
                 # would not get it, while we are uploading the data
                 $this->Syndication->Identity->id = $data['Identity']['id'];
                 $this->Syndication->Identity->saveField('last_generic_feed_upload', date('Y-m-d H:i:s'));

                 # call the internal method
                 # it's not important which feed_type we want, as all
                 # available will be created and being uploaded
                 $this->params['username'] = $data['Identity']['username'];
                 if($this->feed('rss', true, $datetime_last_upload)) {
                     $this->out($data['Identity']['username']);
                 } else {
                     # in this case, we need to set the old timestamp again. because it could
                     # happen, that a rss feed is updated some time after now, but with an item
                     # older than now.
                     $this->Syndication->Identity->id = $data['Identity']['id'];
                     $this->Syndication->Identity->saveField('last_generic_feed_upload', $datetime_last_upload);
                 }
             }    
         }
     }
}