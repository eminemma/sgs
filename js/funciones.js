var Sys = {};
	
	Sys.g = {};
	Sys.g.url = null;
	Sys.g.contenedor = null;

	Sys.paginador = {};
	Sys.paginador.url = null;
function g(url, contenedor){
	iniciar_giro('.icon-refresh');

	if(contiene(url, '.php') && !contiene(url, '.php?')){
		url = url.replace('.php','.php?');
	}
	
	url = url.replace('.php?&','.php?');
	url = url.replace(/.php\?$/,'.php'); //vooo, dejalo así

	if(contenedor == undefined || contenedor == null)
		contenedor = '#contenedor_general';

	Sys.g.url = url;
	Sys.g.contenedor = contenedor;

	$.get(
		url,
		function(data){
			$(contenedor).html(data);
		}
	).error(
		function(jqXHR, textStatus, errorThrown){
			$(contenedor).html('Se ha producido un error ("'+errorThrown+'").<br>'+textStatus);
		}
	).complete(
		function(){
			detener_giro('.icon-refresh');
		}
	);
}



function a(accion){
	return '&accion='+accion;
}

function v(variable){
	return '&'+variable+'='+$('#'+variable).val();
}

function l(link){
	window.location.href = link;
}

function p(url){
	window.open(url, '_blank');
	return false;
}

function recargar(){
	g(Sys.g.url, Sys.g.contenedor);
}

function getUrlPaginador(posicion){
	return Sys.paginador.url;
}

function contiene(cadena, busqueda){
	return cadena.indexOf(busqueda) != -1;
}

function accion_girar(elemento){
	$(elemento).removeClass('girar');
	$('#recargar').html('<i class="icon-refresh"></i> Cargando');
	setTimeout("$('"+elemento+"').addClass('girar');", 50)
}

var intervalo_giro = null;
function iniciar_giro(elemento){
	clearInterval(intervalo_giro);
	accion_girar(elemento);
	intervalo_giro = setInterval('accion_girar("'+elemento+'")', 700);
}

function detener_giro(elemento){
	clearInterval(intervalo_giro);
	$('#recargar').html('<i class="icon-refresh"></i> Recargar');
}