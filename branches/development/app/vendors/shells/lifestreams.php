<?php

class LifestreamsShell extends Shell {
     public function main() {
         $out  = __('Available commands:', true) . "\n";
         $out .= "\t - update (updates all lifestreams with data from associated accounts)\n\n";
         
         $this->out($out);
     }
     
     public function update() {
         if(!Configure::read('NoseRub.manual_feeds_update')) {
              $this->set('data', __('NoseRub.manual_feeds_update in noserub.php not set to do it manually!', true));
          } else {
              $this->Entry = Classregistry::init('Entry');
              $this->Xmpp = Classregistry::init('Xmpp');

              $this->Entry->Account->contain();
              $data = $this->Entry->Account->find(
                  'all',
                  array(
                      'fields'     => 'id',
                      'conditions' => array(
                          'next_update <= NOW()'
                      ),
                      'limit' => 50,
                      'order' => 'next_update ASC'
                  )
              );

              $entries = array();
              foreach($data as $item) {
                  $new_entries = $this->Entry->updateByAccountId($item['Account']['id']);
                  if($new_entries) {
                      $entries = array_merge($entries, $new_entries);
                  }
              }
              $messages = array();
              foreach($entries as $entry) {
                  if(!$entry['restricted']) {
                      $messages[] = $this->Entry->getMessage($entry);
                  }
              }
              $this->Xmpp->broadcast($messages);
              $msg = sprintf(__('%d entries added/updated', true), count($entries));

              $this->out($msg);
          }
     }
}