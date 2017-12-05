
function animarMensajes(){
  $("#error_juego.alert").slideUp("slow");
  $("#warning_juego.alert").slideUp("slow");
  $("#success_juego.alert").slideUp("slow");
}

function preparaIncentivo(){
  $('#alcanzan').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'1',
            accion:'control_ingreso'
          }
        );
      }
    );
  $('#superan_hasta_10').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'2',
            accion:'control_ingreso'
          }
        );
      }
    );
  $('#superan_10_hasta_20').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'3',
            accion:'control_ingreso'
          }
        );
      }
    );
  $('#superan_20').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'4',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#250_omas').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'5',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#100_249').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'6',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#20_99').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'7',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#kit_mundial').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'8',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#401_mas').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'9',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#201_400_1').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'10',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#201_400_2').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'11',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#201_400_3').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'12',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#201_400_4').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'13',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#201_400_5').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'14',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#121_200_1').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'15',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#121_200_2').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'16',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#121_200_3').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'17',
            accion:'control_ingreso'
          }
        );
      }
    );
	$('#121_200_4').click(
      function(){
        $.post(
          'sorteo/operador/loteria_incentivo_ajax.php',
          { incentivo:'18',
            accion:'control_ingreso'
          }
        );
      }
    );
	
	
	
	$('#30_120_1').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'19',accion:'control_ingreso'});});
	$('#30_120_2').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'20',accion:'control_ingreso'});});
	$('#30_120_3').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'21',accion:'control_ingreso'});});
	$('#30_120_4').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'22',accion:'control_ingreso'});});
	$('#30_120_5').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'23',accion:'control_ingreso'});});
	$('#30_120_6').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'24',accion:'control_ingreso'});});
	$('#30_120_7').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'25',accion:'control_ingreso'});});
	$('#30_120_8').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'26',accion:'control_ingreso'});});
	$('#30_120_9').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'27',accion:'control_ingreso'});});
	$('#30_120_10').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'28',accion:'control_ingreso'});});
	$('#30_120_11').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'29',accion:'control_ingreso'});});
	$('#30_120_12').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'30',accion:'control_ingreso'});});
	$('#30_120_13').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'31',accion:'control_ingreso'});});
	$('#30_120_14').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'32',accion:'control_ingreso'});});
	$('#30_120_15').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'33',accion:'control_ingreso'});});
	
	
	$('#vuelta_heli_1').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'34',accion:'control_ingreso'});});
	$('#vuelta_heli_2').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'35',accion:'control_ingreso'});});
	$('#vuelta_auto').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'36',accion:'control_ingreso'});});
	$('#carpa_1').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'37',accion:'control_ingreso'});});
	$('#carpa_2').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'38',accion:'control_ingreso'});});
	$('#carpa_3').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'39',accion:'control_ingreso'});});
	
	$('#carpa_4').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'40',accion:'control_ingreso'});});
	$('#carpa_5').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'41',accion:'control_ingreso'});});
	$('#carpa_6').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'42',accion:'control_ingreso'});});
	
	
	$('#carpa_7').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'43',accion:'control_ingreso'});});
	$('#carpa_8').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'44',accion:'control_ingreso'});});
	$('#carpa_9').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'45',accion:'control_ingreso'});});
	$('#carpa_10').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'46',accion:'control_ingreso'});});
	$('#carpa_11').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'47',accion:'control_ingreso'});});
	$('#carpa_12').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'48',accion:'control_ingreso'});});
	$('#carpa_13').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'49',accion:'control_ingreso'});});
	$('#carpa_14').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'50',accion:'control_ingreso'});});
	$('#carpa_15').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'51',accion:'control_ingreso'});});
	$('#carpa_16').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'52',accion:'control_ingreso'});});
	$('#carpa_17').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'53',accion:'control_ingreso'});});
	$('#carpa_18').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'54',accion:'control_ingreso'});});
	$('#carpa_19').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'55',accion:'control_ingreso'});});
	$('#carpa_20').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'56',accion:'control_ingreso'});});
	$('#carpa_21').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'57',accion:'control_ingreso'});});
	$('#carpa_22').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'58',accion:'control_ingreso'});});
	$('#carpa_23').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'59',accion:'control_ingreso'});});
	$('#carpa_24').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'60',accion:'control_ingreso'});});
	$('#carpa_25').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'61',accion:'control_ingreso'});});
	
	
	
	
	
	$('#incentivo_62').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'62',accion:'control_ingreso',incentivo_b:'62'  });});
	$('#incentivo_64').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'64',accion:'control_ingreso',incentivo_b:'62'  });});
	
	$('#incentivo_66').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'66',accion:'control_ingreso',incentivo_b:'66'  });});
	$('#incentivo_67').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'67',accion:'control_ingreso',incentivo_b:'66'  });});
	$('#incentivo_68').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'68',accion:'control_ingreso',incentivo_b:'66'  });});
	$('#incentivo_69').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'69',accion:'control_ingreso',incentivo_b:'66'  });});
	$('#incentivo_70').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'70',accion:'control_ingreso',incentivo_b:'66'  });});
	
	$('#incentivo_71').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'71',accion:'control_ingreso',incentivo_b:'71'  });});
	$('#incentivo_72').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'72',accion:'control_ingreso',incentivo_b:'71'  });});
	$('#incentivo_73').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'73',accion:'control_ingreso',incentivo_b:'71'  });});
	$('#incentivo_74').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'74',accion:'control_ingreso',incentivo_b:'71'  });});
	$('#incentivo_75').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'75',accion:'control_ingreso',incentivo_b:'71'  });});
	
	$('#incentivo_76').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'76',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_77').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'77',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_78').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'78',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_79').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'79',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_80').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'80',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_81').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'81',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_82').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'82',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_83').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'83',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_84').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'84',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_85').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'85',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_86').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'86',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_87').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'87',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_88').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'88',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_89').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'89',accion:'control_ingreso',incentivo_b:'76'  });});
	$('#incentivo_90').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'90',accion:'control_ingreso',incentivo_b:'76'  });});
	
	
	
	$('#incentivo_91').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'91',accion:'control_ingreso',incentivo_b:'91'  });});
	$('#incentivo_92').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'92',accion:'control_ingreso',incentivo_b:'91'  });});
	$('#incentivo_93').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'93',accion:'control_ingreso',incentivo_b:'91'  });});
	$('#incentivo_94').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'94',accion:'control_ingreso',incentivo_b:'91'  });});
	$('#incentivo_95').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'95',accion:'control_ingreso',incentivo_b:'91'  });});
	
	
	$('#incentivo_96').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'96',accion:'control_ingreso',incentivo_b:'96'  });});
	$('#incentivo_97').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'97',accion:'control_ingreso',incentivo_b:'96'  });});
	$('#incentivo_98').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'98',accion:'control_ingreso',incentivo_b:'96'  });});
	$('#incentivo_99').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'99',accion:'control_ingreso',incentivo_b:'96'  });});
	$('#incentivo_100').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'100',accion:'control_ingreso',incentivo_b:'96'  });});
	
	
	$('#incentivo_101').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'101',accion:'control_ingreso',incentivo_b:'101'  });});
	$('#incentivo_102').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'102',accion:'control_ingreso',incentivo_b:'101'  });});
	$('#incentivo_103').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'103',accion:'control_ingreso',incentivo_b:'101'  });});
	$('#incentivo_104').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'104',accion:'control_ingreso',incentivo_b:'101'  });});
	$('#incentivo_105').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'105',accion:'control_ingreso',incentivo_b:'101'  });});

	$('#incentivo_106').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'106',accion:'control_ingreso',incentivo_b:'106'  });});
	$('#incentivo_107').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'107',accion:'control_ingreso',incentivo_b:'106'  });});
	$('#incentivo_108').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'108',accion:'control_ingreso',incentivo_b:'106'  });});
	$('#incentivo_109').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'109',accion:'control_ingreso',incentivo_b:'106'  });});
	$('#incentivo_110').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'110',accion:'control_ingreso',incentivo_b:'106'  });});

	
	
	$('#incentivo_111').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'111',accion:'control_ingreso',incentivo_b:'111' });});
	$('#incentivo_112').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'112',accion:'control_ingreso',incentivo_b:'111' });});
	$('#incentivo_113').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'113',accion:'control_ingreso',incentivo_b:'111' });});
	$('#incentivo_114').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'114',accion:'control_ingreso',incentivo_b:'111' });});
	$('#incentivo_115').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'115',accion:'control_ingreso',incentivo_b:'111' });});
	
	
	$('#incentivo_116').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'116',accion:'control_ingreso',incentivo_b:'116' });});
	$('#incentivo_117').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'117',accion:'control_ingreso',incentivo_b:'116' });});
	$('#incentivo_118').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'118',accion:'control_ingreso',incentivo_b:'116' });});
	$('#incentivo_119').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'119',accion:'control_ingreso',incentivo_b:'116' });});
	$('#incentivo_120').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'120',accion:'control_ingreso',incentivo_b:'116' });});
	
	
	$('#incentivo_121').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'121',accion:'control_ingreso',incentivo_b:'121' });});
	$('#incentivo_122').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'122',accion:'control_ingreso',incentivo_b:'121' });});
	$('#incentivo_123').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'123',accion:'control_ingreso',incentivo_b:'121' });});
	$('#incentivo_124').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'124',accion:'control_ingreso',incentivo_b:'121' });});
	$('#incentivo_125').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'125',accion:'control_ingreso',incentivo_b:'121' });});
	
	
	$('#incentivo_126').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'126',accion:'control_ingreso',incentivo_b:'126' });});
	$('#incentivo_127').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'127',accion:'control_ingreso',incentivo_b:'126' });});
	$('#incentivo_128').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'128',accion:'control_ingreso',incentivo_b:'126' });});
	$('#incentivo_129').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'129',accion:'control_ingreso',incentivo_b:'126' });});
	$('#incentivo_130').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'130',accion:'control_ingreso',incentivo_b:'126' });}); 
	
	
	$('#incentivo_131').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'131',accion:'control_ingreso',incentivo_b:'131' });}); 
	$('#incentivo_132').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'132',accion:'control_ingreso',incentivo_b:'131' });}); 
	$('#incentivo_133').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'133',accion:'control_ingreso',incentivo_b:'131' });}); 
	$('#incentivo_134').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'134',accion:'control_ingreso',incentivo_b:'131' });}); 
	$('#incentivo_135').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'135',accion:'control_ingreso',incentivo_b:'131' });}); 
	
	$('#incentivo_136').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'136',accion:'control_ingreso',incentivo_b:'136' });}); 
	$('#incentivo_137').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'137',accion:'control_ingreso',incentivo_b:'136' });}); 
	$('#incentivo_138').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'138',accion:'control_ingreso',incentivo_b:'136' });}); 
	$('#incentivo_139').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'139',accion:'control_ingreso',incentivo_b:'136' });}); 
	$('#incentivo_140').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'140',accion:'control_ingreso',incentivo_b:'136' });}); 
	
	$('#incentivo_141').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'141',accion:'control_ingreso',incentivo_b:'141' });}); 
	$('#incentivo_142').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'142',accion:'control_ingreso',incentivo_b:'141' });}); 
	$('#incentivo_143').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'143',accion:'control_ingreso',incentivo_b:'141' });}); 
	$('#incentivo_144').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'144',accion:'control_ingreso',incentivo_b:'141' });}); 
	$('#incentivo_145').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'145',accion:'control_ingreso',incentivo_b:'141' });}); 
	
	$('#incentivo_146').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'146',accion:'control_ingreso',incentivo_b:'146' });}); 
	$('#incentivo_147').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'147',accion:'control_ingreso',incentivo_b:'146' });}); 
	$('#incentivo_148').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'148',accion:'control_ingreso',incentivo_b:'146' });}); 
	$('#incentivo_149').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'149',accion:'control_ingreso',incentivo_b:'146' });}); 
	$('#incentivo_150').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'150',accion:'control_ingreso',incentivo_b:'146' });}); 
	
	
	
	
	
	
	$('#incentivo_151').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'151',accion:'control_ingreso',incentivo_b:'151' });}); 
	$('#incentivo_152').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'152',accion:'control_ingreso',incentivo_b:'151' });}); 
	$('#incentivo_153').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'153',accion:'control_ingreso',incentivo_b:'151' });}); 
	$('#incentivo_154').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'154',accion:'control_ingreso',incentivo_b:'151' });}); 
	$('#incentivo_155').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'155',accion:'control_ingreso',incentivo_b:'151' });}); 
	
	$('#incentivo_156').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'156',accion:'control_ingreso',incentivo_b:'156' });}); 
	$('#incentivo_157').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'157',accion:'control_ingreso',incentivo_b:'156' });}); 
	$('#incentivo_158').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'158',accion:'control_ingreso',incentivo_b:'156' });}); 
	$('#incentivo_159').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'159',accion:'control_ingreso',incentivo_b:'156' });}); 
	$('#incentivo_160').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'160',accion:'control_ingreso',incentivo_b:'156' });}); 
	
	$('#incentivo_161').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'161',accion:'control_ingreso',incentivo_b:'161' });}); 
	$('#incentivo_162').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'162',accion:'control_ingreso',incentivo_b:'161' });}); 
	$('#incentivo_163').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'163',accion:'control_ingreso',incentivo_b:'161' });}); 
	$('#incentivo_164').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'164',accion:'control_ingreso',incentivo_b:'161' });}); 
	$('#incentivo_165').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'165',accion:'control_ingreso',incentivo_b:'161' });}); 
	
	$('#incentivo_166').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'166',accion:'control_ingreso',incentivo_b:'166' });}); 
	$('#incentivo_167').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'167',accion:'control_ingreso',incentivo_b:'166' });}); 
	$('#incentivo_168').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'168',accion:'control_ingreso',incentivo_b:'166' });}); 
	$('#incentivo_169').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'169',accion:'control_ingreso',incentivo_b:'166' });}); 
	$('#incentivo_170').click(function(){$.post('sorteo/operador/loteria_incentivo_ajax.php',{ incentivo:'170',accion:'control_ingreso',incentivo_b:'166' });}); 
	
	
	

}

