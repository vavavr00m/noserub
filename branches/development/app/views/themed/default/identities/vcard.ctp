<?php

$identity = Context::read('identity');

echo $vcf->begin();
echo $vcf->attr('name', array(
    'last' => $identity['lastname'], 
    'first' => $identity['firstname']
));
echo $vcf->attr('fullName', $identity['name']);
if(Context::isContact() || Context::isSelf()) {
    echo $vcf->attr('email', $identity['email']);
}
echo $vcf->end();