<?php
/*
	License GPL v3
	
    base.php is part of OpenChatSupport.

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


//realiza conexion a la Base de Datos
function conectar()
	{
	if( !($link= mysql_connect( "". SERVER. "", "". BASE_USR. "", "". BASE_PASS. "" )) )
		{
		echo "<span id=\"letras_error\">Error 01: Error para Conectarse a MySQL.<br>";
		echo "Error en Usuario y/o Contrase&ntilde;a.<br>";
		echo mysql_error($link). "</span>";
		$link= "INSTALL";
		}
	else if( !mysql_select_db( "". BASE. "", $link ) )
		{
		echo "<span id=\"letras_error\">Error 02: Problemas para Tomar la Base de Datos.<br>";
		echo "No se Encuentra la Base de Datos.<br>";
		echo mysql_error($link). "</span>";
		$link= "INSTALL";
		}
	return $link;
	}

//consulta multiples valores a una Base de Datos. Donde los valores van delimitados
//por un ":" xD
function consultar( $base_t, $valores )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		$valores= str_replace( ":", ",", $valores ); //cambiamos el :  por  ,
		
		if( !($resp= mysql_query( "select ". $valores. " from ". $base_t. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( !strcmp( $valores, "*" ) )
		{
		if( !($resp= mysql_query( "select * from ". $base_t. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( $valores ) //entonces solo se desea consulta 1 valor
		{
		if( !($resp= mysql_query( "select ". $valores. " from ". $base_t. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}


//realizar consulta donde escojeremos el o las celdas de una tabla que
//coincidan con (where) los valores pasados :D. Donde los valores estan
//delimitados por un ":".
function consultar_con( $base_t, $valores )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		$valores= str_replace( ":", " && ", $valores );
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( $valores ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}


//consultamos en ordenacion mediante '$regla'
function consultar_enorden( $base_t, $regla )
	{
	$link= conectar();
	
	if( !($resp= mysql_query( "select * from ". $base_t. " ORDER BY ". $regla. ";", $link )) )
		{
		echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
		echo mysql_error(). "</span>";
		}
	else
		{
		@mysql_close($link);
		return $resp;
		}
	
	@mysql_close($link);
	return "ERROR";
	}

//consultamos valores delimitados por un ":", en ordenacion mediante '$regla'
function consultar_enorden_con( $base_t, $valores, $regla )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		$valores= str_replace( ":", " && ", $valores );
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " ORDER BY ". $regla. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( $valores ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " ORDER BY ". $regla. ";", $link )) )		
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

function consultar_rango_enorden_con( $base_t, $args, $valores, $fechainicio, $fechafin, $regla )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		$valores= str_replace( ":", " && ", $valores );
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " BETWEEN '". $fechainicio. "' AND '". $fechafin. "' AND ". $args. " ORDER BY ". $regla. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( $valores ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " BETWEEN '". $fechainicio. "' AND '". $fechafin. "' AND ". $args. " ORDER BY ". $regla. ";", $link )) )		
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

function consultar_rango_enorden( $base_t, $valores, $fechainicio, $fechafin, $regla )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		$valores= str_replace( ":", " && ", $valores );
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " BETWEEN '". $fechainicio. "' AND '". $fechafin. "' ORDER BY ". $regla. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else if( $valores ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valores. " BETWEEN '". $fechainicio. "' AND '". $fechafin. "' ORDER BY ". $regla. ";", $link )) )		
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

//consultando limitada, ordenada, donde coinsidiran las variables
function consultar_rango_limite_enorden_con( $base_t, $vars, $limite, $fechainicio, $fechafin, $regla )
	{
	$link= conectar();
	
	if( strchr( $limite, "," ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		$vars= str_replace( ":", " && ", $vars ); //cambiamos el :  por  &&
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $vars. " BETWEEN '". $fechainicio. "' AND '". $fechafin. "' ORDER BY ". $regla. " LIMIT ". $limite. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}
	
function consultar_indexados( $base_t, $valor, $muestra )
	{
	$link= conectar();
	
	if( strchr( $valor, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
		echo mysql_error(). "</span>";
		}
	else if( $valor ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valor. " LIKE '%". $muestra. "%';", $link )) )		
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

function consultar_indexados_enorden( $base_t, $valor, $muestra, $orden )
	{
	$link= conectar();
	
	if( strchr( $valor, ":" ) ) //si se encuentra   :   entonces ahi mas de 1 valor
		{
		echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
		echo mysql_error(). "</span>";
		}
	else if( $valor ) //solo se desea comparar con 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $valor. " LIKE '%". $muestra. "%' ORDER BY ". $orden. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}


//consultamos un rango de celdas mediante la instruccion LIMIT y los valores de "Inicio", "Fin"
function consultar_limite( $base_t, $valores )
	{
	$link= conectar();
	
	if( strchr( $valores, ":" ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		$valores= str_replace( ":", ",", $valores ); //cambiamos el :  por  ,
		
		if( !($resp= mysql_query( "select * from ". $base_t. " LIMIT ". $valores. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

//consultamos un rango de celdas mediante la instruccion LIMIT y los valores de "Inicio", "Fin"
function consultar_limite_con( $base_t, $vars, $valores )
	{
	$link= conectar();
	
	if( strchr( $valores, "," ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $vars. " LIMIT ". $valores. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

function consultar_limite_enorden( $base_t, $limite, $regla )
	{
	$link= conectar();
	
	if( strchr( $limite, "," ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		if( !($resp= mysql_query( "select * from ". $base_t. " ORDER BY ". $regla. " LIMIT ". $limite. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}

//consultando limitada, ordenada, donde coinsidiran las variables
function consultar_limite_enorden_con( $base_t, $vars, $limite, $regla )
	{
	$link= conectar();
	
	if( strchr( $limite, "," ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		$vars= str_replace( ":", " && ", $vars ); //cambiamos el :  por  &&
		
		if( !($resp= mysql_query( "select * from ". $base_t. " where ". $vars. " ORDER BY ". $regla. " LIMIT ". $limite. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}
	
	

//insercion de valores a una tabla
function insertar_bdd( $base_t, $valores )
	{
	$link= conectar();
	
	if( count($valores)>0 ) //entonces existen valores en el array
		{
		$vars="";
		$datos="";
		$i=0;

		while( list($a, $b)=each($valores) )
			{
			$i++;

			$vars .= $a;
			$datos .= $b;
		
			if( $i!=count($valores) )
				{
				$datos .= ", ";
				$vars .= ", ";
				}
			}

		if( !($resp= mysql_query( "insert into ". $base_t. " ( ". $vars. " ) values( ". $datos. " );", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			unset( $valores );
			unset($a);
			unset($b);
			unset($i);
			unset( $var );
			unset( $datos );
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}


//actualizar valores existentes de un campos de la tabla
function actualizar_bdd( $base_t, $valores )
	{
	$link= conectar();
	
	if( count($valores)>0 ) //existen valores en el array
		{
		$condicion= "";
		$datos="";
		$i=0;

		while( list($a, $b)=each($valores) )
			{
			$i++;
			if( $i==1 )
				$condicion .= $a. "=". $b;
			else
				{
				$datos .= $a. "=". $b;
			
				if( $i!=count($valores) )
					$datos .= ", ";
				}
			}

		if( !($resp= mysql_query( "update ". $base_t. " set ". $datos. " where ". $condicion. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			unset( $datos );
			unset($i);
			unset($a);
			unset($b);
			@mysql_close($link);
			return $resp;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return "ERROR";
	}


//elimina una celda de la tabla
function eliminar_bdd( $base_t, $valores )
	{
	$link= conectar();
	
	if( strchr( $valores, "=" ) ) //si existe el  :  tons ahi mas de 1 valor
		{
		$resp= mysql_query( "select * from ". $base_t. " where ". $valores. ";", $link );
		if( mysql_num_rows($resp) == 0 )
			return 0;

		if( !(mysql_query( "delete from ". $base_t. " where ". $valores. ";", $link )) )
			{
			echo "<span id=\"letras_error\">Error 03: Problema para Realizar Movimiento/Consulta.";
			echo mysql_error(). "</span>";
			}
		else
			{
			unset( $resp );
			@mysql_close($link);
			return 1;
			}
		}
	else
		echo "<span id=\"letras_error\">Error 04: Problema para Desifrar Datos para Realizar Consulta.</span>";
	
	@mysql_close($link);
	return 0;
	}

//contabilisa el numero de celdas existentes en la tabla '$cad_tabla'
function contador_celdas( $cad_tabla )
	{
	$resp= consultar( $cad_tabla, "*" );
	
	if( $resp=="ERROR" )
		return $resp;
	return mysql_num_rows($resp);
	}

//contabilizada el numero de celdas de una tabla '$cad_tabla' que contienen algun valor en la variable/celda/miembro '$var'
function contador_datos_tabla( $cad_tabla, $var )
	{
	$total=0; //inicializamos
	$cont=0;
	if( ($total=contador_celdas( $cad_tabla ))>0 ) //contabilizamos todas las celdas
		{
		$cons= consultar_con( $cad_tabla, $var );
		if( mysql_num_rows($cons)<$total ) //entonces existen registros con imagenes
			{
			unset($cons); //borramos variable
			$cons= consultar( $cad_tabla, "*" ); //consultamos todos los registros

			while( $filtro=mysql_fetch_array($cons) )
				{
				if( strcmp( $filtro["IMAGENES_NOMBRE"], "" ) ) //filtramos solo dodne existan datos
					{
					if( strchr( $filtro["IMAGENES_NOMBRE"], ":" ) ) //si existe delimitador ":"
						{
						$x= explode( ":", $filtro["IMAGENES_NOMBRE"] );
						$cont += sizeof($x); //contamos los nuevos registros
						}
					else //entonces solo existe una sola imagen
						$cont++; //incrementamos en 1
					}
				}

			unset($filtro);
			unset($x);
			unset($cons);
			return ($cont+1);
			}
		else //entonces no existen registros a contar, por default es la primer imagen
			{
			unset($cons);
			return ($cons+1);
			}
		}
	unset($total);
	return ($total+1);
	}

//realiza concatenacion de variables, llama a la funcion para comprobar existencia
//de un usuarios y retorna un valor 0 o 1.
function login( $user, $pass )
	{
	$link= conectar();
	$r=0;
	$cons= consultar_con( "USUARIOS", "NICK='". $user. "' && PASSWORD='". $pass. "'" ); //realizamos consulta
	
	if( mysql_num_rows($cons) )
		{
		$arr= mysql_fetch_array( $cons ); //metemos el valor al array

		if( $user==$arr["NICK"] && $pass==$arr["PASSWORD"] ) //comprobamos que sea el correcto
			{
			actualizar_bdd( "USUARIOS", array("id"=>"'". $arr["ID"]. "'", "online"=>"'1'") );
			$r= 1; # exito xD
			}
		limpiar($cons);
		}
	
	@mysql_close( $link );
	unset( $var, $cons, $arr );
	if( $r==0 )			bruteforcing_add();
	return $r; //entonces la consulta retorno 0
	}

//funcion para consultar datos especificos en la BDD de USUARIOS
//estos datos pueden ser: email, nick, nombre, tipo_usuario
function consultar_datos_usuario( $usr, $var )
	{
	$cons= consultar_con( "USUARIOS", "NICK='". $usr. "'" );
	$data= mysql_num_rows($cons);
	
	if( $data )
		{
		$tmp= mysql_fetch_array($cons);
		limpiar($cons);
		unset($data);
		return $tmp[strtoupper($var)];
		}
	unset($data);
	limpiar($cons);
	return 0;
	}

//funcion para consultar datos especificos en la BDD de CUALQUIERA, es necesario especificar ID
function consultar_datos_base( $bdt, $bdt_id, $var )
	{
	$cons= consultar_con( $bdt, "ID='". $bdt_id. "'" );
	
	if( mysql_num_rows($cons)==0 )
		return 0;
	else
		{
		$tmp= mysql_fetch_array($cons);		
		return $tmp[strtoupper($var)];
		}
	}

//funcion para consultar datos especificos en la BDD de CUALQUIERA
function consultar_datos_general( $bdt, $bdt_where, $var )
	{
	$cons= consultar_con( $bdt, $bdt_where );
	$data= mysql_num_rows($cons);
	if( $data )
		{
		$tmp= mysql_fetch_array($cons);
		unset($data);
		limpiar($cons);		
		return $tmp[strtoupper($var)];
		}
	unset($data);
	limpiar($cons);
	return 0;
	}
	
function contador_concatenados( $valores, $delimitador )
	{
	if( strchr($valores, $delimitador) )
		{
		$x= explode($delimitador, $valores);
		
		return sizeof($x);
		}
	else	return 1;
	}
	
function usuario_legitimo()
	{
	//concatenamos
	$var= "nick=";
	$var .= "'". proteger_cadena($_SESSION["log_usr"]). "'";	
	$var .= ":password=";
	$var .= "'". proteger_cadena($_SESSION["log_pwd"]). "'";

	$cons= consultar_con( "USUARIOS", $var ); //consultamos los datos de la cookie

	if( mysql_num_rows( $cons )==0 ) //entonces la SESSION es FALSA
		return 0;

	else //la SESSION es correcta y mostramos PANEL y mas Cosas del Usuario
		return 1;
	}
	
	
function inicializar_espacio_personal( $nick )
	{
	$dir_src="usuarios/";
	$tree= array( 'archivos', 'imagenes', 'uploads', 'buzon' ); # directorio

	if( mkdir( $dir_src.$nick, 0755 ) == FALSE )
		echo "<b>Error:</b> Al crear directorio del usuario"; //creamos carpeta con nombre del usuarui
	else
		{
		foreach( $tree as $key )
			{
			if( mkdir( $dir_src.$nick.'/'.$key, 0755 )==FALSE ) //creamos carpeta de 'archivos'
				echo '<center><div id="letras_error">Error: al crear directorio de usuario \''. $key. '\'</div></center>';
			}
		return 1;
		}
	#else if( copy( "admin/imagenes/default.png", $dir_src.htmlentities(proteger_cadena($nick), ENT_QUOTES)."/imagenes/default.png" )==FALSE ) //copiamos la imagen por default de su perfil
	#	echo "<i>Error al crear directorio del usuario</i>";
	
	return 0; //el usuario ya existe
	}

function elimina_espacio_personal( $nick )
	{
	$dir_src="../usuarios/";
	$tree= array( 'archivos', 'imagenes', 'uploads', 'buzon' ); # directorio

	foreach( $tree as $key )
		{
		if( rmdir( $dir_src.$nick.'/'.$key )==FALSE ) // eliminamos carpeta de 'archivos'
			echo '<center><div id="letras_error">Error: al eliminar directorio de usuario \''. $key. '\'</div></center>';
		}
		
	if( rmdir( $dir_src.$nick ) == FALSE )
		echo "<b>Error:</b> Al eliminar directorio principal del usuario"; //creamos carpeta con nombre del usuarui
	else
		return 1; # exito al eliminar 
	
	return 0; // error al eliminar
	}

function navegador_lenguaje( $lenguaje )
	{
	if( !strcmp( $lenguaje, "es-mx" ) )
		return "Espanol-Mexico";
	else if( !strcmp( $lenguaje, "es-ar" ) )
		return "Espanol-Argentina";
	else if( !strcmp( $lenguaje, "es-cl" ) )
		return "Espanol-Chile";
	else if( !strcmp( $lenguaje, "es-ve" ) )
		return "Espanol-Venezuela";
	else if( !strcmp( $lenguaje, "es-br" ) )
		return "Espanol-Brasil";
	else if( !strcmp( $lenguaje, "es-uy" ) )
		return "Espanol-Uruguay";
	else if( !strcmp( $lenguaje, "es-sp" ) )
		return "Espanol-Espana";
	else if( !strcmp( $lenguaje, "en-us" ) )
		return "Ingles-USA";
	else
		return $lenguaje;
	}

function top_decargas( $base_dd, $num )
	{
	$cons= consultar_limite_enorden( $base_dd, "0,".$num, "RATING DESC" );
	if( mysql_num_rows($cons)==0 ) //no existe RATING aun
		echo "Top vacio...";
	else //si existe RATING
		{
		echo "<ul>";
		while( $buf=mysql_fetch_array($cons) )
			{
			echo "<li>";
			echo "<img src=\"". TEMA_URL. "/imagenes/ark.png\" border=\"0\" style=\"width:14px;\">";
			echo "<a href=\"descargar.php?file_id=". $buf["ID"]. "\" alt=\"". $buf["NOMBRE"]. " :: Click para descargar\" title=\"". $buf["NOMBRE"]. " :: Click para descargar\">";
			echo noticia_cortada($buf["NOMBRE"], 22);
			echo "</a>";
			echo "</li>";
			}
		echo "</ul>";
		unset($buf);
		}
	unset($cons);
	}

function checkbrowser()
	{
	}
	
function deamon_logd()
	{
	//Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14
	$ip= $_SERVER['REMOTE_ADDR']; //obtenemos IP
	$nav= get_browser();
	# $nav= checkbrowser();
	
	# verificando si sera nueva la visita 
	$nuevo_cons= consultar_enorden( "LOG", "FECHA_LOGIN DESC" ); #consultamos todo ordenado por fecha inicio login
	$nuevo=1;
	$ret= 0;
	while( $buf_n= mysql_fetch_array($nuevo_cons) )
		{
		if( !strcmp( date("d/m/y", time()), date("d/m/y", $buf_n["FECHA_LOGIN"]) ) ) #mismo dia/mes/anio
			{
			if( !strcmp( $buf_n["IP"], $ip ) ) # si la IP existe
				{
				$nuevo=0; # no creamos registro nuevo
				if( strcmp($buf_n["SESION"], session_id()) ) # si las sessiones son distintas
					$ret=1; # retoma el temporizador de session 
				}
			}
		}
	limpiar($nuevo_cons);
	
	if( $nuevo==1 )	//no existe la SESION, esta entrando nuevo usuario
		{
		//recolectamos informacion
		while( list($key, $val)=each($nav) )
			{
			if( !strcmp($key, "parent" ) )
				$navegador= proteger_cadena($val);
			else if( !strcmp($key, "platform" ) )
				$so= proteger_cadena($val);
			}
		
		//Geo Localizacion por IP
		# require( "admin/geoip.inc" ); //incluimos cabecera
		require( "admin/geoipcity.inc" ); //incluimos cabecera
		include( "admin/geoipregionvars.php" );
		$geoip_bd= geoip_open( "admin/geoip/GeoIPLiteCity.dat", GEOIP_STANDARD ); //abrimos archivos dat
		
		if( strcmp( $_SERVER['HTTP_REFERER'], "" ) )
			$ref= strtolower($_SERVER['HTTP_REFERER']);
		else
			$ref= strtolower($_SERVER['HTTP_HOST']);
			
		//condicion especial para identificar Windows Vista
		if( !strcmp( $so, "unknown" ) )
			$so= "Desconocido";
			
		# verificando si es un robot
		if( is_a_robot($navegador) )
			$human=0;
		else $human=1;
		
		$r= geoip_record_by_addr( $geoip_bd, $ip );
		$ubicacion= proteger_cadena(($r->city. '/'. $GEOIP_REGION_NAME[$r->country_code][$r->region]. '/'. $r->country_name));
		
		$trama= array(
					"nick"=>"'Visitante'",
					 "ip"=>"'". $ip. "'",
					 "so"=>"'". $so. "'",
					 "navegador"=>"'". $navegador. "'",
					 "navegador_lenguaje"=>"'". navegador_lenguaje( substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 5) ). "'", 
					 "sesion"=>"'". proteger_cadena(session_id()). "'",
					 "ubicacion"=>"'". $ubicacion. "'", 
					 "referencia"=>"'". $ref. "'",
					 "nombre_host"=>"'". gethostbyaddr($ip). "'", 
					 "human"=>"'". $human. "'", 
					 "fecha_login"=>"'". time(). "'", 
					 "fecha_logout"=>"'0'"
					);
		
		insertar_bdd( "LOG", $trama );
		geoip_close($geoip_bd);
		unset($ubicacion);
		unset($trama);
		unset($ref);
		unset($human);
		}
	else if( $ret==1 ) # retoma session vencida en el mismo dia 
		{
		$ret_cons= consultar_enorden_con( "LOG", "IP='". $ip. "'", "FECHA_LOGIN DESC" ); # consultamos 
		$buf_ret= mysql_fetch_array($ret_cons); # tomamos valor
		
		$trama= array(
				"id"=>"'". $buf_ret["ID"]. "'", 
				"sesion"=>"'". proteger_cadena(session_id()). "'", 
				"fecha_login"=>"'". time(). "'",
				"fecha_logout"=>"'0'" 
				);
			
		actualizar_bdd( "LOG", $trama );
		unset($trama);
		limpiar($ret_cons);
		}
	else //la SESION o IP ya existe
		{
		# si la session/ip no estan detectadas como cuenta de usuario, entonces lo hacemos
		if( usuario_legitimo() /*&& !strcmp($buf["NICK"], "Visitante")*/ )
			{
			$trama= array(
					"sesion"=>"'". proteger_cadena(session_id()). "'",
					"nick"=>"'". proteger_cadena($_SESSION["log_usr"]). "'"
					);
			
			actualizar_bdd( "LOG", $trama );
			unset($trama);
			}
		unset($buf);
		}
	
	//establece los cierres de sesion
	temporizador_de_sesiones();
	}
	
