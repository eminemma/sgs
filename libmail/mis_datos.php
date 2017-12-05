<?PHP 
if (isset($_POST['enviar'])) { 
	echo "Hola <b>" . $_POST['nombre'] . "</b> que tal estás<BR>n"; 
	echo "Eres " . $_POST['sexo'] . "<BR>n"; 
	echo "Tienes " . $_POST['edad'] . "<BR>n"; 
	echo "Tu sistema favorito es " . $_POST['sistema'] . "<BR>n"; 

	if ($_POST['aficiones'] != "") { 
		echo "Tus aficiones son: <BR>n"; 
		echo nl2br($_POST['aficiones']); 
	} else { 
		echo "NO tienes aficiones <BR>n"; 
	} 
} 

echo "<a href='formulario.htm'>VOLVER AL FORMULARIO</a>" 
?> 
