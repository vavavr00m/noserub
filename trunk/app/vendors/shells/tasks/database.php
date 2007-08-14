<?php

class DatabaseTask extends Shell {
    
    function execute() {
        if (empty($this->args)) {
			$this->__interactive();
		}
	}

    function __interactive() {
        $this->hr();
		$this->out('Update Database:');
		$this->hr();
		$this->interactive = true;
		
		$db =& ConnectionManager::getDataSource('default');
		
		# check, if schema_info is there:
		$schema_info = $db->fetchall('SHOW TABLES;');
		if(!empty($schema_indo)) {	
		    $schema_info = $db->fetchall('SELECT value FROM schema_info');
	    }
	    
		if(empty($schema_info)) {
		    $this->out('Creating table schema_info');
		    $db->execute('CREATE TABLE IF NOT EXISTS `schema_info` (`value` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		    $db->execute('INSERT INTO `schema_info` (`value`) VALUES (0)');
		    $this->out("\n");
		    $this->out('Please run again...');
		    $this->out("\n");
		    return;
		}
		
		$schema_info = $db->fetchall('SELECT value FROM schema_info');
        $current_migration = $schema_info[0]['schema_info']['value'];
        $this->hr();
        $this->out('Current migration: ' . $current_migration);
    
        $recent_migration = $this->getRecentMigration();
        $this->out('Recent migration: ' . $recent_migration);
        $this->hr();
        if($recent_migration <= $current_migration) {
            $this->out('Nothing to be done. Database is up-to-date!'."\n");
            return;
        }
        
        $this->out('Now updating from ' . $current_migration . ' to ' . $recent_migration . ':');
        $migrations = $this->getOpenMigrations($current_migration);

        for($i=$current_migration+1; $i<=$recent_migration; $i++) {
            $this->out($i . ' => SQL: ' . $migrations['sql'][$i]['name'], false);
            foreach($migrations['sql'][$i]['content'] as $sql) {
                $db->execute($sql);
                $this->out('.', false);
            }
            $this->out('');
            if(isset($migrations['php'][$i]['name'])) {
                $this->out($i . ' => Script: ' . $migrations['php'][$i]['name']);
                eval($migrations['php'][$i]['content']);
            }
            $db->execute('UPDATE schema_info SET value='.$i);
        }
        
        $this->hr();
        $this->out('Database is now up-to-date!' . "\n");
    }
    
    function getRecentMigration() {
        $files = scandir(APP . '/config/sql/migrations/');
        $recent_migration = 0;
        foreach($files as $filename) {
            if(preg_match('/([0-9]+)_.*\.sql/i', $filename, $matches)) {
                $num = intval($matches[1]);
                if($num > $recent_migration) {
                    $recent_migration = $num;
                }
            }
        }
        
        return $recent_migration;
    }
    
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
}