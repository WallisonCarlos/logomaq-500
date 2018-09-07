<script type="text/javascript">
function novoServico() {
	$('form.novoServico').submit();
}
</script>
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">Novo Serviço</div>
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center title-2">Adicinar Novo Serviço</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" class="novoServico" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Título serviço</label>
                                                <input id="cc-pament" name="titulo_servico" type="text" class="form-control" aria-required="true" aria-invalid="false">
                                            </div>
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Imagem</label>
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
                                                        <option value="<?php echo $categoria['id_categoria'];?>"><?php echo $categoria['titulo_categoria'];?></option>
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
												</div>
												<div class="clear"></div>
											 </div>
											<div>
                                                <button id="payment-button" type="button" onclick="novoServico();" class="btn btn-lg btn-info btn-block">
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
			$('form.novoServico').ajaxForm({
				url: '../requests.php?f=novoServico',
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
					 $('form.novoServico input').val("");
					result.html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
				  } 
				  else if (data.status == 400) {
					result.html("<div class=\"alert alert-success\" role=\"alert\">"+data.error+"</div>");
				  } 
				}
			  });

		});
	</script>