<?php
/**
 * Site: equestriadaily.com
 */
class Bookmark_equestriadaily {

	public function get_data_equestriadaily( $source ) {

		$key = 'bookmark_data_eqd_' . base64_encode( $source );
		$transient_value = get_transient( $key );
		if ( $transient_value === false || $transient_value == '' ) {
			$api_key = '564a78fdc520da8642fa41';
			$src = urlencode($url);
			$i = json_decode( file_get_contents("https://iframe.ly/api/iframely?url=$src&api_key=$api_key"), true );
			
			$data = array();
			$xml = new DOMDocument();
			$xml->loadHtml(file_get_contents($source));
			$xpath = new DOMXPath($xml);
	
			$data['source'] = $i['meta']['canonical'];
			$data['site'] = 'Equestria Daily';
			foreach ($xpath->query('//h3[contains(@class, "post-title") and contains(@class, "entry-title")]/*') as $node) {
				$data['title'] = trim( strip_tags( $xml->saveXML($node) ) );
			}
			$data['subtitle'] = '';
			foreach ($xpath->query('//a[@class="g-profile"]/*') as $node) {
				$data['author'] = trim( strip_tags( $xml->saveXML($node) ) );
			}
			$data['excerpt'] = '';
			$data['icon'] = $i['links']['icon'][0]['href'];
			
			$transient_value = json_encode( $data );
			set_transient( $key, $transient_value, (30*24*60*60) );
		}
		$json = json_decode( $transient_value, true );
		
		return $json;

	}
	
}