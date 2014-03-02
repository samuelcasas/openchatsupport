//Funcion para pegar codigo de caritas extras
function caritas_extra(modo)
	{
	var ventanita;
	ventanita= window.open( 'loader.php?load='+modo, '', 'width=600px,height=400px' );
	}

function pulsacion(c,e)
	{
	var capa=document.getElementById(c);
	capa.focus();
	if(window.event)
		key_pushed=window.event.keyCode;
	else
		key_pushed=e.which;
		
	if(key_pushed==false)
		return;
	else
		return key_pushed;
	}

function detectar_entradas(c,e,op,ref)
	{
	var capa=document.getElementById(c);
	key=pulsacion(c,e);
	if(key==13)
		{
		if(op=='comentario_add')
			{
			cargar_datos('id=cat&op=add_coment&uri='+ref,'accion','POST','coment_add', '0', '0');
			capa.value='';
			capa.style.height='20px';
			capa.focus();
			}
		else if(op=='comentario_addblog')
			{
			cargar_datos('id=blog&op=add_coment&uri='+ref,'accion','POST','coment_add', '0', '0');
			capa.value='';
			capa.style.height='20px';
			capa.focus();
			}
		else if(op=='buscar_email')
			{
			capa.focus();
			cargar_datos('my=sistema&op=mail_buscar','busqueda','POST','buscar_email', '0', '0');
			}
		else if(op=='login')
			document.accesar.submit();
		else if( op=='chat_addmsg')
			{
			capa.focus();
			cargar_datos('my=chatsupport&op=addmsg#end','chat_messages','POST','chat_msg:chatwin', '1', '1');
			capa.value='';
			}
		}
	else
		{
		if(op=='comentario_add')
			{
			if(capa.value.length==2)
				capa.style.height='50px';
			}
		else if(op=='search_cliente')
			{
			var c= document.getElementById('cliente_factura').value.length;
		
			if( c>2 )
				{
				// alert( 'invocando busqueda...['+ c +']' );
				capa.focus();
				cargar_datos('id=facturas&c=search_cliente','busqueda','POST','cliente_factura', '0', '0');
				}
			// else		alert( 'mas texto...');
			}
		}
	}
	
function reemplazar_carita(text, textarea)
	{
	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
		{
		var caretPos = textarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
		}
	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined")
		{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;

		if (textarea.setSelectionRange)
			{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
			}
		textarea.scrollTop = scrollPos;
		}
	// Just put it on the end.
	else
		{
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
		}
	}

//Funcion para pegar codigo de caritas
function surroundText(text1, text2, textarea)
	{
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
		{
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0)
			{
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
			}
		else
			textarea.focus(caretPos);
		}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined")
		{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange)
			{
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
			}
		textarea.scrollTop = scrollPos;
		}
	// Just put them on the end, then.
	else
		{
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
		}
	}

//Funcion para visualizar codigo en colores resaltados
function bbc_highlight(something, mode)
	{
	something.style.backgroundImage = "url(" + smf_images_url + (mode ? "/bbc/bbc_hoverbg.gif)" : "/bbc/bbc_bg.gif)");
	}
	
//Funcion para obtener conector AJAX
function ajax()
	{
	var pagina=false; //conector ajax
	
	if( window.XMLHttpRequest ) //Navegador Firefox
		pagina= new XMLHttpRequest(); //creamos objeto para el navegador
	else if( window.ActiveXObject ) //Navegador Internet Explorer
		{
		try //Version Actual
			{
			pagina= new ActiveXObject( "Msxml2.XMLHTTP" ); //creamos objeto para el navegador
			}
		catch(e) //version Antigua
			{
			try
				{
				pagina= new ActiveXObject( "Microsoft.XMLHTTP" ); //creamos objeto para el navegador
				}
			catch(e)
				{
				}
			}
		}
		
	return pagina;
	}
	
	
function carga_prueba()
	{
	alert("uno");
	}

function hidinon( capasrc, capashow )
	{
	if( $(capasrc).is(':hidden') ) // si esta oculto
		{
		$(capashow).fadeOut('slow'); // escondemos
		if( capasrc ) // si tiene datos
			{
			$(capasrc).css( "display", "hidden"); // ponemos invisible
			$(capasrc).fadeIn('slow'); // mostramos
			}
		}
	else
		{
		if( capasrc ) // si hay datos
			$(capasrc).fadeOut('slow'); //escondemos
		$(capashow).css( "display", "hidden"); // ponemos invisible
		$(capashow).fadeIn('slow'); // mostramos
		}
	}

function capa_verno( capa )
	{
	var layer;
	layer= document.getElementById(capa);
		
	if( layer.style.visibility=="hidden" )
		{
		layer.style.visibility="visible"; //visible - hidden
		layer.style.display="block"; // none - block
		}
	else
		{
		layer.style.visibility="hidden"; //visible - hidden
		layer.style.display="none"; // none - bloque
		}
	}
	
function get_http_host()
{var httpsrc,x,y;httpsrc=document.URL;x=httpsrc.split('http://');y=x[1].split('/');return y[0];}

