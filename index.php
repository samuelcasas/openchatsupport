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
	include( "modulos/modulos.php" );

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
	echo 'Good ;)... '. VERSION. ' is installed.<br>Now you can test the software...
	<p>Remember !<br>If you implement '. VERSION. ' in your CMS or Platform, please tell us, to help you in made a plugin !</p>
	<p>Contact me:<br>
	Angel Cantu <<a href="mailto:angel.cantu@sie-group.net">angel.cantu@sie-group.net</a>></p>';

	# init chat support
	# look the function to know how made more chat windows
	chat_init();
	}

echo '</body>
</html>
';

ob_end_flush();

?>