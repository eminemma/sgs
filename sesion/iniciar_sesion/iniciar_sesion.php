<?php
session_start();
include_once '../../mensajes.php';

if ((int) $_SESSION['dni'] > 0) {
    info('Seleccione la opción del menú que desee');
    error('Si desea cerrar sesión, haga click en el boton de la esquina superior derecha "Cerrar Sesión"');
    exit;
} else {
    info('Para evitar problemas de compatibilidad, recomendamos utilizar los siguientes navegadores: Mozilla Firefox, o Google Chrome');
}
?>
<br>
<div id="resultado"></div>
<div class="container" style="width: 50%;
    height: 50%;
    min-width: 200px;
    max-width: 400px;
    padding: 40px;
    margin: auto;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;">
    <div class="row">
        <div class="Absolute-Center is-Responsive">
            <div id="logo-container"></div>
            <div class="row">
                <div class="span4">
                    <form class="form-signin" id="loginForm">
                        <h2 class="form-signin-heading">Inicio de Sesión</h2>
                        <div class="controls">
                            <div class="input-prepend" style="width: 91%">
                                <span class="add-on" style="height: 26px; line-height: 26px"><i class="icon-user"></i></span>
                                <input id="dni" type="text" class="input-block-level" placeholder="Numero de documento">
                            </div>
                        </div>
                        <div class="controls">
                            <div class="input-prepend" style="width: 91%">
                                <span class="add-on" style="height: 26px; line-height: 26px"><i class="icon-lock"></i></span>
                                <input id="password" type="password" class="input-block-level" placeholder="Contraseña">
                            </div>
                        </div>
                        <div class="control-group">
                            <button class="btn btn-large  btn-primary" style="width: 100%" type="submit" onclick="g('sesion/iniciar_sesion/iniciar_sesion_ajax.php?'+a('iniciar_sesion')+v('dni')+v('password'),'#resultado'); return false;">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
