<?php
/*
	License GPL v3
	
    functions.php is part of OpenChatSupport.

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

# obtener localidad del usuario
function get_geolocation()
	{
	//Geo Localizacion por IP
	# require( "admin/geoip.inc" ); //incluimos cabecera
	require( "modulos/geoipcity.inc" ); //incluimos cabecera
	include( "modulos/geoipregionvars.php" );
	$geoip_bd= geoip_open( "modulos/geoip/GeoIPLiteCity.dat", GEOIP_STANDARD ); //abrimos archivos dat
	$ip= $_SERVER['REMOTE_ADDR']; //obtenemos IP
	$r= geoip_record_by_addr( $geoip_bd, $ip );
	$ubicacion= proteger_cadena(($r->city. '/'. $GEOIP_REGION_NAME[$r->country_code][$r->region]. '/'. $r->country_name));
	geoip_close($geoip_bd);
	return $ubicacion;
	}

# forza utilizar www 
function httpwwwforce()
	{
	if( !WWWFORCE )	return;

	# si no contiene www, redirigimos
	if( !urlparser($_SERVER['HTTP_HOST'], array("www", "0")) )
		{
		$parsehttp='';

		if( !strcmp($_SERVER['HTTPS'], "on") )
			$parsehttp= 'https://';
		else		$parsehttp= 'http://';

		$parsehttp .= 'www.'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];

		header( "Location: ". $parsehttp );
	 	}
	}

# comprubea url
# arg debe ser un arreglo, el primer valor es la opcion y el segundo, si retornamos una cadena o bit de error o exito
function urlparser( $url, $arg )
	{
	/*
	Ejm: http://username:password@hostname/path?arg=value#anchor
	Array
		(
    	[scheme] => http
    	[host] => hostname
    	[user] => username
    	[pass] => password
    	[path] => /path
    	[query] => arg=value
    	[fragment] => anchor
		)
	*/
	if( !is_array($arg) )		return 0;
	if( count($arg)!=2 )		return 0;
	
	$data= parse_url($url); # parseamos
	
	if( !strcmp($arg[0], "http") ) # forzar usar http
		{
		if( !strcmp($arg[1], "1") ) # retornar cadena
			{
			if( $data['scheme'] ) # si tiene scheme
				$r= $url;
			else		$r= 'http://'. $url;
			}
		else # retorna bit
			{
			if( $data['scheme'] ) # si tiene scheme
				$r=1;
			else		$r=0;
			}
		}

	else if( !strcmp($arg[0], "https") ) # forzar usar https
		{
		if( !strcmp($arg[1], "1") ) # retornar cadena
			{
			if( $data['scheme'] ) # si tiene scheme
				$r= $url;
			else		$r= 'https://'. $url;
			}
		else # retorna bit
			{
			if( $data['scheme'] ) # si tiene scheme
				$r=1;
			else		$r=0;
			}
		}

	else if( !strcmp($arg[0], "www") ) # forzar usar www
		{
		if( !strcmp($arg[1], "1") ) # retornar cadena
			{
			if( strstr($data['path'], "www") ) # si tiene www
				$r= $url;
			else		$r= 'www.'. $url;
			}
		else # retorna bit
			{
			if( strstr($data['path'], "www") ) # si tiene www
				$r=1;
			else		$r=0;
			}
		}
	unset($data);
	return $r; # retornamos respuesta 
	}

# limpia una variable, eliminando salto de linea
function clearjump( $var )
	{
	$out='';
	if( strstr($var, "\n") ) # si existe un de linea
		$out= substr($var, 0, -1);
	else $out=$var;
	return $out;
	}

