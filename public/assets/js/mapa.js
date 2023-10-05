var map;
var directionsDisplay;
var directionsService;
var markers = [];
var pontos = [];		
var directionsArray = [];
var posicoes = [];
var flightPath;
var tempoRefresh = null;

function exibeMapa(){
	directionsDisplay = new google.maps.DirectionsRenderer();
	var pyrmont = new google.maps.LatLng(-5.0839793,-42.786205313);
	map = new google.maps.Map(document.getElementById('map'), {
	  mapTypeId: google.maps.MapTypeId.ROADMAP,
	  center: pyrmont,
	  zoom: 14
	}); 
	directionsDisplay.setMap(map);	
	directionsService = new google.maps.DirectionsService();
}

function removeLines(){
	for (var i = 0; i < flightPath.length; i++) {
		flightPath.setMap(null);
	}
}

function buscar(){
	$('#btnbusca').html('Buscando...');
	$('#btnbusca').prop('disabled',true);	
	var data = $('#data').val();
	if(!data.indexOf("-")>0){
		data = data.split("/");
		data = data[2]+'-'+data[1]+'-'+data[0];
	}
	var vendedor = $('#idFuncionario').val();
	//var zona = $('#zona').val();	
	get_json('/zipmap/visitas/json',setPontosVisita,{idFuncionario: vendedor, data: data});	
}

function buscar_vendedor(){
	$('#btnbusca').html('Buscando...');
	$('#btnbusca').prop('disabled',true);		
	var vendedor = $('#idFuncionario').val();	
	get_json('/zipmap/mapa/ultimaGeolocalizacaovendedore',setPontosVendedores,{idFuncionario: vendedor});	
}

