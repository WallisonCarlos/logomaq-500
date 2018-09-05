<?php 
	// ALTERE OS DADOS DAS STRINGS (NOMES QUE ESTÃO ENTRE AS ASPAS)
	$host = "localhost"; // endereco do banco de dados
	$usuario = "root"; // usuario do banco de dados
	$senhadobanco = ""; // senha do banco de dados
	$nomedobanco = "logomaq"; //nome do banco de dados

// NÃO ATERAR NADA DAQUI PARA BAIXO
	$conn = mysqli_connect($host,$usuario,$senhadobanco, $nomedobanco);
/// REALIZA A CONEXÃO
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();	
	}
	mysqli_set_charset($conn,'utf8');
?>
