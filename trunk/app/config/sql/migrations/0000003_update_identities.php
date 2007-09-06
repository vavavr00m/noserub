$identities = $this->query('SELECT id, is_local, username FROM identities');
foreach($identities as $identity) {
    if($identity['identities']['is_local'] == 0) {
        $username_splitted = split('@', $identity['identities']['username']);
        $username = $username_splitted[1] . '/noserub/' . $username_splitted[0];
        $this->execute('UPDATE identities SET username="'.$username.'" WHERE id='.$identity['identities']['id']);
    } else {
        $full_username = $identity['identities']['username'];
        $username_domain = split('@', $full_username);
        $username_namespace = split(':', $username_domain[0]);
        
        $username  = $username_namespace[0];
        $namespace = isset($username_namespace[1]) ? $username_namespace[1] : '';
        if($namespace) {
            $username = $username . '@' . $namespace;
        }
        $sql = 'UPDATE identities SET username="'.$username.'" WHERE id='.$identity['identities']['id'];
        $this->execute($sql);
    }
}
