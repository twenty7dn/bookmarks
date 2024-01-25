<?php
/**
 * Site: goodreads.com
 */
class Bookmark_goodreads {

	public function get_data_goodreads( $source ) {

		$key = 'bookmark_data_goodreads_' . base64_encode( $source );
		$transient_value = get_transient( $key );
		if ( $transient_value === false || $transient_value == '' ) {
			$api_key = '564a78fdc520da8642fa41';
			$src = urlencode($source);
			$i = json_decode( file_get_contents("https://iframe.ly/api/iframely?url=$src&api_key=$api_key"), true );
			
			$scraped = $this->scraper( $source );
			
			$data = array();
			$data['source'] = $i['meta']['canonical'];
			$data['site'] = 'Goodreads';
			$data['title'] = $scraped['title'];
			$data['subtitle'] = $scraped['subtitle'];
			$data['author'] = $scraped['author'];
			$data['excerpt'] = $scraped['excerpt'];
			$data['thumbnail'] = $i['links']['thumbnail'][0]['href'];
			$data['icon'] = $i['links']['icon'][0]['href'];
			
			if ( strpos( $i['links']['thumbnail'][0]['href'], 'nophoto' ) !== false )
				$data['thumbnail'] = null;
			
			$transient_value = json_encode( $data );
			set_transient( $key, $transient_value, (30*24*60*60) );
		}
		$json = json_decode( $transient_value, true );
		
		return $json;

	}

	private function scraper( $source ) {

		$dom = new DOMDocument('4.0', 'UTF-8');
		$dom->loadHTML(file_get_contents($source));
		
		$title = $dom->getElementById('bookTitle');
		$title = wp_strip_all_tags( $title->nodeValue, true );
		$title = trim( preg_replace('/[\t\n\r\s]+/', ' ', $title ) );
		
		$xpath = new DOMXPath($dom);
		foreach ($xpath->query('//a[@class="authorName"]/node()') as $node) {
			$author = $dom->saveHTML($node);
			break;
		}
		$author = wp_strip_all_tags( $author, true );
		$author = trim( preg_replace('/[\t\n\r\s]+/', ' ', $author ) );
		
		$summary = '';
		foreach ($xpath->query('//div[@id="description"]/node()') as $node) {
			$summary .= $dom->saveHTML($node);
		}
		$breaks = array("<br />","<br>","<br/>");  
		$summary = str_ireplace( $breaks, "\r\n", $summary ); 
		$summary = wp_strip_all_tags( $summary, false );
		$prefixes = array( '...more', '(less)' );
		$summary = str_replace( $prefixes, '', $summary );
		$summary = trim( $summary );
		$summary = htmlentities( utf8_decode( $summary ) );
		
		$series = $dom->getElementById('bookSeries');
		if ( isset( $series ) ) {
			$series = str_replace( ' #', ', Book ', trim( trim( htmlentities( utf8_decode( $series->nodeValue ) ) ), '()' ) );
		} else {
			$series = null;
		}
		
		$data = array(
			'title' => $title,
			'subtitle' => $series,
			'author' => $author,
			'excerpt' => $summary
		);
		return array_filter( $data );

	}

}