function url_amigable( $id, $titulo, $tipo, $arg )
	{
	$t= url_cleaner(desproteger_cadena($titulo));
	$url= HTTP_SERVER;
	
	if( !HAPPY_URL ) # si NO estan activadad
		return $id;
	
	if( !strcmp($tipo, "blog") )
		return HTTP_SERVER. 'blog/'. $id. '-'. $t. '.html';
	else if( !strcmp($tipo, "contenido") )
		return 'http://'. $_SERVER['HTTP_HOST']. '/hoja/'. $id. '-'. $t. '.html';
	else if( !strcmp($tipo, "menu") )
		{
		$url .=  $t. '/';

		if( strstr($arg, ",") ) # contiene delimitador(es)
			{
			$x= explode(",", $arg); # explotamos
			foreach( $x as $key ) # recorremos
				$url .= $key. '/';
			unset($x);
			}
		else if( $arg ) # entonces seccion dentro del menu
			$url .= url_cleaner(desproteger_cadena($arg)). '/';
		
		return $url;
		}
	else if( !strcmp($tipo, "users") ) # calcula la profundidad con las variables
		{
		$url .= 'user/'. $t;
		
		$x= explode( "?", $id ); # explotamos
		$y= explode( "=", $x[1] ); # explotamos 
		$url .= $y[1]; # concatenamos
		unset($x, $y );
		return $url;
		}
	else if( !strcmp($tipo, "login") ) # calcula la profundidad con las variables
		{
		$url .= $t. '/'. $arg;
		return $url;
		}
	else if( !strcmp($tipo, "script") ) # pagina o script
		{
		$x= explode( ":", $arg );
		if( !strcmp($titulo, "share") ) # si son compartidos, cambiamos titulo
			$t= 'compartir';
		else if( !strcmp($titulo, "votos") ) # si son votos, cambiamos titulo
			$t= 'votar';
		else if( !strcmp($titulo, "add") ) # si son addspot, cambiamos titulo
			$t= 'add';
		
		$url .= $t;
		
		foreach($x as $key ) # ponemos argumentos
			$url .= '/'. $key;
		return $url;
		}
	else if( !strcmp($tipo, "auto") ) # calcula la profundidad con las variables
		{
		if( $arg )
			$t2= url_cleaner(desproteger_cadena($arg));
		$url .= $t. '/';
		
		$x= explode( "&", $id ); # explotamos
		foreach( $x as $val )
			{
			$y= explode( "=", $val ); # explotamos 
			foreach($y as $key )
				$url .= $key. '/'; # concatenamos
			}
		unset($x, $y );
		if( $arg )
			$url .= $t2. '.html'; # concatenamos el titulo
		return $url;
		}
	}

function url_cleaner( $data )
	{
	$a= strtolower($data); # pasamos a minusculas 
	$s_letras= array( 'á', 'é', 'í', 'ó', 'ú', 'ñ' ); # buscar letras
	$r_letras= array( 'a', 'e', 'i', 'o', 'u', 'n' ); # sustituir letras 
	$s_signos= array( '/[^a-z0-9_]/' ); # buscar simbolos
	$r_signo= array( '_' );
	
	$a= str_replace( $s_letras, $r_letras, $a ); # re-emplazamos letras
	$a= preg_replace( $s_signos, $r_signo, $a ); # re-emplazamos signos
	
	return $a; 
	}

# limpia la cadena para usarla como 'keyword' content en meta tags, sustituye espacios por ','
# y elimina caracteres raros	
function url_cleaner_meta( $data )
	{
	$a= strtolower($data); # pasamos a minusculas 
	$s_letras= array( 'á', 'é', 'í', 'ó', 'ú', 'ñ' ); # buscar letras
	$r_letras= array( 'a', 'e', 'i', 'o', 'u', 'n' ); # sustituir letras 
	$s_signos= array( '/[^a-z0-9_]/' ); # buscar simbolos
	$r_signo= array( ',' );
	
	$a= str_replace( $s_letras, $r_letras, $a ); # re-emplazamos letras
	$a= preg_replace( $s_signos, $r_signo, $a ); # re-emplazamos signos
	
	return $a; 
	}

function currentURL()
	{
	return proteger_cadena($_SERVER["REQUEST_URI"]);
	}

# obtiene la IP del usuario actual y si esta por proxy tambien la IP de este 
function get_ip()
	{
	$ip= $_SERVER['REMOTE_ADDR']; //obtenemos IP
	
	if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
		$proxy_ip= $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if( isset($_SERVER['HTTP_VIA']) )
		$proxy_ip= $_SERVER['HTTP_VIA'];
	else 	 $proxy_ip=0;
	
	if( $proxy_ip ) # si existe IP de proxy
		return $proxy_ip. ','. $ip; # retornamos ambas IPs: IP Real, IP Proxy 
	else	return $ip; # solo IP, ya que es su IP real
	}

# detecta si una IP esta haciendo bruteforcing
function bruteforcing_detect()
	{
	$brute= 5; # limite para reincidencia de bruteforcing
	$timewait= (60*15); # esperar 15 minutos 
	$ip_src= get_ip(); # obtenemos IP
	$ip_proxy=0; # variable para IP del proxy 
	if( strstr($ip_src, ",") ) # si existe delimitador, existe Ip y proxy 
		{
		$x= explode( ",", $ip_src ); # explotamos 
		$ip= $x[0];
		$ip_proxy= $x[1];
		}
	else # solo es IP
		$ip= $ip_src; # metemos IP real/normal
		
	$cons= consultar_enorden_con( "BRUTE", "IP='". proteger_cadena($ip). "'", "FECHA DESC" );
	
	if( mysql_num_rows($cons)<$brute ) # si es menor que el limite
		{
		limpiar($cons); 
		return 0; # no hay problema, puede logearse
		} 
	else
		{
		$buf= mysql_fetch_array($cons); # obtenemos el primer valor (ultimo intento de login)

		if( ($buf["FECHA"]+$timewait) < time() ) # si ha pasado el tiempo de espera por re-incider (bruteforcing)
			{
			eliminar_bdd( "BRUTE", "IP='". proteger_cadena($ip). "'" ); # eliminamos todos los registros de esta IP
			limpiar($cons);
			return 0; # puede logearse 
			}
		limpiar($cons);
		return 1; # se detectaron intentos masivos, no prodra logearse
		} 
	}

