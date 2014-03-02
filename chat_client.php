<?php
/*
OpenChatSupport v1.0

M.S.I. Angel Cantu Jauregui <angel.cantu@sie-group.net>
Web http://www.sie-group.net/
Date	Feb 02 2014, 20:00

OpenChatSupport is a solution to implement a free tool in Support 
Service our clients. Easy and fast to install in our projects.

Have fun !

	License GPL v3
	
    chat_client.php is part of OpenChatSupport.

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

if( !strcmp($_GET["my"], "chatsupport") )
	{
	if( !strcmp($_GET["op"], "searchclient") ) # busca clientes en espera de soporte
		{
		$cons= consultar_enorden_con( "CHAT_GESTION", "ID_SOPORTE='". $_SESSION["log_id"]. "' && FECHA_END='0'", "FECHA ASC");
		if( mysql_num_rows($cons) )		# si hay ventanas
			{
			while( $buf=mysql_fetch_array($cons) )
				chat_new_window($buf["ID"]);
			}
		unset($cons);
		}
	else if( !strcmp( $_GET["op"], "sendmail") ) # enviar mail, no esta soporte
		{
		if( !$_POST["chat_nombre"] || !$_POST["chat_email"] || !strstr($_POST["chat_email"], "@") || !validar_email($_POST["chat_email"]) || !$_POST["chat_telefono"] || !$_POST["chat_msg"] || 
			!strcmp($_POST["chat_nombre"], "nombre") || !strcmp($_POST["chat_email"], "correo electronico") || 
			!strcmp($_POST["chat_telefono"], "telefono") || !strcmp($_POST["chat_msg"], "su duda?") ) # si faltan variables
			{
			echo '
				<div class="pic">
					<div class="sprite userbot"></div>
				</div>
				<div class="txt">De momento estamos fuera de la oficina, pero puedes dejar un mensaje y lo mas pronto posible nos comunicamos contigo.</div>
								
				<div id="chat_offline">
					<input type="text" value="'. desproteger_cadena($_POST["chat_nombre"]). '" onclick="if(this.value==\'nombre\') this.value=\'\';" 
					onblur="if(this.value==\'\') this.value=\'nombre\';" id="chat_nombre" name="chat_nombre"';
					if( !$_POST["chat_nombre"] || !strcmp($_POST["chat_nombre"], "nombre") )		echo ' class="red_borde"';
					echo '>
					<input type="text" value="'. desproteger_cadena($_POST["chat_email"]). '" onclick="if(this.value==\'correo electronico\') this.value=\'\';" 
						onblur="if(this.value==\'\') this.value=\'correo electronico\';" id="chat_email" name="chat_email"';
					if( !$_POST["chat_email"] || !strcmp($_POST["chat_email"], "correo electronico") || !strstr($_POST["chat_email"], "@") || 
						!validar_email($_POST["chat_email"]) )		echo ' class="red_borde"';
					echo '>
					<input type="text" value="'. desproteger_cadena($_POST["chat_telefono"]). '" onclick="if(this.value==\'telefono\') this.value=\'\';" 
						onblur="if(this.value==\'\') this.value=\'telefono\';" id="chat_telefono" name="chat_telefono"';
					if( !$_POST["chat_telefono"] || !strcmp($_POST["chat_telefono"], "telefono") ) 		echo ' class="red_borde"';
					echo '>
					<textarea onclick="if(this.value==\'su duda?\') this.value=\'\';" 
						onblur="if(this.value==\'\') this.value=\'su duda?\';" id="chat_msg" name="chat_msg"';
					if( !$_POST["chat_msg"] || !strcmp($_POST["chat_msg"], "su duda?") )		echo ' class="red_borde"';
					echo '>
					'. desproteger_cadena_src($_POST["chat_msg"]). '
					</textarea>
					<a href="javascript:;" onclick="cargar_datos(\'my=chatsupport&op=sendmail\', \'chat_box\', \'POST\', \'chat_nombre:chat_email:chat_telefono:chat_msg\', 0, 0);">
						<div class="botonsndmsg">Dejar mensaje</div>
					</a>
				</div>';
			}
		else
			{
			$data= "Nombre: ". proteger_cadena($_POST["chat_nombre"]). "\n";
			$data .= "Telefono(s): ". proteger_cadena($_POST["chat_telefono"]). "\n\n". desproteger_cadena($_POST["chat_msg"])."\n\n";
			$title= 'Soporte Offline - Nuevo Contacto';
			$navegador= get_navegador("name"). '|'. get_navegador("os"). '|'. get_navegador("tipo"); # nombre navegador
			$ip= get_ip(); # obtener IP
			do //generamos numero aleatorio de 4 a 10 digitos
				{
				$idtrack= generar_idtrack(); //obtenemos digito aleatorio
				}while( !strcmp( $idtrack, consultar_datos_general( "CHAT", "ID='". $idtrack. "'", "ID" ) ) );
			
			$form= array( "nombre"=>proteger_cadena($_POST["chat_nombre"]), 
								"mensaje"=>proteger_cadena($_POST["chat_msg"]), 
								"telefono"=>proteger_cadena($_POST["chat_telefono"]), 
								"email"=>proteger_cadena($_POST["chat_email"]) );

			$trama= array( 
				"id"=>"'". $idtrack. "'", 
				"id_soporte"=>"'chatoffline'", # id del asesor de soporte
				"navegador"=>"'". $navegador. "'", # string: navegador|sistemaoperativo|tiponavegador
				"ip"=>"'". $ip. "'", # ip del cliente
				"nombre"=>"'". $form["nombre"]. "'", # nombre cliente
				"mensaje"=>"'". $form["mensaje"]. "'", # el mensaje
				"email"=>"'". $form["email"]. "'", # correo electronico
				"telefono"=>"'". $form["telefono"]. "'",  # telefono
				"fecha"=>"'". time(). "'" # fecha
				);

			echo '
				<div class="pic">
					<div class="sprite userbot"></div>
				</div>
				<div class="txt">';
			# if( enviar_correo( $_POST["chat_nombre"]. "<". $_POST["chat_email"]. ">", $mail, $title, 0, 0, 0, $data ) )
			if( insertar_bdd("CONTACTO", $trama) )
				echo 'Gracias <b>'. desproteger_cadena($_POST["chat_nombre"]). '</b> por dejar un mensaje, pronto nos comunicamos contigo !';
			else
				echo 'Upsss... algo sucedio en el sistema, intentalo mas tarde.';
			echo '</div>';
			unset($data, $title, $form, $idtrack, $navegador, $ip);
			}
		}
	else if( !strcmp( $_GET["op"], "addmsg") && $_POST["chat_msg"] && strcmp($_POST["chat_msg"], " ") ) # agregar mensaje
		{
		# si no hay rastros de la session o por errores el asesor elimino el chat, abre Log
		if( !$_SESSION["chat"] || !consultar_datos_general("CHAT_GESTION", "SESSION='". proteger_cadena($_COOKIE["PHPSESSID"]). "'", "ID") )
			{
			$_SESSION["chat"]='on'; # encendemos chat
			
			do //generamos numero aleatorio de 4 a 10 digitos
				{
				$idtrack= generar_idtrack(); //obtenemos digito aleatorio
				}while( !strcmp( $idtrack, consultar_datos_general( "CHAT_GESTION", "ID='". $idtrack. "'", "ID" ) ) );

			if( is_login() ) # si esta conectado
				{
				$nombre= consultar_datos_general( "USUARIOS", "ID='". proteger_cadena($_SESSION["log_id"]). "'", "NOMBRE");
				$email= consultar_datos_general( "USUARIOS", "ID='". proteger_cadena($_SESSION["log_id"]). "'", "EMAIL");
				$telefono='0';
				$idcliente= $_SESSION["log_id"];
				$sender= $_SESSION["log_id"];   # id del que envia
				}
			else # es anonimo
				{
				$nombre= '0';
				$email= '0';
				$telefono='0';
				$idcliente= '0';
				}		
				
			$trama=array(
				"id"=>"'". $idtrack. "'", 
				"id_soporte"=>"'". $_SESSION["chat_asesorid"]. "'", 
				"id_cliente"=>"'". $idcliente. "'", 
				"session"=>"'". proteger_cadena($_COOKIE["PHPSESSID"]). "'", 
				"navegador"=>"'". get_navegador("name"). "/". get_navegador("os"). "'", 
				"ip"=>"'". get_ip(). "'", 
				"ubicacion"=>"'". get_geolocation(). "'", 
				"nombre"=>"'". $nombre. "'", 
				"email"=>"'". $email. "'", 
				# "telefono"=>"", 
				"fecha"=>"'". time(). "'", 
				"fecha_end"=>"'0'", 
				);
			insertar_bdd( "CHAT_GESTION", $trama); # insertamos
			unset($idtrack, $trama, $nombre, $email, $telefono, $idcliente);
			}

		do //generamos numero aleatorio de 4 a 10 digitos
			{
			$idtrack= generar_idtrack(); //obtenemos digito aleatorio
			}while( !strcmp( $idtrack, consultar_datos_general( "CHAT", "ID='". $idtrack. "'", "ID" ) ) );
			
		# verificaremos quien envia el mensaje y datos de cliente-soporte
		#
		# si es soporte, entonces ponemos el ID del cliente con el que hablamos
		if( is_soporte() )
			{
			$idsender= $_SESSION["log_id"];
			$idcliente= consultar_datos_general( "CHAT_GESTION", "ID='". proteger_cadena($_POST["chatwin"]). "'", "ID_CLIENTE"); # obtenemos id del cliente, del ID chat
			}
		else if( is_login() ) # es un cliente logeado
			{
			$idsender= $_SESSION["log_id"];
			$idcliente= $idsender;
			}
		else		# es anonimo
			{
			$idsender= '0';
			$idcliente= '0';
			}
			
		# inserta mensaje en la bdd
		$tr_msg=array(
			"id"=>"'". $idtrack. "'", # id nuevo por cada mensaje intercambiado
			"id_chat"=>"'". consultar_datos_general("CHAT_GESTION", "SESSION='". proteger_cadena($_COOKIE["PHPSESSID"]). "' && FECHA_END='0'", "ID"). "'",  # la id del gestionador
			"id_soporte"=>"'". consultar_datos_general("CHAT_GESTION", "SESSION='". proteger_cadena($_COOKIE["PHPSESSID"]). "' && FECHA_END='0'", "ID_SOPORTE"). "'",  # el asesor
			"id_cliente"=>"'". $idcliente. "'", # el cliente con el que se conversa
			"session"=>"'". proteger_cadena($_COOKIE["PHPSESSID"]). "'", # nuestra sesion 
			"mensaje"=>"'". proteger_cadena($_POST["chat_msg"]). "'", # el mensaje
			"fecha"=>"'". time(). "'", # tiempo
			"sender"=>"'". $idsender. "'"  # el que envia el mensaje, 0 => cliente
			);
		insertar_bdd( "CHAT", $tr_msg ); # insertamos

		# colocamos el mensaje (mostramos)
		echo '<div id="chat_box">
			<div class="pic">
				<div class="sprite anonimg"></div>
			</div>
			<div class="txt">'. desproteger_cadena($_POST["chat_msg"]). '</div>
		</div>';

		unset($tr_msg, $idtrack, $idcliente);
		}
	}
?>