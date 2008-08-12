<?php

define('MIGRATIONS_FOLDER', APP . 'config/sql/migrations/');

/** 
 * Model for all the admin stuff in NoseRub.
 */
class Admin extends AppModel {
    public $useTable = false;

    public $constants = array('!NOSERUB_DOMAIN' => array(
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
                           'NOSERUB_GOOGLE_MAPS_KEY' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_APP_NAME' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_FULL_BASE_URL' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_USE_FEED_CACHE' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false)),
                           'NOSERUB_USE_CDN' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false))
                          );
    
    private $directories;
    
    public function __construct() {
    	$this->directories = array(APP.'tmp', WWW_ROOT.'static'.DS.'avatars');
    }
    
    /**
     * checks if some directories are writeable
     */
    public function checkWriteable() {
        $out = array();
        foreach($this->directories as $directory) {
            if(!is_writeable($directory)) {
                $out[] = $directory;
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
    public function checkConstants() {
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
                    if(constant($constant) === '') {
                        $out[$constant] = 'no value! (see '.$info['file'].')';
                    }
                }
            }
        }
        
        return $out;
    }
    
    public function checkExtensions() {
        $result = array();
    	
        if (!extension_loaded('curl')) {
        	$result = am($result, array('curl' => 'needed for communicating with other servers'));
        }
        
    	if (!extension_loaded('gd')) {
        	$result = am($result, array('GD' => 'needed for image handling')); 
        }
    	
        if (!(function_exists('gmp_init') || function_exists('bcscale'))) {
        	$result = am($result, array('GMP or BCMath' => 'needed for OpenID functionality'));
        }
        
        return $result;
    }
    
    /**
     * Tests, wether the database is working, or not
     *
     * @param  
     * @return -1 = no config file
     *          0 = connect not working
     *          1 = everything fine
     */
    public function getDatabaseStatus() {
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
    public function getMostRecentMigration() {
        $files = scandir(MIGRATIONS_FOLDER);
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
    public function getCurrentMigration() {
        # check, if schema_info is there:
		$tables = $this->query('SHOW TABLES');
		
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
	        $this->query('CREATE TABLE IF NOT EXISTS `schema_info` (`value` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		    $this->query('INSERT INTO `schema_info` (`value`) VALUES (0)');
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
    public function getOpenMigrations($current_migration) {
        $migrations = array('sql' => array(), 'php' => array());
        $files = scandir(MIGRATIONS_FOLDER);
        foreach($files as $filename) {
            # sql
            if(preg_match('/([0-9]+)_(.*)\.sql/i', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $current_migration) {
                    $name = $matches[2];
                    $content = file(MIGRATIONS_FOLDER . $filename);
                    $migrations['sql'][$num] = array('name'    => $name,
                                                     'content' => $content);
                }
            }
            # php
            if(preg_match('/([0-9]+)_(.*)\.php/iU', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $current_migration) {
                    $name = $matches[2];
                    $content = file_get_contents(MIGRATIONS_FOLDER . $filename);
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
    public function migrate($migrations, $current_migration, $most_recent_migration) {
        for($i=$current_migration+1; $i<=$most_recent_migration; $i++) {
            if(isset($migrations['sql'][$i])) {
                foreach($migrations['sql'][$i]['content'] as $sql) {
                    $this->query($sql);
                }
            }
            if(isset($migrations['php'][$i]['name'])) {
                eval($migrations['php'][$i]['content']);
            }
            
            # update schema_info
            $this->query('UPDATE schema_info SET value='.$i);
        }
    }
}