function temporizador_de_sesiones()
	{
	$log_cons= consultar_con( "LOG", "FECHA_LOGOUT='0'" ); //consultamos sesiones abiertas
	$flag=0; //bandera para saber si debemos cerrar la sesion o dejarla abierta
	
	while( $buf= mysql_fetch_array($log_cons) )
		{
		//si la SESION es igual, aun esta activo en la web
		if( !strcmp( session_id(), $buf["SESION"] ) ) 
			$flag=1; //bandera activada
		
		//si la sesion no esta activada, entonces posiblemente no este conectado
		if( $flag==0 )
			{
			if( ($buf["FECHA_LOGIN"]+(15*60))<time() ) //si el tiempo se vencio se cierra la sesion (10 min)
				{
				$trama= array(
							"ID"=>"'". $buf["ID"]. "'", 
							"FECHA_LOGOUT"=>"'". ($buf["FECHA_LOGIN"]+(15*60)). "'"
							);
				
				actualizar_bdd( "LOG", $trama );
				}
			}
		else //entonces esta conectado, se actualiza el tiempo de sesion
			{
			$trama= array(
							"ID"=>"'". $buf["ID"]. "'", 
							"FECHA_LOGIN"=>"'". time(). "'" 
							);
				
			actualizar_bdd( "LOG", $trama );
			}
				
		unset($trama);
		$flag=0; //desactivamos bandera
		}
	
	unset($buf);
	limpiar($log_cons);
	}

?>