# agregue la IP a la BDD de posibles bruteforcing
function bruteforcing_add()
	{
	$ip_src= get_ip(); # obtenemos IP

	do //generamos numero aleatorio
		{
		$idtrack= generar_idtrack(); //obtenemos digito aleatorio
		}while( !strcmp( $idtrack, consultar_datos_general( "BRUTE", "ID='". $idtrack. "'", "ID" ) ) );
	
	$ip_proxy=0; # variable para IP del proxy 
	if( strstr($ip_src, ",") ) # si existe delimitador, existe Ip y proxy 
		{
		$x= explode( ",", $ip_src ); # explotamos 
		$ip= $x[0];
		$ip_proxy= $x[1];
		}
	else # solo es IP
		$ip= $ip_src; # metemos IP real/normal 
	
	$trama= array(
		"id"=>"'". $idtrack. "'", 
		"ip"=>"'". proteger_cadena($ip). "'", 
		"ip_proxy"=>"'". proteger_cadena($ip_proxy). "'", 
		"fecha"=>"'". time(). "'" 
		);
		
	insertar_bdd( "BRUTE", $trama ); # insertamos nueva IP target que hace bruteforcing 
	unset($trama);
	}

# elimina la IP a la BDD de posibles bruteforcing
function bruteforcing_del()
	{
	$ip_src= get_ip(); # obtenemos IP
	$ip_proxy=0; # variable para IP del proxy 
	if( strstr($ip_src, ",") ) # si existe delimitador, existe Ip y proxy 
		{
		$x= explode( ",", $ip_src ); # explotamos 
		$ip= $x[0];
		$ip_proxy= $x[1];
		}
	else # solo es IP
		$ip= $ip_src; # metemos IP real/normal 
	
	# si existe la IP, entonces se elimina de la BDD de Bruteforcing
	if( ($data= consultar_datos_general("BRUTE", "IP='". $ip. "' && IP_PROXY='". $ip_proxy. "'", "ID")) )
		eliminar_bdd( "BRUTE", "ID='". $data. "'" ); # eliminamos la IP
	unset($data, $ip, $ip_proxy, $x);
	}

function acento( $var )
	{
	return '&'. $var. 'acute;';
	}

function make_seed()
	{
  	list($usec, $sec) = explode(' ', microtime());
  	return (float) $sec + ((float) $usec * 100000);
	}


function generar_idtrack()
	{
	srand(make_seed());
	$dimension = rand(4, 10);

	$arr_abc123= array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 
							'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'y', 'y', 'z' );
						
	$randval="";
	for( $i=0; $i<$dimension; $i++ )
		$randval .= $arr_abc123[rand(1, count($arr_abc123) )];

	return $randval;
	}

# genera numeros/caracteres de $data["start"] a $data["end"] segun el $data["type"] de datos (caracter, numeros, mixto)
# la longitud en base a $data["longstart"] a $data["longend"] 
function generar_idtrack_rango($data)
	{
	if( !is_array($data) )		$r=0;
	else if( count($data)!=5 )		$r=0;
	else if( !$data["start"] || !$data["end"] || !$data["type"] )		$r=0;
	else if( !is_numeric($data["start"]) || !is_numeric($data["end"]) )		$r=0;
	else
		{
		srand(make_seed());
		$dimension = rand($data["longstart"], $data["longend"]);
		
		if( !strcmp($data["type"], "mixmo") )
			{
			$arr_abc123= array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 
								'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'y', 'y', 'z' );
			}
		else if( !strcmp($data["type"], "numeros") )
			{
			$arr_abc123= array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
			}
		else if( !strcmp($data["type"], "caracteres") )
			{
			$arr_abc123= array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 
								'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'y', 'y', 'z' );
			}
		else		$arr_abc123= array();
						
		$r="";
		for( $i=0; $i<$dimension; $i++ )
			$r .= $arr_abc123[rand($data["start"], $data["end"])];
		}
	return $r;
	}

