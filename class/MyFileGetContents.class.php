<?php
/**
 *
 * MyFileGetContents
 * @author Edy Segura, edy@segura.pro.br
 *
 **/

class MyFileGetContents {
	static public function load($url) {
		if(!empty($url)) {
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			
			$contents = curl_exec($ch);
			curl_close($ch);
			
			return $contents;
		}
		
		return false;
	}
}
?>
