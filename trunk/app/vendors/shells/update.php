<?php

class UpdateShell extends Shell {

    var $tasks = array('Database');
    
    function main() {
		if (!config('database')) {
			$this->out("Your database configuration was not found. Take a moment to create one.\n");
			$this->args = null;
			return $this->DbConfig->execute();
		}
    }
}
?>