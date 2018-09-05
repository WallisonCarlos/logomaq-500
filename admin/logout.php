<?php 
	if (!isset($_SESSION)) {
		session_start();
	}
	
	if (isset($_SESSION['usuario'])) {
		unset($_SESSION['usuario']);
	}
	header("Location: ./");
?>