<?php
/**
  *
  * Script para busca por cep com Ajax
  * @author: Edy Segura - edy@segura.pro.br
  *
  */
if(isset($_GET["cep"])) {
	$cep = urlencode($_GET["cep"]);
	//$url = "http://cep.republicavirtual.com.br/web_cep.php?cep=$cep&formato=javascript";
	$url = "http://cep.caosdevelopers.com/web_cep.php?cep=$cep";
	
	$xmlString = @file_get_contents($url);
	
	if(!$xmlString) {
		include dirname(dirname(__FILE__)) . '/class/MyFileGetContents.class.php';
		$xmlString = MyFileGetContents::load($url);
	}
	
	if($xmlString) {
		$xml = simplexml_load_string($xmlString);
		echo json_encode($xml);
	}
}

?>