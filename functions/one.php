<?php

	function LM_LoadPage($page_url = '') {
		global $xc;
		$page         = './' . $page_url . '.phtml';
		$page_content = '';
		ob_start();
		require($page);
		$page_content = ob_get_contents();
		ob_end_clean();
		return $page_content;
	}

	function LM_Secure($string, $censored_words = 1, $br = true) {
		global $conn;
		$string = trim($string);
		$string = mysqli_real_escape_string($conn, $string);
		$string = htmlspecialchars($string, ENT_QUOTES);
		if ($br == true) {
			$string = str_replace('\r\n', " <br>", $string);
			$string = str_replace('\n\r', " <br>", $string);
			$string = str_replace('\r', " <br>", $string);
			$string = str_replace('\n', " <br>", $string);
		} else {
			$string = str_replace('\r\n', "", $string);
			$string = str_replace('\n\r', "", $string);
			$string = str_replace('\r', "", $string);
			$string = str_replace('\n', "", $string);
		}
		$string = stripslashes($string);
		$string = str_replace('&amp;#', '&#', $string);
		if ($censored_words == 1) {
			global $config;
			$censored_words = @explode(",", $config['censored_words']);
			foreach ($censored_words as $censored_word) {
				$censored_word = trim($censored_word);
				$string        = str_replace($censored_word, '****', $string);
			}
		}
		return $string;
	}
	
	function LM_Crip($string) {
		$string = LM_Secure($string);
		$string = hash("sha512", hash("whirlpool", sha1(md5($string))));
		return $string;
	}
	
	function LM_Login($login, $senha) {
		global $conn;
		if (!empty($login) && !empty($senha)) {
			$login = LM_Secure($login);
			$senha = LM_Crip(LM_Secure($senha));
			$sql = mysqli_query($conn,"SELECT * FROM logomaq_usuario WHERE username_usuario='{$login}' AND senha_usuario='{$senha}'"); 
			$rows = mysqli_num_rows($sql);
			
			if (!isset($_SESSION)) {
				session_start(); 
				ob_start();
			}

			if($rows == 1) {
				$_SESSION['usuario'] = $login;
				header("Location: ./");
			} else {
				unset($_SESSION['usuario']);
				header("location: ./?login_errado=erro&logar=errar");
			}
		} else {
			return false;
		}
	}
	
	
	function LM_NovoSlide ($re_data) {
		global $conn, $error;
		if (empty($re_data['imagem_slideshow'])) {
			$error = "Selecione uma imagem!";
			return false;
		} else if (empty($re_data['titulo_slideshow']) && !empty($re_data['descricao_slideshow'])) {
			$error = "Digite um título!";
		} else if (empty($re_data['rotulo_botao_slideshow']) && !empty($re_data['link_botao_slideshow'])) {
			$error = "Digite um rótulo para o botão!";
		} else if (!empty($re_data['rotulo_botao_slideshow']) && empty($re_data['link_botao_slideshow'])) {
			$error = "Digite um link para o botão!";
		}  else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO logomaq_slideshow ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function LM_NovoServico ($re_data) {
		global $conn, $error;
		if (empty($re_data['imagem_servico'])) {
			$error = "Selecione uma imagem!";
			return false;
		} else if (empty($re_data['titulo_servico'])) {
			$error = "Digite um título!";
		} else if (empty($re_data['categoria_servico'])) {
			$error = "Selecione uma categoria!";
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO logomaq_servicos ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function LM_AtualizaServico ($re_data) {
		global $conn, $error;
		if (empty($re_data['titulo_servico'])) {
			$error = "Digite um título!";
		} else if (empty($re_data['categoria_servico'])) {
			$error = "Selecione uma categoria!";
		} else {
			$servico = LM_GetServico($re_data['id_servico']);
			$query = false;
			if (!empty($re_data['imagem_servico'])) {
				unlink($servico['imagem_servico']);
				$query   = mysqli_query($conn, "UPDATE logomaq_servicos SET titulo_servico='".$re_data['titulo_servico']."', categoria_servico=".$re_data['categoria_servico'].",
											imagem_servico='".$re_data['imagem_servico']."' WHERE id_servico = ".$re_data['id_servico']."");
			} else {
				$query   = mysqli_query($conn, "UPDATE logomaq_servicos SET titulo_servico='".$re_data['titulo_servico']."', categoria_servico=".$re_data['categoria_servico']."
											WHERE id_servico = ".$re_data['id_servico']."");
			}
			
			return $query;
		}
	}
	
	function LM_NovaCategoria ($re_data) {
		global $conn, $error;
		if (empty($re_data['titulo_categoria'])) {
			$error = "Digite o título da categoria!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO logomaq_categoria_servico ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	
	function LM_ShareFile($data = array(), $type = 0) {
		global $conn;
		$allowed = '';
		if (empty($data)) {
			return false;
		}
		/*
		if (1 == 1) {
			if (isset($data['types'])) {
				$allowed = $data['types'];
			} else {
				$allowed = "jpg,png,jpeg,gif";
			}
		} else {
			$allowed = 'jpg,png,jpeg,gif';
		}*/
		$new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
		$extension_allowed = explode(',', $allowed);
		$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
		/*
		if (!in_array($file_extension, $extension_allowed)) {
			return false;
		}*/
		if ($data['size'] > 100000000) {
			return false;
		}
		if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
			$folder   = 'photos';
			$fileType = 'image';
		} else if ($file_extension == 'mp4' || $file_extension == 'mov' || $file_extension == 'webm' || $file_extension == 'flv') {
			$folder   = 'videos';
			$fileType = 'video';
		} else if ($file_extension == 'mp3' || $file_extension == 'wav') {
			$folder   = 'sounds';
			$fileType = 'soundFile';
		} else {
			$folder   = 'files';
			$fileType = 'file';
		}
		if (empty($folder) || empty($fileType)) {
			return false;
		}
		/*
		$mime_types = explode(',', str_replace(' ', '', "text/plain,video/mp4,video/mov,video/mpeg,video/flv,video/avi,video/webm,audio/wav,audio/mpeg,video/quicktime,audio/mp3,image/png,image/jpeg,image/gif,application/pdf,application/msword,application/zip,application/x-rar-compressed,text/pdf,application/x-pointplus,text/css". ',application/octet-stream'));
		if (!in_array($data['type'], $mime_types)) {
			return false;
		}*/
		$dir         = "upload";
		$filename    = $dir . '/' . XC_GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
		$second_file = pathinfo($filename, PATHINFO_EXTENSION);
		if (move_uploaded_file($data['file'], $filename)) {
			$last_data             = array();
			$last_data['filename'] = $filename;
			$last_data['name']     = $data['name'];
			return $last_data;
		}
	}
	
	function LM_GetSlides(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM logomaq_slideshow");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['link_botao_slideshow'] =  "<a href='".$final_fetched_data['link_botao_slideshow']."' target='_blank'>Ver</a>";
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function LM_GetCategorias(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM logomaq_categoria_servico");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function LM_GetServicos(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM logomaq_servicos");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['categoria_servico'] = LM_GetCategoria($final_fetched_data['categoria_servico']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	
	function LM_GetSlide($id){
		global $conn;
		$id = LM_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM logomaq_slideshow WHERE `id_slideshow` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function LM_GetServico($id){
		global $conn;
		$id = LM_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM logomaq_servicos WHERE `id_servico` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['categoria_servico'] = LM_GetCategoria($final_fetched_data['categoria_servico']);
			return $final_fetched_data;
		}
		return false;
	}
	
	function LM_GetCategoria($id){
		global $conn;
		$id = LM_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM logomaq_categoria_servico WHERE `id_categoria` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function LM_AtualizaSlide ($re_data) {
		global $conn, $error;
		if (empty($re_data['titulo_slideshow']) && !empty($re_data['descricao_slideshow'])) {
			$error = "Digite um título!";
		} else if (empty($re_data['rotulo_botao_slideshow']) && !empty($re_data['link_botao_slideshow'])) {
			$error = "Digite um rótulo para o botão!";
		} else if (!empty($re_data['rotulo_botao_slideshow']) && empty($re_data['link_botao_slideshow'])) {
			$error = "Digite um link para o botão!";
		} else {
			$query   = mysqli_query($conn, "UPDATE logomaq_slideshow SET titulo_slideshow='".$re_data['titulo_slideshow']."', descricao_slideshow='".$re_data['descricao_slideshow']."',
											rotulo_botao_slideshow='".$re_data['rotulo_botao_slideshow']."', link_botao_slideshow='".$re_data['link_botao_slideshow']."', visivel_slideshow=".$re_data['visivel_slideshow']." WHERE id_slideshow = ".$re_data['id_slideshow']."");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}
	}
	
	function LM_AtualizaCategoira ($re_data) {
		global $conn, $error;
		if (empty($re_data['titulo_categoria'])) {
			$error = "Digite um título!";
		}else {
			$query   = mysqli_query($conn, "UPDATE logomaq_categoria_servico SET titulo_categoria='".$re_data['titulo_categoria']."' WHERE id_categoria = ".$re_data['id_categoria']."");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}
	}
	
	function LM_RemoveSlide($slide_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$slide_id = LM_Secure($slide_id);
		if (empty($slide_id) OR !intval($slide_id)) {
			$error = "Slide inválido!";
			return false;			
		} else {
			$slide = LM_GetSlide($slide_id);
			$query   = mysqli_query($conn, "DELETE FROM logomaq_slideshow WHERE `id_slideshow`={$slide_id}");
			if ($query) {
				unlink("".$slide['imagem_slideshow']);
				return true;
			} else {
				return false;
			}
		}
	}
	
	function LM_RemoveServico($servico_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$servico_id = LM_Secure($servico_id);
		if (empty($servico_id) OR !intval($servico_id)) {
			$error = "Serviço inválido!";
			return false;			
		} else {
			$servico = LM_GetServico($servico_id);
			$query   = mysqli_query($conn, "DELETE FROM logomaq_servicos WHERE `id_servico`={$servico_id}");
			if ($query) {
				unlink("".$servico['imagem_servico']);
				return true;
			} else {
				return false;
			}
		}
	}
	
	function LM_RemoveCategoria($categoria_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$categoria_id = LM_Secure($categoria_id);
		if (empty($categoria_id) OR !intval($categoria_id)) {
			$error = "Categoria inválida!";
			return false;			
		} else {
			$query   = mysqli_query($conn, "DELETE FROM logomaq_categoria_servico WHERE `id_categoria`={$categoria_id}");
			if ($query) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	function LM_AtualizaSlideVisibilidade ($slide_id, $visibilidade) {
		global $conn, $error;
		$slide_id = LM_Secure($slide_id);
		if (empty($slide_id) OR !intval($slide_id)) {
			$error = "Slide inválido!";
		} else {
			$query   = mysqli_query($conn, "UPDATE logomaq_slideshow SET visivel_slideshow=".LM_Secure($visibilidade)." WHERE id_slideshow = ".$slide_id."");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}
	}
	
	
	function XC_LoginCliente($login, $senha) {
		global $conn;
		if (!empty($login) && !empty($senha)) {
			$login = XC_Secure($login);
			$senha = XC_Crip(XC_Secure($senha));
			$conditions = "";
			if ($mobile) {
				$conditions += "AND nivel !=2";
			}
			$sql = mysqli_query($conn,"SELECT * FROM cliente WHERE login='{$login}' AND senha='{$senha}'"); 
			$rows = mysqli_num_rows($sql);			
			if (!isset($_SESSION)) {
				session_start(); 
				ob_start();
			}

			if($rows == 1 ){
				$_SESSION['cliente_session'] = $login;
				unset($_SESSION['login_session']);
				header("Location:cliente.php?btn=inicio");
			} else {
				unset($_SESSION['cliente_session']);
				header("location:logarCliente.php?login_errado=erro&logar=errar");
			}
		} else {
			return false;
		}
	}
	
	function XC_LoginClienteAnonimo($senha) {
		global $conn;
		if (!empty($senha)) {
			$senha = XC_Secure($senha);
			$sql = mysqli_query($conn,"SELECT * FROM conta WHERE senha='{$senha}'"); 
			$fetch = mysqli_fetch_assoc($sql);
			$rows = mysqli_num_rows($sql);			
			if (!isset($_SESSION)) {
				session_start(); 
				ob_start();
			}

			if($rows == 1){
				$_SESSION['cliente_anonimo_session'] = $fetch['cliente_anonimo'];
				unset($_SESSION['login_session']);
				unset($_SESSION['cliente_session']);
				header("Location:cliente.php?btn=inicio");
			} else {
				unset($_SESSION['cliente_anonimo_session']);
				header("location:logarClienteAnonimo.php?login_errado=erro&logar=errar&senha=".$senha);
			}
		} else {
			return false;
		}
	}
	
	function XC_CheckUsuario($login) {
		global $conn;
		if (!empty($login)) {
			$login = XC_Secure($login);
			$sql = mysqli_query($conn, "SELECT * FROM usuario WHERE login='{$login}'");
			$rows = mysqli_num_rows($sql);
			return ($rows > 0);			
		} else {
			return false;
		}
	}

	function XC_CheckClienteAssosiado($id) {
		global $conn;
		if (!empty($id)) {
			$id = XC_Secure($id);
			$sql = mysqli_query($conn, "SELECT * FROM clientes_associados WHERE cliente_xcomanda='{$id}'");
			$rows = mysqli_num_rows($sql);
			return ($rows > 0);			
		} else {
			return false;
		}
	}
	
	function XC_CheckCliente($login) {
		global $conn;
		if (!empty($login)) {
			$login = XC_Secure($login);
			$sql = mysqli_query($conn, "SELECT * FROM cliente WHERE login='{$login}'");
			$rows = mysqli_num_rows($sql);
			return ($rows > 0);			
		} else {
			return false;
		}
	}
	
	function XC_NovoUsuario ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['login'])) {
			$error = "Login de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['senha'])) {
			$error = "Senha de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['nivel']) && $re_data['nivel'] != '0') {
			$error = "Nível de usuário não pode ser vazio!";
			return false;
		} else {
			if (XC_CheckUsuario($re_data['login'])) {
				$error = "Já existe um usuário cadastrado com este login!";
				return false;	
			} else {
				$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
				$data    = '\'' . implode('\', \'', $re_data) . '\'';
				$query   = mysqli_query($conn, "INSERT INTO usuario ({$fields}) VALUES ({$data})");
				return mysqli_insert_id($conn);
			}
		}
	}
	
	function XC_AtualizaUsuario ($re_data, $atualLogin) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['login'])) {
			$error = "Login de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['nivel']) && $re_data['nivel'] != '0') {
			$error = "Nível de usuário não pode ser vazio!";
			return false;
		} else {
			if ($re_data['login'] != $atualLogin && XC_CheckUsuario($re_data['login'])) {
				$error = "Já existe um usuário cadastrado com este login!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE usuario SET `nome`='".$re_data['nome']."', `login`='".$re_data['login']."', `nivel`=".$re_data['nivel']." WHERE `id_usuario`=".$re_data['id_usuario']."");
				if ($query) {
					return true;
				} else {
					false;
				}
			}
		}
	}
	
	function XC_RemoveUsuario($user_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$user_id = XC_Secure($user_id);
		if (empty($user_id) OR !intval($user_id)) {
			$error = "Usuário inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM usuario WHERE `id_usuario`={$user_id}");
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveProduto($produto_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$produto_id = XC_Secure($produto_id);
		if (empty($produto_id) OR !intval($produto_id)) {
			$error = "Produto inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM produtos WHERE `cod`={$produto_id}");
				if ($query) {
					XC_RemoveEstoque($produto_id);
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveEstoque($produto_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$produto_id = XC_Secure($produto_id);
		if (empty($produto_id) OR !intval($produto_id)) {
			$error = "Produto inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM estoque WHERE `produto`={$produto_id}");
				if ($query) {
					$sql = mysqli_query($conn, "SELECT * FROM estoque WHERE `produto`={$produto_id}");
					$fetch = mysqli_fetch_assoc($sql);
					$estoque_id = $fetch['id'];
					XC_RemoveEstoqueEntradas($estoque_id);
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveEstoqueEntradas($estoque_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$estoque_id = XC_Secure($estoque_id);
		if (empty($estoque_id) OR !intval($estoque_id)) {
			$error = "Estoque inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM estoque_entradas WHERE `estoque`={$estoque_id}");
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveMesa($mesa_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		if (empty($mesa_id) OR !intval($mesa_id)) {
			$error = "Mesa inválida!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM mesa WHERE `id_mesa`={$mesa_id}");
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_NovoProduto ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['imagem'])) {
			$error = "Selecione uma imagem!";
			return false;
		} else if (empty($re_data['id_categoria'])) {
			$error = "Categoria não pode ser vazia!";
			return false;
		} else if (empty($re_data['destino']) && is_numeric($re_data['destino']) && $re_data['destino'] != 0) {
			$error = "Destino inválido!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO produtos ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_AtualizaPrecoProduto ($re_data) {
		global $conn, $error;
		if (empty($re_data['produto']) && !is_numeric($re_data['produto'])) {
			$error = "Preço inválido!";
			return false;
		} else if (empty($re_data['preco']) && !is_numeric($re_data['preco'])) {
			$error = "Preço inválido!";
			return false;
		} else {
			$query   = mysqli_query($conn, "UPDATE produtos SET preco=".$re_data['preco']." WHERE cod = ".$re_data['produto']."");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}
	}
	
	function XC_AtualizaProduto ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome de usuário não pode ser vazio!";
			return false;
		}else if (empty($re_data['id_categoria'])) {
			$error = "Categoria não pode ser vazia!";
			return false;
		} else if (empty($re_data['destino']) && !is_numeric($re_data['destino']) && $re_data['destino'] != '0') {
			$error = "Destino inválido!";
			return false;
		} else {
			$query   = mysqli_query($conn, "UPDATE produtos SET nome='".$re_data['nome']."', descricao='".$re_data['descricao']."',
											id_categoria=".$re_data['id_categoria'].", destino=".$re_data['destino']." WHERE cod = ".$re_data['cod']."");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}
	}
	
	function XC_NovaEntradaEstoque ($re_data) {
		global $conn, $error;
		if (empty($re_data['estoque']) or !is_numeric($re_data['estoque'])) {
			return false;
		} if (empty($re_data['quantidade_anterior']) or !is_numeric($re_data['quantidade_anterior'])) {
			return false;
		} if (empty($re_data['quantidade_atual']) or !is_numeric($re_data['quantidade_atual'])) {
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO estoque_entradas ({$fields}) VALUES ({$data})");
			$estoque = XC_GetEstoque($re_data['estoque']);
			$produto = XC_GetProduto($estoque['produto']);
			$valor = $produto['preco']['preco_compra'] * ($re_data['quantidade_atual'] - $re_data['quantidade_anterior']);
			$saida_data = array(
				"valor" => XC_Secure($valor),
				"motivo" => XC_Secure("Compra de ".($re_data['quantidade_atual'] - $re_data['quantidade_anterior'])." unidades do produto ".$produto['nome']),
				"responsavel" => XC_Secure($user['id_usuario']),
				"data" => time()
			);
			$saida = XC_NovaSaidaCaixa ($saida_data);
			return mysqli_insert_id($conn);
		}
	}
	
	
	
	function XC_NovaEntradaCaixa ($re_data) {
		global $conn, $error;
		if (empty($re_data['responsavel']) or !is_numeric($re_data['responsavel'])) {
			$error = "Responsável inválido!";
			return false;
		} if (empty($re_data['valor']) or !is_numeric($re_data['valor'])) {
			$error = "Valor inválido!";
			return false;
		} if (empty($re_data['motivo'])) {
			$error = "Deve ser apresentado um motivo!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO caixa_entradas ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_NovaSaidaCaixa ($re_data) {
		global $conn, $error;
		if (empty($re_data['responsavel']) or !is_numeric($re_data['responsavel'])) {
			$error = "Responsável inválido!";
			return false;
		} if (empty($re_data['valor']) or !is_numeric($re_data['valor'])) {
			$error = "Valor inválido!";
			return false;
		} if (empty($re_data['motivo'])) {
			$error = "Deve ser apresentado um motivo!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO caixa_saidas ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_NovaSaidaEstoque ($re_data) {
		global $conn, $error;
		if (empty($re_data['estoque']) or !is_numeric($re_data['estoque'])) {
			return false;
		} if (empty($re_data['quantidade_anterior']) or !is_numeric($re_data['quantidade_anterior'])) {
			return false;
		} if (empty($re_data['quantidade_atual']) or !is_numeric($re_data['quantidade_atual'])) {
			return false;
		} if (empty($re_data['usuario']) or !is_numeric($re_data['usuario'])) {
			return false;
		}  if (empty($re_data['tipo'])) {
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO estoque_saidas ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_NovoEstoque ($re_data) {
		global $conn, $error;
		if (empty($re_data['produto']) or !is_numeric($re_data['produto'])) {
			return false;
		} else if (empty($re_data['quantidade']) or !is_numeric($re_data['quantidade'])) {
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO estoque ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_NovoPrecoProduto ($re_data) {
		global $conn, $error;
		if (empty($re_data['produto']) or !is_numeric($re_data['produto'])) {
			return false;
		} else if (empty($re_data['preco_compra']) or !is_numeric($re_data['preco_compra'])) {
			return false;
		} else if (empty($re_data['preco_venda']) or !is_numeric($re_data['preco_venda'])) {
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO preco_produto ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_AtualizaEstoque ($re_data, $tipo = "Administração") {
		global $conn, $error;
		if (empty($re_data['id']) or !is_numeric($re_data['id'])) {
			return false;
		} else if (empty($re_data['quantidade']) or !is_numeric($re_data['quantidade'])) {
			return false;
		} else {
			$estoque = XC_GetEstoque($re_data['id']);
			$query   = mysqli_query($conn, "UPDATE estoque SET `quantidade`=".$re_data['quantidade']." WHERE `id`=".$re_data['id']."");
			if ($query) {
				if (!isset($_SESSION)) {
					session_start();
				}
				$usuario = XC_GetUsuarioFromLogin($_SESSION['login_session']);
				if ($estoque['quantidade'] < $re_data['quantidade']) {
					$data_entrada = array(
						"estoque" => $re_data['id'],
						"quantidade_anterior" => $estoque['quantidade'],
						"quantidade_atual" => $re_data['quantidade'],
						"data" => time(),
						"usuario" => $usuario['id_usuario']
					);
					XC_NovaEntradaEstoque($data_entrada);
				} else if ($estoque['quantidade'] > $re_data['quantidade']) {
					
					$data_retirada = array(
						"estoque" => $re_data['id'],
						"quantidade_anterior" => $estoque['quantidade'],
						"quantidade_atual" => $re_data['quantidade'],
						"data" => time(),
						"usuario" => $usuario['id_usuario'],
						"tipo" => $tipo
					);
					XC_NovaSaidaEstoque($data_retirada);
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	function XC_NovoCliente ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome não pode ser vazio!";
			XC_Log("Oi 1");
			return false;
		} else if (empty($re_data['sobrenome'])) {
			$error = "Sobrenome não pode ser vazio!";
			XC_Log("Oi 2");
			return false;
		} else if (empty($re_data['login'])) {
			$error = "Login não pode ser vazio!";
			XC_Log("Oi 3");
			return false;
		} else if (empty($re_data['senha'])) {
			$error = "Senha não pode ser vazio!";
			XC_Log("Oi 4");
			return false;
		} else if (!empty($re_data['numero']) or !is_numeric($re_data['numero'])){
			$error = "Número de residência inválido!";
			XC_Log("Oi 5");
			return false;
		} else {
			XC_Log("Oi 6");
			if (!XC_CheckCliente($re_data['login'])) {
				XC_Log("Oi 7");
				$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
				$data    = '\'' . implode('\', \'', $re_data) . '\'';
				$query   = mysqli_query($conn, "INSERT INTO cliente ({$fields}) VALUES ({$data})");
				$id_cliente = mysqli_insert_id($conn);
				XC_Log("Oi 8");
				XC_Log("Cliente id".$id_cliente);
				XC_Log("Oi 9");
				$config = XC_GetConfig();
				if ($config['desconto_cadastro_cliente'] == 1) {
					XC_Log("Oi 10");
					$desconto_data = array(
						"valor" => XC_Secure($config['valor_desconto_cadastro_cliente']),
						"validade" => strtotime("+2 days", date("d/M/y h:m")),
						"cliente" => XC_Secure($id_cliente),
						"data" => time()
					);
					XC_Log("Oi 11");
					$desconto_id = XC_NovoDesconto ($desconto_data);
					XC_Log("Oi 12");
				}
				return $id_cliente;
			} else {
				XC_Log("Oi 13");
				$error = "Já existe um cliente com esse login!";
				return false;
			}
		}
	}
	
	function XC_AtualizaContaCliente ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome não pode ser vazio!";
			return false;
		} else if (empty($re_data['sobrenome'])) {
			$error = "Sobrenome não pode ser vazio!";
			return false;
		} else if (empty($re_data['login'])) {
			$error = "Login não pode ser vazio!";
			return false;
		} else if (!empty($re_data['numero']) && !is_numeric($re_data['numero'])){
			$error = "Número de residência inválido!";
			return false;
		} else {
			if (!($re_data['login'] != $re_data['login_antigo']) OR !XC_CheckCliente($re_data['login'])) {
				$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
				$data    = '\'' . implode('\', \'', $re_data) . '\'';
				$query   = mysqli_query($conn, "UPDATE `cliente` SET `nome`='".$re_data['nome']."',`sobrenome`='".$re_data['sobrenome']."',`login`='".$re_data['login']."',`telefone`='".$re_data['telefone']."',`cidade`='".$re_data['cidade']."',`email`='".$re_data['email']."',`bairro`='".$re_data['bairro']."',`rua`='".$re_data['rua']."',`numero`='".$re_data['numero']."' WHERE `id` = ".$re_data['id']."");
				return true;
			} else {
				$error = "Já existe um cliente com esse login!";
				return false;
			}
		}
	}
	
	function XC_GetUsuario($id){
		global $conn;
		$id = XC_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT id_usuario, nome, login, nivel FROM usuario WHERE `id_usuario` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetEstoque($id){
		global $conn;
		$id = XC_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM estoque WHERE `id` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetEstoqueFromProduto($produto_id){
		global $conn;
		$produto_id = XC_Secure($produto_id);
		if (empty($produto_id) || !is_numeric($produto_id) || $produto_id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM estoque WHERE `produto` = {$produto_id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetPrecoProduto($id){
		global $conn;
		$id = XC_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM preco_produto WHERE `id_preco` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetProduto($id){
		global $conn;
		$id = XC_Secure($id);
		if (empty($id) || !is_numeric($id) || $id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM produtos WHERE `cod` = {$id}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['preco'] = XC_GetPrecoProduto($final_fetched_data['preco']);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetUltimoProdutoCarrinhoConta($produto_id, $conta_id){
		global $conn;
		$produto_id = XC_Secure($produto_id);
		$conta_id = XC_Secure($conta_id);
		if (empty($produto_id) || !is_numeric($produto_id) || $produto_id < 1) {
			return false;
		}
		if (empty($conta_id) || !is_numeric($conta_id) || $conta_id < 1) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM carrinho WHERE `produto` = {$produto_id} AND `conta`= {$conta_id} ORDER BY `data` DESC LIMIT 1");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);;
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetUsuarioFromLogin($login){
		global $conn;
		$login = XC_Secure($login);
		if (empty($login)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT id_usuario, nome, login, nivel FROM usuario WHERE `login` = '{$login}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetCategoria($id_categoria){
		global $conn;
		$id_categoria = XC_Secure($id_categoria);
		if (empty($id_categoria)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM categoria WHERE `id_categoria` = {$id_categoria}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetQuantidadeEntradasCaixa(){
		global $conn;
		$query = mysqli_query($conn, "SELECT SUM(valor) as total_valor FROM caixa_entradas");
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return round($final_fetched_data['total_valor'],2);
		return false;
	}
	
	function XC_GetQuantidadeSaidasCaixa(){
		global $conn;
		$query = mysqli_query($conn, "SELECT SUM(valor) as total_valor FROM caixa_saidas");
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return round($final_fetched_data['total_valor'], 2);
		return false;
	}
	
	function XC_GetEntradaCaixa($id_entrada){
		global $conn;
		$id_entrada = XC_Secure($id_entrada);
		if (empty($id_entrada)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM caixa_entradas WHERE `id_entrada` = {$id_entrada}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['data'] = date("d/M/Y h:m", $final_fetched_data['data']);
			$final_fetched_data['responsavel'] = XC_GetUsuario($final_fetched_data['responsavel']);
			$final_fetched_data['valor'] = round($final_fetched_data['valor'], 2);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetSaidaCaixa($id_saida){
		global $conn;
		$id_saida = XC_Secure($id_saida);
		if (empty($id_saida)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM caixa_saidas WHERE `id_saida` = {$id_saida}");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['data'] = date("d/M/Y h:m", $final_fetched_data['data']);
			$final_fetched_data['responsavel'] = XC_GetUsuario($final_fetched_data['responsavel']);
			$final_fetched_data['valor'] = round($final_fetched_data['valor'], 2);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetClienteFromLogin($login){
		global $conn;
		$login = XC_Secure($login);
		if (empty($login)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM cliente WHERE `login` = '{$login}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data['senha'] = '';
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetEntradasEstoque($estoque_id){
		global $conn;
		$estoque_id = XC_Secure($estoque_id);
		$data = array();
		if (empty($estoque_id)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM estoque_entradas WHERE `estoque` = '{$estoque_id}' ORDER BY id_entrada DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['data'] = date("d/M/y h:m", $final_fetched_data['data']);
			$final_fetched_data['usuario'] = XC_GetUsuario($final_fetched_data['usuario']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetPrecosProduto($produto_id){
		global $conn;
		$produto_id = XC_Secure($produto_id);
		$data = array();
		if (empty($produto_id)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM preco_produto WHERE `produto` = '{$produto_id}' ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetSaidasEstoque($estoque_id){
		global $conn;
		$estoque_id = XC_Secure($estoque_id);
		$data = array();
		if (empty($estoque_id)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM estoque_saidas WHERE `estoque` = '{$estoque_id}' ORDER BY id_saida DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['data'] = date("d/M/y h:m", $final_fetched_data['data']);
			$final_fetched_data['usuario'] = XC_GetUsuario($final_fetched_data['usuario']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetSaidasCaixa(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM caixa_saidas ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['data'] = date("d/M/Y h:m", $final_fetched_data['data']);
			$final_fetched_data['responsavel'] = XC_GetUsuario($final_fetched_data['responsavel']);
			$final_fetched_data['valor'] = round($final_fetched_data['valor'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetEntradasCaixa(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM caixa_entradas ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['data'] = date("d/M/Y h:m", $final_fetched_data['data']);
			$final_fetched_data['responsavel'] = XC_GetUsuario($final_fetched_data['responsavel']);
			$final_fetched_data['valor'] = round($final_fetched_data['valor'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetProdutosDeCategoria($id_categoria){
		global $conn;
		$id_categoria = XC_Secure($id_categoria);
		$data = array();
		if (empty($id_categoria)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM produtos WHERE `id_categoria` = '{$id_categoria}' ORDER BY cod DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['preco'] = XC_GetPrecoProduto($final_fetched_data['preco']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetProdutos(){
		global $conn;
		$query = mysqli_query($conn, "SELECT * FROM produtos ORDER BY cod DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['preco'] = XC_GetPrecoProduto($final_fetched_data['preco']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetProdutosCardapio(){
		global $conn;
		$query = mysqli_query($conn, "SELECT * FROM produtos GROUP BY id_categoria ORDER BY nome DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['categoria'] = XC_GetCategoria($final_fetched_data['id_categoria']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetCategoriasProdutos(){
		global $conn;
		$query = mysqli_query($conn, "SELECT * FROM categoria ORDER BY nome ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetClientes(){
		global $conn;
		$query = mysqli_query($conn, "SELECT * FROM cliente ORDER BY nome ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['nome_completo'] = $final_fetched_data['nome']." ".$final_fetched_data['sobrenome'];
			$data[] = $final_fetched_data;

		}
		return $data;
	}
	
	function XC_IsFileAllowed($file_name) {
		// $file_name = $_FILES['test']['name'];
		$new_string        = pathinfo($file_name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		$extension_allowed = explode(',', "jpg,png,jpeg,gif");
		$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
		if(!in_array($file_extension, $extension_allowed)){
			return false;
		}
		return true;
	}
	
	
	
	function XC_CompressImage($source_url, $destination_url, $quality) {
		$imgsize = getimagesize($source_url);
		$finfof  = $imgsize['mime'];
		$image_c = 'imagejpeg';
		if ($finfof == 'image/jpeg') {
			$image = @imagecreatefromjpeg($source_url);
		} else if ($finfof == 'image/gif') {
			$image = @imagecreatefromgif($source_url);
		} else if ($finfof == 'image/png') {
			$image = @imagecreatefrompng($source_url);
		} else {
			$image = @imagecreatefromjpeg($source_url);
		}
		if (function_exists('exif_read_data')) {
			$exif = @exif_read_data($source_url);
			if (!empty($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 3:
						$image = @imagerotate($image, 180, 0);
						break;
					case 6:
						$image = @imagerotate($image, -90, 0);
						break;
					case 8:
						$image = @imagerotate($image, 90, 0);
						break;
				}
			}
		}
		@imagejpeg($image, $destination_url, $quality);
		return $destination_url;
	}
	
	function XC_Log($text) {
		global $conn;
		if (empty($text)) {
			return false;
		} else {
			$sql = mysqli_query($conn, "INSERT INTO log (text) VALUES ('{$text}')");
			return true;
		}
	}

	function XC_NovoClienteAssociado($id) {
		global $conn;
		$id = XC_Secure($id);
		if (empty($id)) {
			return false;
		} else {
			$sql = mysqli_query($conn, "INSERT INTO clientes_associados (cliente_xcomanda, time) VALUES ({$id}, ".time().")");
			return true;
		}
	}
	
	function XC_GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
		$charset = '';
		if ($uselower) {
			$charset .= "abcdefghijklmnopqrstuvwxyz";
		}
		if ($useupper) {
			$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		if ($usenumbers) {
			$charset .= "123456789";
		}
		if ($usespecial) {
			$charset .= "~@#$%^*()_+-={}|][";
		}
		if ($minlength > $maxlength) {
			$length = mt_rand($maxlength, $minlength);
		} else {
			$length = mt_rand($minlength, $maxlength);
		}
		$key = '';
		for ($i = 0; $i < $length; $i++) {
			$key .= $charset[(mt_rand(0, strlen($charset) - 1))];
		}
		return $key;
	}
	
	function XC_Time_Elapsed_String($ptime) {
		$etime = time() - $ptime;
		if ($etime < 1) {
			return '0 segundos';
		}
		$a        = array(
			365 * 24 * 60 * 60 => "ano",
			30 * 24 * 60 * 60 => "mês",
			24 * 60 * 60 => "dia",
			60 * 60 => "hora",
			60 => "minuto",
			1 => "segundo"
		);
		$a_plural = array(
			$wo['lang']['year'] => "anos",
			$wo['lang']['month'] => "meses",
			$wo['lang']['day'] => "dias",
			$wo['lang']['hour'] => "horas",
			$wo['lang']['minute'] => "minutos",
			$wo['lang']['second'] => "segundos"
		);
		foreach ($a as $secs => $str) {
			$d = $etime / $secs;
			if ($d >= 1) {
				$r = round($d);
				if ($wo['language_type'] == 'rtl') {
					$time_ago = "atrás" . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
				} else {
					$time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . "atrás";
				}
				return $time_ago;
			}
		}
	}
	
	function XC_AbrirMesa ($re_data) {
		global $conn, $error;
		if (empty($re_data['garcom']) or !is_numeric($re_data['garcom'])) {
			$error = "Garçom inválido!";
			return false;
		} else if (empty($re_data['numero']) or !is_numeric($re_data['numero'])) {
			$error = "Número de mesa inválido!";
			return false;
		} else {
			$query   = mysqli_query($conn, "UPDATE mesa SET situacao = '1' WHERE id_mesa = '".$re_data['numero']."'");
			if ($query) {
				$comanda_data = array(
					"mesa" => $re_data['numero'],
					"garcom" => $re_data['garcom'],
					"aberta" => time(),					
					"estado" => 1,					
					"data" => time()					
				);
				XC_NovaComanda ($comanda_data);
				return true;
			} else {
				$error =  mysqli_error($conn);
				return false;
			}
		}
	}
	
	
	function XC_AlterarGarcom ($re_data) {
		global $conn, $error;
		if (empty($re_data['garcom']) or !is_numeric($re_data['garcom'])) {
			$error = "Garçom inválido!";
			return false;
		} else if (empty($re_data['comanda']) or !is_numeric($re_data['comanda'])) {
			$error = "Número de comanda inválido!";
			return false;
		} else {
			$query   = mysqli_query($conn, "UPDATE comanda SET garcom = '".$re_data['garcom']."' WHERE id_comanda = '".$re_data['comanda']."'");
			if ($query) {
				return true;
			} else {
				$error =  mysqli_error($conn);
				return false;
			}
		}
	}
		
	function XC_NovaComanda ($re_data) {
		global $conn, $error;
		if (empty($re_data['mesa']) or !is_numeric($re_data['mesa'])) {
			$error = "Mesa inválida!";
			return false;
		} else if (empty($re_data['garcom']) or !is_numeric($re_data['garcom'])) {
			$error = "Garçom inválido!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO comanda ({$fields}) VALUES ({$data})");
			if ($query) {
				$conta_data = array(
					"comanda" => mysqli_insert_id($conn),
					"estado" => 1,
					"data" => time()
				);
				XC_NovaConta($conta_data);	
			} else {
				return false;
			}
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_NovaConta ($re_data) {
		global $conn, $error;
		if (empty($re_data['comanda']) or !is_numeric($re_data['comanda'])) {
			$error = "Comanda inválida!";
			return false;
		} else {
			$re_data['cliente_anonimo'] = "anonimo_".time();
			$re_data['senha'] = XC_GerarSenha(6, 8);
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO conta ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_GetMesa($id_mesa){
		global $conn;
		$id_mesa = XC_Secure($id_mesa);
		if (empty($id_mesa)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM mesa WHERE `id_mesa` = '{$id_mesa}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetTodasComandas(){
		global $conn;
		$query = mysqli_query($conn, "SELECT * FROM mesa WHERE situacao=1");
		$data = array();
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['comanda'] = XC_GetComanda(XC_GetAtualComandaDeMesa($final_fetched_data['id_mesa']));
			$data[] =  $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetComanda($id){
		global $conn;
		$id = XC_Secure($id);
		if (empty($id)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM comanda WHERE `id_comanda` = '{$id}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['contas'] = XC_GetContasComanda($id);
			return $final_fetched_data;
		}
		return false;
	}
	
	function XC_GetAtualComandaDeMesa($id_mesa){
		global $conn;
		$id_mesa = XC_Secure($id_mesa);
		if (empty($id_mesa)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT id_comanda FROM comanda WHERE `mesa` = '{$id_mesa}' AND `estado` = 1 ORDER BY `id_comanda` DESC LIMIT 1");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data['id_comanda'];
		} else {
			return 0;
		}
	}
	
	function XC_GetCliente($id_cliente){
		global $conn;
		$id_cliente = XC_Secure($id_cliente);
		if (empty($id_cliente)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM cliente WHERE `id` = '{$id_cliente}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['descontos_ativos'] = XC_GetDescontosAtivos($id_cliente);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_GetDesconto($id_desconto){
		global $conn;
		$id_desconto = XC_Secure($id_desconto);
		if (empty($id_desconto)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM desconto WHERE `id_desconto` = '{$id_desconto}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_GetDescontos($id_cliente){
		global $conn;
		$data = array();
		if (empty($id_cliente) OR !is_numeric($id_cliente)) {
			return false;
		} else {
			$query = mysqli_query($conn, "SELECT * FROM desconto WHERE cliente = {$id_cliente} ORDER BY validade DESC");
			while ($final_fetched_data = mysqli_fetch_assoc($query)) {
				$data[] = $final_fetched_data;
			}
		}
		return $data;
	}
	
	function XC_GetDescontosAtivos($id_cliente){
		global $conn;
		$data = array();
		if (empty($id_cliente) OR !is_numeric($id_cliente)) {
			return false;
		} else {
			$query = mysqli_query($conn, "SELECT * FROM desconto WHERE cliente = {$id_cliente} AND validade >= ".time()." ORDER BY validade DESC");
			while ($final_fetched_data = mysqli_fetch_assoc($query)) {
				$data[] = $final_fetched_data;
			}
		}
		return $data;
	}
	
	function XC_NovoDesconto ($re_data) {
		global $conn, $error;
		if (empty($re_data['valor']) or !is_numeric($re_data['valor'])) {
			$error = "Valor inválido!";
			return false;
		} else if (empty($re_data['cliente']) or !is_numeric($re_data['cliente'])) {
			$error = "Cliente inválido!";
			return false;
		} else if (empty($re_data['validade']) or !is_numeric($re_data['validade'])) {
			$error = "Data de validade inválida!";
			return false;
		} else if ($re_data['validade'] >= time()) {
			$error = "Data de validade deve ser posterior a agora!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO desconto ({$fields}) VALUES ({$data})");
			$id_desconto = mysqli_insert_id($conn);
			if ($id_desconto > 0) {
				$query2 = mysqli_query ($conn, "UPDATE cliente SET desconto={$id_desconto} WHERE id=".$re_data['cliente']."");
				if ($query2) {
					return $id_desconto;
				} else {
					return 0;
				}
			}
		}
	}
	
	function XC_RemoveDesconto($desconto_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$desconto_id = XC_Secure($desconto_id);
		if (empty($desconto_id)) {
			$error = "Desconto inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM desconto WHERE `id_desconto`={$desconto_id}");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_GetContasComanda($id_comanda){
		global $conn;
		$id_comanda = XC_Secure($id_comanda);
		$data = array();
		if (empty($id_comanda)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM conta WHERE `comanda` = '{$id_comanda}' AND  `estado` = 1 ORDER BY id_conta ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['cliente'] = XC_GetCliente($final_fetched_data['cliente']);
			$final_fetched_data['carrinho'] = XC_GetCarrinhosConta($final_fetched_data['id_conta']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetConta($id_conta){
		global $conn;
		$id_conta = XC_Secure($id_conta);
		if (empty($id_conta)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM conta WHERE `id_conta` = '{$id_conta}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['carrinho'] = XC_GetCarrinhoConta($final_fetched_data['id_conta']);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_GetContaCliente($id_cliente){
		global $conn;
		$id_cliente = XC_Secure($id_cliente);
		if (empty($id_cliente)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM conta WHERE `cliente` = '{$id_cliente}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['carrinho'] = XC_GetCarrinhoConta($final_fetched_data['id_conta']);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_GetContaClienteAnonimo($cliente_anonimo){
		global $conn;
		$cliente_anonimo = XC_Secure($cliente_anonimo);
		if (empty($cliente_anonimo)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM conta WHERE `cliente_anonimo` = '{$cliente_anonimo}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			$final_fetched_data['carrinho'] = XC_GetCarrinhoConta($final_fetched_data['id_conta']);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_AdicionaProdutoCarrinho ($re_data) {
		global $conn, $error;
		if (empty($re_data['produto']) or !is_numeric($re_data['produto'])) {
			$error = "Produto inválido!";
			return false;
		} else if (empty($re_data['conta']) or !is_numeric($re_data['conta'])) {
			$error = "Conta inválida!";
			return false;
		} else if (empty($re_data['garcom']) or !is_numeric($re_data['garcom'])) {
			$error = "Garçom inválido!";
			return false;
		} else {
			$fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
			$data    = '\'' . implode('\', \'', $re_data) . '\'';
			$query   = mysqli_query($conn, "INSERT INTO carrinho ({$fields}) VALUES ({$data})");
			return mysqli_insert_id($conn);
		}
	}
	
	function XC_MaisProdutoCarrinho ($id_carrinho) {
		global $conn, $error;
		if (empty($id_carrinho) or !is_numeric($id_carrinho)) {
			$error = "Carrinho inválido!";
			return false;
		} else {
			$id_carrinho = XC_Secure($id_carrinho);
			$carrinho = XC_GetCarrinho($id_carrinho);
			$query   = mysqli_query($conn, "UPDATE carrinho SET situacao=0, quantidade = ".($carrinho['quantidade']+1)." WHERE id_carrinho = '".$carrinho['id_carrinho']."'");
			if ($query) {
				return $carrinho['id_carrinho'];
			} else {
				return false;
			}
		}
	}
	
	function XC_MenosProdutoCarrinho ($produto, $conta) {
		global $conn, $error;
		if (empty($produto) or !is_numeric($produto)) {
			$error = "Produto inválido!";
			return false;
		} else if (empty($conta) or !is_numeric($conta)) {
			$error = "Conta inválida!";
			return false;
		} else {
			$carrinho = XC_GetUltimoProdutoCarrinhoConta($produto, $conta);
			if ($carrinho['situacao'] != 2 && $carrinho['situacao'] != 3 && $carrinho['feito'] != 1) {
				$query   = mysqli_query($conn, "DELETE FROM carrinho WHERE id_carrinho = '".$carrinho['id_carrinho']."'");
				if ($query) {
					return $carrinho['id_carrinho'];
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Esse produto não pode ser removido, pois está sendo preparado ou já foi feito!";
				return false;
			}
		}
	}
	
	function XC_GetCarrinhoProdutoConta($id_produto, $id_conta){
		global $conn;
		$id_produto = XC_Secure($id_produto);
		$id_conta = XC_Secure($id_conta);
		if (empty($id_conta)) {
			return false;
		}
		$query = mysqli_query($conn, "SELECT * FROM carrinho WHERE `produto` = '{$id_produto}' AND `conta`='{$id_conta}'");
		if (mysqli_num_rows($query) == 1) {
			$final_fetched_data              = mysqli_fetch_assoc($query);
			return $final_fetched_data;
		} else {
			return array();
		}
	}
	
	function XC_GetCarrinhoConta($id_conta){
		global $conn;
		$id_conta = XC_Secure($id_conta);
		if (empty($id_conta) OR !is_numeric($id_conta)) {
			return false;
		}
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM carrinho WHERE `conta` = {$id_conta} ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['produto'] = XC_GetProduto($final_fetched_data['produto']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetCarrinho($id_carrinho){
		global $conn;
		$id_carrinho = XC_Secure($id_carrinho);
		if (empty($id_carrinho) OR !is_numeric($id_carrinho)) {
			return false;
		}
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM carrinho WHERE `id_carrinho` = {$id_carrinho} ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['produto'] = XC_GetProduto($final_fetched_data['produto']);
			return $final_fetched_data;
		}
	}
	
	function XC_GetCarrinhosConta($id_conta){
		global $conn;
		$id_conta = XC_Secure($id_conta);
		if (empty($id_conta) OR !is_numeric($id_conta)) {
			return false;
		}
		$data = array();
		$query = mysqli_query($conn, "SELECT *, COUNT(produto) as qtd FROM carrinho WHERE `conta` = {$id_conta} GROUP BY produto ORDER BY data DESC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['produto'] = XC_GetProduto($final_fetched_data['produto']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_RemoveCarrinho($id_carrinho){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$id_carrinho = XC_Secure($id_carrinho);
		if (empty($id_carrinho) OR !intval($id_carrinho)) {
			$error = "Carrinho inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$carrinho = XC_GetCarrinho($id_carrinho);
			$conta = XC_GetConta($carrinho['conta']);
			$comanda = XC_GetComanda($conta['comanda']);
			if ($user['nivel'] == 0 OR $user['id_usuario'] == $comanda['garcom']) {
				$query   = mysqli_query($conn, "DELETE FROM carrinho WHERE `id_carrinho`={$id_carrinho}");
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveCarrinhoProduto($id_produto){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$id_produto = XC_Secure($id_produto);
		if (empty($id_produto) OR !intval($id_produto)) {
			$error = "Produto inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$carrinho = XC_GetCarrinho($id_produto);
			$conta = XC_GetConta($carrinho['conta']);
			$comanda = XC_GetComanda($conta['comanda']);
			if ($user['nivel'] == 0 OR $user['id_usuario'] == $comanda['garcom']) {
				$query   = mysqli_query($conn, "DELETE FROM carrinho WHERE `produto`={$id_produto}");
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_RemoveCarrinhoProdutoCliente($id_produto){
		global $conn, $error;
		$id_produto = XC_Secure($id_produto);
		if (empty($id_produto)) {
			$error = "Produto inválido!";
			return false;			
		} else {
			$query   = mysqli_query($conn, "DELETE FROM carrinho WHERE `produto`={$id_produto} AND situacao <= 1");
			if ($query) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	function XC_FecharConta ($re_data) {
		global $conn, $error;
		if (empty($re_data['conta']) or !is_numeric($re_data['conta'])) {
			$error = "Conta inválida!";
			return false;
		} else if (empty($re_data['total_venda']) or !is_numeric($re_data['total_venda'])) {
			$error = "Total venda inválido!";
			return false;
		} else if (empty($re_data['percentual_garcom']) or !is_numeric($re_data['percentual_garcom'])) {
			$error = "Percentual do garçom inválido!";
			return false;
		} else if ((empty($re_data['desconto_cliente']) && $re_data['desconto_cliente'] != 0) or !is_numeric($re_data['desconto_cliente'])) {
			$error = "Desconto do cliente inválido!";
			return false;
		} else if (empty($re_data['total_pagar']) or !is_numeric($re_data['total_pagar'])) {
			$error = "Total a pagar inválido!";
			return false;
		} else if (empty($re_data['troco']) or !is_numeric($re_data['troco'])) {
			$error = "Troco inválido!";
			return false;
		} else if (empty($re_data['dinheiro']) or !is_numeric($re_data['dinheiro'])) {
			$error = "Dinheiro inválido!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$conta = XC_GetConta($re_data['conta']);
			$comanda = XC_GetComanda($conta['comanda']);
			if ($user['nivel'] == 0 OR $user['id_usuario'] == $comanda['garcom']) {
				$query   = mysqli_query($conn, "UPDATE conta SET troco=".$re_data['troco'].", dinheiro=".$re_data['dinheiro'].", 
				total_venda=".$re_data['total_venda'].", percentual_garcom=".$re_data['percentual_garcom'].", 
				desconto_cliente=".$re_data['desconto_cliente'].", total_pagar=".$re_data['total_pagar'].",
				estado = 0 WHERE id_conta = ".$re_data['conta']."");
				if ($query) {
					$garcom = XC_GetUsuario($comanda['garcom']);
					/*
					
					$motivo = "Garçom ".$garcom['nome']." resposável por venda de produtos da conta ".$conta['id_conta'].": <br />";
					foreach ($conta['carrinho'] as $carrinho) {
						$produto = $carrinho['produto'];
						$motivo += "{Produto: ".$produto['nome'].", Preço: R$ ".$produto['preco']['preco_venda']."}";
					}*/
					$entrada_data = array(
						"valor" => XC_Secure($re_data['total_venda']),
						"motivo" => XC_Secure("Venda de produtos"),
						"responsavel" => XC_Secure($garcom['id_usuario']),
						"data" => time()
					);
					$entrada = XC_NovaEntradaCaixa ($entrada_data);
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_FecharMesa ($mesa_id) {
		global $conn, $error;
		if (empty($mesa_id) or !is_numeric($mesa_id)) {
			$error = "Conta inválida!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$comanda_id = XC_GetAtualComandaDeMesa ($mesa_id);
			$comanda = XC_GetComanda($comanda_id);
			if ($user['nivel'] == 0 OR $user['id_usuario'] == $comanda['garcom']) {
				$check = mysqli_query($conn, "SELECT id_conta FROM conta WHERE comanda = {$comanda_id} AND estado = 1");
				if (mysqli_num_rows($check) >= 1) {
					$error = "Essa mesa ainda possui contas em aberto, por favor feche todas as contas antes de fechar a mesa!";
					return false;
				} else {
					$query = mysqli_query($conn, "UPDATE comanda SET estado = 0, fechada = ".time()."  WHERE id_comanda = {$comanda_id}");
					if ($query) {
						$query2   = mysqli_query($conn, "UPDATE mesa SET situacao = 0 WHERE id_mesa = {$mesa_id}");
						if ($query2) {
							return true;
						} else {
							$error =  mysqli_error($conn);
							return false;
						}
					} else {
						$error =  mysqli_error($conn);
						return false;
					}
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_PrepararPedido ($carrinho_id) {
		global $conn, $error;
		if (empty($carrinho_id) or !is_numeric($carrinho_id)) {
			$error = "Pedido inválido!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 2) {
				$query   = mysqli_query($conn, "UPDATE carrinho SET situacao = 2 WHERE id_carrinho = {$carrinho_id}");
				if ($query) {
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_PedidoPronto ($carrinho_id) {
		global $conn, $error;
		if (empty($carrinho_id) or !is_numeric($carrinho_id)) {
			$error = "Pedido inválido!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 2) {
				$query   = mysqli_query($conn, "UPDATE carrinho SET feito = 1, situacao = 3 WHERE id_carrinho = {$carrinho_id} AND situacao = 2");
				if ($query) {
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_EnviarPedidos ($conta_id) {
		global $conn, $error;
		if (empty($conta_id) or !is_numeric($conta_id)) {
			$error = "Conta inválida!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 1 OR $user['nivel'] == 0) {
				$query   = mysqli_query($conn, "UPDATE carrinho SET situacao = 1 WHERE conta = {$conta_id} AND situacao = 0");
				if ($query) {
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para enviar estes pedidos!";
				return false;
			}
		}
	}
	
	function XC_EnviarPedidosCliente ($conta_id) {
		global $conn, $error;
		if (empty($conta_id) or !is_numeric($conta_id)) {
			$error = "Conta inválida!";
			return false;
		} else {
			$query   = mysqli_query($conn, "UPDATE carrinho SET situacao = 1 WHERE conta = {$conta_id} AND situacao = 0");
			if ($query) {
				return true;
			} else {
				$error =  mysqli_error($conn);
				return false;
			}
		}
	}
	
	function XC_PedidoEntregue ($carrinho_id) {
		global $conn, $error;
		if (empty($carrinho_id) or !is_numeric($carrinho_id)) {
			$error = "Pedido inválido!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 1) {
				$query   = mysqli_query($conn, "UPDATE carrinho SET entregue = 1, feito = 1 WHERE id_carrinho = {$carrinho_id}");
				if ($query) {
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_AlterarClienteConta ($conta_id, $cliente_id) {
		global $conn, $error;
		if (empty($conta_id) or !is_numeric($conta_id)) {
			$error = "Conta inválida!";
			return false;
		} else if (empty($cliente_id) or !is_numeric($cliente_id)) {
			$error = "Cliente inválido!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$conta = XC_GetConta($conta_id);
			$comanda = XC_GetComanda($conta['comanda']);
			if ($user['nivel'] == 0 OR $user['id_usuario'] == $comanda['garcom']) {
				$query   = mysqli_query($conn, "UPDATE conta SET cliente = {$cliente_id} WHERE id_conta = {$conta_id}");
				if ($query) {
					return true;
				} else {
					$error =  mysqli_error($conn);
					return false;
				}
			} else {
				$error = "Você não permissão para fechar esta conta!";
				return false;
			}
		}
	}
	
	function XC_AlterarContaUsuario ($re_data) {
		global $conn, $error;
		if (empty($re_data['nome'])) {
			$error = "Nome de usuário não pode ser vazio!";
			return false;
		} else if (empty($re_data['login'])) {
			$error = "Login de usuário não pode ser vazio!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($re_data['login'] != $user['login'] && XC_CheckUsuario($re_data['login'])) {
				$error = "Já existe um usuário cadastrado com este login!";
				return false;	
			} else {
				
				$query   = mysqli_query($conn, "UPDATE usuario SET `nome`='".$re_data['nome']."', `login`='".$re_data['login']."' WHERE `id_usuario`=".$user['id_usuario']."");
				if ($query) {
					$_SESSION['login_session'] = $re_data['login'];
					return true;
				} else {
					false;
				}
			}
		}
	}
	
	function XC_AlterarSenhaUsuario ($re_data) {
		global $conn, $error;
		if (empty($re_data['senha'])) {
			$error = "Digite sua senha antiga!";
			return false;
		} else if (empty($re_data['nova_senha'])) {
			$error = "Digite sua nova senha!";
			return false;
		} else if (empty($re_data['confirma_senha'])) {
			$error = "Confirme sua senha!";
			return false;
		} else if ($re_data['nova_senha'] != $re_data['confirma_senha']) {
			$error = "Senha de confirmação não confere com nova senha!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			$consult = mysqli_query($conn, "SELECT senha FROM usuario WHERE login='".$_SESSION['login_session']."'");
			$result = mysqli_fetch_assoc($consult);
			if ($re_data['senha'] != $result['senha']) {
				$error = "Senha antiga inválida!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE usuario SET `senha`='".$re_data['confirma_senha']."' WHERE `id_usuario`=".$user['id_usuario']."");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					false;
				}
			}
		}
	}
	function XC_AlterarSenhaCliente ($re_data) {
		global $conn, $error;
		if (empty($re_data['senha'])) {
			$error = "Digite sua senha antiga!";
			return false;
		} else if (empty($re_data['nova_senha'])) {
			$error = "Digite sua nova senha!";
			return false;
		} else if (empty($re_data['confirma_senha'])) {
			$error = "Confirme sua senha!";
			return false;
		} else if ($re_data['nova_senha'] != $re_data['confirma_senha']) {
			$error = "Senha de confirmação não confere com nova senha!";
			return false;
		} else {
			if (!isset($_SESSION)) {
				session_start();
			}
			$user = XC_GetClienteFromLogin($_SESSION['cliente_session']);
			$consult = mysqli_query($conn, "SELECT senha FROM cliente WHERE login='".$_SESSION['cliente_session']."'");
			$result = mysqli_fetch_assoc($consult);
			if ($re_data['senha'] != $result['senha']) {
				$error = "Senha antiga inválida!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE cliente SET `senha`='".$re_data['confirma_senha']."' WHERE `id`=".$user['id']."");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					false;
				}
			}
		}
	}
	
	function XC_RemoveCliente($cliente_id){
		global $conn, $error;
		if (!isset($_SESSION)) {
			session_start();
		}
		$cliente_id = XC_Secure($cliente_id);
		if (empty($cliente_id) OR !intval($cliente_id)) {
			$error = "Usuário inválido!";
			return false;			
		} else {
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] == 0) {
				$query   = mysqli_query($conn, "DELETE FROM cliente WHERE `id`={$cliente_id}");
				if ($query) {
					return true;
				} else {
					$error = mysqli_fetch_assoc($conn); 
					return false;
				}
			} else {
				$error = "Você não permissão para realizar esta exclusão!";
				return false;
			}
		}
	}
	
	function XC_GetVendasDiarias($mes = "January", $ano = 2018){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT data as date, SUM(total_venda) as units FROM conta WHERE 
		FROM_UNIXTIME(data,'%M') = '{$mes}' AND FROM_UNIXTIME(data,'%Y') = '{$ano}'GROUP BY FROM_UNIXTIME(data,'%D') ORDER BY data ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['units'] = round($final_fetched_data['units'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetEntradasDiariasCaixa($mes = "January", $ano = 2018){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT data as x, SUM(valor) as y FROM caixa_entradas WHERE 
		FROM_UNIXTIME(data,'%M') = '{$mes}' AND FROM_UNIXTIME(data,'%Y') = '{$ano}'GROUP BY FROM_UNIXTIME(data,'%D') ORDER BY data ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['y'] = round($final_fetched_data['y'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_GetSaidasDiariasCaixa($mes = "January", $ano = 2018){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT data as x, SUM(valor) as y FROM caixa_saidas WHERE 
		FROM_UNIXTIME(data,'%M') = '{$mes}' AND FROM_UNIXTIME(data,'%Y') = '{$ano}'GROUP BY FROM_UNIXTIME(data,'%D') ORDER BY data ASC");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['y'] = round($final_fetched_data['y'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_TopGarcons($limit=5){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT *, SUM(conta.total_venda) as total_vendas  FROM (comanda INNER JOIN conta ON comanda.id_comanda = conta.comanda) GROUP BY comanda.garcom ORDER BY total_vendas DESC LIMIT {$limit}");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['garcom'] = XC_GetUsuario($final_fetched_data['garcom']);
			$final_fetched_data['total_vendas'] = round($final_fetched_data['total_vendas'], 2);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_TopProdutos($limit=5){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT *, COUNT(carrinho.produto) as total_produtos_vendidos  FROM (((comanda INNER JOIN conta ON comanda.id_comanda = conta.comanda) INNER JOIN carrinho ON conta.id_conta = carrinho.conta) INNER JOIN produtos ON carrinho.produto = produtos.cod) GROUP BY carrinho.produto ORDER BY total_produtos_vendidos DESC LIMIT {$limit}");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			$final_fetched_data['produto'] = XC_GetProduto($final_fetched_data['produto']);
			$data[] = $final_fetched_data;
		}
		return $data;
	}
	
	function XC_IndexItemArray($array, $item) {
		for ($i=0;$i<count($array);$i++) {
			if ($array[$i] == $item) {
				return $i;
			}
		}
	}
	
	function sendMail($to){
		$to = "somebody@example.com, somebodyelse@example.com";
		$subject = "Disconto";

		$message = "
		<html>
		<head>
		<title>Você ganhou disconto!</title>
		</head>
		<body>
		<p>Você ganhou disconto!</p>
		<table>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		</tr>
		<tr>
		<td>John</td>
		<td>Doe</td>
		</tr>
		</table>
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <xcomanda@gmail.com>' . "\r\n";
		$headers .= 'Cc: myboss@example.com' . "\r\n";

		mail($to,$subject,$message,$headers);

	}
	
	function XC_ConfigInfo ($re_data) {
		global $conn, $error;
		if (empty($re_data['empresa'])) {
			$error = "Nome de empresa não pode ser vazio!";
			return false;
		} else if (empty($re_data['exibir_logo_ou_nome']) && $re_data['exibir_logo_ou_nome'] != 0 && !is_numeric($re_data['exibir_logo_ou_nome'])) {
			$error = "Exbir logo/nome não pode ser vazio!";
			return false;
		} else {
			if (!isset($_SESSION)){
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] != 0) {
				$error = "Você não tem permissão para alterar estes dados!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE config SET exibir_logo_ou_nome = '".$re_data['exibir_logo_ou_nome']."', empresa = '".$re_data['empresa']."', descricao = '".$re_data['descricao']."', telefone = '".$re_data['telefone']."', email = '".$re_data['email']."'");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			}
		}
	}
	
	function XC_ConfigGarcom($re_data) {
		global $conn, $error;
		if (empty($re_data['percentual_garcom']) && $re_data['percentual_garcom'] != 0 && !is_numeric($re_data['percentual_garcom'])) {
			$error = "Percentual do garcom não pode ser vazio!";
			return false;
		} else if (empty($re_data['percentual_garcom_ativo']) && $re_data['percentual_garcom_ativo'] != 0 && !is_numeric($re_data['percentual_garcom_ativo'])) {
			$error = "Percentual do garcom ativo/não ativo não pode ser vazio!";
			return false;
		} else if (empty($re_data['garcom_abre_mesa']) && $re_data['garcom_abre_mesa'] != 0 && !is_numeric($re_data['garcom_abre_mesa'])) {
			$error = "Garcom abre/não abre não pode ser vazio!";
			return false;
		} else {
			if (!isset($_SESSION)){
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] != 0) {
				$error = "Você não tem permissão para alterar estes dados!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE config SET percentual_garcom = ".$re_data['percentual_garcom'].", percentual_garcom_ativo = ".$re_data['percentual_garcom_ativo'].", garcom_abre_mesa = ".$re_data['garcom_abre_mesa']."");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			}
		}
	}
	
	function XC_ConfigCliente($re_data) {
		global $conn, $error;
		if (empty($re_data['valor_desconto_cadastro_cliente']) && $re_data['valor_desconto_cadastro_cliente'] != 0 && !is_numeric($re_data['valor_desconto_cadastro_cliente'])) {
			$error = "Valor do desconto do cadastro do cliente não pode ser vazio!";
			return false;
		} else if (empty($re_data['desconto_cadastro_cliente']) && $re_data['desconto_cadastro_cliente'] != 0 && !is_numeric($re_data['desconto_cadastro_cliente'])) {
			$error = "Desconto do cliente ativo/não ativo não pode ser vazio!";
			return false;
		} else {
			if (!isset($_SESSION)){
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] != 0) {
				$error = "Você não tem permissão para alterar estes dados!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE config SET valor_desconto_cadastro_cliente = ".$re_data['valor_desconto_cadastro_cliente'].", desconto_cadastro_cliente = ".$re_data['desconto_cadastro_cliente']."");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			}
		}
	}
	
	function XC_ConfigAlterarLogo($logo) {
		global $conn, $error;
		if (empty($logo)) {
			$error = "Envie um logo!";
			return false;
		} else {
			if (!isset($_SESSION)){
				session_start();
			}
			$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
			if ($user['nivel'] != 0) {
				$error = "Você não tem permissão para alterar estes dados!";
				return false;	
			} else {
				$query   = mysqli_query($conn, "UPDATE config SET logo = '{$logo}'");
				if ($query) {
					return true;
				} else {
					$error = mysqli_error($conn);
					return false;
				}
			}
		}
		
		
	}
	
	function XC_ConfigRemoverLogo() {
		global $conn, $error;
		if (!isset($_SESSION)){
			session_start();
		}
		$user = XC_GetUsuarioFromLogin($_SESSION['login_session']);
		if ($user['nivel'] != 0) {
			$error = "Você não tem permissão para alterar estes dados!";
			return false;	
		} else {
			$query   = mysqli_query($conn, "UPDATE config SET logo = ''");
			if ($query) {
				return true;
			} else {
				$error = mysqli_error($conn);
				return false;
			}
		}		
	}

	function XC_GetConfig(){
		global $conn;
		$data = array();
		$query = mysqli_query($conn, "SELECT * FROM config");
		while ($final_fetched_data = mysqli_fetch_assoc($query)) {
			return $final_fetched_data;
		}
	}
	
	function XC_GerarSenha($tamanho=9, $forca=0) {
		$vogais = 'aeuy';
		$consoantes = 'bdghjmnpqrstvz';
		if ($forca >= 1) {
			$consoantes .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($forca >= 2) {
			$vogais .= "AEUY";
		}
		if ($forca >= 4) {
			$consoantes .= '23456789';
		}
		if ($forca >= 8 ) {
			$vogais .= '@#$%';
		}
 
		$senha = '';
		$alt = time() % 2;
		for ($i = 0; $i < $tamanho; $i++) {
			if ($alt == 1) {
				$senha .= $consoantes[(rand() % strlen($consoantes))];
				$alt = 0;
			} else {
				$senha .= $vogais[(rand() % strlen($vogais))];
				$alt = 1;
			}
		}
		return $senha;
	}

?>