function setPontos(pt){	
	//removeLines();//
	clearOverlays();
	$('#btnbusca').prop('disabled',false);
	$('#btnbusca').html('Buscar');
	if(pt==null){
		$('#descricaoDistancia').empty();
		$('#dv_lista').empty();
		$('#infoquantidade').empty();
		alert('Nenhum registro encontrado!');
	}else{
		if(pt.vendas.length>0 && pt.vendas[0].localizacao!=null){
			clearOverlays();				
			//console.log(pt);
			$('#infoquantidade').html(pt.vendas.length+' registros encontrados');
			var pontos = new Array();
			$('#descricaoDistancia').empty();
			$('#dv_lista').empty();
			var str = '';
			str+= '<table id="table-custom-1" class="table table-hover table-bordered"><tr>';
			str += "<th>ITEM</th>"+
				"<th>TIPO</th>"+
				"<th>CÓDIGO</th>"+
				"<th>DATA</th>"+
				"<th>CLIENTE</th>"+					
				"<th>ZONA</th>"+					
				"<th>VL.TOTAL</th>"+
				"<th>COND.PGTO</th>"+					
				"<th>OBS</th>"+
				"<th>STATUS VISITA</th>"+
				"<th>LOCALIZAÇÃO</th>"+
				"</tr><tbody>";
			coord = pt.vendas[0].localizacao.split(',');
			map.setCenter(new google.maps.LatLng(coord[0],coord[1]));
			var local1 = '';
			var cont_mark = 0;
			var flightPlanCoordinates = [];
			var polylineLength = 0;
			Total = 0;
			distancia = 0;
			var cont_total_pontos = 0;
			for(i=0;i<pt.vendas.length;i++){				
				if(pt.vendas[i].localizacao!=null){
					coord = pt.vendas[i].localizacao.split(',');						
					if(coord[0]+','+coord[1]!=local1){
						cont_total_pontos++;
					}
				}
			}
			local1 = '';
			for(i=0;i<pt.vendas.length;i++){				
				if(pt.vendas[i].localizacao!=null){
					coord = pt.vendas[i].localizacao.split(',');						
					if(coord[0]+','+coord[1]!=local1){
						local1 = coord[0]+','+coord[1];		
						//posicoes.push(local1);				
						//pontos.push({location: new google.maps.LatLng(coord[0],coord[1]), stopover:false});			
						flightPlanCoordinates.push(new google.maps.LatLng(coord[0],coord[1]));
						
						if (cont_mark > 0){
							polylineLength = google.maps.geometry.spherical.computeDistanceBetween(flightPlanCoordinates[cont_mark-1], flightPlanCoordinates[cont_mark]);	
							polylineLength = polylineLength/1000;
							distancia += polylineLength;
							polylineLength = polylineLength.toFixed(2);
							$('#descricaoDistancia').append('<div style="border:1px solid #ccc;">'+pt.vendas[cont_mark-1].NomeCliente + ' <strong>até</strong> '+pt.vendas[cont_mark].NomeCliente + '<br>Distância: <strong>'+(polylineLength) + ' Km</strong></div>');
						}
						cont_mark++;
						createMarker(coord[0],coord[1],cont_mark,cont_total_pontos);
					}
					Total += eval(pt.vendas[i].Total);						
					//1900-01-01 12:20:45.000
					var hora = pt.vendas[i].Hora;
					str += "<tr><td>"+(i+1)+"</td>"+
						"<td>"+pt.vendas[i].Tipo+"</td>"+
						"<td>"+pt.vendas[i].CodVenda+"</td>"+
						"<td>"+pt.vendas[i].Data+' '+(hora.substring(11,17))+"</td>"+
						"<td>"+pt.vendas[i].NomeCliente+"</td>"+
						"<td>"+pt.vendas[i].NomeZona+"</td>"+
						"<td>"+float2moeda(pt.vendas[i].Total)+"</td>"+
						"<td>"+pt.vendas[i].NomeCondPgto+"</td>"+
						"<td>"+pt.vendas[i].Obs+"</td>"+
						"<td>"+pt.vendas[i].NomeStatusVisita+"</td>"+
						"<td>"+local1+"</td>"+
						"</tr>";
				}
			}
			//posicoes = [];
			str += "</tbody></table>";
			str+= '<table id="table-custom-2" class="table table-hover table-bordered"><tr>';
			str += "<th>TOTAL: </th>"+
					"<td>"+float2moeda(Total)+"</td>"+
				"</tr><tbody>";
			str += "</tbody></table>";
			$('#dv_lista').append(str);
			//calcRoute(pontos);
			console.log(flightPlanCoordinates);
			flightPath = new google.maps.Polyline({
				path: flightPlanCoordinates,
				geodesic: true,
				strokeColor: ['#0008AC','#FF0000'],
				strokeOpacity: 1.0,
				strokeWeight: 2
			  });
			
			  flightPath.setMap(map);
			  $('#descricaoDistancia').append('<br><br>Distância total: <strong>'+distancia.toFixed(2)+' km</strong>');
		}
	}
}

