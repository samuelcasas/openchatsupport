<?php
/*
	License GPL v3
	
    config.php is part of OpenChatSupport.

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

define( BASE_USR, "" );		# change this
define( BASE_PASS, "" );	# change this
define( SERVER, "" );		# change this
define( BASE, "" );			# change this
define( VERSION, file_get_contents("VERSION") );
define( HAPPY_URL, 1 ); # 1=enable, 0=disable
define( WWWFORCE, 0 ); # force WWW on URL (don't use on subdomain)

# scheme
if( !strcmp($_SERVER['HTTPS'], "on") )
	$parsehttp= 'https://';
else		$parsehttp= 'http://';

if( !strstr($_SERVER['HTTP_HOST'], "www.") && WWWFORCE ) # si no tiene www y esta activado el force www
	$parsehttp .= 'www.'; # ponemos www

if( !HAPPY_URL )
	define( HTTP_SERVER, "" );
else
	define( HTTP_SERVER, $parsehttp. $_SERVER['HTTP_HOST']. "/" );

?>