function generar_idnumber( $dato )
	{
	#if( $dato ) $maximo= $dato;
	#else $maximo= 10;
	srand(make_seed());
	#$dimension = strlen($maximo);

	#$arr_123= array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

	#return $arr_123[rand(1, count($arr_123) )];
	return $rand_val= rand(1, $dato );
	}
	
# navegador movil
function get_navegador($t)
	{
	$r=0;

	$nav= get_browser(); //navegador
	if( strcmp( $_SERVER['HTTP_REFERER'], "" ) )
		$ref= strtolower($_SERVER['HTTP_REFERER']);
	else
		$ref= strtolower($_SERVER['HTTP_HOST']);

	while( list($key, $val)=each($nav) )
		{
		if( !strcmp($key, "parent" ) )
			$navegador= $val;
		else if( !strcmp($key, "platform" ) )
			$so= $val;
		}

	if( !strcmp( $t, "all" ) ) # todo el array
		$r= $nav;
	else if( !strcmp($t, "name") ) # nombre del navegador
		$r= $navegador;
	else if( !strcmp($t, "version") ) # version del navegador
		$r= $navegador;
	else if( !strcmp($t, "os") ) # sistema operativo
		$r= $so;
	else if( !strcmp($t, "tipo") ) # tipo de navegador: movil o desktop
		{
		if( strstr(strtolower($so), "and") )					$r= 'movil';
		else if( strstr(strtolower($so), "ipho") )			$r= 'movil';
		else if( strstr(strtolower($so), "symb") )			$r= 'movil';
		else if( strstr(strtolower($so), "blackberry") )	$r= 'movil';
		else if( strstr(strtolower($so), "nok") )			$r= 'movil';
		else if( strstr(strtolower($so), "sams") )			$r= 'movil';
		else		$r= 'desktop';
		}

	unset($nav, $so, $navegador);
	return $r;
	}

//Funcion para comprobar que es un usuario logeado legitimo 
function is_login()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "ID" );
	if( !strcmp( $data, $_SESSION["log_id"] ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}
	
//Funcion para comprobar que es administrador
function is_admin()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "1" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

//Funcion para comprobar que es co-admin
function is_coadmin()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "4" ) || !strcmp( $data, "1" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

function is_autor()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "2" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

function is_editor()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "3" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

function is_soporte()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "5" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

function is_usuario()
	{
	# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soport, 6=>usuario
	
	$data= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "TIPO_USR" );
	if( !strcmp( $data, "10" ) )
		{
		unset($data);
		return 1;
		}
	unset($data);
	return 0;
	}

//Funcion para comprobar si es un robot 
function is_a_robot( $nav )
	{
	# lista de robots 
	$robots= array( 
			"google"=>"Google", 
			"slurp"=>"Yahoo",
			"yahoo"=>"Yahoo", 
			"scooter"=>"Altavista", 
			"lnktomi"=>"Altavista", 
			"baidu"=>"Baidu/China", 
			"yandex"=>"Yandex/Rusia", 
			"msn"=>"MSN Microsoft", 
			"rippers"=>"Robot Rippers", 
			"feeds blogs"=>"Feed", 
			"sogou"=>"Sogou/China", 
			"entireweb"=>"Entireweb", 
			"general crawler"=>"General Crawler" );
	
	$m= strtolower($nav); # convertimos a minusculas 
	foreach( $robots as $key=>$val )
		{
		if( !strcmp($key, $m) ) # si son iguales, es un robot 
			return 'Indexado por '. $val; # retornamos nombre del indexador 
		}
	
	return 0;
	}