function setPontosVisita(pt){
	$('#btnbusca').prop('disabled',false);
	$('#btnbusca').html('Buscar');	
	clearOverlays();
	setPontosT(pt.matrizPosicoes);
	if(pt==null){
		alert('Nenhum registro encontrado!');	
	}else{
		if(pt.visitas.length>0 && pt.visitas[0].localizacao!=null){			
			//console.log(pt);
			$('#infoquantidade').html(pt.visitas.length+' registros encontrados');
			var pontos = new Array();
			$('#descricaoDistancia').empty();
			$('#dv_lista').empty();
			var str = '<table id="table-custom-1" class="table table-hover table-bordered"><tr>';
			str += "<th>ITEM</th><th>CÓDIGO</th>"+
				"<th>DATA</th>"+
				"<th>CLIENTE</th>"+					
				"<th>CIDADE/UF</th>"+
				"<th>BAIRRO</th>"+
				"<th>ENDEREÇO CLIENTE</th>"+
				"<th>STATUS</th>"+
				"<th>OBS</th>"+
				"<th>LOCALIZAÇÃO</th>"+
				"<th>ENDEREÇO</th>"+
				"</tr><tbody>";
			coord = pt.visitas[0].localizacao.split(',');
			map.setCenter(new google.maps.LatLng(coord[0],coord[1]));
			var local1 = '';
			var cont_mark = 0;
			var flightPlanCoordinates = [];
			var polylineLength = 0;
			distancia = 0;
			var cont_total_pontos = 0;
			for(i=0;i<pt.visitas.length;i++){				
				if(pt.visitas[i].localizacao!=null){
					coord = pt.visitas[i].localizacao.split(',');						
					if(coord[0]+','+coord[1]!=local1){
						cont_total_pontos++;
					}
				}
			}
			local1 = '';
			for(i=0;i<pt.visitas.length;i++){				
				if(pt.visitas[i].localizacao!=null){
					coord = pt.visitas[i].localizacao.split(',');
					
					if(coord[0]+','+coord[1]!=local1){
						local1 = coord[0]+','+coord[1];		
						//posicoes.push(local1);				
						//pontos.push({location: new google.maps.LatLng(coord[0],coord[1]), stopover:false});			
						flightPlanCoordinates.push(new google.maps.LatLng(coord[0],coord[1]));
						
						if (cont_mark > 0){
							polylineLength = google.maps.geometry.spherical.computeDistanceBetween(flightPlanCoordinates[cont_mark-1], flightPlanCoordinates[cont_mark]);	
							polylineLength = polylineLength/1000;
							distancia += polylineLength;
							polylineLength = polylineLength.toFixed(2);
							$('#descricaoDistancia').append('<div style="border:1px solid #ccc;">'+pt.visitas[cont_mark-1].cliente + ' <strong>até</strong> '+pt.visitas[cont_mark].cliente + '<br>Distância: <strong>'+(polylineLength) + ' Km</strong></div>');
						}
						cont_mark++;
						createMarker(coord[0],coord[1],cont_mark,cont_total_pontos);
					}
											
					//1900-01-01 12:20:45.000
					var hora = pt.visitas[i].horareg;
					str += "<tr><td>"+(i+1)+"</td>"+
						"<td>"+pt.visitas[i].id+"</td>"+
						"<td>"+formataDateBr(pt.visitas[i].datareg+' '+hora)+"</td>"+
						"<td>"+pt.visitas[i].cliente+"</td>"+
						"<td>"+pt.visitas[i].municipio+'/'+pt.visitas[i].uf+"</td>"+
						"<td>"+pt.visitas[i].bairro+"</td>"+
						"<td>"+pt.visitas[i].logradouro+"</td>"+
						"<td>"+pt.visitas[i].tipo+"</td>"+
						"<td>"+pt.visitas[i].obs+"</td>"+
						"<td>"+local1+"</td>"+
						"<td>"+pt.visitas[i].endereco+"</td>"+
						"</tr>";
				}
			}
			//posicoes = [];
			str += "</tbody></table>";
			$('#dv_lista').append(str);
			//calcRoute(pontos);
			//console.log(flightPlanCoordinates);
			
			/*
			flightPath = new google.maps.Polyline({
				path: flightPlanCoordinates,
				geodesic: true,
				strokeColor: ['#0008AC','#FF0000'],
				strokeOpacity: 1.0,
				strokeWeight: 2
			  });
			
			  flightPath.setMap(map);
			  */
			  $('#descricaoDistancia').append('<br><br>Distância total: <strong>'+distancia.toFixed(2)+' km</strong>');
		}else{
			//alerta('Nenhum registro encontrado!');
		}
	}
}

function setPontosVendedores(pt){
	$('#btnbusca').prop('disabled',false);
	$('#btnbusca').html('Buscar');
	clearOverlays();
	if(pt==null){
		alert('Nenhum registro encontrado!');	
	}else{		
		if(pt.vendedores.length>0){
			coord = pt.vendedores[0].localizacao.split(',');
			map.setCenter(new google.maps.LatLng(coord[0],coord[1]));
			for(i=0;i<pt.vendedores.length;i++){
				if(pt.vendedores[i].localizacao!=null){
					coord = pt.vendedores[i].localizacao.split(',');
					createMarkerVendedor(coord[0],coord[1],pt.vendedores[i]);
				}
			}
		}else{
			//alerta('Nenhum registro encontrado!');
		}
	}
}

