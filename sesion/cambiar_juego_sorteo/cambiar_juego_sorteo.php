<h1>Cambiar Juego/Sorteo</h1>


<button onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=1&descripcion_juego=LOTERIA'+a('ver_sorteos'), '#contenedor_sorteos'); $('#quiniela_poceada, #quiniela, #loteria,#instantanea').removeClass('active'); $('#loteria').addClass('active');" id="loteria" id="loteria" class="btn btn-large btn-block" type="button">LOTERIA</button>



<button onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=2&descripcion_juego=QUINIELA'+a('ver_sorteos'), '#contenedor_sorteos'); $('#quiniela_poceada, #quiniela, #loteria,#instantanea').removeClass('active'); $('#quiniela').addClass('active');" id="quiniela" name="quiniela" class="btn btn-large btn-block" type="button">QUINIELA</button>


<button onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=32&descripcion_juego=POCEADA CORDOBESA'+a('ver_sorteos'), '#contenedor_sorteos'); $('#quiniela_poceada, #quiniela, #loteria,#instantanea').removeClass('active'); $('#quiniela_poceada').addClass('active');" id="quiniela_poceada" name="quiniela" class="btn btn-large btn-block" type="button"> POCEADA CORDOBESA</button>

<!--<button onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=3&descripcion_juego=INSTANTANEA'+a('ver_sorteos'), '#contenedor_sorteos'); $('#quiniela, #loteria, #instantanea').removeClass('active'); $('#instantanea').addClass('active');" id="instantanea" name="instantanea" class="btn btn-large btn-block" type="button">RESOLUCION INSTANTANEA</button>-->

<br><br>

<div id="contenedor_sorteos"></div>