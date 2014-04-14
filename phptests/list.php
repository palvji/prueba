<!DOCTYPE html>   
<html> 
<head>   
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<title>Name - Email Table</title>  
	<style>
		table,th,td
		{
			border:1px solid black;
			border-collapse:collapse;
		}

		.error {color: #FF0000;}
	</style>
	<script>


	function showHint(str)
	{
	if (str.length==0)
	  {
	  $("#txtHint").html("");
	  return;
	  }
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
	    $("#txtHint").html(xmlhttp.responseText);
	    }
	  }
	xmlhttp.open("GET","gethint.php?q="+str,true);
	xmlhttp.send();
	}

	function listaAjax(pag,nElem)
	{

		var xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function()
		{

		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    var respuesta = xmlhttp.responseText;
  				var respParseada = JSON.parse(respuesta);
  				var vectorUsuarios = respParseada["vector"];
  				var tablaNueva;

  				tablaNueva = "<tr><td>ID</td><td>NAME</td><td>EMAIL</td><td>CP</td><td>DELETE</td></tr>";
  				for (var i = 0; i < vectorUsuarios.length; i++) {
  					tablaNueva += "<tr><td>" + vectorUsuarios[i]["id"] + "</td>";
  					tablaNueva += "<td>" + vectorUsuarios[i]["nombre"] + "</td>";
  					tablaNueva += "<td>" + vectorUsuarios[i]["email"] + "</td>";
  					tablaNueva += "<td>" + vectorUsuarios[i]["cp"] + "</td>";
  					tablaNueva += "<td><input id='idBotonBorrar' type='button' value='Eliminar' onClick='borrarAjax(" + vectorUsuarios[i]["id"] + ")' ></td></tr>";
  				};

  				$("#idTabla").html(tablaNueva);

  				$("#idTabla").find("input").click(function(){
  					$(this).parents("tr").fadeOut("4000");
  				})


  				$("#idNumEltos").html("Num elementos: " + respParseada["numTotal"]);
  				var totalPags = Math.ceil(respParseada["numTotal"] / nElem);
					$("#idPag").html("Pagina: " + ((pag / nElem) + 1) + "/" + totalPags);
					$("#idPagListar").val(pag);


					//Si no estoy en la primera pagina, muestro "Anterior"
					if(pag > 0)
					{
						var enlacePagAnterior = "<a href=# onClick='listaAjax(" + (pag - nElem) + "," + nElem + ")'> << ANTERIOR </a>";
						$("#idPagAnt").html(enlacePagAnterior);
					}
					else
					{
						$("#idPagAnt").html("");
					}


					//Si no estoy en la ultima pagina, muestro "Siguiente"
					if(((pag/nElem) + 1) < totalPags)
					{

      				var enlacePagSiguiente = "<a href=# onClick='listaAjax(" + (pag + nElem) + "," + nElem + ")'> SIGUIENTE >> </a>";
      				$("#idPagSig").html(enlacePagSiguiente);
      			}
      			else
      			{
      				$("#idPagSig").html("");
      			}
		    }
		}

		var query = $("#idNombre").val();
		var queryCP = $("#idCp").val();

		var cadenaUrl = "?query=" + query + "&queryCP=" + queryCP + "&pag=" + pag + "&nElem=" + nElem;
		
		xmlhttp.open("GET","getusersajax.php"+cadenaUrl,true);
		xmlhttp.send();

	}

	function borrarAjax(id)
	{
		var xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		  {
		    var pag = parseInt($("#idPagListar").val());
		    listaAjax(pag,10);
		  }
		}
		xmlhttp.open("GET","deleteuserajax.php?id="+id,true);
		xmlhttp.send();
	}

$(document).ready(function(){
  $("#idTabla").find("input").click(function(){
  	$(this).parents("tr").fadeOut("4000");
  })
}); 

	</script>