function serielizeJSON(myarr)
	{
	var json={};
	for(i=0;i<(myarr.length);i++)
		json[myarr[i]]=document.getElementById(myarr[i]).value;
		
	return json;
	}

function cargar_datos( vars, capaview, flujoddatos, varsform, outloader, streamhtmlview )
	{
	var http_host=get_http_host();
	var urlscript='http://'+http_host+'/ajax.php?'+vars;
	if( varsform==0 )		var tipoflujo='GET';
	else
		{
		var trama="";
		var tipoflujo= 'POST';
		var myarr=varsform.split(':');
		trama= serielizeJSON(myarr);
		}
	var capa=document.getElementById(capaview);
	
	if( varsform==0 )
		{
		$.ajax({
			beforeSend: function(){if( !outloader) capa.innerHTML='<center><img src="http://'+http_host+'/imagenes/loading.gif"> Cargando...</center>';},
			url: urlscript,  
			type: tipoflujo,  
			error: function(err, textStatus, errorThrown){
						capa.innerHTML='<center><img src="http://'+http_host+'/imagenes/error2.png"> Sucedio un error :: '+textStatus+'</center>';
         			console.log(err);
         			console.log(errorThrown);
						},  
			success: function(data)	{
					var content= $(data).find('#big_content');
					if( !streamhtmlview )
						{
						$('#'+capaview).css( "display", "none");
						$('#'+capaview).html(data);
						$('#'+capaview).fadeIn( "slow" );
						}
					else 
						$('#'+capaview).prepend(data);
					}
		});
		}
	else
		{
		$.ajax({
			beforeSend: function(){if( !outloader) capa.innerHTML='<center><img src="http://'+http_host+'/imagenes/loading.gif"> Cargando...</center>';},
			url: urlscript,  
			type: tipoflujo, 
			data: trama, 
			error: function(err, textStatus, errorThrown){
						capa.innerHTML='<center><img src="http://'+http_host+'/imagenes/error2.png"> Sucedio un error :: '+textStatus+'</center>';
         			console.log(err);
         			console.log(errorThrown);
						},  
			success: function(data)	{
					var content= $(data).find('#big_content');
					if( !streamhtmlview )
						{
						$('#'+capaview).css( "display", "none");
						$('#'+capaview).html(data);
						$('#'+capaview).fadeIn( "slow" );
						}
					else
						$('#'+capaview).prepend(data);
					}
		});
		}
	}
	
function carga_datos_recursiva( vars, capaview, flujoddatos, varsform )
	{
	var cont;
	
	cont= document.getElementById('contenedor_session').value;
	setInterval( "cargar_datos( '"+vars+"', '"+capaview+"', '"+flujoddatos+"', '"+varsform+"', '0', '0' )", 3000 );
	}

function resultadoUpload(estado, file)
	{
	if( file==0 )
		var mensaje = 'Error, debes seleccionar un archivo a subir [Recarga la pagina].';
	else
		{
		var http_host= get_http_host();
		
		if (estado == 0)
			var mensaje = file + ' <img src="http://'+http_host+'/imagenes/loading.gif" border="0">';
		else if (estado == 1)
			var mensaje = file + ' <img src="http://'+http_host+'/imagenes/palomita.png" border="0">';
		else if (estado == 2)
			var mensaje = 'Error: No proceso ha procesado un archivo.';
		else if (estado == 3)
			var mensaje = 'Error: No se pudo subir el archivo.';
		else
			var mensaje = 'Error Desconocido';
		}

	document.getElementById('upload').innerHTML=mensaje;
	}

function escribirCapa( texto, capa )
	{
	document.getElementById(capa).innerHTML=texto;
	}

function invisible(capa)
	{
	var layer;
	layer= document.getElementById(capa);
	layer.style.visibility="hidden"; //visible - hidden
	layer.style.display="none"; // none - bloque
	}

function visible(capa)
	{
	var layer;
	layer= document.getElementById(capa);
	layer.style.visibility="visible"; //visible - hidden
	layer.style.display="block"; // none - block
	}

function supercapa(capa,color,opacidad)
{var nav=1;if(navigator.userAgent.indexOf("MSIE")>=0)nav=0;var m=document.getElementById(capa);m.id=capa;m.style.width=document.body.offsetWidth+'px';m.style.height=document.body.offsetHeight+'px';m.style.top=0;m.style.left=0;m.style.visibility='visible';m.style.display='block';document.body.appendChild(m);}

function stream_video_error( e )
	{
	switch( e.target.error.code )
		{
		case e.target.error.MEDIA_ERR_ABORTED:
			alert('Haz abortado la reproduccion del video.');
			break;
		case e.target.error.MEDIA_ERR_NETWORK:
			alert('Error en la red, no se ha podido continuar descargando y reproduciendo el video.');
			break;
		case e.target.error.MEDIA_ERR_DECODE:
			alert('La repduccion se ha abortado por problemas de corrupcion del video o posiblemente su navegador no soporta el video.');
			break;
		case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
			alert('No se puede cargar el video, debido a que el servidor, la red o el soporte de su navegador han fallado.');
			break;
		default:
			alert('Ha ocurrido un error desconocido...');
			break;
		}
	}

