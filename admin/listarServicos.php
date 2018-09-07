<script> 
  function deleteSlide(id){
	$( "#dialog-delete" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Sim": function() {
          $.ajax({
					url: '../requests.php?f=removeServico',
					type: 'POST',
					dataType: 'json',
					data: {f: 'removeServico',id_servico:id},
				  })
				  .done(function(data) {
					if (data.status == 200) {
						$('.result').html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
						$(".servico_"+id).remove();
					} else if (data.status == 400) {
						$('.result').html("<div class=\"alert alert-danger\" role=\"alert\">"+data.error+"</div>");
					}
					else if(data.status == 404){
					  alert("Fail");
					}
				  })
				  .fail(function(data) {
					console.log(data);
				  })
				  $( this ).dialog( "close" );
        },
        "Não": function() {
          $( this ).dialog( "close" );
        }
      }
    });
  }
</script>

<div id="dialog-delete" title="Apagar Serviço" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Tem certeza que deseja apagar este serviço?</p>
</div>

<div class="section__content section__content--p30">
	<div class="container-fluid">
		<div class="row">
		<div class="col-md-12">
                                <!-- DATA TABLE -->
                                <h3 class="title-5 m-b-35">Serviços</h3>
								<div class="result"></div>
                                <div class="table-data__tool">
                                    <div class="table-data__tool-right">
                                        <a href="?pag=novoServico" class="au-btn au-btn-icon au-btn--green au-btn--small">
                                            <i class="zmdi zmdi-plus"></i>add item</a>
                                    </div>
                                </div>
                                <div class="table-responsive table-responsive-data2">
                                    <table class="table table-data2">
                                        <thead>
                                            <tr>
                                                <th>Imagem</th>
                                                <th>Título</th>
                                                <th>Categoria</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?php 
												foreach (LM_GetServicos() as $servico) {
											?>
                                            <tr class="tr-shadow servico_<?php echo $servico['id_servico']; ?>">
                                                <td><img src="../<?php echo $servico['imagem_servico'];?>" width="150px" height="100px;" /></td>
                                                <td>
                                                    <span class="block-email"><?php echo $servico['titulo_servico'];?></span>
                                                </td>
                                                <td><?php echo $servico['categoria_servico']['titulo_categoria'];?></td>
                                                <td>
                                                    <div class="table-data-feature">
                                                        <a href="?pag=editarServico&s=<?php echo $servico['id_servico']; ?>"class="item" data-toggle="tooltip" data-placement="top" title="Editar">
                                                            <i class="zmdi zmdi-edit"></i>
                                                        </a>
                                                        <button class="item" data-toggle="tooltip" data-placement="top" onclick="deleteSlide(<?php echo $servico['id_servico']?>);" title="Apagar">
                                                            <i class="zmdi zmdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="spacer"></tr>
											<?php
												}
											?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- END DATA TABLE -->
                            </div>
                         
		</div>
	</div>
</div>

