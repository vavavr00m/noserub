<?php

/*
 * CDN - Content Delivery Network
 * eg. Amazon S3
 */
vendor('s3/s3.class');

class CdnComponent extends Object {

    public function __construct() {
        $this->key_id = NOSERUB_CDN_S3_ACCESS_KEY;
        $this->secret_key = NOSERUB_CDN_S3_SECRET_KEY;
        $this->s3 = new AmazonS3($this->key_id, $this->secret_key);
        $this->bucket_name = NOSERUB_CDN_S3_BUCKET;
    }
    
    public function setBucket($new_name) {
        $this->bucket_name = $new_name;
    }
    
    public function getBucket() {
        return $this->bucket_name;
    }
    
    public function writeContent($path, $type, $content) {
        $bucket = $this->s3->Bucket($this->bucket_name);
        $object = $bucket->S3Object($path);
        $object->setData($content);
        $object->SetType($type);
        $object->SetCannedACL(AS3_ACL_PUBLIC_READ);
        $object->Put();
    }
    
    public function listBucket($bucket = null) {
        $bucket = $this->s3->Bucket($this->bucket_name);
        $objects = array();
        $bucket->ListObjects($objects);
        return $objects;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getBuckets() {
        return $this->s3->ListBuckets();
    }
    
    private $bucket_name;
    private $s3;
    private $key_id;
    private $secret_key;   
}

?>