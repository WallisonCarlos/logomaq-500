<script type="text/javascript">
function novaCategoria() {
	$('form.novaCategoria').submit();
}
</script>
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">Nova Categoria</div>
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center title-2">Adicionar Nova Categoria</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" class="novaCategoria" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Título categoria</label>
                                                <input id="cc-pament" name="titulo_categoria" type="text" class="form-control" aria-required="true" aria-invalid="false">
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
                                                <button id="payment-button" type="button" onclick="novaCategoria();" class="btn btn-lg btn-info btn-block">
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
			  $('form.novaCategoria').ajaxForm({
				url: '../requests.php?f=novaCategoria',
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