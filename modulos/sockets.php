<?php
/*
	License GPL v3
	
    sockets.php is part of OpenChatSupport.

    OpenChatSupport is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenChatSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

# obtiene datos por socket, segun peticion POST o GET
# data = array( stream, argumentos, datos_post );
function socket_iodata( $host, $data, $port )
	{
	$r='';
	
	$http_request  = "$data[0] $data[1] HTTP/1.0\r\n";
	$http_request .= "Host: $host\r\n";
	if( !strcmp($data[0], "POST") ) # si es post
		{
		if( $data[3] && !strcmp($data[3], "json") ) # datos json
			$http_request .= "Content-Type: application/json;\r\n";
		else
			$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($data[2]) . "\r\n";
		}
	#$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
	# $http_request .= "Connection: keep-alive\r\n";
	$http_request .= "\r\n";
	if( !strcmp($data[0], "POST") ) # si es post
		$http_request .= $data[2];
		
	if( !strcmp($port, "443") )
		$fullhost= 'ssl://'. $host;
	else		$fullhost= $host;
		
	if( ($fs = @fsockopen($fullhost, $port, $errno, $errstr, 10))==FALSE )
		{
		echo 'No se puede abrir socket :: ['. $errno. '] '. $errstr;
		}
	else
		{
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$r .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$r= explode("\r\n\r\n", $r, 2);
		return $r;
		}
	}

# obtiene datos por socket, segun peticion POST o GET
# data = array( stream, argumentos, datos_post );
function curl_iodata( $host, $data, $port )
	{
	$r='';
	if( !strcmp($port, "443") ) #puerto seguro
		$fullhost= 'https://'. $host;
	else		$fullhost= $host;
	$curl= curl_init(); # inciamos url
	curl_setopt($curl, CURLOPT_URL, $fullhost.$data[1] );
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt($curl, CURLOPT_TIMEOUT, 30 );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $data[0] );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE );
	if( !strcmp($data[0], "POST") ) # si es post
		{
		curl_setopt($curl, CURLOPT_HEADER, 1 );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		
		if( $data[3] && !strcmp($data[3], "json") ) # datos json
			$contenido= array( 'Content-Type: application/json' );
		else if( $data[3] && !strcmp($data[3], "oauth") ) # datos oauth
			$contenido= array( 'User-Agent: OpenChatSupport/PHP', 'Accept: */*', 'Authorization: OAuth '. $data[2] );
		else		$contenido= array( 'Content-Type: application/x-www-form-urlencoded', 'Content-Length: '. strlen($data[2]));

		if( $data[4] && !strcmp($data[3], "oauth") ) # datos oauth
			{
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array("Expect:") );
			$contenido[]= 'Content-Length: '. strlen($data[4]);
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data[4] );
			}
		else if( !$data[4] && !strcmp($data[3], "oauth") ) # datos oauth
			{
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array("Expect:") );
			$contenido[]= 'Content-Length: 0';
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data[4] );
			}	
		else
			{
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $contenido );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data[2] );
			}
		curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
		}
	$r= curl_exec($curl);
	curl_close($curl);
	return $r;
	}
?>