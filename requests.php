<?php
	include "config/conexao.php";
	require_once "functions/one.php";
	date_default_timezone_set('America/Fortaleza');
	if (isset($_GET['f']) OR isset($_POST['f'])) {
		$f = LM_Secure((!empty($_GET['f'])) ? $_GET['f'] : $_POST['f']);
		
		if ($f == "novoSlide") {	
			if (isset($_FILES['imagem_slideshow']['name'])) {
				$invalid_file = 0;
				if ($_FILES['imagem_slideshow']['size'] > 10000000) {
					$invalid_file = 1;
				} else {
					$fileInfo = array(
						'file' => $_FILES["imagem_slideshow"]["tmp_name"],
						'name' => $_FILES['imagem_slideshow']['name'],
						'size' => $_FILES["imagem_slideshow"]["size"],
						'type' => $_FILES["imagem_slideshow"]["type"]
					);
					$media    = LM_ShareFile($fileInfo);
					if (!empty($media)) {
						$mediaFilename = $media['filename'];
						$mediaName     = $media['name'];
					}
				}
			}
			
			$slide_data = array(
				"titulo_slideshow" => LM_Secure($_POST['titulo_slideshow']),
				"descricao_slideshow" => LM_Secure($_POST['descricao_slideshow']),
				"imagem_slideshow" => $mediaFilename,
				"link_botao_slideshow" => LM_Secure($_POST['link_botao_slideshow']),
				"visivel_slideshow" => LM_Secure($_POST['visivel_slideshow']),
				"rotulo_botao_slideshow" => LM_Secure($_POST['rotulo_botao_slideshow'])
			);
			$error = "";
			$data = array();
			$slide_id = LM_NovoSlide($slide_data);
			if ($slide_id) {
				$data = array(
					'status' => 200,
					'invalid_file' => $invalid_file,
					'success' => "Slide cadastrado com sucesso cadastrado com sucesso, veja clicando <a href='?pag=listarSlides'>aqui</a>!"
				);
			} else {
				$data = array(
					'status' => 400,
					'invalid_file' => $invalid_file,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "novoServico") {	
			if (isset($_FILES['imagem_servico']['name'])) {
				$invalid_file = 0;
				if ($_FILES['imagem_servico']['size'] > 10000000) {
					$invalid_file = 1;
				} else {
					$fileInfo = array(
						'file' => $_FILES["imagem_servico"]["tmp_name"],
						'name' => $_FILES['imagem_servico']['name'],
						'size' => $_FILES["imagem_servico"]["size"],
						'type' => $_FILES["imagem_servico"]["type"]
					);
					$media    = LM_ShareFile($fileInfo);
					if (!empty($media)) {
						$mediaFilename = $media['filename'];
						$mediaName     = $media['name'];
					}
				}
			}
			
			$servico_data = array(
				"titulo_servico" => LM_Secure($_POST['titulo_servico']),
				"categoria_servico" => LM_Secure($_POST['categoria_servico']),
				"imagem_servico" => $mediaFilename
			);
			$error = "";
			$data = array();
			$servico_data = LM_NovoServico($servico_data);
			if ($servico_data) {
				$data = array(
					'status' => 200,
					'invalid_file' => $invalid_file,
					'success' => "Serviço cadastrado com sucesso, veja clicando <a href='?pag=listarServicos'>aqui</a>!"
				);
			} else {
				$data = array(
					'status' => 400,
					'invalid_file' => $invalid_file,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "atualizaServico") {	
			$mediaFilename =  "";
			if (isset($_FILES['imagem_servico']['name'])) {
				if ($_FILES['imagem_servico']['size'] > 10000000) {
				} else {
					$fileInfo = array(
						'file' => $_FILES["imagem_servico"]["tmp_name"],
						'name' => $_FILES['imagem_servico']['name'],
						'size' => $_FILES["imagem_servico"]["size"],
						'type' => $_FILES["imagem_servico"]["type"]
					);
					$media    = LM_ShareFile($fileInfo);
					if (!empty($media)) {
						$mediaFilename = $media['filename'];
						$mediaName     = $media['name'];
					}
				}
			}
			
			$servico_data = array(
				"titulo_servico" => LM_Secure($_POST['titulo_servico']),
				"categoria_servico" => LM_Secure($_POST['categoria_servico']),
				"id_servico" => LM_Secure($_POST['id_servico']),
				"imagem_servico" => $mediaFilename
			);
			$error = "";
			$data = array();
			$servico_data = LM_AtualizaServico($servico_data);
			if ($servico_data) {
				$data = array(
					'status' => 200,
					'success' => "Serviço atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "novaCategoria") {	
			
			$categoria_data = array(
				"titulo_categoria" => LM_Secure($_POST['titulo_categoria'])
			);
			$error = "";
			$data = array();
			$categoria = LM_NovaCategoria($categoria_data);
			if ($categoria) {
				$data = array(
					'status' => 200,
					'success' => "Categoria cadastrada com sucesso, veja clicando <a href='?pag=listarCategoriasServico'>aqui</a>!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "atualizaSlide") {	
			$slide_data = array(
				"titulo_slideshow" => LM_Secure($_POST['titulo_slideshow']),
				"descricao_slideshow" => LM_Secure($_POST['descricao_slideshow']),
				"id_slideshow" => LM_Secure($_POST['id_slideshow']),
				"link_botao_slideshow" => LM_Secure($_POST['link_botao_slideshow']),
				"visivel_slideshow" => LM_Secure($_POST['visivel_slideshow']),
				"rotulo_botao_slideshow" => LM_Secure($_POST['rotulo_botao_slideshow'])
			);
			$error = "";
			$data = array();
			$produto_id = LM_AtualizaSlide ($slide_data);
			if ($produto_id) {
				$data = array(
					'status' => 200,
					'success' => "Slide atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "atualizaCategoria") {	
			$categoria_data = array(
				"titulo_categoria" => LM_Secure($_POST['titulo_categoria']),
				"id_categoria" => LM_Secure($_POST['id_categoria'])

			);
			$error = "";
			$data = array();
			$categoria_id = LM_AtualizaCategoira ($categoria_data);
			if ($categoria_id) {
				$data = array(
					'status' => 200,
					'success' => "Categoria atualizada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "removeSlide") {
			$error = "";
			$data = array();
			$id = LM_Secure($_POST['id_slideshow']);
			$slide_id = LM_RemoveSlide ($id);
			if ($slide_id) {
				$data = array(
					'status' => 200,
					'success' => "Slide removido com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "removeCategoria") {
			$error = "";
			$data = array();
			$id = LM_Secure($_POST['id_categoria']);
			$categoria = LM_RemoveCategoria ($id);
			if ($categoria) {
				$data = array(
					'status' => 200,
					'success' => "Categoria removida com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "removeServico") {
			$error = "";
			$data = array();
			$id = LM_Secure($_POST['id_servico']);
			$servico = LM_RemoveServico ($id);
			if ($servico) {
				$data = array(
					'status' => 200,
					'success' => "Serviço removido com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "tornarVisivel") {
			$error = "";
			$data = array();
			$id = LM_Secure($_POST['id_slideshow']);
			$slide_id = LM_AtualizaSlideVisibilidade ($id, 1);
			if ($slide_id) {
				$data = array(
					'status' => 200,
					'success' => "O slide agora está visível em sua página principal!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "tornarInvisivel") {
			$error = "";
			$data = array();
			$id = LM_Secure($_POST['id_slideshow']);
			$slide_id = LM_AtualizaSlideVisibilidade ($id, 0);
			if ($slide_id) {
				$data = array(
					'status' => 200,
					'success' => "O slide agora está invisível em sua página principal!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "novoUsuario") {
			$user_data = array(
				"nome" => XC_Secure($_POST['nome']),
				"login" => XC_Secure($_POST['login']),
				"senha" => XC_Crip(XC_Secure($_POST['senha'])),
				"nivel" => XC_Secure($_POST['nivel'])
			);
			$error = "";
			$data = array();
			$user_id = XC_NovoUsuario ($user_data);
			if ($user_id) {
				$data = array(
					'status' => 200,
					'user_data' => XC_GetUsuario($user_id),
					'success' => "Usuário cadastrado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		if ($f == "atualizaUsuario") {
			$user_data = array(
				"id_usuario" => XC_Secure($_POST['id']),
				"nome" => XC_Secure($_POST['nome']),
				"login" => XC_Secure($_POST['login']),
				"nivel" => XC_Secure($_POST['nivel'])
			);
			$error = "";
			$data = array();
			$user_id = XC_AtualizaUsuario ($user_data, XC_Secure($_POST['atualLogin']));
			if ($user_id) {
				$data = array(
					'status' => 200,
					'success' => "Usuário atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "removeUsuario") {
			$error = "";
			$data = array();
			$user_id = XC_RemoveUsuario ($_POST['id']);
			if ($user_id) {
				$data = array(
					'status' => 200,
					'success' => "Usuário removido com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "abrirAlterarUsuario") { 
			$id = XC_Secure($_GET['id']);
			$user = XC_GetUsuario($id);
			$xc['user'] = $user;
			$data = array(
				"status" => 200,
				"title" => "Alterar Usuário",
				"content" => XC_LoadPage('alterarUsuario'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "AbrirVerContaCliente") { 
			$id = XC_Secure($_GET['conta']);
			$conta = XC_GetConta($id);
			$xc['conta'] = $conta;
			$xc['config'] = XC_GetConfig();
			$data = array(
				"status" => 200,
				"title" => "Minha conta",
				"content" => XC_LoadPage('verContaCliente'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}

			
		if ($f == "abrirRemoverMesa") { 
		
			$id = XC_Secure($_GET['id']);
			$mesa = XC_GetMesa($id);
			$xc['mesa'] = $mesa;
			$data = array(
				"status" => 200,
				"title" => "Remover mesa Nº ".$mesa['id_mesa'],
				"content" => XC_LoadPage('removerMesa'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirNovoCliente") { 
			$data = array(
				"status" => 200,
				"title" => "Cadastrar Cliente",
				"content" => XC_LoadPage('cadastraCliente'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		
		if ($f == "abrirRemoverUsuario") { 
			$id = XC_Secure($_GET['id']);
			$user = XC_GetUsuario($id);
			$xc['user'] = $user;
			$data = array(
				"status" => 200,
				"title" => "Remover Usuário, ".$user['nome'],
				"content" => XC_LoadPage('removeUsuario'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirAlterarProduto") { 
			$id = XC_Secure($_GET['id']);
			$produto = XC_GetProduto($id);
			$xc['produto'] = $produto;
			$data = array(
				"status" => 200,
				"title" => "Alterar Produto",
				"content" => XC_LoadPage('alterarProduto'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirAlterarEstoque") { 
			$id = XC_Secure($_GET['id']);
			$produto = XC_GetProduto($id);
			$estoque = XC_GetEstoqueFromProduto($id);
			$xc['produto'] = $produto;
			$xc['estoque'] = $estoque;
			$xc['entradas'] = XC_GetEntradasEstoque($estoque['id']);
			$xc['saidas'] = XC_GetSaidasEstoque($estoque['id']);
			$data = array(
				"status" => 200,
				"title" => "Estoque de ".$produto['nome'],
				"content" => XC_LoadPage('estoque'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirAlterarPreco") { 
			$id = XC_Secure($_GET['id']);
			$produto = XC_GetProduto($id);
			$xc['produto'] = $produto;
			$xc['precos'] = XC_GetPrecosProduto($id);
			$data = array(
				"status" => 200,
				"title" => "Preços do(a) ".$produto['nome'],
				"content" => XC_LoadPage('preco'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirRemoverProduto") { 
			$id = XC_Secure($_GET['id']);
			$produto = XC_GetProduto($id);
			$xc['produto'] = $produto;
			$data = array(
				"status" => 200,
				"title" => "Remover produto, ".$produto['nome'],
				"content" => XC_LoadPage('removerProduto'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirFecharMesa") { 
			$id = XC_Secure($_GET['id']);
			$mesa = XC_GetMesa($id);
			$xc['mesa'] = $mesa;
			$data = array(
				"status" => 200,
				"title" => "Fechar mesa, Nº ".$mesa['id_mesa'],
				"content" => XC_LoadPage('fecharMesa'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirRemoverCliente") { 
			$id = XC_Secure($_GET['id']);
			$cliente = XC_GetCliente($id);
			$xc['cliente'] = $cliente;
			$data = array(
				"status" => 200,
				"title" => "Remover cliente, ".$cliente['nome'],
				"content" => XC_LoadPage('removerCliente'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirRemoverDesconto") { 
			$id = XC_Secure($_GET['id']);
			$desconto = XC_GetDesconto($id);
			$xc['desconto'] = $desconto;
			$data = array(
				"status" => 200,
				"title" => "Remover Desconto",
				"content" => XC_LoadPage('removerDesconto'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "AbrirDarDescontoCliente") { 
			$id = XC_Secure($_GET['id']);
			$cliente = XC_GetCliente($id);
			$xc['cliente'] = $cliente;
			$xc['descontos'] = XC_GetDescontos($id);
			$data = array(
				"status" => 200,
				"title" => "Desconto cliente, ".$cliente['nome'],
				"content" => XC_LoadPage('darDescontoCliente'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "AbrirGerarQRCode") { 
			$id = XC_Secure($_GET['id']);
			$mesa = XC_GetMesa($id);
			$xc['mesa'] = $mesa;
			$data = array(
				"status" => 200,
				"title" => "QRCode mesa Nº ".$mesa['id_mesa'],
				"content" => XC_LoadPage('gerarQRCodeMesa'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "AbrirMesaQRCode") { 
			$data = array(
				"status" => 200,
				"title" => "Abrir Mesa QRCode",
				"content" => XC_LoadPage('abrirMesaQRCode'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "AbrirCarregarContaQRCode") { 
			$data = array(
				"status" => 200,
				"title" => "Carregar Conta QRCode",
				"content" => XC_LoadPage('carregarContaQRCode'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirFecharConta") { 
			$id = XC_Secure($_GET['id']);
			$conta = XC_GetConta($id);
			$xc['conta'] = $conta;
			$xc['carrinho'] = XC_GetCarrinhosConta($xc['conta']['id_conta']);
			$xc['comanda'] = XC_GetComanda($conta['comanda']);
			$xc['config'] = XC_GetConfig();
			$xc['cliente'] = XC_GetCliente($conta['cliente']);
			$xc['dinheiro'] = XC_Secure($_GET['dinheiro']);
			$xc['troco'] = XC_Secure($_GET['troco']);
			$data = array(
				"status" => 200,
				"title" => "Fechar conta",
				"content" => XC_LoadPage('fecharConta'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "abrirImprimirTodasAsComandas") { 
			$comandas = XC_GetTodasComandas();
			$xc['comandas'] = $comandas;
			$xc['config'] = XC_GetConfig();
			$data = array(
				"status" => 200,
				"title" => "Imprimir todas as comandas abertas",
				"content" => XC_LoadPage('imprimirTodasAsComandas'),
				"footer" => ''
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "atualizaProduto") {	
			$mediaFilename = "";
			$produto_data = array(
				"cod" => XC_Secure($_POST['cod']),
				"nome" => XC_Secure($_POST['nome']),
				"descricao" => XC_Secure($_POST['descricao']),
				"imagem" => $mediaFilename,
				"id_categoria" => XC_Secure($_POST['categoria']),
				"destino" => XC_Secure($_POST['destino'])
			);
			$error = "";
			$data = array();
			$produto_id = XC_AtualizaProduto ($produto_data);
			if ($produto_id) {
				$data = array(
					'status' => 200,
					'success' => "Produto atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "novaEntradaCaixa") {	
			$mediaFilename = "";
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$entrada_data = array(
				"valor" => XC_Secure($_POST['valor']),
				"motivo" => XC_Secure($_POST['motivo']),
				"responsavel" => XC_Secure($user['id_usuario']),
				"data" => time()
			);
			$error = "";
			$data = array();
			$entrada = XC_NovaEntradaCaixa ($entrada_data);
			if ($entrada) {
				$entradas = XC_GetQuantidadeEntradasCaixa();
				$saidas = XC_GetQuantidadeSaidasCaixa();
				$caixa = $entradas - $saidas;
				$caixa = round($caixa, 2);
				$data = array(
					'status' => 200,
					'entrada' => XC_GetEntradaCaixa($entrada),
					'entradas' => $entradas,
					'saidas' => $saidas,
					'caixa' => $caixa,
					'success' => "Entrada adicionada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "novaSaidaCaixa") {	
			$mediaFilename = "";
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$saida_data = array(
				"valor" => XC_Secure($_POST['valor']),
				"motivo" => XC_Secure($_POST['motivo']),
				"responsavel" => XC_Secure($user['id_usuario']),
				"data" => time()
			);
			$error = "";
			$data = array();
			$saida = XC_NovaSaidaCaixa ($saida_data);
			if ($saida) {
				$entradas = XC_GetQuantidadeEntradasCaixa();
				$saidas = XC_GetQuantidadeSaidasCaixa();
				$caixa = $entradas - $saidas;
				$caixa = round($caixa, 2);
				$data = array(
					'status' => 200,
					'saida' => XC_GetSaidaCaixa($saida),
					'entradas' => $entradas,
					'saidas' => $saidas,
					'caixa' => $caixa,
					'success' => "Saída adicionada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "atualizaEstoque") {	
			$mediaFilename = "";
			$estoque_data = array(
				"id" => XC_Secure($_POST['id']),
				"quantidade" => XC_Secure($_POST['quantidade']),
			);
			$error = "";
			$data = array();
			$produto_id = XC_AtualizaEstoque ($estoque_data);
			if ($produto_id) {
				$data = array(
					'status' => 200,
					'success' => "Estoque atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "getVendasDiarias") {	
			$mes = (isset($_GET['mes']) && !empty($_GET['mes'])) ? XC_Secure($_GET['mes']) : date("F");
			$ano = (isset($_GET['ano']) && !empty($_GET['ano'])) ? XC_Secure($_GET['ano']) : date("Y");
			$data = XC_GetVendasDiarias($mes, $ano);
			$datas = array();
			$meses = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "Octuber", "November", "December");
			for ($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, XC_IndexItemArray($meses, $mes), 2018);$i++) {
				$achou = false;
				foreach ($data as $d) {
					if ($i == date("d", $d['date'])) {
						$datas[] = $d;
						$achou = true;
					}
				}
				if (!$achou) {
					$datas[] = array ("date" => strtotime($ano."-".XC_IndexItemArray($meses, $mes)."-".$i), "units"=>0);
					$achou = false;
				}
			}
			header("Content-type: application/json");
			echo json_encode($datas);
			exit();
		}
		
		if ($f == "getEntradasDiarias") {	
			$mes = (isset($_GET['mes']) && !empty($_GET['mes'])) ? XC_Secure($_GET['mes']) : date("F");
			$ano = (isset($_GET['ano']) && !empty($_GET['ano'])) ? XC_Secure($_GET['ano']) : date("Y");
			$data = XC_GetEntradasDiariasCaixa($mes, $ano);
			$datas = array();
			$meses = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "Octuber", "November", "December");
			for ($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, XC_IndexItemArray($meses, $mes), 2018);$i++) {
				$achou = false;
				foreach ($data as $d) {
					if ($i == date("d", $d['x'])) {
						$datas[] = $d;
						$achou = true;
					}
				}
				if (!$achou) {
					$datas[] = array ("x" => strtotime($ano."-".XC_IndexItemArray($meses, $mes)."-".$i), "y"=>0);
					$achou = false;
				}
			}
			header("Content-type: application/json");
			echo json_encode($datas);
			exit();
		}
		
		if ($f == "getSaidasDiarias") {	
			$mes = (isset($_GET['mes']) && !empty($_GET['mes'])) ? XC_Secure($_GET['mes']) : date("F");
			$ano = (isset($_GET['ano']) && !empty($_GET['ano'])) ? XC_Secure($_GET['ano']) : date("Y");
			$data = XC_GetSaidasDiariasCaixa($mes, $ano);
			$datas = array();
			$meses = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "Octuber", "November", "December");
			for ($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, XC_IndexItemArray($meses, $mes), 2018);$i++) {
				$achou = false;
				foreach ($data as $d) {
					if ($i == date("d", $d['x'])) {
						$datas[] = $d;
						$achou = true;
					}
				}
				if (!$achou) {
					$datas[] = array ("x" => strtotime($ano."-".XC_IndexItemArray($meses, $mes)."-".$i), "y"=>0);
					$achou = false;
				}
			}
			header("Content-type: application/json");
			echo json_encode($datas);
			exit();
		}
		
		if ($f == "getRankingGarcons") {	
			$data = XC_TopGarcons();
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "getRankingProdutos") {	
			$data = XC_TopProdutos();
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "atualizaPreco") {	
			$mediaFilename = "";
			$preco_data = array(
				"produto" => XC_Secure($_POST['id']),
				"preco_compra" => XC_Secure($_POST['preco_compra']),
				"preco_venda" => XC_Secure($_POST['preco_venda']),
				"data" => time()
			);
			$error = "";
			$data = array();
			$preco_id = XC_NovoPrecoProduto ($preco_data);
			if ($preco_id) {
				$atualiza_produto_data = array(
					"produto" => XC_Secure($_POST['id']),
					"preco" => $preco_id
				);
				XC_AtualizaPrecoProduto($atualiza_produto_data);
				$preco = XC_GetPrecoProduto($preco_id);
				$preco['data'] = date("d/M/Y h:m", $preco['data']);
				$data = array(
					'status' => 200,
					'preco' => $preco,
					'success' => "Preço atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "darDescontoCliente") {	
			$desconto_data = array(
				"valor" => XC_Secure($_POST['valor']),
				"validade" => strtotime(XC_Secure($_POST['data']." ".$_POST['hora'])),
				"cliente" => XC_Secure($_POST['cliente']),
				"data" => time()
			);
			$error = "";
			$data = array();
			$desconto_id = XC_NovoDesconto ($desconto_data);
			if ($desconto_id) {
				$desconto = XC_GetDesconto($desconto_id);
				$desconto['validade'] = date("d/M/Y h:m",$desconto['validade']);
				$desconto['data'] = date("d/M/Y h:m",$desconto['data']);
				$data = array(
					'status' => 200,
					'desconto' => $desconto,
					'success' => "Desconto aplicado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "getProdutosCategoria") { 
			$id = XC_Secure($_GET['id']);
			$produtos = array();
			if ($id != 0) {
				$produtos = XC_GetProdutosDeCategoria($id);
			} else {
				$produtos = XC_GetProdutos();
			}
			$html = "";
			$xc['produtos'] = $produtos;
			$data = array(
				"status" => 200,
				"html" => XC_LoadPage('listCardProdutos')
			);
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "fecharConta") {
			$error = "";
			$data = array();
			$re_data = array(
				"conta" => XC_Secure($_POST['conta']),
				"total_venda" => XC_Secure($_POST['total_venda']),
				"percentual_garcom" => XC_Secure($_POST['percentual_garcom']),
				"desconto_cliente" => XC_Secure($_POST['desconto_cliente']),
				"dinheiro" => XC_Secure($_POST['dinheiro']),
				"troco" => XC_Secure($_POST['troco']),
				"total_pagar" => XC_Secure($_POST['total_pagar'])
			);
			$conta_id = XC_FecharConta ($re_data);
			if ($conta_id) {
				$data = array(
					'status' => 200,
					'success' => "Conta fechada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "alterarClienteConta") {
			$error = "";
			$data = array();
			$conta_id = XC_AlterarClienteConta (XC_Secure($_POST['conta']), XC_Secure($_POST['cliente']));
			if ($conta_id) {
				$data = array(
					'status' => 200,
					'cliente' => XC_GetCliente(XC_Secure($_POST['cliente'])),
					'success' => "Cliente de conta atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "fecharMesa") {
			$error = "";
			$data = array();
			$mesa_id = XC_FecharMesa ($_POST['id']);
			if ($mesa_id) {
				$data = array(
					'status' => 200,
					'success' => "Mesa fechada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "qrCodeAbreMesa") {
			$error = "";
			$data = array();
			$mesa_id = str_replace("wallass_love_teteh_","", base64_decode(($_GET['result'])));
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 1) {
				$re_data = array(
					"garcom" => $user['id_usuario'],
					"numero" => $mesa_id
				);
				$abrir = XC_AbrirMesa($re_data);
				if ($abrir) {
					$data = array(
						'status' => 200,
						'numero' => $mesa_id,
						'success' => "Mesa aberta com sucesso! Você será redirecionado em poucos segundos para página da mesa...."
					);
				} else {
					$data = array(
						'status' => 400,
						'error' => $error
					);
				} 
			} else {
				$data = array(
					'status' => 400,
					'error' => "Você não é um garçom!"
				);
			}
			
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "alteraContaUsuario") {
			$error = "";
			$re_data = array (
				"nome" => XC_Secure($_POST['nome']),
				"login" => XC_Secure($_POST['login'])
			);
			$data = array();
			$usuario = XC_AlterarContaUsuario ($re_data);
			if ($usuario) {
				$data = array(
					'status' => 200,
					'success' => "Conta atualizada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "alteraSenhaUsuario") {
			$error = "";
			$re_data = array (
				"senha" => XC_Crip(XC_Secure($_POST['senha'])),
				"nova_senha" => XC_Crip(XC_Secure($_POST['nova_senha'])),
				"confirma_senha" => XC_Crip(XC_Secure($_POST['confirma_senha']))
			);
			$data = array();
			$usuario = XC_AlterarSenhaUsuario ($re_data);
			if ($usuario) {
				$data = array(
					'status' => 200,
					'success' => "Senha atualizada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		if ($f == "alteraSenhaCliente") {
			$error = "";
			$re_data = array (
				"senha" => XC_Crip(XC_Secure($_POST['senha'])),
				"nova_senha" => XC_Crip(XC_Secure($_POST['nova_senha'])),
				"confirma_senha" => XC_Crip(XC_Secure($_POST['confirma_senha']))
			);
			$data = array();
			$usuario = XC_AlterarSenhaCliente ($re_data);
			if ($usuario) {
				$data = array(
					'status' => 200,
					'success' => "Senha atualizada com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		//Configuração
		if ($f == "configInfo") {
			$error = "";
			$data = array();
			$re_data = array(
				"empresa" => XC_Secure($_POST['empresa']),
				"descricao" => XC_Secure($_POST['descricao']),
				"telefone" => XC_Secure($_POST['telefone']),
				"exibir_logo_ou_nome" => XC_Secure($_POST['exibir_logo_ou_nome']),
				"email" => XC_Secure($_POST['email'])
			);
			$info = XC_ConfigInfo ($re_data);
			if ($info) {
				$data = array(
					'status' => 200,
					'success' => "Informações atualizas com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		if ($f == "configGarcom") {
			$error = "";
			$data = array();
			$re_data = array(
				"percentual_garcom" => XC_Secure($_POST['percentual_garcom']),
				"percentual_garcom_ativo" => XC_Secure($_POST['percentual_garcom_ativo']),
				"garcom_abre_mesa" => XC_Secure($_POST['garcom_abre_mesa'])
			);
			$configGarcom = XC_ConfigGarcom($re_data);
			if ($configGarcom) {
				$data = array(
					'status' => 200,
					'success' => "Informações sobre garçons atualizadas com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}

		if ($f == "configCliente") {
			$error = "";
			$data = array();
			$re_data = array(
				"valor_desconto_cadastro_cliente" => XC_Secure($_POST['valor_desconto_cadastro_cliente']),
				"desconto_cadastro_cliente" => XC_Secure($_POST['desconto_cadastro_cliente'])
			);
			$configCliente = XC_ConfigCliente($re_data);
			if ($configCliente) {
				$data = array(
					'status' => 200,
					'success' => "Informações sobre clientes atualizadas com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'error' => $error
				);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
		
		if ($f == "configLogo") {
			$mediaFilename	= "";		
			if (isset($_FILES['logo']['name'])) {
				$invalid_file = 0;
				if ($_FILES['logo']['size'] > 10000000) {
					$invalid_file = 1;
				} else {
					$fileInfo = array(
						'file' => $_FILES["logo"]["tmp_name"],
						'name' => $_FILES['logo']['name'],
						'size' => $_FILES["logo"]["size"],
						'type' => $_FILES["logo"]["type"]
					);
					$media    = XC_ShareFile($fileInfo);
					if (!empty($media)) {
						$mediaFilename = $media['filename'];
						$mediaName     = $media['name'];
					}
				}
			}
			
			$error = "";
			$data = array();
			$logo = XC_ConfigAlterarLogo ($mediaFilename);
			if ($logo) {
				$data = array(
					'status' => 200,
					'logo' => $mediaFilename,
					'invalid_file' => $invalid_file,
					'success' => "Logo atualizado com sucesso!"
				);
			} else {
				$data = array(
					'status' => 400,
					'invalid_file' => $invalid_file,
					'error' => $error
				);
				XC_Log($error);
			} 
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}

		
		if ($f ==  'addProduto') {
			$produto_data = array(
				"produto" => XC_Secure($_POST['produto']),
				"conta" => XC_Secure($_POST['conta_produto']),
				"garcom" => XC_Secure($_POST['garcom']),
				"situacao" => 0,
				"quantidade" => 1,
				"data" => time()
			);
			$addProduto = XC_AdicionaProdutoCarrinho($produto_data);
			$data = array();
			if ($addProduto) {
				$data = array(
					"status" => 200,
					"success" => "Produto adicionado com sucesso!",
				);
			} else {
				$data = array(
					"status" => 400,
					"error" => "Problemas em adicionar produto!",
				);
			}
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}

		if ($f == 'enviarPedidos') {
			$enviarPedidos = XC_EnviarPedidosCliente(XC_Secure($_POST['conta']));
			$id = XC_Secure($_POST['conta']);
			$conta = XC_GetConta($id);
			$xc['conta'] = $conta;
			$xc['config'] = XC_GetConfig();
			$data = array();
			if ($enviarPedidos) {
				$data = array(
					"status" => 200,
					"success" => "Produtos enviados com sucesso!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			} else {
				$data = array(
					"status" => 400,
					"error" => "Problemas em enviar produtos!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			}
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		if ($f == 'menosProdutoCarrinho') {
			$produto = XC_Secure($_POST['produto']);
			$conta = XC_Secure($_POST['conta']);
			$error = "";
			$menosProdutos = XC_MenosProdutoCarrinho ($produto, $conta);
			$conta = XC_GetConta($conta);
			$xc['conta'] = $conta;
			$xc['config'] = XC_GetConfig();
			$data = array();
			if ($menosProdutos) {
				$data = array(
					"status" => 200,
					"success" => "Produto removido com sucesso!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			} else {
				$data = array(
					"status" => 400,
					"error" => $error,
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			}
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		if ($f == 'maisProdutoCarrinho') {
			$produto_data = array(
				"produto" => XC_Secure($_POST['produto']),
				"conta" => XC_Secure($_POST['conta_produto']),
				"garcom" => XC_Secure($_POST['garcom']),
				"situacao" => 0,
				"quantidade" => 1,
				"data" => time()
			);
			$addProduto = XC_AdicionaProdutoCarrinho($produto_data);
			$id = XC_Secure($_POST['conta_produto']);
			$conta = XC_GetConta($id);
			$xc['conta'] = $conta;
			$xc['config'] = XC_GetConfig();
			$data = array();
			if ($addProduto) {
				$data = array(
					"status" => 200,
					"success" => "Produto adicionado com sucesso!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			} else {
				$data = array(
					"status" => 400,
					"error" => "Problemas em adicionar o produto!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			}
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		if ($f == 'removeCarrinho') {
			$id_produto = XC_Secure($_POST['produto']);
			$removeCarrinho = XC_RemoveCarrinhoProdutoCliente ($id_produto);
			$id = XC_Secure($_POST['conta']);
			$conta = XC_GetConta($id);
			$xc['conta'] = $conta;
			$xc['config'] = XC_GetConfig();
			$data = array();
			if ($removeCarrinho) {
				$data = array(
					"status" => 200,
					"success" => "Item removido com sucesso!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			} else {
				$data = array(
					"status" => 400,
					"error" => "Problemas em remover o item!",
					"content" => XC_LoadPage('verContaClienteLoadModal')
				);
			}
			header("Content-type: application/json");
			echo json_encode($data);
			exit();
		}
		
	} else {
		echo "Acesso inválido!";		
	}
?>