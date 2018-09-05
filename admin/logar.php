<?php 
include "../config/conexao.php";
include "../functions/one.php";
$login = $_POST['usuario']; 
$senha = $_POST['senha'];
LM_Login($login, $senha);
?>