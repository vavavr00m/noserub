<?php
// class name starts with '_' as it is not allowed to use a number as first character
class _23hqService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#23hq.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.23hq.com/'.$username;
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        #<a href="http://www.23hq.com/DonDahlmann/photo/2204674
        if(preg_match('/<a href="http:\/\/www.23hq.com\/.*\/photo\/.*<\/a>/iU', $raw_content, $matches)) {
            $content = str_replace('standard', 'quad100', $matches[0]);
            $content = preg_replace('/width="[0-9]+".+height="[0-9]+"/i', '', $content);
            return $content;
        }
        return '';
	}
	
	public function getFeedUrl($username) {
		return 'http://www.23hq.com/rss/'.$username;
	}
}