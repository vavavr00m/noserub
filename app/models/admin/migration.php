<?php

define('MIGRATIONS_FOLDER', APP . 'config/sql/migrations/');

class Migration extends AppModel {
	public $useTable = false;
	
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
     	$dataSource = $db->getDataSource('default');
     	
     	return $dataSource->isConnected() ? 1 : 0;
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
		if(!$this->existsSchemaInfoTable()) {
	        $this->createSchemaInfoTable();
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
    
    private function createSchemaInfoTable() {
    	$this->query('CREATE TABLE IF NOT EXISTS `schema_info` (`value` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		$this->query('INSERT INTO `schema_info` (`value`) VALUES (0)');
    }
    
    private function existsSchemaInfoTable() {
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
		
		return $is_present;
    }
}