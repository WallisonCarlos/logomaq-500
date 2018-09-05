<?php
	if (isset($_GET['pag'])) {
		$pag = $_GET['pag'];
		if ($pag == "inicio") {
		
		} else if ($pag == "novoSlide") {
			include "adicionarNovoSlide.php";
		} else if ($pag == "listarSlides") {
			include "listarSlides.php";
		} else if ($pag == "editarSlide") {
			include "editarSlide.php";
		}  else {
			echo "Página não encontrada";
		}
	} else {
		
		
	}
?>