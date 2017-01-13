<?php
/*
Código reestruturado por: Daniel William Schultz
Email: hospedavip@hospedavip.com

Este código foi construido sem nenhuma reutilização de código alheio
Fique livre pra mudar este programa, redistribuir de graça, vender...
Só peço que não roube os creditos, ok? ;)

Liberado sob a licença FBPMV (Faça Bom Proveito e Modifique à Vontade)
*/

//cria variáveis vindas do post
foreach ($_POST as $key => $value) $$key = $value;

if (!$query) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Busca por CEP</title>
<style type="text/css">
<!--
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
}
.style1 {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: bold;
}
.style2 {font-size: 14px; font-family: Arial, Helvetica, sans-serif;}
-->
</style></head>

<body>
<table width="400" border="0" align="center">
  <tr>
    <td height="113" align="center"><form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <img src="postman2.jpg" width="177" height="73" vspace="10" /> <br />
      <br />
      <table width="294" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="115" height="22" valign="middle"><span class="style1">Consulte CEP: </span></td>
          <td width="179" align="left" valign="top" class="style2"><label>
            <input name="query" type="text" id="query" size="10" maxlength="8" />
          </label>            <label>ex: 36000000 </label></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td height="30" align="left"><input type="submit" name="Submit" value="Checar CEP" /></td>
        </tr>
      </table>
        </form>    </td>
  </tr>
</table>
</body>
</html>
<?php
}
else {
    // FUNÇÃO QUE GRAVA COOKIE EM VARIAVEL...MUITO MAIS FÁCIL :)
    // função encontrada em http://svetlozar.net/page/free-code.html
    // O resto, eu fiz :)

    function read_header($ch, $string)
    {
        global $location;
        global $cookiearr;
        global $ch; 
           # ^overrides the function param $ch
           # this is okay because we need to 
           # update the global $ch with 
           # new cookies
        
        $length = strlen($string);
        if(!strncmp($string, "Location:", 9))
        {
          $location = trim(substr($string, 9, -1));
        }
        if(!strncmp($string, "Set-Cookie:", 11))
        {
          $cookiestr = trim(substr($string, 11, -1));
          $cookie = explode(';', $cookiestr);
          $cookie = explode('=', $cookie[0]);
          $cookiename = trim(array_shift($cookie)); 
          $cookiearr[$cookiename] = trim(implode('=', $cookie));
        }
        $cookie = "";
        if(trim($string) == "") 
        {
          foreach ($cookiearr as $key=>$value)
          {
            $cookie .= "$key=$value; ";
          }
          curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        return $length;
    }

    //PRIMEIRO CONECTA NESTE LINK, SÓ PRA ROUBAR O COOKIE :)

    $ch= curl_init(); 
    curl_setopt($ch, CURLOPT_URL,"http://www.correios.com.br/servicos/dnec/consultaLogradouroAction.do"); 
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, "Metodo=listaLogradouro&TipoConsulta=cep&StartRow=1&EndRow=10&CEP=$query"); 
    curl_setopt($ch, CURLOPT_VERBOSE, 1); 
    curl_setopt($ch, CURLOPT_HEADER,0); // return header with output 
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'read_header');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $log1 = curl_exec($ch); 
    curl_close($ch);
    $cookie = "JSESSIONID=".$GLOBALS[cookiearr][JSESSIONID];
    $line = ereg_replace("/\n\r|\r\n|\n|\r/", "", strip_tags($log1));
    $line = preg_replace("/\t/", "", $line);
    if (strpos($line,"O CEP $query") == true) {
        die("CEP $query não encontrado");
    }

    //AGORA CONECTA NESTE, PRA PEGAR O CEP, USANDO O COKIE ROUBADO.
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.correios.com.br/servicos/dnec/detalheCEPAction.do");
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, "Metodo=detalhe&Posicao=1&TipoCep=2&CEP=$query"); 
    curl_setopt($ch, CURLOPT_COOKIE,$cookie);
    curl_setopt($ch, CURLOPT_REFERER, "http://www.correios.com.br/servicos/dnec/consultaLogradouroAction.do");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $log1 = curl_exec($ch); 
    curl_close($ch);

    $line = ereg_replace("/\n\r|\r\n|\n|\r/", "", strip_tags($log1));
    $line = preg_replace("/\t/", "", $line);

    //Logradouro
    $comeco = strpos($line,"Logradouro:");
    $fim = strpos($line,"Bairro:");
    $caracteres = $fim - $comeco;
    $rua_av = trim(substr($line, $comeco, $caracteres));

    //Bairro
    $comeco = strpos($line,"Bairro:");
    $fim = strpos($line,"Localidade / UF:");
    $caracteres = $fim - $comeco;
    $bairro = trim(substr($line, $comeco, $caracteres));

    //Localidade
    $comeco = strpos($line,"Localidade / UF:");
    $fim = strpos($line,"CEP:");
    $caracteres = $fim - $comeco;
    $localidade = trim(substr($line, $comeco, $caracteres));

    echo "
        $rua_av<br>
        $bairro<br>
        $localidade<br>
        CEP: $query
        "
        ;
}
?>  