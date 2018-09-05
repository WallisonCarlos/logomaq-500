<?php
	if (isset($_GET['pag'])) {
		$pag = $_GET['pag'];
		if ($pag == "inicio") {
		
		} else if ($pag == "novoSlide") {
			include "adicionarNovoSlide.php";
		} else if ($pag == "listarSlides") {
			
		} else if ($pag == "editarSlide") {
			
		}  else {
			echo "Página não encontrada";
		}
	} else {
		
		
	}
?>