<?php
/**
 * Site: fanfiction.net
 */
class Bookmark_fanfiction {

	public function get_data_fanfiction( $source ) {

		$key = 'bookmark_data_fanfiction_' . base64_encode( $source );
		$transient_value = get_transient( $key );
		if ( $transient_value === false || $transient_value == '' ) {
			$api_key = '564a78fdc520da8642fa41';
			$src = urlencode($source);
			$i = json_decode( file_get_contents("https://iframe.ly/api/iframely?url=$src&api_key=$api_key"), true );
			$i = enlarge_bookmark_thumbnail( $i );
			
			$data = array();
			$data['source'] = $i['meta']['canonical'];
			$data['site'] = 'FanFiction.net';
			$data['title'] = $i['meta']['title'];
			$data['subtitle'] = $i['meta']['subtitle'];
			$data['author'] = $i['meta']['author'];
			$data['excerpt'] = $i['meta']['description'];
			$data['thumbnail'] = $i['links']['thumbnail'][0]['href'];
			$data['icon'] = 'https://www.fanfiction.net/static/images/favicon_2010_site.png';
			
			$transient_value = json_encode( $data );
			set_transient( $key, $transient_value, (30*24*60*60) );
		}
		$json = json_decode( $transient_value, true );
		
		return $json;

	}

}