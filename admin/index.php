<?php

	require_once "../config/conexao.php";
	require_once "../functions/one.php";

	if (!isset($_SESSION)) {
		session_start();
	}
	
	if (isset($_SESSION['usuario'])) {
		include "dashboard.php";
	} else {
		include "login.php";
	}
?>