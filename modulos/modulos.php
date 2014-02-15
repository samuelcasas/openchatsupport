<?php
/*
	License GPL v3
	
    modulos.php is part of OpenChatSupport.

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

session_start();

include( "config.php" );
include( "base.php" );
include( "functions.php" );
include( "short_urls.php" );
include( "sockets.php" );
include( "chat_support.php" );

# navegador
$movil=0;
if( !strcmp( get_navegador("tipo"), "movil") ) # si es navegador movil
	$movil=1;
else if( !strcmp($_GET["ver"], "movil") )		$movil=1;

?>
