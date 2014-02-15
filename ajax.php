<?php
/*
	License GPL v3
	
    ajax.php is part of OpenChatSupport.

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

# funcion para comprimir pagina web
function comprimir_web($buffer)
	{
    $busca = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $reemplaza = array('>','<','\\1');
    return preg_replace($busca, $reemplaza, $buffer); 
	}

ob_start("comprimir_web"); # calcular peso web y compresion

session_start();
header ('Content-type: text/html; charset=utf-8');
include( "modulos/modulos.php" );

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<meta http-equiv="Content-Type" content="text/html" charset="UTF-8">';

# chat online
if( !strcmp($_GET["my"], "chatsupport") )
	include( "chat_client.php" );

else //error, ninguno coincidio :D
	echo "No puedes usar este AJAX :O";

ob_end_flush(); # fin objeto

?>