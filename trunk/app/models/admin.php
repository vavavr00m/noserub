<?php

/** 
 * Model for all the admin stuff in NoseRub.
 */
class Admin extends AppModel {
    var $useTable = false;

    var $constants = array('!NOSERUB_DOMAIN' => array(
                                'file' => 'noserub.php'),
                           'NOSERUB_ADMIN_HASH' => array(
                                'file' => 'noserub.php'),
                           'NOSERUB_REGISTRATION_TYPE' => array(
                                'values' => array('all', 'none', 'invitation'),
                                'file'   => 'noserub.php'),
                           'NOSERUB_EMAIL_FROM' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_USE_SSL' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false)),
                           #'NOSERUB_USE_PHPIDS' => array(
                            #   'file'   => 'noserub.php',
                            #   'values' => array(true, false))
                          );
    
    var $directories = array('tmp');
    
    /**
     * checks if some directories are writeable
     */
    function checkWriteable() {
        $out = array();
        foreach($this->directories as $directory) {
            if(!is_writeable(APP.$directory)) {
                $out[] = APP.$directory;
            }
        }
        
        return $out;
    }
    
    /**
     * check some constants
     *
     * @param  
     * @return 
     * @access 
     */
    function checkConstants() {
        $out = array();
        foreach($this->constants as $constant => $info) {
            if(strpos($constant, '!') === 0) {
                $constant = str_replace('!', '', $constant);
                if(defined($constant)) {
                    $out[$constant] = 'obsolete! Please remove it from '.$info['file'];
                }
            } else if(!defined($constant)) {
                $out[$constant] = 'not defined! (see '.$info['file'].')';
            } else {
                if(isset($info['values'])) {
                    if(!in_array(constant($constant), $info['values'])) {
                        $out[$constant] = 'value might only be: "' . join('", "', $info['values']) . '" (see '.$info['file'].')';
                    }
                } else {
                    if(constant($constant) == '') {
                        $out[$constant] = 'no value! (see '.$info['file'].')';
                    }
                }
            }
        }
        
        return $out;
    }
    
    /**
     * Tests, wether the database is working, or not
     *
     * @param  
     * @return -1 = no config file
     *          0 = connect not working
     *          1 = everything fine
     */
    function getDatabaseStatus() {
        $filePresent = file_exists(CONFIGS.'database.php');
        if(!$filePresent) {
            return -1;
        }
        
        uses('model' . DS . 'connection_manager');
    	$db = ConnectionManager::getInstance();
     	$connected = $db->getDataSource('default');
     	
     	return $connected->isConnected() ? 1 : 0;
    }
    
    /**
     * Get number of most recent migration in the system.
     * This is not the most recent executed migration!
     * @see getCurrentMigration()
     * @param  
     * @return 
     * @access 
     */
    function getMostRecentMigration() {
        $files = scandir(APP . '/config/sql/migrations/');
        $most_recent_migration = 0;
        foreach($files as $filename) {
            if(preg_match('/([0-9]+)_.*\.(sql|php)/i', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $most_recent_migration) {
                    $most_recent_migration = $num;
                }
            }
        }
        
        return $most_recent_migration;
    }
    
    /**
     * Get number of the current active migration.
     * This is the last migration, that was executed
     * on this system.
     * @see getMostrecentMigration()
     * @param  
     * @return 
     * @access 
     */
    function getCurrentMigration() {
        # check, if schema_info is there:
		$tables = $this->execute('SHOW TABLES');
		
		$is_present = false;
		foreach($tables as $table) {
		    foreach($table as $row) {
		        if(in_array('schema_info', $row)) {
		            $is_present = true;
		            break;
		        }
	        }
	        if($is_present) {
	            break;
	        }
		}
		
		if(!$is_present) {
	        $this->execute('CREATE TABLE IF NOT EXISTS `schema_info` (`value` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		    $this->execute('INSERT INTO `schema_info` (`value`) VALUES (0)');
	        return 0;
	    } 
	    
	    $schema_info = $this->query('SELECT value FROM schema_info');
	    return $schema_info[0]['schema_info']['value'];
    }
    
    /**
     * Return all the open migrations
     *
     * @param  
     * @return 
     * @access 
     */
    function getOpenMigrations($current_migration) {
        $migrations = array('sql' => array(), 'php' => array());
        $files = scandir(APP . '/config/sql/migrations/');
        foreach($files as $filename) {
            # sql
            if(preg_match('/([0-9]+)_(.*)\.sql/i', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $current_migration) {
                    $name = $matches[2];
                    $content = file(APP . '/config/sql/migrations/' . $filename);
                    $migrations['sql'][$num] = array('name'    => $name,
                                                     'content' => $content);
                }
            }
            # php
            if(preg_match('/([0-9]+)_(.*)\.php/iU', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $current_migration) {
                    $name = $matches[2];
                    $content = file_get_contents(APP . '/config/sql/migrations/' . $filename);
                    $migrations['php'][$num] = array('name'    => $name,
                                                     'content' => $content);
                }
            }
        }
        
        return $migrations;
    }
    
    /**
     * Runnning all migration scripts from $current_migration to
     * $most_recent_migration
     *
     * @param  
     * @return 
     * @access 
     */
    function migrate($migrations, $current_migration, $most_recent_migration) {
        for($i=$current_migration+1; $i<=$most_recent_migration; $i++) {
            if(isset($migrations['sql'][$i])) {
                foreach($migrations['sql'][$i]['content'] as $sql) {
                    $this->execute($sql);
                }
            }
            if(isset($migrations['php'][$i]['name'])) {
                eval($migrations['php'][$i]['content']);
            }
            
            # update schema_info
            $this->execute('UPDATE schema_info SET value='.$i);
        }
    }
}