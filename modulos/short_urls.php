<?php
# src.mx acortador
function src_mx( $url )
	{
	# definicion de variables
	$srcmx_api= 'src.mx';
	$srcmx_path= '/api.php'; # servidor de ap
	$jsondata["url"]= urlencode($url);
	$longurl= json_encode($jsondata); # url encodeada
	
	$r= curl_iodata( $srcmx_api, array( 'POST', $srcmx_path, $longurl, 'json' ), 80 ); # pedimos la url corta
	$a= explode( "\r\n\r\n", $r);
	$urlnew= json_decode($a[1]);
	unset($srcmx_api, $srcmx_path, $jsondata, $longurl, $url, $r, $a);
	return $urlnew->url;
	}

function is_gd( $url )
	{
	$host= 'is.gd'; # hostname
	$get= 'create.php?format=simple'; # get basico  
	$url= urlencode($url); # acondicionamos url 
	$get .= '&url='. $url; # concatenamos la url
	$puerto= 80; # puerto a consultar 
	
	# trama HTTP 
	$http_request= "GET /$get HTTP/1.1\r\n";
	$http_request .= "Host: $host \r\n";
	# $http_request .= "User-Agent: Mozilla/5.0 (X11; U; Linux i686; es-MX; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.04 (lucid) Firefox/3.6.13\r\n";
	# $http_request .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
	# $http_request .= "Accept-Language: es-MX,es;q=0.8,en-us;q=0.5,en;q=0.3\r\n";
	# $http_request .= "Accept-Encoding: gzip,deflate\r\n";
	# $http_request .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
	# $http_request .= "Keep-Alive: 115\r\n";
	# $http_request .= "Connection: keep-alive";
	$http_request .= "Connection: close\r\n";
	$http_request .= "\r\n";
	
	# consultamos 
	if( ($fd= @fsockopen($host, $puerto, $errno, $errstr, 10 ))==FALSE ) # abrimos socket
		return 0; # error, no se abrio el socket
		
	fwrite($fd, $http_request); # enviamos datos
	$buf=''; # buffer de recepcion 
	
	while( !feof($fd) ) # leeremos sockets hasta que termine  
		$buf .= fgets($fd, 2048); # leemos
	fclose($fd); # cerramos flujo  
	
	$estado='';
	preg_match("{[0-9]{3}}", $buf, $a); # obtenemos codigo recivido en transferencia
	$estado= $a[0]; # copiamos estado 
	unset($a); 
	
	$failstatus= array( "400", "406", "502", "503" ); # codigos de error en consulta 
	foreach( $failstatus as $key )
		{
		if( !strcmp($key, $estado ) ) # si son iguales
			echo 'Error en consulta: '. $estado;
			# return 0; # error en la consulta
		}
	
	$x= explode( "\r\n\r\n", $buf );
	$r= explode( "\r\n", $x[1]);
	$data= $r[1];
	return $data;
	}

?>