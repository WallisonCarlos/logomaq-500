<script type="text/javascript">
function atualizaSlide() {
	$('form.atualizaSlide').submit();
}
</script>
<?php
	$slide = LM_GetSlide($_GET['s']);
?>
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">Edita Slide</div>
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center title-2">Editar Slide</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" class="atualizaSlide" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Título slide</label>
                                                <input id="cc-pament" name="titulo_slideshow" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?php echo $slide['titulo_slideshow']?>" >
                                            </div>
											<div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="textarea-input" class=" form-control-label">Descrição</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <textarea name="descricao_slideshow" id="textarea-input" rows="9" placeholder="" class="form-control" value="<?php echo $slide['descricao_slideshow']?>"><?php echo $slide['descricao_slideshow']?></textarea>
                                                </div>
                                            </div>
											<div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Rótulo botão</label>
                                                <input id="cc-pament" name="rotulo_botao_slideshow" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?php echo $slide['rotulo_botao_slideshow']?>">
                                            </div>
											<div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Link botão</label>
                                                <input id="cc-pament" name="link_botao_slideshow" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?php echo $slide['link_botao_slideshow']?>">
                                            </div>   
											<div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Visível</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="visivel_slideshow" id="select" class="form-control">
                                                        <option value="1" <?php if ($slide['visivel_slideshow'] == 1) { echo "selected='selected'"; }?>>Sim</option>
                                                        <option value="0" <?php if ($slide['visivel_slideshow'] == 0) { echo "selected='selected'"; }?>>Não</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="result"></div>
											<div id="progress" style="display:none;">
												<span id="percent">0%</span>
												<div class="progress">
												<div id="bar" class="progress-bar progress-bar-striped active"></div> 
												</div>
												<input name="id_slideshow" type="hidden" class="form-control" value="<?php echo $slide['id_slideshow']?>" >
												<div class="clear"></div>
											 </div>
											<div>
                                                <button id="payment-button" type="button" onclick="atualizaSlide();" class="btn btn-lg btn-info btn-block">
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
			  $('form.atualizaSlide').ajaxForm({
				url: '../requests.php?f=atualizaSlide',
				beforeSend: function() { 
					result.append("<div class=\"alert alert-primary\" role=\"alert\">Enviando...</div>");			
				},
				success: function(data) {
					console.log(data);
					if (data.status == 200) {
						result.html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");						
					}else if (data.status == 400) {
						result.html("<div class=\"alert alert-danger\" role=\"alert\">"+data.error+"</div>");
					} else {
						result.html("<div class=\"alert alert-danger\" role=\"alert\">Ocorreu algum problema, entre em contato com nosco!</div>");
					}
				}
			});

		});
	</script>