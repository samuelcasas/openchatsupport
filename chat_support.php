<?php
/*
OpenChatSupport v1.0

M.S.I. Angel Cantu Jauregui <angel.cantu@sie-group.net>
Web http://www.sie-group.net/
Date	Feb 02 2014, 20:00

OpenChatSupport is a solution to implement a free tool in Support 
Service our clients. Easy and fast to install in our projects.

Have fun !

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

# looking for something in Support Team
function get_support( $op )
	{
	if( !strcmp($op, "ison") ) # busca si esta alguien conectadp
		{
		# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soporte, 10=>usuario
		$cons= consultar_con( "USUARIOS", "TIPO_USR='5' && ONLINE='1'" ); # buscamos gente de soporte en linea
		if( !mysql_num_rows($cons) )
			$r=0;
		else
			{
			$r=1;
			limpiar($cons);
			}
		unset($cons);
		}
	else if( !strcmp( $op, "random") )	# enlaza el chat con una persona de soporte
		{
		# 1=> admin, 2=>autor, 3=>editor, 4=>coadmin, 5=>soporte, 10=>usuario
		$cons= consultar_con( "USUARIOS", "TIPO_USR='5' && ONLINE='1'" ); # buscamos gente de soporte en linea
		if( !mysql_num_rows($cons) )
			$r=0;
		else
			{
			$elasesor= generar_idtrack_rango( array("start"=>"1", "end"=>mysql_num_rows($cons), "type"=>"numeros", "longstart"=>"1", "longend"=>"1") );
			$cons= consultar_enorden_con( "USUARIOS", "TIPO_USR='5' && ONLINE='1'", "FECHA_REGISTRO DESC" );
			if( mysql_num_rows($cons) )
				{
				$asesores= array();
				while( $buf=mysql_fetch_array($cons) )
					$asesores[]= $buf["ID"];
				limpiar($cons);
				}
			$r= $asesores[($elasesor-1)];
			unset($asesores, $elasesor);
			}
		unset($cons);
		}
	return $r;
	}


# initializing chat support styles
function chat_init()
	{
echo '
<style type="text/css" rel="stylesheet">
<!--
.chatimg{margin:auto;padding:0px;background:url(../logo_9090.png) center center no-repeat;width:80px;height:80px;margin-top:10px;}
#chat_bar{position:fixed;bottom:0;left:0;background-attachment:fixed;width:100%;margin:auto;z-index:9000;}
#chat_bar #chat_widget{margin:auto;padding:0px;float:right;width:350px;margin-left:10px;}
#chat_bar #chat_widget #chat_windows{float:right;margin:0px 10px 0px 0px;padding:0px;width:350px;font-size:15px;
		box-shadow:0px 0px 10px #8c8c8c;-moz-box-shadow:0px 0px 10px #8c8c8c;-webkit-box-shadow:0px 0px 10px #8c8c8c;-o-box-shadow:0px 0px 10px #8c8c8c;-ms-box-shadow:0px 0px 10px #8c8c8c;
		border-top-left-radius:10px;border-top-right-radius:10px;position:relative;z-index:200;}

#chat_bar #chat_widget #hey{float:right;margin:auto;padding:0px;width:350px;height:120px;margin-right:10px;}
#chat_bar #chat_widget #hey .pic{float:left;margin:auto;padding:0px;text-align:center;background:#ffffff;width:90px;height:100px;border:solid 5px #4781d9;
	border-radius:60px;-moz-border-radius:60px;-webkit-border-radius:60px;-o-border-radius:60px;-ms-border-radius:60px;
	box-shadow:0px 0px 10px #000000;-moz-box-shadow:0px 0px 10px #000000;-webkit-box-shadow:0px 0px 10px #000000;-o-box-shadow:0px 0px 10px #000000;-ms-box-shadow:0px 0px 10px #000000;}
#chat_bar #chat_widget #hey .txt{float:right;margin:auto;padding:10px 20px 10px 20px;text-align:center;background:#27a9e3;width:200px;font:18px helvetica;
	border-radius:20px;-moz-border-radius:20px;-webkit-border-radius:20px;-o-border-radius:20px;-ms-border-radius:20px;color:#ffffff;
	border-bottom-left-radius:0px;-moz-border-bottom-left-radius:0px;-webkit-border-bottom-left-radius:0px;-o-border-bottom-left-radius:0px;-ms-border-bottom-left-radius:0px;
	border:solid 2px #626262;
	box-shadow:0px 0px 10px #cccccc;-moz-box-shadow:0px 0px 10px #cccccc;-webkit-box-shadow:0px 0px 10px #cccccc;-o-box-shadow:0px 0px 10px #cccccc;-ms-box-shadow:0px 0px 10px #cccccc;}

#chat_bar #chat_widget #chat_windows .tab{margin:auto;padding:10px 10px 0px 10px;background:#4781d9;float:right;
		width:330px;height:30px;z-index:190;text-align:left;
		border-top-left-radius:10px;border-top-right-radius:10px;}
#chat_bar #chat_widget #chat_windows .tab:hover{cursor:pointer;}
#chat_bar #chat_widget #chat_windows .content{margin:auto;padding:10px;float:left;width:330px;height:350px;background:#eeeeee;
		text-align:left;overflow:auto;font-size:13px;word-wrap:break-word;}
#chat_bar #chat_widget #chat_windows .input{margin:auto;padding:10px 10px 0px 10px;float:right;width:330px;height:40px;background:#ffffff;}
#chat_bar #chat_widget #chat_windows input{background:#ffffff;margin:auto;padding:0px;width:330px;height:30px;outline:none;border:none;color:#8c8c8c;}
#chat_bar #chat_widget #chat_windows ifram{margin:auto;padding:0px;outline:none;}

#chat_bar #chat_widget #chat_windows #chat_box{float:left;text-align:left;width:330px;margin-bottom:10px;word-wrap:break-word;background:none;}
#chat_bar #chat_widget #chat_windows #chat_box .pic{margin:auto;padding:0px;float:left;width:45px;height:45px;background:#ffffff;margin-right:10px;overflow:hidden;
		border-radius:20px;-moz-border-radius:20px;-webkit-border-radius:20px;-o-border-radius:20px;-ms-border-radius:20px;
		box-shadow:0px 0px 10px #cccccc;-moz-box-shadow:0px 0px 10px #cccccc;-webkit-box-shadow:0px 0px 10px #cccccc;-o-box-shadow:0px 0px 10px #cccccc;-ms-box-shadow:0px 0px 10px #cccccc;}
#chat_bar #chat_widget #chat_windows #chat_box .txt{margin:auto;padding:0px;float:left;width:270px;word-wrap:break-word;}
#chat_bar #chat_widget #chat_windows #chat_box .error{color:red;background:none;font-size:12px;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline{margin:auto;padding:0px;margin-top:10px;float:left;overflow:hidden;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline input{margin:auto;padding:5px;outline:none;margin-bottom:10px;margin-right:10px;width:315px;border:solid 2px #d3d4d6;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline textarea{margin:auto;padding:5px;outline:none;width:315px;height:50px;
	background:#ffffff;border:solid 2px #d3d4d6;color:gray;font:13px helvetica;margin-bottom:10px;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline .botonsndmsg{margin:auto;padding:10px;background:#aecf5f;border:solid 0px #9fc150;
	width:100px;border-bottom-width:2px;color:#ffffff;text-align:center;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline .botonsndmsg:hover{
	box-shadow:0px 0px 10px #cccccc;-moz-box-shadow:0px 0px 10px #cccccc;-webkit-box-shadow:0px 0px 10px #cccccc;-o-box-shadow:0px 0px 10px #cccccc;-ms-box-shadow:0px 0px 10px #cccccc;}
#chat_bar #chat_widget #chat_windows #chat_box #chat_offline .red_borde{border:solid 2px red;}
//-->
</style>
';

	echo '
	<div id="chat_bar">';
	global $idwindows;
	
	if( !is_soporte() )
		chat_new_window();
	echo '</div>

<script type="text/javascript">
$(document).ready(function(){';

foreach( $idwindows as $key=>$val )
		{
		echo '$(\'#tab_'. $val. '\').click( function(event){
				if( $(\'.tabin_'. $val. '\').is(\':hidden\') )
				{
				$(\'.tabin_'. $val. '\').css( "display", "block");
				$(\'#hey\').css( "display", "none");
				}
			else
				{
					$(\'.tabin_'. $val. '\').css( "display", "none");
				$(\'#hey\').css( "display", "block");
				}
			});';
		}

if( is_soporte() )		# buscador de ventanas para soporte
	{
	echo '
	setInterval( function()	{
			cargar_datos(\'my=chatsupport&op=searchclient\',\'chat_bar\',\'GET\',\'chat_msg\', \'1\', \'1\');
		}, 1000);';
	}

echo '});
</script>

';

	}


# make new chat window
function chat_new_window()
	{
	global $idwindows;		# contador de ventanas creadas
	$id= generar_idtrack();
	$idwindows[]= $id;

	echo '
		<div id="chat_widget">
			<div id="hey">
				<div class="pic"><div class="chatimg"></div></div>
				<div class="txt">
					Te puedo apoyar en algo ?<br>
					Estaremos aqu'. acento("i"). ' para atenderle !
				</div>
			</div>
			<div id="chat_windows">
				<div id="tab_'. $id. '" class="tab">En que te puedo ayudar ?</div>
					<div class="tabin_'. $id. '" style="display:none;">
					<div class="content" id="chat_messages">
							<div id="chat_box"">';
						if( get_support( "ison" ) )
							{
							# si no se le ha asignado un asesor a la session actual
							if( !$_SESSION["chat_asesorid"] )
								{
								# buscamos si estuvo antes aqui
								$cons= consultar_enorden_con( "CHAT", "ID_CHAT='". consultar_datos_general("CHAT_GESTION", "SESSION='". proteger_cadena($_COOKIE["PHPSESSID"]). "' && FECHA_END='0'", "ID"). "'", "FECHA DESC");
								if( mysql_num_rows($cons) ) # si estuvo aqui
									{
									$buf=mysql_fetch_array($cons);
									$_SESSION["chat_asesorid"]= $buf["ID_SOPORTE"]; # reasignamos el mismo asesor
									limpiar($cons);
									unset($buf);
									}
								else		 # no ha estado aqui
									$_SESSION["chat_asesorid"]= get_support( "random" );		# asignamos asesor aleatorio
								}
						
							# si hay variable chat entonces se le cerro el navegador y es necesario imprimirle todos los mensaje de nuevo
							if( $_SESSION["chat"] )
								{
								$cons= consultar_enorden_con( "CHAT", "ID_CHAT='". consultar_datos_general("CHAT_GESTION", "SESSION='". proteger_cadena($_COOKIE["PHPSESSID"]). "' && FECHA_END='0'", "ID"). "'", "FECHA DESC");
								
								if( !mysql_num_rows($cons) ) # no hay mensajes, se cerro antes de intercambiar
									{
									$nick= consultar_datos_general( "USUARIOS", "ID='". $_SESSION["chat_asesorid"]. "'", "NICK");
									$avatar= consultar_datos_general( "USUARIOS", "ID='". $_SESSION["chat_asesorid"]. "'", "AVATAR");
									$tipo= '_mini.jpg';
									echo '
									<div class="pic">';
									if( !$avatar )
										echo '<div class="sprite anonimg"></div>';
									else
										echo '<img src="'. HTTP_SERVER.substr($avatar, 0, -4).$tipo. '" border="0">';
									echo '</div>
									<div class="txt"><b>'. desproteger_cadena($nick). '</b>: Tienes alguna duda ?, estar'. acento("e"). ' aqui para apoyarte en cualquier cosa !.</div>';
									}
								else 		# recperando los mensajes
									{
									while( $buf=mysql_fetch_array($cons) )
										{
										if( $buf["SENDER"] ) # si hay Id del que envia
											{ 
											$nick= consultar_datos_general( "USUARIOS", "ID='". $buf["SENDER"]. "'", "NICK");
											$avatar= consultar_datos_general( "USUARIOS", "ID='". $_SESSION["chat_asesorid"]. "'", "AVATAR");
											}
										else	# es un visitante 
											{
											$nick= 'Visitante:';
											$avatar=0;
											}

										echo '
											<div class="pic">';
											if( !$avatar )
												echo '<div class="sprite anonimg"></div>';
											else
												echo '<img src="'. $avatar. '" border="0">';
											echo '</div>
											<div class="txt"><b>'. desproteger_cadena($nick). '</b>: '. desproteger_cadena($buf["MENSAJE"]). '</div>';
										}
									limpiar($cons);
									}
								}
							else
								{
								$nick= consultar_datos_general( "USUARIOS", "ID='". $_SESSION["chat_asesorid"]. "'", "NICK");
								$avatar= consultar_datos_general( "USUARIOS", "ID='". $_SESSION["chat_asesorid"]. "'", "AVATAR");
								$tipo= '_mini.jpg';
								echo '
								<div class="pic">';
								if( !$avatar )
									echo '<div class="sprite anonimg"></div>';
								else
									echo '<img src="'. HTTP_SERVER.substr($avatar, 0, -4).$tipo. '" border="0">';
								echo '</div>
								<div class="txt"><b>'. desproteger_cadena($nick). '</b>: Tienes alguna duda ?, estar'. acento("e"). ' aqui para apoyarte en cualquier cosa !.</div>';
								}
							}
						else
							{
							echo '
								<div class="pic">
									<div class="sprite userbot"></div>
								</div>
								<div class="txt">De momento estamos fuera de la oficina, pero puedes dejar un mensaje y lo mas pronto posible nos comunicamos contigo.</div>
								
								<div id="chat_offline">
									<input type="text" value="nombre" onclick="if(this.value==\'nombre\') this.value=\'\';" 
										onblur="if(this.value==\'\') this.value=\'nombre\';" id="chat_nombre" name="chat_nombre">
									<input type="text" value="correo electronico" onclick="if(this.value==\'correo electronico\') this.value=\'\';" 
										onblur="if(this.value==\'\') this.value=\'correo electronico\';" id="chat_email" name="chat_email">
									<input type="text" value="telefono" onclick="if(this.value==\'telefono\') this.value=\'\';" 
										onblur="if(this.value==\'\') this.value=\'telefono\';" id="chat_telefono" name="chat_telefono">
									<textarea onclick="if(this.value==\'su duda?\') this.value=\'\';" 
										onblur="if(this.value==\'\') this.value=\'su duda?\';" id="chat_msg" name="chat_msg">
									su duda?
									</textarea>
									<a href="javascript:;" onclick="cargar_datos(\'my=chatsupport&op=sendmail\', \'chat_box\', \'POST\', \'chat_nombre:chat_email:chat_telefono:chat_msg\', 0, 0);">
										<div class="botonsndmsg">Dejar mensaje</div>
									</a>
								</div>';
							}
						echo '</div>
					</div>';

					if( get_support( "ison" ) )
						{
						echo '<div class="input">
						<input type="text" name="chat_msg" id="chat_msg" value="Escribe algo..." onclick="if(this.value==\'Escribe algo...\') this.value=\'\';" onblur="if( this.value==\'\') this.value=\'Escribe algo...\';" 
						onkeyup="detectar_entradas( \'chat_msg\', event, \'chat_addmsg\', \''. currentURL(). '\', \'1\', \'1\' );">
						</div>';
						}
				echo '</div>
			</div>
		</div>
';
	unset($id);
	}
?>