function setPontosTodos(pt){
	$('#btnbusca').prop('disabled',false);
	$('#btnbusca').html('Buscar');
	clearOverlays();
	if(pt==null){
		//alert('Nenhum registro encontrado!');	
	}else{		
		if(pt.registros.length>0){
			$('#datahoraatualizacao').html('Ultima Atualização: '+DataHoraBr(getDataHora()));
			coord = pt.registros[0].localizacao.split(',');
			//map.setCenter(new google.maps.LatLng(coord[0],coord[1]));
			for(i=0;i<pt.registros.length;i++){
				if(pt.registros[i].localizacao!=null){
					coord = pt.registros[i].localizacao.split(',');
					createMarkerTodos(coord[0],coord[1],pt.registros[i]);
				}
			}
			if($('#atualizaAuto').prop('checked')){
				clearInterval(tempoRefresh);
        		tempoRefresh = setTimeout('buscar_todos()',9000);
        	}
		}else{
			//alerta('Nenhum registro encontrado!');
		}
	}
}

function get_json(url,func,parametros){	
	jQuery.ajax({
		  type: 'POST',
		  url: url,
		  data: parametros,
		  success: function(data){
			//console.log(data);
			//var resp = jQuery.parseJSON(data);
			func(data);
		  },
		  error: function(xhr, textStatus, error){
			alerta('Ocorreu um erro de conexao com a internet: '+xhr.statusText);
			$('#btnbusca').prop('disabled',false);
			$('#btnbusca').html('Buscar');
		  }
	});
}

function calcRoute(pontos){		
	$('#descricaoDistancia').empty();
	var start = pontos[0].location;
	var end = pontos[pontos.length-1].location;
	pontos.pop();
	pontos.shift();
	console.log(pontos);
	map.setCenter(start)
	var request = {
		origin: start,
		destination: end,
		waypoints: pontos,
		optimizeWaypoints: true,
		travelMode: google.maps.TravelMode.DRIVING
	};

	directionsService.route(request, function(result, status) {
		console.log(status);
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(result);	
			directionsArray.push(directionsDisplay);	  				
			var route = result.routes[0];
			//var bounds = route.bounds;
			//map.fitBounds(bounds);
			//map.setCenter(bounds.getCenter());
			//$('#descricaoDistancia').append('Distancia: '+route.legs[0].distance.text+'<br>');
			var distancia = 0;
			
			for (var i = 0; i < route.legs.length; i++) {
				var routeSegment = i + 1;
				//$('#descricaoDistancia').append('<b>Route Segment: ' + routeSegment + '</b><br>');
				$('#descricaoDistancia').append('<div style="border:1px solid #ccc;">'+route.legs[i].start_address + ' <strong>até</strong> '+route.legs[i].end_address + '<br>Distância: <strong>'+route.legs[i].distance.text + '</strong></div>');
				dist = route.legs[i].distance.text.replace(' km','');
				dist = dist.replace(' m','');
				dist = dist.replace(',','.');
				distancia += parseFloat(dist);
			}		
			$('#descricaoDistancia').append('<br><br>Distância total: <strong>'+distancia+' km</strong>');
		}			
	});

}

function createMarker(lat,lng,cont,total) {
	// console.log('teste::'+total);
	var marker = new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(lat, lng),
		title: lat+','+lng,
		icon: '/zipmap/visitas/marker/'+cont+'/'+total
	});
	markers.push(marker);
	/*google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent('Teste');
		infowindow.open(map, this);
	});*/
}

function createMarkerVendedor(lat,lng,vendedor) {
	// console.log('teste::'+total);
	var marker = new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(lat, lng),
		title: vendedor.nome
	});
	var infowindow = new google.maps.InfoWindow({
    	//content: contentString
    });
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent('Vendedor: '+vendedor.nome+'. Data: '+formataDateBr(vendedor.datareg)+' '+vendedor.horareg);
		infowindow.open(map, this);
	});
	google.maps.event.addListener(marker, 'click', function() {});  
	markers.push(marker);	
}

