<?php
function error($e){
	echo '<div class="error alert alert-error" onclick="$(this).fadeOut()"><i class="icon-warning-sign"></i> '.$e.'</div>';
}

function info($e){
	echo '<div class="info alert alert-info" onclick="$(this).fadeOut()"><i class="icon-info-sign"></i> '.$e.'</div>';
}

function ok($e){
	echo '<div class="ok alert alert-success" onclick="$(this).fadeOut()"><i class="icon-ok"></i> '.$e.'</div>';
}