function habilitarIncentivo(id_incentivo){
  
  switch (id_incentivo){
    case ('1'):
      $("#alcanzan").prop('disabled', false);
      break;
    case ('2'):
      $("#superan_hasta_10").prop('disabled', false);
      break;
    case ('3'):
      $("#superan_10_hasta_20").prop('disabled', false);
      break;
    case ('4'):
      $("#superan_20").prop('disabled', false);
      break;
	  
	case ('5'):
      $("#250_omas").prop('disabled', false);
      break;
	case ('6'):
      $("#100_249").prop('disabled', false);
      break;
	case ('7'):
      $("#20_99").prop('disabled', false);
      break;
	  
	case ('8'):
      $("#kit_mundial").prop('disabled', false);
      break;
	  
	  
	  
	case ('9'):
      $("#401_mas").prop('disabled', false);
      break;
	  
	case ('10'):
      $("#201_400_1").prop('disabled', false);
      break;
	case ('11'):
      $("#201_400_2").prop('disabled', false);
      break;
	case ('12'):
      $("#201_400_3").prop('disabled', false);
      break;
	case ('13'):
      $("#201_400_4").prop('disabled', false);
      break;
	case ('14'):
      $("#201_400_5").prop('disabled', false);
      break;
	  
	
	
	
	case ('15'):
      $("#121_200_1").prop('disabled', false);
      break;
	case ('16'):
      $("#121_200_2").prop('disabled', false);
      break;
	 case ('17'):
      $("#121_200_3").prop('disabled', false);
      break;
	 case ('18'):
      $("#121_200_4").prop('disabled', false);
      break;
	  
	
	case ('19'): $("#30_120_1").prop('disabled', false); break;
	case ('20'): $("#30_120_2").prop('disabled', false); break;
	case ('21'): $("#30_120_3").prop('disabled', false); break;
	case ('22'): $("#30_120_4").prop('disabled', false); break;
	case ('23'): $("#30_120_5").prop('disabled', false); break;
	case ('24'): $("#30_120_6").prop('disabled', false); break;
	case ('25'): $("#30_120_7").prop('disabled', false); break;
	case ('26'): $("#30_120_8").prop('disabled', false); break;
	case ('27'): $("#30_120_9").prop('disabled', false); break;
	case ('28'): $("#30_120_10").prop('disabled', false); break;
	case ('29'): $("#30_120_11").prop('disabled', false); break;
	case ('30'): $("#30_120_12").prop('disabled', false); break;
	case ('31'): $("#30_120_13").prop('disabled', false); break;
	case ('32'): $("#30_120_14").prop('disabled', false); break;
	case ('33'): $("#30_120_15").prop('disabled', false); break;
	
	
	
	case ('34'): $("#vuelta_heli_1").prop('disabled', false); break;
	case ('35'): $("#vuelta_heli_2").prop('disabled', false); break;
	case ('36'): $("#vuelta_auto").prop('disabled', false); break;
	case ('37'): $("#carpa_1").prop('disabled', false); break;
	case ('38'): $("#carpa_2").prop('disabled', false); break;
	case ('39'): $("#carpa_3").prop('disabled', false); break;
	
	case ('40'): $("#carpa_4").prop('disabled', false); break;
	case ('41'): $("#carpa_5").prop('disabled', false); break;
	case ('42'): $("#carpa_6").prop('disabled', false); break;
	
	case ('43'): $("#carpa_7").prop('disabled', false); break;
	case ('44'): $("#carpa_8").prop('disabled', false); break;
	case ('45'): $("#carpa_9").prop('disabled', false); break;
	case ('46'): $("#carpa_10").prop('disabled', false); break;
	case ('47'): $("#carpa_11").prop('disabled', false); break;
	case ('48'): $("#carpa_12").prop('disabled', false); break;
	case ('49'): $("#carpa_13").prop('disabled', false); break;
	case ('50'): $("#carpa_14").prop('disabled', false); break;
	case ('51'): $("#carpa_15").prop('disabled', false); break;
	case ('52'): $("#carpa_16").prop('disabled', false); break;
	case ('53'): $("#carpa_17").prop('disabled', false); break;
	case ('54'): $("#carpa_18").prop('disabled', false); break;
	case ('55'): $("#carpa_19").prop('disabled', false); break;
	case ('56'): $("#carpa_20").prop('disabled', false); break;
	case ('57'): $("#carpa_21").prop('disabled', false); break;
	case ('58'): $("#carpa_22").prop('disabled', false); break;
	case ('59'): $("#carpa_23").prop('disabled', false); break;
	case ('60'): $("#carpa_24").prop('disabled', false); break;
	case ('61'): $("#carpa_25").prop('disabled', false); break;
	
	
	case ('62'): $("#incentivo_62").prop('disabled', false); break;
	case ('64'): $("#incentivo_64").prop('disabled', false); break;
	case ('66'): $("#incentivo_66").prop('disabled', false); break;
	case ('67'): $("#incentivo_67").prop('disabled', false); break;
	case ('68'): $("#incentivo_68").prop('disabled', false); break;
	case ('69'): $("#incentivo_69").prop('disabled', false); break;
	case ('70'): $("#incentivo_70").prop('disabled', false); break;
	case ('71'): $("#incentivo_71").prop('disabled', false); break;
	case ('72'): $("#incentivo_72").prop('disabled', false); break;
	case ('73'): $("#incentivo_73").prop('disabled', false); break;
	case ('74'): $("#incentivo_74").prop('disabled', false); break;
	case ('75'): $("#incentivo_75").prop('disabled', false); break;
	case ('76'): $("#incentivo_76").prop('disabled', false); break;
	case ('77'): $("#incentivo_77").prop('disabled', false); break;
	case ('78'): $("#incentivo_78").prop('disabled', false); break;
	case ('79'): $("#incentivo_79").prop('disabled', false); break;
	case ('80'): $("#incentivo_80").prop('disabled', false); break;
	case ('81'): $("#incentivo_81").prop('disabled', false); break;
	case ('82'): $("#incentivo_82").prop('disabled', false); break;
	case ('83'): $("#incentivo_83").prop('disabled', false); break;
	case ('84'): $("#incentivo_84").prop('disabled', false); break;
	case ('85'): $("#incentivo_85").prop('disabled', false); break;
	case ('86'): $("#incentivo_86").prop('disabled', false); break;
	case ('87'): $("#incentivo_87").prop('disabled', false); break;
	case ('88'): $("#incentivo_88").prop('disabled', false); break;
	case ('89'): $("#incentivo_89").prop('disabled', false); break;
	case ('90'): $("#incentivo_90").prop('disabled', false); break;
	
	
	
	case ('91'): $("#incentivo_91").prop('disabled', false); break;
	case ('92'): $("#incentivo_92").prop('disabled', false); break;
	case ('93'): $("#incentivo_93").prop('disabled', false); break;
	case ('94'): $("#incentivo_94").prop('disabled', false); break;
	case ('95'): $("#incentivo_95").prop('disabled', false); break;
	case ('96'): $("#incentivo_96").prop('disabled', false); break;
	case ('97'): $("#incentivo_97").prop('disabled', false); break;
	case ('98'): $("#incentivo_98").prop('disabled', false); break;
	case ('99'): $("#incentivo_99").prop('disabled', false); break;
	case ('100'): $("#incentivo_100").prop('disabled', false); break;
	case ('101'): $("#incentivo_101").prop('disabled', false); break;
	case ('102'): $("#incentivo_102").prop('disabled', false); break;
	case ('103'): $("#incentivo_103").prop('disabled', false); break;
	case ('104'): $("#incentivo_104").prop('disabled', false); break;
	case ('105'): $("#incentivo_105").prop('disabled', false); break;
	case ('106'): $("#incentivo_106").prop('disabled', false); break;
	case ('107'): $("#incentivo_107").prop('disabled', false); break;
	case ('108'): $("#incentivo_108").prop('disabled', false); break;
	case ('109'): $("#incentivo_109").prop('disabled', false); break;
	case ('110'): $("#incentivo_110").prop('disabled', false); break;
	
	
	case ('111'): $("#incentivo_111").prop('disabled', false); break;
	case ('112'): $("#incentivo_112").prop('disabled', false); break;
	case ('113'): $("#incentivo_113").prop('disabled', false); break;
	case ('114'): $("#incentivo_114").prop('disabled', false); break;
	case ('115'): $("#incentivo_115").prop('disabled', false); break;
	case ('116'): $("#incentivo_116").prop('disabled', false); break;
	case ('117'): $("#incentivo_117").prop('disabled', false); break;
	case ('118'): $("#incentivo_118").prop('disabled', false); break;
	case ('119'): $("#incentivo_119").prop('disabled', false); break;
	case ('120'): $("#incentivo_120").prop('disabled', false); break;
	case ('121'): $("#incentivo_121").prop('disabled', false); break;
	case ('122'): $("#incentivo_122").prop('disabled', false); break;
	case ('123'): $("#incentivo_123").prop('disabled', false); break;
	case ('124'): $("#incentivo_124").prop('disabled', false); break;
	case ('125'): $("#incentivo_125").prop('disabled', false); break;
	case ('126'): $("#incentivo_126").prop('disabled', false); break;
	case ('127'): $("#incentivo_127").prop('disabled', false); break;
	case ('128'): $("#incentivo_128").prop('disabled', false); break;
	case ('129'): $("#incentivo_129").prop('disabled', false); break;
	case ('130'): $("#incentivo_130").prop('disabled', false); break; 
	
	case ('131'): $("#incentivo_131").prop('disabled', false); break; 
	case ('132'): $("#incentivo_132").prop('disabled', false); break; 
	case ('133'): $("#incentivo_133").prop('disabled', false); break; 
	case ('134'): $("#incentivo_134").prop('disabled', false); break; 
	case ('135'): $("#incentivo_135").prop('disabled', false); break; 
	case ('136'): $("#incentivo_136").prop('disabled', false); break; 
	case ('137'): $("#incentivo_137").prop('disabled', false); break; 
	case ('138'): $("#incentivo_138").prop('disabled', false); break; 
	case ('139'): $("#incentivo_139").prop('disabled', false); break; 
	case ('140'): $("#incentivo_140").prop('disabled', false); break; 
	case ('141'): $("#incentivo_141").prop('disabled', false); break; 
	case ('142'): $("#incentivo_142").prop('disabled', false); break; 
	case ('143'): $("#incentivo_143").prop('disabled', false); break; 
	case ('144'): $("#incentivo_144").prop('disabled', false); break; 
	case ('145'): $("#incentivo_145").prop('disabled', false); break; 
	case ('146'): $("#incentivo_146").prop('disabled', false); break; 
	case ('147'): $("#incentivo_147").prop('disabled', false); break; 
	case ('148'): $("#incentivo_148").prop('disabled', false); break; 
	case ('149'): $("#incentivo_149").prop('disabled', false); break; 
	case ('150'): $("#incentivo_150").prop('disabled', false); break; 
	
	
	
	case ('151'): $("#incentivo_151").prop('disabled', false); break; 
	case ('152'): $("#incentivo_152").prop('disabled', false); break; 
	case ('153'): $("#incentivo_153").prop('disabled', false); break; 
	case ('154'): $("#incentivo_154").prop('disabled', false); break; 
	case ('155'): $("#incentivo_155").prop('disabled', false); break; 
	case ('156'): $("#incentivo_156").prop('disabled', false); break; 
	case ('157'): $("#incentivo_157").prop('disabled', false); break; 
	case ('158'): $("#incentivo_158").prop('disabled', false); break; 
	case ('159'): $("#incentivo_159").prop('disabled', false); break; 
	case ('160'): $("#incentivo_160").prop('disabled', false); break; 
	case ('161'): $("#incentivo_161").prop('disabled', false); break; 
	case ('162'): $("#incentivo_162").prop('disabled', false); break; 
	case ('163'): $("#incentivo_163").prop('disabled', false); break; 
	case ('164'): $("#incentivo_164").prop('disabled', false); break; 
	case ('165'): $("#incentivo_165").prop('disabled', false); break; 
	case ('166'): $("#incentivo_166").prop('disabled', false); break; 
	case ('167'): $("#incentivo_167").prop('disabled', false); break; 
	case ('168'): $("#incentivo_168").prop('disabled', false); break; 
	case ('169'): $("#incentivo_169").prop('disabled', false); break; 
	case ('170'): $("#incentivo_170").prop('disabled', false); break; 
	
	
	
    }
}

