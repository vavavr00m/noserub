$identities = $this->query('SELECT id, username FROM identities WHERE is_local=1');
foreach($identities as $identity) {
    loadModel('Identity');
    $splitted = Identity::splitUsername($identity['identities']['username']);
    $sql = 'UPDATE identities SET username="'.$splitted['username'].'" WHERE id='.$identity['identities']['id'];
    $this->execute($sql);
}
