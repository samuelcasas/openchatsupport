<?php
/*
	License GPL v3
	
    index.php is part of OpenChatSupport.

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

# compressing web (html)
function comprimir_web($buffer)
	{
    $busca = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $reemplaza = array('>','<','\\1');
    return preg_replace($busca, $reemplaza, $buffer); 
	}

ob_start( 'comprimir_web');

# date_default_timezone_set('America/Monterrey');
$offset= 3600*24;
header ('Content-type: text/html; charset=utf-8');
header( 'Expires: '. gmdate("D, d M Y H:i:s", time() + $offset). ' GMT' );
header( 'Last-Modified: '. gmdate("D, d M Y H:i:s", time()). ' GMT' );
header( 'Cache-Control: public, max-age=3600' );
header( 'Vary: Accept-Encoding' );
unset($offset);

# loading modules (other php functions)
if( BASE_USR && BASE_PASS && SERVER && BASE )
	{
	include( "modulos/modulos.php" );
	httpwwwforce();
	}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html>
	<head>
		<title>OpenChat Support - Test Index</title>
		<script language="JavaScript" type="text/javascript" src="js/script.js"></script>
		<script language="JavaScript" type="text/javascript" src="js/jquery.js"></script>
		<link href="css/estilo.css" type="text/css" rel="stylesheet">
	</head>
	
	<body>';

if( !BASE_USR || !BASE_PASS || !SERVER || !BASE )
	echo "Please try to install ". VERSION. " please made this:
	<ul>
		<li>mysql -u your_username -h your_hostname dababasename -p < mysql.db</li>
		<li>Edit file <b>config.php</b> and set the vars.</li>
	</ul>";
else
	{
	echo '
	<div id="main">
		<div class="info">
			Good ;)... '. VERSION. ' is installed.<br>Now you can test the software...
			<p>Default Accounts:<br>
			user: root, pass: free123<br>
			user: soporte, pass: free123<br>
			user: anon, pass: free123</p>
			<p>Remember !<br>If you implement '. VERSION. ' in your CMS or Platform, please tell us, to help you in made a plugin !</p>
			<p>Contact me:<br>
			Angel Cantu <<a href="mailto:angel.cantu@sie-group.net">angel.cantu@sie-group.net</a>></p>
		</div>
		<div class="login_area">';
		
		if( is_login() )
			{
			$mod= array( "1"=>"admin", "2"=>"autor", "3"=>"editor", "4"=>"coadmin", "5"=>"support", "6"=>"usuario" );
			echo 'Welcome <b>'. desproteger_cadena($_SESSION["log_usr"]). '</b><br>
			Account type: <b>'. $mod[consultar_datos_general("USUARIOS", "ID='". proteger_cadena($_SESSION["log_id"]). "'", "TIPO_USR")]. '</b>
			<a href="'. url_amigable("?id=logout", "log", "login", "out"). '">Logout</a>';
			}
		else
			{
			echo '
			<form action="'. url_amigable( "?log=entrar", "log", "login", "in"). '" method="POST" name="form">
				<ul>
					<li>User: <input type="text" value="" id="user" name="user"></li>
					<li>Pass: <input type="password" value="" id="pass" name="pass"></li>
					<li><input type="submit" value="Login"></li>
				</ul>
			</form>';
			}
	echo '</div>
		<div class="info">
		<hr>
		* The system set ONLINE=1 on db USUARIO to identified if the user is online or not (ONLINE=0).<br>
		* The system set a session CHAT=on to restore messages or set a new chat window.<br>
		* The system use the session CHAT=on to know when need to create a new insertion on db CHAT_GESTION.<br>
		* The db CHAT_GESTION identify your conversation, but not have the mssages.<br>
		* The db CHAT have the messages from your conversation, to know what messages are from you, use the ID created on db CHAT_GESTION.<br>
		* If not exists a SUPPORT User loged, the Window by Default is a contact form.<br>
		* If exists a SUPPORT User loged, the Windows by Default is a conversation :D<br>
		* The system make a random number to assign the chat window to a diferent Support User every time.<br>
		* <b>problem</b> - how to close de session ?, how to know or detect when the user isn\'t in the website ?<br>
		* <b>problem</b> - if you invoque the function <i>chat_new_window()</i> the system made a new chat window, but exist a CSS style error :\'( 
			because the first window is "upper" from the last... so wear !<br>
		</div>
	</div>
';

	# init chat support
	# look the function to know how made more chat windows
	chat_init();
	}

echo '</body>
</html>
';

ob_end_flush();

?>