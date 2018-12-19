<?php
session_start();
header("Content-type: text/css; charset: UTF-8");
?>@font-face {
    font-family: 'MyriadPro-Bold';
    src: url('../../fonts/MyriadPro-Bold.ttf');
}


@font-face {
    font-family: 'Impact';
    src: url('../../fonts/impact.ttf');
}

body{
    font-family: Verdana;
}

#sorteo{
    font-size: 26px;
    left: 105px;
    position: relative;
    top: 42px;
}

#sorteo_resumen{
    font-size: 26px;
    left: 101px;
    position: relative;
    top: 16px;
}


#fecha_sorteo{
font-size: 21px;
    left: 250px;
    position: relative;
    top: 16px;

}

#fecha_sorteo_resumen{
    font-size: 16px;
    left: 260px;
    position: relative;
    top: -10px;

}
#sorteo_2{
    position: relative;
    top: 45px;
    left: 170px;
    font-size: 22px;
}

#fecha_sorteo_2{
    position: relative;
    left: 470px;
    font-size: 21px;
    top: 15px;
}

#sorteo_4{
    position: relative;
    top: 41px;
    left: 109px;
    font-size: 22px;
}

#fecha_sorteo_4{
    position: relative;
    left: 242px;
    font-size: 21px;
    top: 16px;
}

#zona1,
#zona2,
#zona3,
#zona4,
#escribano_resumen{
    width: 1366px;
    height: 768px;
    margin: 0 auto;
    position: relative;
}

#zona1{
    background-image: url(escribano_img/zona1_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
   background-size: 1366px 768px;
}
#zona2{
    background-image: url(escribano_img/zona2_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
	 background-size: 1366px 768px;
    display: none;
}
#zona3{
    background-image: url(escribano_img/zona3_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
	 background-size: 1366px 768px;
    display: none;
}

/*SORTEO BILLETE ENTERO*/
#zona4{
    background-image: url(escribano_img/zona4_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
    display: none;
}

.billete{
    border: 0px solid #000;
    text-align: center;
    position: absolute;
    font-family: 'MyriadPro-Bold';
}

#primer_premio{
    background-size: 319px 60px;
    top: 142px;
    left: 169px;
    position: absolute;
    width: 319px;
    height: 60px;
}

#primer_premio_resumen{
    background-size: 338px 77px;
    top: 138px;
    left: 162px;
    position: absolute;
    width: 338px;
    height: 72px;
}
#resultado_primer_premio{
display: block;
    background-size: 248px 63px;
    top: 246px;
    left: 161px;
    position: absolute;
    width: 250px;
    height: 62px;
}


/*SOLO PARA EL RESUMEN*/
#escribano_resumen{
    background-image: url(escribano_img/resumen_<?php echo $_SESSION['sorteo'] ?>.png);
	background-size: 1024px 768px;
}

#bola01{
    font-size: 144px;
    left: 139px;
    position: absolute;
    top: 195px;
}

#progresion{
    top: 312px;
    left: 1063px;
    position: absolute;
    font-size: 72px;
    font-family: 'MyriadPro-Bold';
}


#bola02{
    top: 474px;
    left: 101px;
    position: absolute;
    font-size: 67px;
}

#bola03{
    top: 473px;
    left: 387px;
    position: absolute;
    font-size: 67px;
}

#bola04{
    top: 657px;
    left: 103px;
    position: absolute;
    font-size: 67px;
}

#bola05{
    top: 657px;
    left: 389px;
    position: absolute;
    font-size: 67px;
}

#bola06{
    top: 163px;
    left: 782px;
    position: absolute;
    font-size: 48px;
}

#bola07{top: 213px;left: 782px;position: absolute;font-size: 48px;}

#bola08{
    top: 260px;
    left: 782px;
    position: absolute;
    font-size: 48px;
}

#bola09{
    top: 307px;
    left: 782px;
    position: absolute;
    font-size: 48px;
}

#bola10{
    top: 354px;
    left: 782px;
    position: absolute;
    font-size: 48px;
}


#bola11{top: 494px;left: 769px;position: absolute;font-size: 48px;}

#bola12{
 top: 541px;
 left: 769px;
 position: absolute;
 font-size: 48px;
}

#bola13{
    top: 588px;
    left: 769px;
    position: absolute;
    font-size: 48px;
}

#bola14{
    top: 639px;
    left: 769px;
    position: absolute;
    font-size: 48px;
}

#bola15{top: 689px;left: 768px;position: absolute;font-size: 48px;}

#bola16{top: 492px;left: 1066px;position: absolute;font-size: 48px;}

#bola17{
    top: 541px;
    left: 1066px;
    position: absolute;
    font-size: 48px;
}

#bola18{
    top: 590px;
    left: 1066px;
    position: absolute;
    font-size: 48px;
}

#bola19{
    top: 640px;
    left: 1066px;
    position: absolute;
    font-size: 48px;
}

#bola20{
    top: 687px;
    left: 1066px;
    position: absolute;
    font-size: 48px;
}

#bola21{
    top: 550px;
    left: 495px;
    position: absolute;
    font-size: 15px;
}
#fraccion21{
    top: 550px;
    left: 555px;
    position: absolute;
    font-size: 15px;
}

#bola22{
    top: 550px;
    left: 590px;
    position: absolute;
    font-size: 15px;
}
#fraccion22{
    top: 550px;
    left: 650px;
    position: absolute;
    font-size: 15px;
}

#bola23{
    top: 550px;
    left: 685px;
    position: absolute;
    font-size: 15px;
}
#fraccion23{
    top: 550px;
    left: 747px;
    position: absolute;
    font-size: 15px;
}

#bola24{
     top: 550px;
    left: 782px;
    position: absolute;
    font-size: 15px;
}
#fraccion24{
    top: 550px;
    left: 841px;
    position: absolute;
    font-size: 15px;
}


#bola25{
    top: 550px;
    left: 877px;
    position: absolute;
    font-size: 15px;
}
#fraccion25{
    top: 550px;
    left: 937px;
    position: absolute;
    font-size: 15px;
}


#bola26{
    top: 550px;
    left: 975px;
    position: absolute;
    font-size: 15px;
}
#fraccion26{
    top: 550px;
    left: 1035px;
    position: absolute;
    font-size: 15px;
}




#bola27{
    top: 685px;
    left: 802px;
    position: absolute;
    font-size: 42px;
    font-family: 'MyriadPro-Bold';
}






#premio_sorteo_entero{
    top: 390px;
    font-size: 174px;
    left: 91px;
}

#agencia_sorteo_entero{
    font-size: 22px;
    top: 376px;
    left: 440px;
}

#delegacion_sorteo_entero{
    font-size: 22px;
    top: 441px;
    left: 440px;
}

#localidad_sorteo_entero{
   font-size: 22px;
top: 502px;
left: 440px;
}
