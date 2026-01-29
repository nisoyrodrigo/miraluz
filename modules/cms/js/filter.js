function getFilters(){
	$("#content_cms").find(".filter").each(function (index, item){
		console.log(index + "-" + item);
	});
}

function leerCookie(nombre) {
	var lista = document.cookie.split(";");
	for (i in lista) {
		var busca = lista[i].search(nombre);
		if (busca > -1) {micookie=lista[i]}
	}
	var igual = micookie.indexOf("=");
	var valor = micookie.substring(igual+1);
	return valor;
}

function borrar() {
	var lista = document.cookie.split(";");
	for (i in lista) {
		var igual = lista[i].indexOf("=");
		var nombre = lista[i].substring(0,igual);
		lista[i] = nombre+"="+""+";expires=1 Dec 2000 00:00:00 GMT"
		document.cookie = lista[i]
	} 
}
$(document).ready(function (){
	getFilters();
	alert("hola");
});