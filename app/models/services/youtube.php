<?php
class YoutubeService extends AbstractService {
	
	public function init() {
	    $this->name = 'YouTube';
        $this->url = 'http://youtube.com/';
        $this->service_type = 6;
        $this->icon = 'youtube.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#youtube.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.youtube.com/user/' . $username;
	}
	
	public function getDatetime($feeditem, $format = 'Y-m-d H:i:s') {
	  $pubDate = $feeditem->get_item_tags('', 'pubDate');
	  $pubDate = @$pubDate[0]['data'];
	  
	  return date($format, strtotime($pubDate));
	}
	
	public function getContent($feeditem) {
	    $content = array();
	    $raw_content = $feeditem->get_content();
	    if(preg_match('/watch\?v=(.*)"/iU', $raw_content, $matches)) {
	        $content['id'] = $matches[1];
	        $content['url'] = 'http://www.youtube.com/watch?v=' . $content['id'];
	        $content['thumb'] = 'http://i.ytimg.com/vi/' . $content['id'] . '/default.jpg';
	        $content['embedd'] = '<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/' . $content['id'] . '&hl=de&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $content['id'] . '&hl=de&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>';
        }

		return $content;
	}
	
	public function getFeedUrl($username) {
	    return 'http://www.youtube.com/rss/user/' . $username . '/videos.rss';
	}
}