//Funcion para conectarse a la pagina
function autenticacion( $log )
	{
	if( $log )
		{
		if( !strcmp($log, "entrar") )
			{
			if( !strcmp( $_GET["social"], "facebook") ) # autentificado por facebook account
				include( "autentificacion/auth_facebook.php" );
			else if( !strcmp( $_GET["social"], "twitter") ) # autentificado por facebook account
				include( "autentificacion/auth_twitter.php" );
			else
				{
				$user_site= proteger_cadena($_POST["log_usr"]);
				$pass_site= proteger_cadena($_POST["log_pass"]);
				if( bruteforcing_detect() ) # si se detecta bruteforcing
					header( "Location: ". url_amigable( "?log=brute", "log", "login", "dos_prev") );
				else if( login( $user_site, $pass_site ) )
					{
					$_SESSION["log_usr"]= proteger_cadena($_POST["log_usr"]);
					$_SESSION["log_pwd"]= proteger_cadena($_POST["log_pass"]);
					$_SESSION["log_id"]= consultar_datos_general( "USUARIOS", "NICK='". proteger_cadena($_SESSION["log_usr"]). "':PASSWORD='". proteger_cadena($_SESSION["log_pwd"]). "'", "ID" );
					bruteforcing_del(); # eliminamos la IP del Bruteforcing (si es que existe)
					if( !strcmp($_SERVER['HTTP_REFERER'], HTTP_SERVER. 'admin/index.php' ) || !strcmp($_SERVER['HTTP_REFERER'], HTTP_SERVER. 'admin/' ) )
						$ref= HTTP_SERVER. 'admin/index.php';
					else if( $_SESSION["ref"] ) # si existe referencia
						$ref= $_SESSION["ref"];
					else		  $ref= "/"; # raiz
					header( "Location: ". $ref );
					}
				else
					{
					if( $_SESSION["ref"] ) # si existe referencia
						$ref= $_SESSION["ref"];
					else		$ref= url_amigable( "?log=error", "log", "login", "error");
					header( "Location: ". url_amigable( "?log=error", "log", "login", "error") );
					}
				}
			}
		else if( !strcmp($log, "salir") )
			{
			# $trama= array(
			#		"SESION"=>"'". session_id(). "'",
			#		"FECHA_LOGOUT"=>"'". time(). "'"  
			#		);
			# actualizar_bdd( "USUARIOS", array("id"=>"'". $_SSSION["log_id"]. "'", "online"=>"'0'") );
			# unset($trama);
			actualizar_bdd( "USUARIOS", array("id"=>"'". $_SESSION["log_id"]. "'", "online"=>"'0'") );
			if( !strcmp( $_GET["social"], "facebook") ) # autentificado por facebook account
				include( "autentificacion/auth_facebook.php" );
			session_destroy();
			header( "Location: /");
			}
		}
	}
	
# traduce dias al espanol
function dias_en2es( $dato )
	{
	$mes= array( 
			"january"=>"enero", 
			"february"=>"febrero", 
			"march"=>"marzo", 
			"april"=>"abril", 
			"may"=>"mayo", 
			"june"=>"junio", 
			"july"=>"julio", 
			"august"=>"agosto", 
			"september"=>"septiembre", 
			"october"=>"octubre", 
			"november"=>"noviembre", 
			"december"=>"diciembre"
			);
	$dias= array(
			"monday"=>"lunes", 
			"tuesday"=>"martes", 
			"wednesday"=>"miercoles", 
			"thursday"=>"jueves", 
			"friday"=>"viernes", 
			"saturday"=>"sabado", 
			"sunday"=>"domingo"
			);
	
	$m= strtolower($dato);

	# recorriendo meses	
	foreach($mes as $key=>$val)
		{
		if( !strcmp($key, $m) )
			return $val;
		}

	# recorriendo dias
	foreach($dias as $key=>$val)
		{
		if( !strcmp($key, $m) )
			return $val;
		}
	
	return '';
	}

// Funcion para limpiar consulta a MySQL
function limpiar( $con )
	{
	mysql_free_result($con);
	}
	
function proteger_cadena( $cadena )
	{
	return htmlentities($cadena, ENT_QUOTES);
	}

function desproteger_cadena_src( $cadena )
	{
	return html_entity_decode($cadena, ENT_QUOTES);
	}
	
function desproteger_cadena( $cadena )
	{
	$out=$cadena;
	$out= html_entity_decode( $out, ENT_QUOTES);

	if( strchr( $out, "<" ) )
		$out= str_replace( "<", htmlentities("<", ENT_QUOTES ), $out );
	if( strchr( $out, ">" ) )
		$out= str_replace( ">", htmlentities(">", ENT_QUOTES), $out );

	if( strchr( $out, "\n" ) )
		$out= str_replace( "\n", "<br>", $out );
	if( strchr( $out, "\t" ) )
		$out= str_replace( "\t", "&nbsp;&nbsp;&nbsp;", $out );
	
	$out= utf8_encode( str_replace("|","/",$out) );
	
	return $out;
	}

function desproteger_cadena_xml( $cadena )
	{
	$out= desproteger_cadena($cadena);
	$out= utf8_decode($out);
	$arr= array( "\n"=>"<br />", "&nbsp;"=>"&#160;", "&ntilde;"=>"&#241;" );
	
	foreach( $arr as $key=>$val )
		{
		if( strstr($out, $key ) )
			$out= str_replace( $key, $val, $out );
		}
	
	$out= utf8_encode( $out );
	return $out;
	}
	
