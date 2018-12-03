<?php
require_once 'config/config.php';
require_once 'api/get/olhovivo.php';

$config = new Config();
$olhovivo = new OlhoVivo();

//Valida a chave da API
$olhovivo->validaChave();

?>
<html>
  <head>
    <title>E-Find My Bus v0.2</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.cyan-light_blue.min.css">
    <style>
      #map {
        height: 90%;
      }
      html, body {
        height: 100%;
        margin: 0
        padding: 0;
      }
	</style>
  </head>

	<body>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
			<header class="mdl-layout__header">
			<div class="mdl-layout__header-row">
				<span class="mdl-layout-title">E-Find My Bus v0.1</span>
				<div class="mdl-layout-spacer"></div>
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
					<label class="mdl-button mdl-js-button mdl-button--icon" for="search">
						<i class="material-icons">search</i>
					</label>
					<div class="mdl-textfield__expandable-holder">
						<input class="mdl-textfield__input" type="search" onKeydown="Javascript: if (event.keyCode==13) getLocation();" id="search">
						<label class="mdl-textfield__label" for="search" id>Buscar</label>
					</div>
				</div>

				<button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
					<i class="material-icons">settings</i>
				</button>
				<ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
					<td>
						<label class = "mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect"
							for = "onibus">
							<input type = "checkbox" id = "onibus" class = "mdl-checkbox__input" onclick = "checkBus()">
							<span class = "mdl-checkbox__label">Ônibus</span>
						</label>
					</td>
					<td>
						<label class = "mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect"
							for = "parada">
							<input type = "checkbox" id = "parada" class = "mdl-checkbox__input" onclick = "checkParada()">
							<span class = "mdl-checkbox__label">Paradas</span>
						</label>
					</td>
				</ul>
				<button class="mdl-button mdl-js-button mdl-button--icon" onclick="update()">
					<i class="material-icons">cached</i>
				</button>
			</div>
			</header>
		</div>


		<div id="map"></div>

		<footer class="mdl-mini-footer">
			<div class="mdl-mini-footer__left-section">
			<div class="mdl-logo">Grupo Info</div>
				<ul class="mdl-mini-footer__link-list">
				<li>EACH, Solulções Web - 2018</li>
				<li>Matheus O. Lêu, 8802621 - </li>
				<li>Georgios P. Koutantos, 9277214 -</li>
				<li>Lucas Manoel Magueta, 9277385</li>
				</ul>
			</div>
		</footer>

	<script>
    var posGeo;
    var map, infoWindow;
	var busMarkers = [];
	var paradasMarkers = [];
	var n, s, l, o;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
        	zoom: 18
        });
        infoWindow = new google.maps.InfoWindow;

		//SE MUDA O ZOOM OU ARRASTA O MAPA, ATUALIZA OS MARCADORES
		map.addListener('zoom_changed', function() {
			setLimites();
			update();
  		});

		map.addListener('drag', function() {
			setLimites();
			update();
  		});

        // TENTA USAR O geolocation DO NAVEGADOR
        if (navigator.geolocation) {
        	navigator.geolocation.getCurrentPosition(function(position) {
				var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};

				this.posGeo = pos;
				infoWindow.setPosition(pos);
				infoWindow.setContent('Location found.');
				infoWindow.open(map);
				map.setCenter(pos);
				setLimites();
          	},
			function() {
				posInicial();
        		handleLocationError(true, infoWindow, map.getCenter());		
          	});
        } else {
			posInicial();
        	handleLocationError(false, infoWindow, map.getCenter());
        }
      }

	function posInicial(){
		var pos = {
				lat: -23.4820165,
				lng: -46.5035557
			};
		map.setCenter(pos);
	}
	

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
    }

	function setLimites(){
		//.f.b sul
		//.f.f norte
		//.b.b oeste
		//.b.f leste

		let pos = map.getBounds();
		s = pos.f.b;
		n = pos.f.f;
		o = pos.b.b;
		l = pos.b.f;
	}

	//RECEBE O VALOR DE SEARCH E VAI PARA O ENDEREÇO DIGITADO
	function getLocation(){
		let address = document.getElementById('search').value;
		geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == 'OK') {
				map.setCenter(results[0].geometry.location);
				setLimites();
				map.setZoom(18);
			} else {
				alert('Não foi possível encontrar o endereço solicitado: ' + status);
			}
		});
	}

	//CRIA MARCADOR PARA OS ÔNIBUS
    function criaMarcadorOnibus(onibus, j){
        let pos = {
            lat: onibus.py[j],
            lng: onibus.px[j]
        };
		let icon = <?php echo json_encode($config->getBusIcon()); ?>;
		let info = onibus.letreiro + " " + onibus.origem + " - " + onibus.destino;

		let infowindow = new google.maps.InfoWindow({
          content: info
        });

		var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: info,
            icon: icon
        });

		marker.addListener('click', function() {
        	infowindow.open(map, marker);
        });

		busMarkers.push(marker);
      }

	//CRIA MARCADOR PARA AS PARADAS
	function criaMarcadorParadas(parada){
        let pos = {
            lat: parada.py,
            lng: parada.px
        };

		let info = parada.np + ' - ' + parada.ed;

		let infowindow = new google.maps.InfoWindow({
        	content: info
        });

		let icon = <?php echo json_encode($config->getStopIcon()); ?>;
        var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: info,
			icon: icon
        });

		marker.addListener('click', function() {
        	infowindow.open(map, marker);
        });

		paradasMarkers.push(marker);
    }

	//PARA CADA ÔNIBUS NA LISTA, CRIA MARCADOR
    function atualizaListaOnibus(busList){
        for(i = 0; i < busList.length; i++){
          	for(j = 0; j < busList[i].px.length; j++){
            	//if(posGeo.lat - 2 < busList[i].py[j] && busList[i].py[j] < posGeo.lat+2 && posGeo.lng -2 < busList[i].px[j] && busList[i].px[j] < posGeo.lng+2){
            	//VERIFICA SE ESTÁ DENTRO DA TELA
				if(busList[i].py[j] > s && busList[i].py[j] < n && busList[i].px[j] > o && busList[i].px[j] < l){
					criaMarcadorOnibus(busList[i], j);
            	}
          	}
        }
    }

	// "" "" "" PARADA "" ""
	function atualizaListaParadas(paradas){
        for(i = 0; i < paradas.length; i++){
            //if(posGeo.lat - 0.2 < paradas[i].py && paradas[i].py < posGeo.lat+0.2 && posGeo.lng - 0.2 < paradas[i].px && paradas[i].px < posGeo.lng+0.2){
            //VERIFICA SE ESTÁ DENTRO DA TELA
			if(paradas[i].py > s && paradas[i].py < n && paradas[i].px > o && paradas[i].px < l){
			  	criaMarcadorParadas(paradas[i]);
            }
		}
    }

	//CHECA SE O CAMPO ONIBUS ESTÁ MARCADO
    function checkBus(){

        if(document.getElementById('onibus').checked){
			console.log('Creating markers');
		    let allBus = getAllBusPositions();
            atualizaListaOnibus(allBus);
        }
        if(!document.getElementById('onibus').checked){
        	console.log('Cleaning markers');
			limpaMarcadores(busMarkers);

		busMarkers.length = 0;
        }
      }

	//"" "" "" PARADA "" ""
	function checkParada(){

		if(document.getElementById('parada').checked){
			console.log('Creating markers');
			let allParadas = getAllParadasPositions();
		    atualizaListaParadas(allParadas);
        }

        if(!document.getElementById('parada').checked){
			console.log('Cleaning markers');
			limpaMarcadores(paradasMarkers);

        }
	  }

	//RECEBE UM ARRAY DE MARCADORES E O LIMPA
	function limpaMarcadores(marker){
		for (let i = 0; i < marker.length; i++ ) {
				marker[i].setMap(null);
			}
			marker.length = 0;
	}

	//ATUALIZA OS MARCADORES QUANDO ACONTECE ALGUM EVENTO
  	function update(){

		if(document.getElementById('onibus').checked){
			console.log('Updating bus markers');
			bus = getAllBusPositions();
			limpaMarcadores(busMarkers);
			atualizaListaOnibus(bus);
			for (var i=0; i<busMarkers.length; i++){
    			if( !map.getBounds().contains(busMarkers[i].getPosition()) ){
					console.log('Removing unused markers');
					busMarkers[i].setMap(null);
    			}
			}
		}
		if(document.getElementById('parada').checked){
			console.log('Updating stops markers');
			paradas = getAllParadasPositions();
			limpaMarcadores(paradasMarkers);
			atualizaListaParadas(paradas);
			for (var i=0; i<paradasMarkers.length; i++){
    			if( !map.getBounds().contains(paradasMarkers[i].getPosition()) ){
					console.log('Removing unused markers');
					paradasMarkers[i].setMap(null);
    			}
			}

		}
	}

	//RECEBE A POSIÇÃO DOS ÔNIBUS VINDO DA REQUISIÇÃO DA API
    function getAllBusPositions(){
        let allBus = <?php echo json_encode($olhovivo->getPosAllOnibus()); ?>;
        return allBus;
    }

	//RECEBE AS PARADAS "" "" "" ""
    function getAllParadasPositions(){
		let allParadas = <?php echo json_encode($olhovivo->getParadaPorCorredor()); ?>;
        return allParadas;
    }

    </script>

    <script src=
         <?php echo $config->getGURL() . $config->getGToken() . "&callback=initMap" ?>
          async defer>
    </script>


	<script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
	</body>
</html>