<?php
session_start();
header("Content-type: text/css; charset: UTF-8");
?>
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
    width: 1080px;
    height: 600px;
    margin: 0 auto;
    position: relative;
}

#zona1{
    background-image: url(escribano_img/zona1_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
	background-size: 1080px 600px;
}
#zona2{
    background-image: url(escribano_img/zona2_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
	background-size: 1080px 600px;
    display: none;
}
#zona3{
    background-image: url(escribano_img/zona3_<?php echo date('Y') ?>_<?php echo $_SESSION['sorteo'] ?>.png);
	background-size: 1080px 600px;
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
}

#primer_premio{
    background-size: 303px 47px;
    top: 59px;
    left: 106px;
    position: absolute;
    width: 305px;
    height: 52px;
}

#primer_premio_resumen{
    background-size: 303px 47px;
    top: 124px;
    left: 97px;
    position: absolute;
    width: 305px;
    height: 52px;
}
#resultado_primer_premio{
    display: block;
    background-size: 303px 47px;
    top: 115px;
    left: 106px;
    position: absolute;
    width: 305px;
    height: 49px;
}


/*SOLO PARA EL RESUMEN*/
#escribano_resumen{
    background-image: url(escribano_img/resumen_<?php echo $_SESSION['sorteo'] ?>.png);
	background-size: 1080px 600px;
}

#bola01{
   font-size: 95px;
    left: 97px;
    position: absolute;
    top: 173px;
}

#progresion{
    top: 219px;
    left: 820px;
    position: absolute;
    font-size: 42px;
}


#bola02{
    top: 380px;
    left: 66px;
    position: absolute;
    font-size: 47px;
}

#bola03{
    top: 380px;
    left: 286px;
    position: absolute;
    font-size: 47px;
}

#bola04{
    top: 523px;
    left: 66px;
    position: absolute;
    font-size: 47px;
}

#bola05{
    top: 523px;
    left: 286px;
    position: absolute;
    font-size: 47px;
}

#bola06{
    top: 115px;
    left: 597px;
    position: absolute;
    font-size: 32px;
}

#bola07{
    top: 146px;
    left: 597px;
    position: absolute;
    font-size: 32px;
}

#bola08{
    top: 177px;
    left: 597px;
    position: absolute;
    font-size: 32px;
}

#bola09{
    top: 208px;
    left: 597px;
    position: absolute;
    font-size: 32px;
}

#bola10{
    top: 239px;
    left: 597px;
    position: absolute;
    font-size: 32px;
}


#bola11{
    top: 330px;
    left: 599px;
    position: absolute;
    font-size: 32px;
}

#bola12{
    top: 365px;
    left: 599px;
    position: absolute;
    font-size: 32px;
}

#bola13{
    top: 398px;
    left: 599px;
    position: absolute;
    font-size: 32px;
}

#bola14{
    top: 431px;
    left: 599px;
    position: absolute;
    font-size: 32px;
}

#bola15{
    top: 463px;
    left: 599px;
    position: absolute;
    font-size: 32px;
}

#bola16{
    top: 330px;
    left: 830px;
    position: absolute;
    font-size: 32px;
}

#bola17{
    top: 365px;
    left: 830px;
    position: absolute;
    font-size: 32px;
}

#bola18{
    top: 398px;
    left: 830px;
    position: absolute;
    font-size: 32px;
}

#bola19{
    top: 432px;
    left: 830px;
    position: absolute;
    font-size: 32px;
}

#bola20{
    top: 462px;
    left: 830px;
    position: absolute;
    font-size: 32px;
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
    top: 542px;
    left: 686px;
    position: absolute;
    font-size: 42px;
}






#premio_sorteo_entero{
    top: 337px;
    font-size: 174px;
    left: 448px;
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