# funcion para envio de correos
function enviar_correo( $to, $asunto, $modo, $enlace, $adjunto, $from, $log, $link_custom )
	{
	//generacion de log de envio
	$boundary= md5(time()); //valor boundary
	$htmlalt_boundary= $boundary. "_htmlalt"; //boundary suplementario
	$subject=$asunto; //titulo del correo
	
	# asignando Return-Path
	if( ($aux= consultar_datos_general( "USUARIOS", "ID='4dm1n'", "EMAIL" )) )
		$extra_args= '-f'. $aux;
	else		$extra_args= NULL;

	$headers='';
	if( !strcmp( $modo, "6") || (strcmp($from, "0") && strcmp($from, "")) ) //se incluye FROM del cliente, sistema publicidad
		$headers .= "From: ". $from. "\r\n"; //correo del que lo envia
	else 
		$headers .= "From: ". TITULO_WEB. "<". consultar_datos_general("USUARIOS", "ID='4dm1n'", "EMAIL"). ">\r\n"; //correo del que lo envia

	//cabeceras para enviar correo en formato HTML
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/related; boundary=\"". $boundary. "\"\r\n"; //datos mixteados
	$headers .= 'X-Mailer: PHP\r\n';

	//incia cuerpo del mensaje que se visualiza
	$cuerpo="--". $boundary. "\r\n";
	$cuerpo .= "Content-Type: multipart/alternative; boundary=\"". $htmlalt_boundary. "\"\r\n\r\n"; //datos mixteados
	$cuerpo .="--". $htmlalt_boundary. "\r\n";
	$cuerpo .= "Content-Type: text/html; charset=UTF-8\r\n";
	$cuerpo .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
	
	$cuerpo .= '<html>
	<head>
	<style type="text/css" rel="stylesheet">
	body {margin:auto;padding:0;text-align:center;}
	#contenido{margin:auto;padding:0;text-align:left;width:800px;}
	</style>
	</head>
	<body>
	<div id=3D"contenido">';

	if( $modo==0 ) //enviar correo para comentarios
		{
		//Cuerpo o contexto del mensaje, la esencia del correo, el todo ;) 
		$cuerpo .= "Han publicado un nuevo comentario en <b>http://". $_SERVER['HTTP_HOST']. "</b>.<br>Enlace al tema: ";
		$cuerpo .= "<b></b><a href=3D\"". $enlace. "\" target=3D\"_blank\">". $enlace. "</a></b>";
		}
	else if( $modo==1 ) //error en correo para comentarios
		{
		$trama_deficiente= $_POST;
		//Cuerpo o contexto del mensaje, la esencia del correo, el todo ;) 
		$cuerpo .= "Se produjo un error en el servidor <b>http://". $_SERVER['HTTP_HOST']. "</b> al interntar enviar aviso de notificacion a las bandejas, ";
		$cuerpo .= "la notificacion se intento enviar en blanco o sin u enlace hacia la noticia donde se publico el comentario.";
		$cuerpo .= "<p>A continuacion de muestra la trama deficiente obtenida: <br>". $trama_deficiente;
		unset($trama_deficiente);
		}
	else if( $modo==2 ) //enviar correo de nuevo usuario
		{
		$cuerpo .= "Un nuevo usuario se ha registrado al sitio <b>http://". $_SERVER['HTTP_HOST']. "</b>, los datos del usuario son:";
		$cuerpo .= "<p>Usuario <b>". $enlace. "</b></p>";
		}
	else if( $modo==3 ) //enviar correo de noticia nueva
		{
		$cuerpo .= "Te escribimos para informarte que se a publicado una nueva noticia en el sitio <b>". $_SERVER['HTTP_HOST']. "</b>, ";
		$cuerpo .= "gracias por preferir nuestra comunidad y esperamos que este nuevo <b>servicio informativo</b> te sea de ayuda para enterarte ";
		$cuerpo .= "de una forma rapida y facil de las novedades de nuestro sitio web.";
		}
	else if( $modo==4 ) //enviar correo de recuperacion de datos del usuario
		{
		$cuerpo .= "<b>Sistema de Recuperacion de Datos.</b>";
		$cuerpo .= "<p>Te informamos que hemos obtenido una solicitud de datos de tu cuenta en nuestra pagina <b>". $_SERVER['HTTP_HOST']. "</b>, con motivos de ";
		$cuerpo .= "<b>recuperacion de usuario y password</b> dicha solicitud provino de:";
		$cuerpo .= "<p>IP: <b>". $_SERVER['REMOTE_ADDR']. "</b><br>";
		$cuerpo .= "Nombre Host: <b>";
		//obteniendo nombre del host
		$info= gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if( strcmp( $info, $_SERVER['REMOTE_ADDR']) )
			$cuerpo .= $info;
		else $cuerpo.= "<b>no se pudo obtener</b>";
		unset($info);
		
		$cuerpo .= "</b><br>";
		$cuerpo .= "Fecha: <b>". date( "d/m/y", time() ). " a las ". date( "g:i a", time() ). "</b>";
		$cuerpo .= "<p>Los datos solicitados referentes a tu cuenta son:";
		
		$x= explode( "|", $enlace );
		$cuerpo .= "<p><b>Username: </b>". $x[0];
		$cuerpo .= "<br><b>Password: </b>". $x[1];
		unset($x);
		}
	else if( $modo==5 ) //enviar correo de Mensaje Privado recivido
		{
		$cuerpo .= "Te escribimos para informarte que has recivido un Mensaje Privado de <b>". $enlace. "</b> en tu cuenta con <b>". $_SERVER['HTTP_HOST']. "</b>, ";
		$cuerpo .= "gracias por preferir nuestra comunidad y esperamos que este <b>servicio informativo</b> te sea de utilidad.";
		}
	else if( $modo==6 ) //publicidad, no existe cuerpo este biene explicitamente creado en $enlace
		{
		if( strcmp($log, "0") ) //si existe registo del log en la BDD
			{
			$mailing_log='<center><img src="http://'. $_SERVER['HTTP_HOST']. '/monitor.php?id='. $log. '"><center><br>';
			$cuerpo .= $mailing_log;
			unset($mailing_log);
			}
		//opciones de compartimiento en redes sociales
		$cuerpo .= '<br><center>Comparte este Anuncio:<br>';
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=digg">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/digg.png" border="0" alt="Digg" title="Digg"></a>'; # Digg
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=delicious">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/delicious.png" border="0" alt="del.icio.us" title="del.icio.us"></a>'; # del.icio.us
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=facebook">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/facebook.png" border="0" alt="Facebook" title="Facebook"></a>'; # facebook 
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=google_bookmarks">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/google_bookmarks.png" border="0" alt="Google Bookmarks" title="Google Bookmarks"></a>'; # google bookmarks
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=barrapunto">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/barrapunto.png" border="0" alt="Barrapunto" title="Barrapunto"></a>'; # Barrapunto
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=meneame">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/meneame.png" border="0" alt="Meneame" title="Meneame"></a>'; # meneame
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=technorati">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/technorati.png" border="0" alt="Technorati" title="Technorati"></a>'; # Technorati
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=twitter">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/twitter.png" border="0" alt="Twitter" title="Twitter"></a>'; # Twitter
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=yahoo_bookmarks">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/yahoo_bookmarks.png" border="0" alt="Yahoo! Bookmarks" title="Yahoo! Bookmarks"></a>'; # Yahoo! Bookmarks
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=identica">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/identica.png" border="0" alt="identi.ca" title="identi.ca"></a>'; # identi.ca
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=live">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/live.png" border="0" alt="Live" title="Live"></a>'; # Live
		$cuerpo .= '<a href="http://'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=bitacoras">
			<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/bitacoras.png" border="0" alt="Bitacoras.com" title="Bitacoras.com"></a>'; # Bitacoras.com
		# $cuerpo .= '<a href="'. $_SERVER['HTTP_HOST']. '/share.php?id='. $log. '&net=">
		#	<img src="http://'. $_SERVER['HTTP_HOST']. '/admin/imagenes/redes_sociales/" border="0" alt="" title=""></a>';
		$cuerpo .= '</center><br>';
		
		//si no ves el mail, enlace 
		//$cuerpo .= '<center>Si no puede ver la imagen da <a hre=""><b>CLICK AQUI</b></a></center><br>';
			
		if( strcmp( $link_custom, "" ) && strcmp( $link_custom, "0" ) ) //agregamos link del anuncio/contenido
			$cuerpo .= "<a href=\"http://". $_SERVER['HTTP_HOST']. "/monitor.php?id=". $log. "&x=a\">";
			
		$cuerpo .= desproteger_cadena($enlace);

		if( strcmp( $link_custom, "" ) && strcmp( $link_custom, "0" ) ) //agregamos cierre de link del anuncio/contenido
			$cuerpo .= "</a>";
		}
	else if( $modo==7 ) // el usuario manda el contenido
		$cuerpo .= $enlace;
	
	//Tag del Robot :: "No contestar al mail"
	if( $modo!=6 ) //si es distinto de publicidad, entonces se pone el tag del ROBOT
		{
		$cuerpo .= "<p>Este mensaje fue generado automaticamanete por nuestro sistema web, asi que no es necesario contestar este correo.";
		$cuerpo .= "<p>". TITULO_WEB. "<br>http://". $_SERVER['HTTP_HOST'];
		}
	else if( $modo==6 ) //agregamos pie del mensaje por SIE-Group Services
		{
		$cuerpo .= '<br><center><font color="darkblue"><b>Sistema de E-Marketing proporcionado por <a href="http://www.sie-group.net">SIE-Group</a></b></font><br>
<font color="brown"><b>... Anunciate ahora, aumenta tus ventas e incrementa tus clientes...</b></font><br>
... <a href="http://www.sie-group.net/index.php?ver=s1n3ntw5">CLICK AQUI</a> para contratar nuestros servicios ahora...<br>
... <a href="http://www.sie-group.net/index.php?ver=ofidrw5i3">CLICK AQUI</a> para darte de baja del sistema...<br>
... <b>AVISO IMPORTANTE:</b> Nuestro sistema de e-marketing se ajusta en su totalidad a la <a href="http://www.ftc.gov/bcp/edu/pubs/business/ecommerce/bus61.shtm">Ley Anti-SPAM</a> 
[<a href="http://translate.google.com.mx/translate?hl=es&langpair=en|es&u=http://www.ftc.gov/bcp/edu/pubs/business/ecommerce/bus61.shtm">Traduccion</a>]...</center>
';
		}
	
	$cuerpo .= '</body>
	</html>'; # fin de contenido
	$cuerpo .= "\r\n";
	$cuerpo .= "--". $htmlalt_boundary. "--\r\n"; // fin todo el cuerpo
	
	//archivos adjuntos
	if( strcmp($adjunto, "0") && strcmp($adjunto, "vacio") && strcmp($adjunto, "") )
		{
		set_time_limit(600);
		$archivo= $adjunto;
		$buf_type= obtener_extencion_stream_archivo($adjunto); //obtenemos tipo archivo
		
		$fp= fopen( $archivo, "r" ); //abrimos archivo
		$buf= fread( $fp, filesize($archivo) ); //leemos archivo completamente
		fclose($fp); //cerramos apuntador;
				
		$cuerpo .= "--". $boundary. "\r\n";
		$cuerpo .= "Content-Type: ". $buf_type. "; name=\"". $archivo. "\"\r\n"; //envio directo de datos
		$cuerpo .= "Content-Transfer-Encoding: base64\r\n";
		$cuerpo .= "Content-Disposition: attachment; filename=\"". $archivo. "\"\r\n\r\n";
		$cuerpo .= chunk_split(base64_encode($buf)). "\r\n\r\n";
		}
	$cuerpo .= "--". $boundary. "--\r\n\r\n"; 

	//funcion para enviar correo
	set_time_limit(600);
	if( mail($to, $subject, $cuerpo, $headers, $extra_args ) == FALSE )
		return 0;
	return 1;
	}

