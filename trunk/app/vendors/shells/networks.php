<?php

class NetworksShell extends Shell {
    public function main() {
        $out  = __('Available commands:', true) . "\n";
        $out .= "\t - get_data (gets data from all networks, eg. comments, favorites, groups, etc.)\n";
        $out .= "\t - sync (get's list of networks from http://noserub.com/networks)\n\n";
         
        $this->out($out);
    }
     
    public function get_data() {
        # Classregistry::init() did not work for Favorite, only Comment...
        App::import('Model', array('Comment', 'Favorite'));

        $this->Comment = new Comment();
        $this->Favorite = new Favorite();

        $this->out(print_r($this->Comment->poll(), 1));
        $this->out(print_r($this->Favorite->poll(), 1));
    }
     
     public function sync() {
         $this->Network = Classregistry::init('Network');
         $this->out(print_r($this->Network->sync(), 1));
     }
}