</head>  
<body>
	<?php

	function muestra($cadena)
	{
		echo $cadena;
	}
	function checkCP ($cp)
	{
		if($cp != '')
		{
			if (!preg_match("/^[0-9]{5}$/",$cp))
	 		{
	  			return false;
	  		}
	  		else
	  			return true;
  		}
  		else
  			return true;
	}

	function listUsers($data, $pagina)
	{
		//la página para el usuario empieza en la 1, pero los datos empiezan en la 0
		$pagina = $pagina - 1;

		$copiaLocalData = $data;
		$nuevoData = array_slice($copiaLocalData, $pagina*10);
		
		echo '<table id="idTabla" style="width:400px">';
	  	echo '<tr><td>ID</td><td>NAME</td><td>EMAIL</td><td>CP</td><td>DELETE</td></tr>';
	  	
	  	$total = min (10, count($nuevoData));
	  	for($i = 0; $i < $total; $i++)
	  	{
	  		echo '<tr>';
	  		echo '<td>' . $nuevoData[$i]['id'] . '</td>';
			echo '<td>' . $nuevoData[$i]['nombre'] . '</td>';
	  		echo '<td>' . $nuevoData[$i]['email'] . '</td>';
	  		echo '<td>' . $nuevoData[$i]['cp'] . '</td>';
	  		echo '<td>';
	  		echo '<form action="list.php" method="get">';
			echo '<input id="idBotonBorrar" type="submit" value="Eliminar">
					<input type="hidden" name="action" value="delete">
	  				<input type="hidden" name="id" value="' . $nuevoData[$i]['id'] . '">
	  				</form>';
	  		echo '</td>';
	  		echo '</tr>';
	  	}
		
		echo '</table>';
	}

	session_start();
	include 'loadData.php';

	//establezco si hay página o no
	if($_GET['page'])
	{
		$pagina = $_GET['page'];
	}
	//si no la tengo, estamos en la página
	else
	{
		$pagina = 1;
	}

	//si el filtro de la sesion tiene valor, lo guardo en $filtro
	if(isset($_SESSION['filtro']))
	{
		$filtro = $_SESSION['filtro']['nombre'];
		$filtroCP = $_SESSION['filtro']['cp'];
	}

	//miro si tengo algun filtro de busqueda
	if ($_POST["action"] == "search")
	{
		$filtroCP = $_POST["queryCP"];
		$filtro = $_POST["query"];
		
		$_SESSION['filtro']['nombre'] = $filtro;
		$_SESSION['filtro']['cp'] = $filtroCP;
	
	}

	if ($_GET["action"] == "delete"){
		delete($_GET["id"]);
	}

	if ($_POST["action"] == "insert")
	{
		if(checkCP($_POST["cp"]))
		{
			insert($_POST["nombre"], $_POST["email"], $_POST["cp"]);
			$nombreInsertar = '';
			$emailInsertar = '';
			$cpInsertar = '';
		}
		else
		{
			$nombreInsertar = $_POST["nombre"];
			$emailInsertar = $_POST["email"];
			$cpInsertar = $_POST["cp"];
			$cpError = '* El CP deben ser 5 digitos!';
		}
	}


	echo "<h1> Search an user: </h1>";
	echo '<input type="hidden" name="action" value="search">
		  	Nombre: <input id="idNombre" type="text" name="query" value="' . $filtro . '" onkeyup="showHint(this.value)">
		  	<span id="txtHint"> </span>
		  	<br/>CP: <input id="idCp" type="text" name="queryCP" value=' . $filtroCP . '>
		  	<br/><input type="button" value="Buscar" onClick="listaAjax(0,10)">';
	
	echo "<h1> Here's your name-email list </h1>";
	//Cargo los usuarios que cumplen el filtro (si hay)
	$data = cargarDatos($_SESSION['filtro']);

	//calculo el número de páginas totales y muestro página actual/páginas totales
	$paginasTotales = ceil(count($data)/10);
	//echo "Pagina ". $pagina . "/" . $paginasTotales;
	echo "<div id=idPag> Pagina: " . $pagina . "/" . $paginasTotales . "</div>";
	echo "<div id=idNumEltos>Num elementos: " . count($data) . "</div>";
	echo '<input type="hidden" id="idPagListar" value="0">';

	//llamo a la función de mostrar usuarios, y la página a mostrar
	listUsers($data, $pagina);

	echo '<div id=idPagAnt></div>';
	echo '<div id=idPagSig>';
	if($pagina < $paginasTotales)
		echo '<a href=# onClick="listaAjax(10,10)"> SIGUIENTE >></a>';
	echo '</div>';

	echo "<h1> Insert an user </h1>";
	echo '<form action="list.php" method="post">
				<input type="hidden" name="action" value="insert">
				Nombre:<input type="text" name="nombre" value="' . $nombreInsertar . '">
				email:<input type="text" name="email" value="' . $emailInsertar . '">
				CP:<input type="text" name="cp" value="' . $cpInsertar . '">
				<span id="idPrueba" class="error"> ' . $cpError . '</span>
				<input type="submit" value="Insertar">
			</form>';



	?>
	
	<br/>
	<a href="index.html"> Back </a>
	<br/>
	<a href="ajax.php"> ajax </a>


</body>  
</html>