function validar_dominio( $dominio )
	{
	$dom='';
	# si tiene arroba, hay que validar dominio de un correo
	if( strstr( $dominio, "@") ) # si tiene arroba, hay que validar dominio de un correo
		{
		$x= explode("@", $dominio);
		$dom= $x[1]; # tomamos dominio 
		}
	else		$dom= $dominio;
	
	if( gethostbynamel($dom) ) # comprobando dominio con www
		return 1; # exito
	else if( gethostbynamel('www.'.$dom) ) # comprobando dominios sin www
		return 1;
	return 0;
	}

function validar_email( $mail )
	{
	if( !strstr($mail, "@") ) # si no contiene la arroba
		return 0;
	else
		{
		$validinbox= array("hotmail.com", "gmail.com", "yahoo.com"); #mails comerciales
		$x= explode( "@", $mail );
		
		# comprobaremos si lo que esta la izquierda del @ no tenga caracteres invalidos
		if( !$x[0] )	return 0; # si no existe dato, entonces esta erroneo el mail
		$novalid= array( "|", "/", "\\", ",", "+", "?", "\'", "(", ")", "&", "%", "$", "\"", "@", "!", "~", "{", "}", "[", "]", "<", ">" );
		foreach( $novalid as $key )
			{
			if( strstr($x[0], $key) ) # si tiene el caracter
				return 0; # error
			}
		
		# comprobamos que el dominio tenga si quiera un punto
		if( !strstr($x[1], ".") )		return 0;

		# comprobamos que no sea un proveedor de correo publico conocido 
		foreach($validinbox as $key)
			{
			if( !strcmp($key, $x[1]) )
				return 1;
			}
			
		# comprobamos lo que esta a la derecha del @
		if( !validar_dominio($x[1]) )
			return 0;
		return 1;
		}
	}
 
?>
