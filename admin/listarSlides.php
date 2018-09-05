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
					url: '../requests.php?f=removeSlide',
					type: 'POST',
					dataType: 'json',
					data: {f: 'removeSlide',id_slideshow:id},
				  })
				  .done(function(data) {
					if (data.status == 200) {
						$('.result').html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
						$(".slide_"+id).remove();
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
  function tornarVisivel(id){
	$( "#dialog-tornarVisivel" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Sim": function() {
          $.ajax({
					url: '../requests.php?f=tornarVisivel',
					type: 'POST',
					dataType: 'json',
					data: {f: 'removeSlide',id_slideshow:id},
				  })
				  .done(function(data) {
					if (data.status == 200) {
						$('.result').html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
						$('.visibilidade').html("<button class=\"item\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Tornar visível\" onclick=\"tornarVisivel("+id+")\"><i class=\"zmdi zmdi-eye\"></i></button>");
						
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
  
  function tornarInvisivel(id){
	$( "#dialog-tornarInvisivel" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Sim": function() {
          $.ajax({
					url: '../requests.php?f=tornarInvisivel',
					type: 'POST',
					dataType: 'json',
					data: {f: 'removeSlide',id_slideshow:id},
				  })
				  .done(function(data) {
					if (data.status == 200) {
						$('.result').html("<div class=\"alert alert-success\" role=\"alert\">"+data.success+"</div>");
						$('.visibilidade').html("<button class=\"item\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Tornar visível\" onclick=\"tornarVisivel("+id+")\"><i class=\"zmdi zmdi-eye-off\"></i></button>");
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

<div id="dialog-delete" title="Apagar Slide" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Tem certeza que deseja apagar este slide?</p>
</div>

<div id="dialog-tornarVisivel" title="Tornar visível" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Tem certeza que deseja tornar esse slide visível?</p>
</div>

<div id="dialog-tornarInvisivel" title="Tornar invisível" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Tem certeza que deseja tornar esse slide invisível?</p>
</div>

<div class="section__content section__content--p30">
	<div class="container-fluid">
		<div class="row">
		<div class="col-md-12">
                                <!-- DATA TABLE -->
                                <h3 class="title-5 m-b-35">Slideshow</h3>
								<div class="result"></div>
                                <div class="table-data__tool">
                                    <div class="table-data__tool-right">
                                        <a href="?pag=novoSlide" class="au-btn au-btn-icon au-btn--green au-btn--small">
                                            <i class="zmdi zmdi-plus"></i>add item</a>
                                    </div>
                                </div>
                                <div class="table-responsive table-responsive-data2">
                                    <table class="table table-data2">
                                        <thead>
                                            <tr>
                                                <th>Imagem</th>
                                                <th>Título</th>
                                                <th>Descrição</th>
                                                <th>Rótulo botão</th>
                                                <th>Link botão</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?php 
												foreach (LM_GetSlides() as $slide) {
											?>
                                            <tr class="tr-shadow slide_<?php echo $slide['id_slideshow']; ?>">
                                                <td><img src="../<?php echo $slide['imagem_slideshow'];?>" width="150px" height="100px;" /></td>
                                                <td>
                                                    <span class="block-email"><?php echo $slide['titulo_slideshow'];?></span>
                                                </td>
                                                <td><?php echo $slide['descricao_slideshow'];?></td>
                                                <td><?php echo $slide['rotulo_botao_slideshow'];?></td>
                                                <td>
                                                    <?php echo $slide['link_botao_slideshow'];?>
                                                </td>
                                                <td>
                                                    <div class="table-data-feature">
														<div class="visibilidade">
														<?php 
															if ($slide['visivel_slideshow'] == 0) {
														?>
														<button class="item" data-toggle="tooltip" data-placement="top" title="Tornar visível" onclick="tornarVisivel(<?php echo $slide['id_slideshow']?>)"><i class="zmdi zmdi-eye-off"></i></button>
														<?php } else {?>
														<button class="item" data-toggle="tooltip" data-placement="top" title="Tornar invisível" onclick="tornarInvisivel(<?php echo $slide['id_slideshow']?>)"><i class="zmdi zmdi-eye"></i></button>
														<?php
															}
														?>
														</div>
                                                        <a href="?pag=editarSlide&s=<?php echo $slide['id_slideshow']; ?>"class="item" data-toggle="tooltip" data-placement="top" title="Editar">
                                                            <i class="zmdi zmdi-edit"></i>
                                                        </a>
                                                        <button class="item" data-toggle="tooltip" data-placement="top" onclick="deleteSlide(<?php echo $slide['id_slideshow']?>);" title="Apagar">
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

