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
		} else if ($pag == "novaCategoriaServico") {
			include "adicionarCategoriaServico.php";
		} else if ($pag == "listarCategoriasServico") {
			include "listarCategoriasServico.php";
		} else if ($pag == "editarCategoria") {
			include "editarCategoriaServico.php";
		} else if ($pag == "novoServico") {
			include "adicionarNovoServico.php";
		} else if ($pag == "listarServicos") {
			include "listarServicos.php";
		} else if ($pag == "editarServico") {
			include "editarServico.php";
		} else {
			echo "Página não encontrada";
		}
	} else {
		
		
	}
?>