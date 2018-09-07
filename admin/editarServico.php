<script type="text/javascript">
function atualizaServico() {
	$('form.atualizaServico').submit();
}

</script>

<?php 
	$servico = LM_GetServico($_GET['s']);
?>
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">Edita Serviço</div>
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center title-2">Editar Serviço</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" class="atualizaServico" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Título serviço</label>
                                                <input id="cc-pament" name="titulo_servico" value="<?php echo $servico['titulo_servico']; ?>"type="text" class="form-control" aria-required="true" aria-invalid="false">
                                            </div>
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Imagem</label><br>
												<img src="../<?php echo $servico['imagem_servico']; ?>" width="200px" height="200px">
                                                <input id="cc-pament" name="imagem_servico" type="file" class="form-control" aria-required="true" aria-invalid="false">
                                            </div> 
											<div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Categoria</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="categoria_servico" id="select" class="form-control">
														<option value="0">Selecione uma categoria</option>
													<?php
														foreach (LM_GetCategorias() as $categoria) {
													?>
                                                        <option value="<?php echo $categoria['id_categoria'];?>" <?php if ($servico['categoria_servico']['id_categoria'] == $categoria['id_categoria']) { echo "selected='selected'"; }?>><?php echo $categoria['titulo_categoria'];?></option>
													<?php
														}
													?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="result"></div>
											<div id="progress" style="display:none;">
												<span id="percent">0%</span>
												<div class="progress">
												<div id="bar" class="progress-bar progress-bar-striped active"></div> 
												<input name="id_servico" value="<?php echo $servico['id_servico']; ?>" type="hidden">
												</div>
												<div class="clear"></div>
											 </div>
											<div>
                                                <button id="payment-button" type="button" onclick="atualizaServico();" class="btn btn-lg btn-info btn-block">
                                                    <i class="fa fa-plus"></i>&nbsp;
                                                    <span id="payment-button-amount">Salvar</span>
                                                    <span id="payment-button-sending" style="display:none;">Sending…</span>
                                                </button>
                                            </div>
											
                                        </form>
                                    </div>
                                </div>
                            </div>
</div>
                                </div>
                            </div>
							
							
							
	<script type="text/javascript">
		$(function () {
			var bar = $('#bar');
			bar.hide();
			var percent = $('#percent');
			var result = $('.result');
			$('form.atualizaServico').ajaxForm({
				url: '../requests.php?f=atualizaServico',
				beforeSend: function () {
					bar.show();
				  var percentVal = '0%';
				  bar.width(percentVal);
				  percent.html(percentVal);
				},
				uploadProgress: function (event, position, total, percentComplete) {
				  var percentVal = percentComplete + '%';
				  bar.width(percentVal);
				  $('#progress').slideDown(200);
				  $('#payment-button-sending').show();
				  $('#payment-button-amount').hide();
				  if(percentComplete > 50) {
					percent.addClass('white');
				  }
				  percent.html(percentVal);
				},
				success: function (data) {
					console.log(data);
					$('#payment-button-sending').hide();
				  $('#payment-button-amount').show();
				  $('#progress').slideUp(200);
				  percent.removeClass('white');
				  if(data.status == 200) {
					result.html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
				  } 
				  else if (data.status == 400) {
					result.html("<div class=\"alert alert-danger\" role=\"alert\">"+data.error+"</div>");
				  } 
				}
			  });

		});
	</script>