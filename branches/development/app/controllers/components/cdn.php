<?php

/*
 * CDN - Content Delivery Network
 * eg. Amazon S3
 */
App::import('Vendor', 's3', array('file' => 's3'.DS.'s3.class.php'));

class CdnComponent extends Object {
	private $bucket_name;
    private $s3;
    private $key_id;
    private $secret_key;
	
    public function __construct() {
        $this->key_id = Configure::read('NoseRub.cdn_s3_access_key');
        $this->secret_key = Configure::read('NoseRub.cdn_s3_secret_key');
        $this->s3 = new AmazonS3($this->key_id, $this->secret_key);
        $this->bucket_name = Configure::read('NoseRub.cdn_s3_bucket');
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
    
    public function copyTo($from_filename, $to_filename, $mime_type = 'image/jpeg') {
        $content = file_get_contents($from_filename);
        if($content) {
            $this->writeContent($to_filename, $mime_type, $content);
        }
    }
    
    public function getBuckets() {
        return $this->s3->ListBuckets();
    }
    
    public function delete($filename) {
        $s3object = new S3Object($filename, $this->bucket_name, $this->key_id, $this->secret_key);
        $s3object->Delete();
    }
}