function deshabilitarIncentivo(id_incentivo){
  switch (id_incentivo){
    case ('1'):
      $("#alcanzan").prop('disabled', true);
      break;
    case ('2'):
      $("#superan_hasta_10").prop('disabled', true);
      break;
    case ('3'):
      $("#superan_10_hasta_20").prop('disabled', true);
      break;
	  
    case ('4'):
      $("#superan_20").prop('disabled', true);
      break;
	  
	case ('5'):
      $("#250_omas").prop('disabled', true);
      break;
	case ('6'):
      $("#100_249").prop('disabled', true);
      break;
	case ('7'):
      $("#20_99").prop('disabled', true);
      break;
	  
	case ('8'):
      $("#kit_mundial").prop('disabled', true);
      break;
	
	
	
	
	case ('9'):
      $("#401_mas").prop('disabled', true);
      break;
	case ('10'):
      $("#201_400_1").prop('disabled', true);
      break;
	case ('11'):
      $("#201_400_2").prop('disabled', true);
      break;
	case ('12'):
      $("#201_400_3").prop('disabled', true);
      break;
	case ('13'):
      $("#201_400_4").prop('disabled', true);
      break;
	case ('14'):
      $("#201_400_5").prop('disabled', true);
      break;
	  
	  
	case ('15'):
      $("#121_200_1").prop('disabled', true);
      break;
	case ('16'):
      $("#121_200_2").prop('disabled', true);
      break;
	case ('17'):
      $("#121_200_3").prop('disabled', true);
      break;
	case ('18'):
      $("#121_200_4").prop('disabled', true);
      break;
	  
	  
	case ('19'): $("#30_120_1").prop('disabled', true); break;
	case ('20'): $("#30_120_2").prop('disabled', true); break;
	case ('21'): $("#30_120_3").prop('disabled', true); break;
	case ('22'): $("#30_120_4").prop('disabled', true); break;
	case ('23'): $("#30_120_5").prop('disabled', true); break;
	case ('24'): $("#30_120_6").prop('disabled', true); break;
	case ('25'): $("#30_120_7").prop('disabled', true); break;
	case ('26'): $("#30_120_8").prop('disabled', true); break;
	case ('27'): $("#30_120_9").prop('disabled', true); break;
	case ('28'): $("#30_120_10").prop('disabled', true); break;
	case ('29'): $("#30_120_11").prop('disabled', true); break;
	case ('30'): $("#30_120_12").prop('disabled', true); break;
	case ('31'): $("#30_120_13").prop('disabled', true); break;
	case ('32'): $("#30_120_14").prop('disabled', true); break;
	case ('33'): $("#30_120_15").prop('disabled', true); break;
	
	
	
	case ('34'): $("#vuelta_heli_1").prop('disabled', true); break;
	case ('35'): $("#vuelta_heli_2").prop('disabled', true); break;
	case ('36'): $("#vuelta_auto").prop('disabled', true); break;
	case ('37'): $("#carpa_1").prop('disabled', true); break;
	case ('38'): $("#carpa_2").prop('disabled', true); break;
	case ('39'): $("#carpa_3").prop('disabled', true); break;
	
	case ('40'): $("#carpa_4").prop('disabled', true); break;
	case ('41'): $("#carpa_5").prop('disabled', true); break;
	case ('42'): $("#carpa_6").prop('disabled', true); break;
	
	case ('43'): $("#carpa_7").prop('disabled', true); break;
	case ('44'): $("#carpa_8").prop('disabled', true); break;
	case ('45'): $("#carpa_9").prop('disabled', true); break;
	case ('46'): $("#carpa_10").prop('disabled', true); break;
	case ('47'): $("#carpa_11").prop('disabled', true); break;
	case ('48'): $("#carpa_12").prop('disabled', true); break;
	case ('49'): $("#carpa_13").prop('disabled', true); break;
	case ('50'): $("#carpa_14").prop('disabled', true); break;
	case ('51'): $("#carpa_15").prop('disabled', true); break;
	case ('52'): $("#carpa_16").prop('disabled', true); break;
	case ('53'): $("#carpa_17").prop('disabled', true); break;
	case ('54'): $("#carpa_18").prop('disabled', true); break;
	case ('55'): $("#carpa_19").prop('disabled', true); break;
	case ('56'): $("#carpa_20").prop('disabled', true); break;
	case ('57'): $("#carpa_21").prop('disabled', true); break;
	case ('58'): $("#carpa_22").prop('disabled', true); break;
	case ('59'): $("#carpa_23").prop('disabled', true); break;
	case ('60'): $("#carpa_24").prop('disabled', true); break;
	case ('61'): $("#carpa_25").prop('disabled', true); break;

	case ('62'): $("#incentivo_62").prop('disabled', true); break;
	case ('64'): $("#incentivo_64").prop('disabled', true); break;
	case ('66'): $("#incentivo_66").prop('disabled', true); break;
	
	case ('67'): $("#incentivo_67").prop('disabled', true); break;
	case ('68'): $("#incentivo_68").prop('disabled', true); break;
	case ('69'): $("#incentivo_69").prop('disabled', true); break;
	case ('70'): $("#incentivo_70").prop('disabled', true); break;
	case ('71'): $("#incentivo_71").prop('disabled', true); break;
	case ('72'): $("#incentivo_72").prop('disabled', true); break;
	case ('73'): $("#incentivo_73").prop('disabled', true); break;
	case ('74'): $("#incentivo_74").prop('disabled', true); break;
	case ('75'): $("#incentivo_75").prop('disabled', true); break;
	case ('76'): $("#incentivo_76").prop('disabled', true); break;
	case ('77'): $("#incentivo_77").prop('disabled', true); break;
	case ('78'): $("#incentivo_78").prop('disabled', true); break;
	case ('79'): $("#incentivo_79").prop('disabled', true); break;
	case ('80'): $("#incentivo_80").prop('disabled', true); break;
	case ('81'): $("#incentivo_81").prop('disabled', true); break;
	case ('82'): $("#incentivo_82").prop('disabled', true); break;
	case ('83'): $("#incentivo_83").prop('disabled', true); break;
	case ('84'): $("#incentivo_84").prop('disabled', true); break;
	case ('85'): $("#incentivo_85").prop('disabled', true); break;
	case ('86'): $("#incentivo_86").prop('disabled', true); break;
	case ('87'): $("#incentivo_87").prop('disabled', true); break;
	case ('88'): $("#incentivo_88").prop('disabled', true); break;
	case ('89'): $("#incentivo_89").prop('disabled', true); break;
	case ('90'): $("#incentivo_90").prop('disabled', true); break;
	
	
	case ('91'): $("#incentivo_91").prop('disabled', true); break;
	case ('92'): $("#incentivo_92").prop('disabled', true); break;
	case ('93'): $("#incentivo_93").prop('disabled', true); break;
	case ('94'): $("#incentivo_94").prop('disabled', true); break;
	case ('95'): $("#incentivo_95").prop('disabled', true); break;
	
	case ('96'): $("#incentivo_96").prop('disabled', true); break;
	case ('97'): $("#incentivo_97").prop('disabled', true); break;
	case ('98'): $("#incentivo_98").prop('disabled', true); break;
	case ('99'): $("#incentivo_99").prop('disabled', true); break;
	case ('100'): $("#incentivo_100").prop('disabled', true); break;
	
	case ('101'): $("#incentivo_101").prop('disabled', true); break;
	case ('102'): $("#incentivo_102").prop('disabled', true); break;
	case ('103'): $("#incentivo_103").prop('disabled', true); break;
	case ('104'): $("#incentivo_104").prop('disabled', true); break;
	case ('105'): $("#incentivo_105").prop('disabled', true); break;
	
	case ('106'): $("#incentivo_106").prop('disabled', true); break;
	case ('107'): $("#incentivo_107").prop('disabled', true); break;
	case ('108'): $("#incentivo_108").prop('disabled', true); break;
	case ('109'): $("#incentivo_109").prop('disabled', true); break;
	case ('110'): $("#incentivo_110").prop('disabled', true); break;
	
	case ('111'): $("#incentivo_111").prop('disabled', true); break;
	case ('112'): $("#incentivo_112").prop('disabled', true); break;
	case ('113'): $("#incentivo_113").prop('disabled', true); break;
	case ('114'): $("#incentivo_114").prop('disabled', true); break;
	case ('115'): $("#incentivo_115").prop('disabled', true); break;
	case ('116'): $("#incentivo_116").prop('disabled', true); break;
	case ('117'): $("#incentivo_117").prop('disabled', true); break;
	case ('118'): $("#incentivo_118").prop('disabled', true); break;
	case ('119'): $("#incentivo_119").prop('disabled', true); break;
	case ('120'): $("#incentivo_120").prop('disabled', true); break;
	case ('121'): $("#incentivo_121").prop('disabled', true); break;
	case ('122'): $("#incentivo_122").prop('disabled', true); break;
	case ('123'): $("#incentivo_123").prop('disabled', true); break;
	case ('124'): $("#incentivo_124").prop('disabled', true); break;
	case ('125'): $("#incentivo_125").prop('disabled', true); break;
	case ('126'): $("#incentivo_126").prop('disabled', true); break;
	case ('127'): $("#incentivo_127").prop('disabled', true); break;
	case ('128'): $("#incentivo_128").prop('disabled', true); break;
	case ('129'): $("#incentivo_129").prop('disabled', true); break;
	case ('130'): $("#incentivo_130").prop('disabled', true); break; 
	
	case ('131'): $("#incentivo_131").prop('disabled', true); break; 
	case ('132'): $("#incentivo_132").prop('disabled', true); break; 
	case ('133'): $("#incentivo_133").prop('disabled', true); break; 
	case ('134'): $("#incentivo_134").prop('disabled', true); break; 
	case ('135'): $("#incentivo_135").prop('disabled', true); break; 
	case ('136'): $("#incentivo_136").prop('disabled', true); break; 
	case ('137'): $("#incentivo_137").prop('disabled', true); break; 
	case ('138'): $("#incentivo_138").prop('disabled', true); break; 
	case ('139'): $("#incentivo_139").prop('disabled', true); break; 
	case ('140'): $("#incentivo_140").prop('disabled', true); break; 
	case ('141'): $("#incentivo_141").prop('disabled', true); break; 
	case ('142'): $("#incentivo_142").prop('disabled', true); break; 
	case ('143'): $("#incentivo_143").prop('disabled', true); break; 
	case ('144'): $("#incentivo_144").prop('disabled', true); break; 
	case ('145'): $("#incentivo_145").prop('disabled', true); break; 
	case ('146'): $("#incentivo_146").prop('disabled', true); break; 
	case ('147'): $("#incentivo_147").prop('disabled', true); break; 
	case ('148'): $("#incentivo_148").prop('disabled', true); break; 
	case ('149'): $("#incentivo_149").prop('disabled', true); break; 
	case ('150'): $("#incentivo_150").prop('disabled', true); break; 
	
	
	
	case ('151'): $("#incentivo_151").prop('disabled', true); break;
	case ('152'): $("#incentivo_152").prop('disabled', true); break;
	case ('153'): $("#incentivo_153").prop('disabled', true); break;
	case ('154'): $("#incentivo_154").prop('disabled', true); break;
	case ('155'): $("#incentivo_155").prop('disabled', true); break;
	case ('156'): $("#incentivo_156").prop('disabled', true); break;
	case ('157'): $("#incentivo_157").prop('disabled', true); break;
	case ('158'): $("#incentivo_158").prop('disabled', true); break;
	case ('159'): $("#incentivo_159").prop('disabled', true); break;
	case ('160'): $("#incentivo_160").prop('disabled', true); break;
	case ('161'): $("#incentivo_161").prop('disabled', true); break;
	case ('162'): $("#incentivo_162").prop('disabled', true); break;
	case ('163'): $("#incentivo_163").prop('disabled', true); break;
	case ('164'): $("#incentivo_164").prop('disabled', true); break;
	case ('165'): $("#incentivo_165").prop('disabled', true); break;
	case ('166'): $("#incentivo_166").prop('disabled', true); break;
	case ('167'): $("#incentivo_167").prop('disabled', true); break;
	case ('168'): $("#incentivo_168").prop('disabled', true); break;
	case ('169'): $("#incentivo_169").prop('disabled', true); break;
	case ('170'): $("#incentivo_170").prop('disabled', true); break;
	
	
	
  }
}

function mostrarMensaje(json_mensaje){
  if(json_mensaje.tipo=='error'){
    animarMensajes();
    $("#error_juego.alert").slideDown("slow");
    $('#error_juego > .contenido_error').html(json_mensaje.mensaje);
  }else if(json_mensaje.tipo=='success'){
    animarMensajes();
    $("#success_juego.alert").slideDown("slow");
    $('#success_juego > .contenido_error').html(json_mensaje.mensaje);       
  }else if(json_mensaje.tipo=='info'){
    $("#warning_juego.alert").slideDown("slow");
    $('#warning_juego > .contenido_error').html(json_mensaje.mensaje);   
  }
  $("#fraccion_div, .subtitulo_juego, #entero_div").css("display","none");
  $("#entero, #fraccion").val('');
  $("#posicion").focus();     
      
}