function createMarkerTodos(lat,lng,registro) {
	// console.log('teste::'+total);
	icone = '/zipmap/images/vendedor-icon-56.png';
	if(registro.tipo=='cliente'){
		icone = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
	}
	var marker = new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(lat, lng),
		title: registro.nome,
		icon: icone
	});
	var infowindow = new google.maps.InfoWindow({
    	//content: (registro.nome+'<br>'+registro.fone+'<br><br>Localização em: '+registro.datareg+' '+registro.horareg);
    });
	google.maps.event.addListener(marker, 'click', function() {
		//infowindow.setContent((registro.tipo=='vendedor'?'Vendedor':'Cliente')+': '+registro.nome);
		if(registro.tipo=='vendedor'){
			str = '';
			if(registro.atendimento!=null){
				dados = registro.atendimento.split('#');
				str = '<div style="border:1px solid #ccc;"><strong>Atendimento:</strong> '+dados[0]+'<br><strong>Cliente:</strong> '+dados[1]+'<br>'+dados[2]+'</div>';
			}
			var string = '<b>'+registro.nome+'</b><br>'+registro.fone+'<br>'+registro.nivelRedeCel.toUpperCase()+'<br><img src="https://cdn2.iconfinder.com/data/icons/freecns-cumulus/16/519973-036_Battery-128.png" width="20" height="18"> '+registro.nivelBateriaCel+'%<br>'+registro.geoEndereco+'<br>'+str+'Localização em:<br>'+formataDateBr(registro.datareg).substring(0,11)+' '+registro.horareg;
			//infowindow.setContent(registro.nome+'<br>'+registro.fone+'<br>Localização em: '+formataDateBr(registro.datareg).substring(0,11)+' '+registro.horareg);
			infowindow.setContent(string);
		}else{
			infowindow.setContent(registro.nome+'<br>'+registro.fone);
		}
		infowindow.open(map, this);
	});
	google.maps.event.addListener(marker, 'click', function() {});  
	markers.push(marker);
}

function clearOverlays() {
	if(typeof flightPath == 'object'){			
		flightPath.setMap(null);
	}
	setAllMap(null);
}
function setAllMap(map) {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(map);
	}

	// for (var i = 0;i< directionsArray.length; i ++) {
	// 	if (directionsArray [i] !== -1) {
	// 		directionsArray [i].setMap(null); 
	// 	}
	// }		
	directionsArray = [];
}

function setPontosT(pt){	
	//removeLines();//
	clearOverlays();	
	if(pt==null){		
		alert('Nenhum registro encontrado!');
	}else{				
		console.log('matrizPosicoes:'+pt.length);
		var pontos = new Array();		
		var local1 = '';
		var cont_mark = 0;
		var flightPlanCoordinates = [];
		var polylineLength = 0;
		Total = 0;
		distancia = 0;
		var cont_total_pontos = 0;			
		local1 = '';
		for(i=0;i<pt.length;i++){				
			if(pt[i].localizacao!=null){
				coord = pt[i].localizacao.split(',');						
				if(coord[0]+','+coord[1]!=local1){
					local1 = coord[0]+','+coord[1];					
					flightPlanCoordinates.push(new google.maps.LatLng(coord[0],coord[1]));
					// if (cont_mark > 0){
					// 	polylineLength = google.maps.geometry.spherical.computeDistanceBetween(flightPlanCoordinates[cont_mark-1], flightPlanCoordinates[cont_mark]);	
					// 	polylineLength = polylineLength/1000;
					// 	distancia += polylineLength;
					// 	polylineLength = polylineLength.toFixed(2);
					// }
					cont_mark++;
				}				
			}
		}
		flightPath = new google.maps.Polyline({
			path: flightPlanCoordinates,
			geodesic: true,
			strokeColor: ['#f7931e'],
			strokeOpacity: 1.0,
			strokeWeight: 2
		});		
		flightPath.setMap(map);
	}
}
