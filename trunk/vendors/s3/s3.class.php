<?php
/* SVN FILE: $Id: app_model.php 3181 2007-05-15 09:02:10Z uliw $ */
/**
 * Ormigo Profiles <https://ormigo.com/>
 * Copyright (c)    2006, Fabian Thylmann
 * 
 * @copyright        Copyright (c) 2006, Fabian Thylmann
 * @package          vendors
 * @subpackage       amazons3
 * @version          $Revision$
 * @modifiedby       $LastChangedBy: uliw $
 * @lastmodified     $LastChangedDate$
 * 
 */
 
	define('AS3R_CODE', 0);
	define('AS3R_BODY', 1);
	define('AS3R_HEADER', 2);
	define('AS3R_COMBO', 3);

	define('AS3_ACL_PRIVATE', 'private');
	define('AS3_ACL_PUBLIC_READ', 'public-read');
	define('AS3_ACL_PUBLIC_READ_WRITE', 'public-read-write');
	define('AS3_ACL_AUTHED_READ', 'authenticated-read');

	define('AS3_PERM_FULL', 'FULL_CONTROL');
	define('AS3_PERM_READ', 'READ');
	define('AS3_PERM_WRITE', 'WRITE');
	define('AS3_PERM_READ_ACP', 'READ_ACP');
	define('AS3_PERM_WRITE_ACP', 'WRITE_ACP');

	/* Class doing the AmazonS3 basics */
	class AmazonS3 {
		/* important variables needed */
		private $__awss3objectid;
		private $__secrets3object;
		private $__awshost = 's3.amazonaws.com';
		private $__awshttps = TRUE;
		
		function __construct($awss3objectid, $secrets3object) {
			/* store the s3object and secret for all future requests we run */
			$this->__awss3objectid = $awss3objectid;
			$this->__secrets3object = $secrets3object;
		}

		function GetAWSKeyId() {
			return $this->__awss3objectid;
		}
		function GetSecretKey() {
			return $this->__secrets3object;
		}

		function __nat($a, $b) {
			return strnatcmp($a, $b);
		}
		
		/* build authorization header for signing a request */
		function __sign_header($verb, $uri, $query, $headers) {
			/* make sure all of query and headers uses lowercased s3objects for us */
			$query = array_change_key_case($query, CASE_LOWER);
			$headers = array_change_key_case($headers, CASE_LOWER);
			
			/* nat sort our header s3objects */
			uksort($headers, Array($this, '__nat'));

			/* build string to sign */
			$parts = Array();
			
			$parts[] = strtoupper($verb);
			$parts[] = isset($headers['content-md5']) ? $headers['content-md5'] : '';
			$parts[] = isset($headers['content-type']) ? $headers['content-type'] : '';
			$parts[] = isset($headers['date']) ? $headers['date'] : '';
			
			/* go through all headers */
			foreach($headers AS $s3object=>$val) {
				if (substr($s3object, 0, 6) == 'x-amz-') {
					/* trim $val */
					$val = trim($val);
					/* add to parts */
					$parts[] = $s3object.':'.$val;
				}
			}
			
			if (in_array('acl', $query) || array_key_exists('acl', $query)) $uri .= '?acl';
			if (in_array('torrent', $query) || array_key_exists('torrent', $query)) $uri .= '?torrent';

			$parts[] = $uri;
			
			$string = implode("\n", $parts);
			
			/* return our authorization header value */
			return 'AWS '.$this->GetAWSKeyId().':'.base64_encode(hash_hmac('sha1', $string, $this->GetSecretKey(), TRUE));
		}

		/* build query string for signing a request */
		function __sign_query($verb, $uri, $query, $headers, $expires=5) {
			/* make sure all of query and headers uses lowercased s3objects for us */
			$query = array_change_key_case($query, CASE_LOWER);
			$headers = array_change_key_case($headers, CASE_LOWER);
			
			/* nat sort our header s3objects */
			uksort($headers, Array($this, '__nat'));

			/* build string to sign */
			$parts = Array();
			
			$parts[] = strtoupper($verb);
			$parts[] = $headers['content-md5'];
			$parts[] = $headers['content-type'];
			
			/* figure out the seconds since epoch right now, and add expires seconds to it,
				thats our expires time */
			$expires += time();
			$parts[] = $expires;
			
			/* go through all headers */
			foreach($headers AS $s3object=>$val) {
				if (substr($s3object, 0, 6) == 'x-amz-') {
					/* trim $val */
					$val = trim($val);
					/* add to parts */
					$parts[] = $s3object.':'.$val;
				}
			}
			
			if (in_array('acl', $query) || array_key_exists('acl', $query)) $uri .= '?acl';
			if (in_array('torrent', $query) || array_key_exists('torrent', $query)) $uri .= '?torrent';

			$parts[] = $uri;
			
			$string = implode("\n", $parts);
			
			/* return the (rest of the) query string */
			return ((strpos($uri, '?') !== FALSE)?'&':'?').'Signature='.
				urlencode(base64_encode(hash_hmac('sha1', $string, $this->GetSecretKey(), TRUE))).
				'&Expires='.$expires.'&AWSAccesss3objectId='.$this__awss3objectid;
		}

		/* run a curl request, return error code, body and header */
		function __curl_exec($req) {
			curl_setopt($req, CURLOPT_FOLLOWLOCATION, FALSE);
			curl_setopt($req, CURLOPT_HEADER, TRUE);
			curl_setopt($req, CURLOPT_RETURNTRANSFER, TRUE);
			$data = curl_exec($req);
			$code = curl_getinfo($req, CURLINFO_HTTP_CODE);
			
			/* get the header and body */
			list($header, $body) = explode("\r\n\r\n", $data);

			if ($code >= 300) {
				/* error result, throw an exception */
				throw new Exception("HTTP Result $code:\n". $data);
			}
			
			/* return code, header and body */
			return Array($code, $body, $header);
		}
	
		/* run any kind of request */
		function __request($verb, $uri, $query=Array(), $headers=Array(), $data=FALSE, $file=FALSE, $return_type=AS3R_CODE) {
			/* add x-amz-date header */
			$headers['x-amz-date'] = gmdate('D, d M Y H:i:s').' GMT';

			/* build signature */
			$headers['Authorization'] = $this->__sign_header($verb, $uri, $query, $headers);

			/* build query string */
			$elements = Array();
			foreach($query AS $s3object=>$val) {
				if (is_numeric($s3object)) $elements[] = urlencode($val);
				else if ($val === TRUE) $elements[] = urlencode($s3object);
				else $elements[] = urlencode($s3object).'='.urlencode($val);
			}
			$qstr = implode('&', $elements);

			/* add query to uri */
			if ($qstr) $uri .= ((strpos($uri, '?')!== FALSE)?'&':'?').$qstr;

			/* build the curl setup depending on the verb */
			$req = curl_init();
			curl_setopt($req, CURLOPT_USERAGENT, 'AmazonS3PHPClass');
			curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($req, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($req, CURLOPT_URL, ($this->__awshttps?'https://':'http://').$this->__awshost.$uri);

			$h = Array();
			foreach($headers AS $s3object=>$val) $h[]= $s3object.': '.$val;
			curl_setopt($req, CURLOPT_HTTPHEADER, $h);

			/* setup the curl object depending on the verb */
			switch($verb) {
				case 'PUT':
					if ($data) {
						curl_setopt($req, CURLOPT_CUSTOMREQUEST, 'PUT');
						curl_setopt($req, CURLOPT_POSTFIELDS, $data);
					} else if ($file) {
						curl_setopt($req, CURLOPT_PUT, TRUE);
						curl_setopt($req, CURLOPT_INFILE, $file);
						curl_setopt($req, CURLOPT_INFILESIZE, filesize($file));
					}
					break;
				case 'GET':
					break;
				case 'DELETE':
					curl_setopt($req, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}
			
			/* execute via another function we can overrride */
			list($code, $body, $header) = $this->__curl_exec($req);
			curl_close($req);

			switch($return_type) {
				case AS3R_BODY:
					return $body;
					break;
				case AS3R_HEADER:
					return $header;
					break;
				case AS3R_COMBO:
					return Array($code, $body, $header);
					break;
				case AS3R_CODE:
				default:
					return $code;
					break;
			}
		}
		
		/* parse headers */
		function __parse_headers($header) {
			$headers = Array();
			$lines = explode("\n", $header);
			array_shift($lines);
			foreach($lines AS $line) {
				$line = trim($line);
				list($s3object, $val) = explode(':', $line, 2);
				$val = trim($val);
				if ($val{0} == '"') {
					$val = substr($val, 1, -1);
				}
				$headers[strtolower(trim($s3object))] = $val;
			}
			
			return $headers;
		}

		/* run ACL */
		function __acl($bucket, $s3object=FALSE) {
			$data = $this->__request('GET', '/'.$bucket.($s3object?'/'.$s3object:''), Array('acl'), Array(), FALSE, FALSE, AS3R_BODY);
			return $data;
		}
		function __put_acl($bucket, $s3object, $acl) {
			$headers = Array();
			$headers['Content-Length'] = strlen($acl);
			return ($this->__request('PUT', '/'.$bucket.($s3object?'/'.$s3object:''), Array('acl'), $headers, $acl, FALSE, AS3R_CODE) == 200)?TRUE:FALSE;
		}
		
		/* run GET */
		function ___get($bucket, $s3object) {
			return $this->__request('GET', '/'.$bucket.($s3object?'/'.$s3object:''), Array(), Array(), FALSE, FALSE, AS3R_COMBO);
		}

		/* run HEAD */
		function __head($bucket, $s3object) {
			return $this->__request('HEAD', '/'.$bucket.'/'.$s3object, Array(), Array(), FALSE, FALSE, AS3R_HEADER);
		}

		/* run PUT */
		function __put($bucket, $s3object=FALSE, $data=FALSE, $file=FALSE, $headers=Array()) {
			return $this->__request('PUT', '/'.$bucket.($s3object?'/'.$s3object:''), Array(), $headers, $data, $file);
		}

		/* run DELETE */
		function __delete($bucket, $s3object=FALSE) {
			return $this->__request('DELETE', '/'.$bucket.($s3object?'/'.$s3object:''));
		}

		/* list all buckets this aws s3object id has */
		function ListBuckets() {
			/* run request */
			list($code, $result, $header) = $this->__request('GET', '/', Array(), Array(), FALSE, FALSE, AS3R_COMBO);

			/* parse result with simple xml */
			$xml = new SimpleXMLElement($result);
			
			/* build bucket objects */
			$buckets = Array();
			foreach($xml->Buckets->Bucket AS $bucket) {
				$obj = new S3Bucket((string)$bucket->Name[0], $this->GetAWSKeyId(), $this->GetSecretKey());
				$obj->SetCreationDate((string)$bucket->CreationDate[0]);
				$buckets[(string)$bucket->Name[0]] =& $obj;
			}
			
			/* return our list of bucket objects */
			return $buckets;
		}
		
		/* setup a new bucket, optionally we can create it if it does not exist */
		function Bucket($name, $create=TRUE) {
			/* to figure out if the bucket exsits, get one s3object from it */
			$obj = new S3Bucket($name, $this->GetAWSKeyId(), $this->GetSecretKey());
			
			$s3objects = Array();
			if ($obj->ListObjects($s3objects, '', '', 1)) {
				/* all good, bucket exists */
				return $obj;
			} else {
				/* no good, lets create the bucket */
				if (!$obj->Create()) {
					unset($obj);
				} else {
					return $obj;
				}
			}
			
			/* if we get here, return FALSE for error, it did not work */
			return FALSE;
		}
	}
	
	class S3Bucket extends AmazonS3 {
		private $__name;
		private $__creation;

		function __construct($bucket, $awss3objectid, $secrets3object) {
			/* create our bucket object */
			$this->__name = $bucket;
			parent::__construct($awss3objectid, $secrets3object);
		}

		function SetCreationDate($date) {
			$this->__creation = $date;
		}
		
		function GetCreationDate() {
			return $this->__creation;
		}

		/* list my s3objects */
		function ListObjects(&$s3objects, $prefix='', $marker='', $max=0, $delimiter='') {
			$__s3objects = Array();
			/* setup s3objects array if nothing there yet */
			if (!$s3objects) $s3objects = Array();
			
			/* fetch the s3objects */
			$query = Array();
			if ($prefix) $query['prefix'] = $prefix;
			if ($marker) $query['marker'] = $marker;
			if ($max) $query['max-keys'] = $max;
			if ($delimiter) $query['delimiter'] = $delimiter;
			list($code, $result, $header) = $this->__request('GET', '/'.$this->__name, $query, Array(), FALSE, FALSE, AS3R_COMBO);

			if ($code != 200) return FALSE;

			/* parse result */
			$xml = new SimpleXMLElement($result);
			
			/* get all the s3objects */
			$last_s3object = FALSE;
			foreach($xml->Contents AS $s3object) {
				$obj = new S3Object($this->__name, (string)$s3object->Key[0], $this->GetAWSKeyId(), $this->GetSecretKey());
				$obj->SetLastModified((string)$s3object->LastModified[0]);
				$obj->SetSize((string)$s3object->Size[0]);
				$last_s3object = (string)$s3object->Key[0];
				$__s3objects[$last_s3object] =& $obj;
			}
			
			/* if this is truncated, we need to recur */
			if (strtolower(substr((string)$xml->IsTruncated, 0, 5)) != 'false') {
				if (count($__s3objects) <= $max) {
					$max -= count($__s3objects);

					/* get more */
					$my_s3objects = FALSE;
					$this->ListObjects($my_s3objects, $prefix, $last_s3object, $max, $delimiter);
					/* add to $__s3objects */
					$__s3objects += $my_s3objects;
				}
			}
			
			/* assign $__s3objects to $s3objects */
			$s3objects = $__s3objects;

			return TRUE;
		}
		
		/* list all s3objects */
		function ListAllObjects() {
			$s3objects = FALSE;
			if (!$this->ListObjects($s3objects)) return FALSE;
			
			return $s3objects;
		}
		
		function Create() {
			/* create this bucket */
			if ($this->__put($this->__name, FALS, FALSE, FALSE, Array('Content-Length'=>0)) == 200) return TRUE;
			else return FALSE;
		}
		
		function Delete() {
			/* delete this bucket */
			if ($this->__delete($this->__name) == 204) return TRUE;
			else return FALSE;
		}
		
		function Clear() {
			/* get all s3objects */
			$s3objects = $this->ListAlls3objects();
			/* delete each one */
			foreach($s3objects AS $s3object) {
				$s3object->Delete();
				unset($s3object);
			}
		}
		
		/* setup a new s3object object based on this bucket */
		function S3Object($name) {
			return new S3Object($this->__name, $name, $this->GetAWSKeyId(), $this->GetSecretKey());
		}
		
		/* Get ACL for this bucket */
		function ACL() {
			return new S3ACL($this->__bucket, FALSE, $this->GetAWSKeyId(), $this->GetSecretKey());
		}
	}

	class S3Object extends AmazonS3 {
		private $__name;
		private $__bucket;
		private $__file = FALSE;
		private $__data = FALSE;
		private $__modified;
		private $__size = 0;
		private $__meta = Array();
		private $__expires = 0;
		private $__type = FALSE;
		private $__md5 = FALSE;
		private $__disposition = FALSE;
		private $__encoding = FALSE;
		private $__canned_acl = AS3_ACL_PRIVATE;

		function __construct($bucket, $name, $awss3objectid, $secrets3object) {
			/* create our bucket object */
			$this->__bucket = $bucket;
			$this->__name = $name;
			parent::__construct($awss3objectid, $secrets3object);
		}

		function SetLastModified($date) {
			$this->__modified = $date;
		}
		
		function GetLastModified() {
			return $this->__modified;
		}

		function SetCannedACL($acl) {
			$this->__canned_acl = $acl;
		}

		function SetSize($size) {
			$this->__size = $size;
		}
		
		function GetSize() {
			return $this->__size;
		}
		
		/* add a meta data field */
		function AddMetaData($s3object, $value) {
			$this->__meta[$s3object] = $value;
		}
		
		/* set expires in seconds from now */
		function SetExpires($expire) {
			$this->__expires = time()+$expire;
		}

		/* content type */
		function SetType($type) {
			$this->__type = $type;
		}
		function GetType() {
			return $this->__type;
		}
		
		/* md5 */
		function SetMd5($md5) {
			$this->__md5 = $md5;
		}
		function GetMd5() {
			return $this->__md5;
		}

		/* disposition */
		function SetDisposition($disposition) {
			$this->__disposition = $disposition;
		}
		function GetDisposition() {
			return $this->__disposition;
		}

		/* encoding */
		function SetEncoding($encoding) {
			$this->__encoding = $encoding;
		}
		function GetEncoding() {
			return $this->__encoding;
		}

		/* data/file */
		function SetData($data) {
			$this->__data = $data;
		}
		function GetData() {
			return $this->__data;
		}
		function SetFile($file) {
			$this->__file = $file;
		}
		function GetFile() {
			return $this->__file;
		}
		
		/* get this s3object's data */
		function Get() {
			list($code, $body, $header) = $this->___get($this->__bucket, $this->__name);
			if ($code != 200) return FALSE; // no good error code
			/* parse the header */
			$headers = $this->__parse_headers($header);
			/* store certain vars */
			if ($headers['content-type']) $this->SetType($headers['content-type']);
			if ($headers['content-length']) $this->SetSize($headers['content-length']);
			if ($headers['etag']) $this->SetMd5($headers['etag']);
			if ($headers['last-modified']) $this->SetLastModified($headers['last-modified']);
			/* go through all headers manually looking for meta data */
			foreach($headers AS $s3object=>$val) {
				if (substr($s3object, 0, 11) == 'x-amz-meta-') {
					$this->AddMetaData(substr($s3object, 11), $val);
				}
			}
			/* store the actual body */
			$this->SetData($body);
		}

		/* save this s3object to a local file */
		function SaveToFile($file) {
			$data = $this->GetData();
			if (!$data) {
				/* we have to store the data in a file but fetch it from the system,
					lets have curl store to the file directly to save memory usage */
				/* XXX */
			} else {
				/* have data, lets store it */
				return file_put_contents($file, $data);
			}
		}
		
		/* store this s3object's data */
		function Put() {
			$data = $this->__data;
			$file = $this->__file;
			/* needs at least one type of data input */
			if (!$data && !$file) throw new Exception(__CLASS__.': Need data or file for ::Put()');

			if (!$this->__size) {
				if ($data) $this->__size = strlen($data);
				else $this->__size = filesize($file);
			}

			/* build headers from meta */
			$headers = Array();
			foreach($this->__meta AS $s3object=>$val) $headers['x-amz-meta-'.$s3object] = $val;
			
			/* set other headers */
			if ($this->__expires) $headers['Expires'] = gmdate('D, d M Y H:i:s', $this->__expires).' GMT';
			if ($this->__type) $headers['Content-Type'] = $this->__type;
			if ($this->__md5) $headers['Content-MD5'] = $this->__md5;
			if ($this->__disposition) $headers['Content-Disposition'] = $this->__disposition;
			if ($this->__encoding) $headers['Content-Encoding'] = $this->__encoding;
			if ($this->__size) $headers['Content-Length'] = $this->__size;
			$headers['x-amz-acl'] = $this->__canned_acl;
			
			/* run the request */
			if ($this->__put($this->__bucket, $this->__name, $data, $file, $headers) == 200) return TRUE;
			else return FALSE;
		}
		
		/* delete this s3object */
		function Delete() {
			/* delete this s3object */
			if ($this->__delete($this->__bucket, $this->__name) == 204) return TRUE;
			else return FALSE;
		}
		
		/* Get ACL for this s3object */
		function ACL() {
			return new S3ACL($this->__bucket, $this->__name, $this->GetAWSKeyId(), $this->GetSecretKey());
		}
	}

	class S3ACL extends AmazonS3 {
		private $__s3object;
		private $__bucket;
		private $__owner_id;
		private $__owner_display;
		private $__xml;

		function __construct($bucket, $name, $awss3objectid, $secrets3object) {
			/* create our bucket object */
			$this->__bucket = $bucket;
			$this->__s3object = $name;
			parent::__construct($awss3objectid, $secrets3object);
			
			/* get the actual acl data */
			$this->__build_acl_from_xml($this->__acl($this->__bucket, $this->__s3object));
		}
		
		function __build_acl_from_xml($data) {
			/* parse xml */
			$xml = new SimpleXMLElement($data);
			
			$this->__xml = $xml;
			
			$this->__owner_id = (string)$xml->Owner->ID;
			$this->__owner_display = (string)$xml->Owner->DisplayName;
		}
		
		function AddGranteePermission($grantee_id, $grantee_display='', $permission=AS3_PERM_FULL) {
			$acl = $this->__xml->AccessControlList;
			$grant = $acl->addChild('Grant');
			$grantee = $grant->addChild('Grantee');
			$grantee->addAttribute('xsi', 'http://www.w3.org/2001/XMLSchema-instance', 'xmlns');
			$grantee->addAttribute('type', 'CanonicalUser', 'xsi');
			$grantee->addChild('ID', $grantee_id);
			$grantee->addChild('DisplayName', $grantee_display);
			$grant->addChild('Permission', $permission);
		}

		function RemoveGranteeByID($grantee_id) {
			$dom = dom_import_simplexml($this->__xml);
			if (!$dom) return FALSE;
			
			/* get all grant notes */
			$nodes = $dom->getElementsByTagName('Grant');
			
			/* loop through each node checking if this is the one we want removed */
			$found = FALSE;
			for($i = 0; $i < $nodes->length; $i++) {
				$node = $nodes->item($i);
				/* go through each child of $node */
				$children = $node->childNodes;
				if ($children) {
					for($j = 0; $j < $children->length; $j++) {
						$child = $children->item($j);
						if (($child->nodeName == 'ID') && ($child->nodeValue == $grantee_id)) {
							/* $node is the grantee we want to remove! */
							$node->parentNode->removeChild($node);
							$found = TRUE;
							break;
						}
					}
					if ($found) break;
				}
			}
			
			if ($found) {
				/* $dom is now the correct XML, lets convert it to a simpleXML and putit into __xml */
				$this->__xml = simplexml_import_dom($dom);
				return TRUE;
			}
			return FALSE;
		}
		
		function AddPublicPermission($permission) {
			$acl = $this->__xml->AccessControlList;
			$grant = $acl->addChild('Grant');
			$grantee = $grant->addChild('Grantee');
			$grantee->addAttribute('xsi', 'http://www.w3.org/2001/XMLSchema-instance', 'xmlns');
			$grantee->addAttribute('type', 'Group', 'xsi');
			$grantee->addChild('URL', 'http://acs.amazonaws.com/groups/global/AllUsers');
			$grant->addChild('Permission', $permission);
		}

		function RemovePublic() {
			$dom = dom_import_simplexml($this->__xml);
			if (!$dom) return FALSE;
			
			/* get all grant notes */
			$nodes = $dom->getElementsByTagName('Grant');
			
			/* loop through each node checking if this is the one we want removed */
			for($i = 0; $i < $nodes->length; $i++) {
				$node = $nodes->item($i);
				/* check if this is a group grantee node */
				$grantee = $node->getElementsByTagName('Grantee')->item(0);
				if ($grantee->attributes) {
					if ($grantee->attributes->Item(0)->nodeValue == 'Group') {
						/* $node is the grantee we want to remove! */
						$node->parentNode->removeChild($node);
					}
				}
			}
			
			/* $dom is now the correct XML, lets convert it to a simpleXML and putit into __xml */
			$this->__xml = simplexml_import_dom($dom);
			return TRUE;
		}
		
		/* store this ACL */
		function Save() {
			return $this->__put_acl($this->__bucket, $this->__s3object, $this->__xml->asXML());
		}
		
		/* return this ACL's XML representation */
		function XML() {
			return $this->__xml->asXML();
		}
	}
?>