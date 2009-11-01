<?php
/* SVN FILE: $Id:$ */
 
class Message extends AppModel {
    public $belongsTo = array(
        'Identity' => array(
            'counterCache' => true,
            'counterScope' => array(
                'folder' => 'inbox',
                'read' => 0 
            )
        ) 
    );
    
    /**
     * Tests if given folder name is valid
     *
     * @param string $folder
     *
     * @return bool
     */
    public function isValidFolder($folder) {
        $folder = strtolower($folder);
        
        return ($folder == 'inbox' || $folder == 'sent');
    }
    
    /**
     * Returns an array which is the reply data for the given
     * $data and given $identity_id
     *
     * @param array $data
     * @param int $identity_id
     *
     * @return array
     */
    public function reply($data, $identity_id) {
        if(isset($data['Message'])) {
            $data = $data['Message'];
        }
        if(stripos($data['subject'], 're: ') === 0) {
            $subject = $data['subject'];
        } else {
            $subject = 'Re: ' . $data['subject'];
        }
        
        $to = $data['to_from'];
        
        $text_array = split("\n", $data['text']);
        $text = '';
        foreach($text_array as $line) {
            $text .= '> ' . $line . "\n";
        }

        $intro = sprintf(__('On %s %s wrote:', true), $data['created'], $data['to_from']);
        $text = "\n\n" . $intro . "\n\n" . $text;
        
        return array(
                'Message' => array(
                    'to_from' => $to,
                    'subject' => $subject,
                    'text' => $text
        ));
    }
}