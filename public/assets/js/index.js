function sendForm(formid,div) {

	carregando(1);

	jQuery("#"+formid).ajaxSubmit(function(resposta){ 

		jQuery('#'+div).html(resposta);	

		carregando(0);	

	});

}

var abrirDivRequest = null;

function abrir_div(url, div, index, callBack, obj){
	if(typeof(div)=='undefined'){
		div = 'container';
	}
    console.log(`abrir_div('${url}', '${div}', '${index}')`)
	$('.side-menu li').each(function(index, el) {
		a = $(this).find('a').attr('href');
		if(a != $(obj).attr('href')){
			$(this).removeClass('active');
			$(this).removeClass('current-page');
		}else{
			u = $(this).parent('ul').parent('li');
			$(u).addClass('active');
		}
	});
	if(abrirDivRequest && typeof abrirDivRequest != 'undefined'){
		abrirDivRequest.abort();		
	}
	carregando(1);
	abrirDivRequest = jQuery.post(url,{index:index},function(resposta){ 
		jQuery('#'+div).html(resposta);
		carregando(0);
	}).done(function() {
		if($('.hamburger').hasClass('is-active')){
			$('.hamburger').click();
		}

    	if(typeof callBack != 'undefined'){
    		setTimeout(callBack,200);
    	}
  	}).fail(function(error){  	
		if($('.hamburger').hasClass('is-active')){
			$('.hamburger').click();
		}

    	if(error.statusText != 'abort'){	
    		alerta('Erro ocorrido: '+error.statusText);
    		setTimeout(()=>{carregando(0)},100);
    	}
  	});
}

function replaceAll(str, de, para){	
    var pos = str.indexOf(de);
    while (pos > -1){
		str = str.replace(de, para);
		pos = str.indexOf(de);
	}
    return (str);
}

function carregando(n){	
	// 'use strict'; 
	// if(n){
	// 	if(!$('#preloader').html()){
	// 		$('body').prepend(`<div id="preloader">
	// 								<div class="lds-ripple">
	// 									<div></div>
	// 									<div></div>
	// 								</div>
	// 						    </div>`);
	// 		jQuery('#preloader').css('display','flex');
	// 		$('#main-wrapper').removeClass('show');
	// 	}		
	// }else{
	// 	jQuery('#preloader').remove();
	// 	$('#main-wrapper').addClass('show');
	// }

	if(n==1){
    	if($("#overlay").css('display') != 'block'){
        	$("#overlay").fadeIn(300);
    	}
    }else{
        $("#overlay").fadeOut(300);
    }
	
}

function closeDialogo(){
	$('.modal').modal('hide');
	$('.modal-backdrop').remove();
	$('body').removeClass('modal-open');
}

function removerDialogo(id){
	$('#'+id).remove();
}

function dialogo(pagina,titulo,callBackClose){

	var nid = Math.floor((Math.random()*200)+1);

	var div = `<div class="modal fade bd-example-modal-lg" tabindex="-1" id="jusModal_${nid}" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel_${nid}"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" id="modal-body_${nid}"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Fechar</button>                                
                            </div>
                        </div>
                    </div>
                </div>`;

	$('#container').append(div);

	$('#jusModal_'+nid+'').modal({		

					keyboard: true

				},'show').on('hidden.bs.modal', function (e) {

					if(callBackClose!='')

						setTimeout(callBackClose,1);

						$('#jusModal_'+nid+'').remove();

				});

	if(titulo!=''){

		$("#myModalLabel_"+nid).html(titulo);

	}else{

		$("#myModalLabel_"+nid).remove();

	}

	//carregando(1);

	jQuery('#modal-body_'+nid+'').html("Aguarde...");	

	jQuery.post(pagina,{tipo_janela:'pop',nid: nid},function(resposta){ 

		jQuery('#modal-body_'+nid+'').html(resposta);

		//carregando(0);

		$('#jusModal_'+nid+'').modal('toggle')		

	});

}

function dialogoHTML(html,titulo, fromPost = false){

	var nid = Math.floor((Math.random()*200)+1);

    var div = `<div class="modal fade bd-example-modal-lg" tabindex="-1" id="jusModal_${nid}" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel_${nid}"></h5>
                                <button type="button" onClick="removerDialogo('jusModal_${nid}')" class="btn-close" data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" id="modal-body_${nid}"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal" onClick="removerDialogo('jusModal_${nid}')">Fechar</button>                                
                            </div>
                        </div>
                    </div>
                </div>`;

	$('#container').append(div);

	$('#jusModal_'+nid+'').modal({
					backdrop: 'static',
					keyboard: false
				},'show');				

	if(titulo!=''){

		$("#myModalLabel_"+nid).html(titulo);

	}else{

		$("#myModalLabel_"+nid).remove();

	}	

	jQuery('#modal-body_'+nid+'').html(html);	

	$('#jusModal_'+nid+'').modal('toggle')	

	return '#jusModal_'+nid;
}


function formataDateBr(data){

	if(data=='' | data==null) return "";

	var UNIX_timestamp = Date.parse(data);

	var a = new Date(UNIX_timestamp);

    var hour = a.getHours();

    var minuto = a.getMinutes();
    if(minuto<10){
    	minuto = '0'+minuto;
    }

    var time = (hour<10?'0'+hour:hour)+':'+(minuto=='0'? '00' : minuto);

	var mes = a.getMonth()+1;

	mes = mes<10 ? '0'+mes : mes;

	var data = (a.getDate()<10?'0'+a.getDate():a.getDate())+'/'+mes+'/'+ a.getFullYear();

    return data+' '+time;

}


function alerta(msn,ok){ //alert-success
	"use strict"
	if(ok){
		swal("Tudo certo!", msn, "success")
	}else{
		sweetAlert("Oops...", msn, "error")
	}
}

function toast(msn, ok){
	if(ok){
		toastr.success(msn, "Atenção!", {
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    positionClass: "toast-top-right",
                    preventDuplicates: !1,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                })
	}else{
		toastr.warning(msn, "Atenção!", {
                    positionClass: "toast-top-right",
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    preventDuplicates: !1,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                })
		 
	}
}


function exibe_select2(id){

	carregando(1);

	

	if(typeof id != 'undefined'){

            $("#"+id).each(function(index, element){

                $('#'+this.id).select2({placeholder:this.title, allowClear: true});

            });

    }else{

           $(".input-select2").each(function(index, element){

               $('#'+this.id).select2({placeholder:this.title, allowClear: true});

           });	

    }

	carregando(0);

}

function confirmar(msn,fn){
	swal({
        title: "Tem certeza?",
        text: msn,
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sim, tenho certeza",
        closeOnConfirm: !1,
        cancelButtonText: "Cancelar",
    }, function () {    	
    }).then((result) => {    	
    	console.log(result)
        if (result.value) {
        	setTimeout(fn,100);	  		    
		}
	});
}


var isMobile = {

    Android: function() {

        return navigator.userAgent.match(/Android/i);

    },

    BlackBerry: function() {

        return navigator.userAgent.match(/BlackBerry/i);

    },

    iOS: function() {

        return navigator.userAgent.match(/iPhone|iPad|iPod/i);

    },

    Opera: function() {

        return navigator.userAgent.match(/Opera Mini/i);

    },

    Windows: function() {

        return navigator.userAgent.match(/IEMobile/i);

    },

    any: function() {

        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());

    }

};

function getDataHora(){
	data = new Date();
	dia = data.getDate();
	if(dia<10)
		dia = '0'+dia;
		
	mes = data.getMonth()+1;
	if(mes<10)
		mes = '0'+mes;
	
	ano = data.getFullYear();
	hora = data.getHours();
	if(hora<10)
		hora = '0'+hora;
	
	minuto = data.getMinutes();
	if(minuto<10)
		minuto = '0'+minuto;
		
	segundos = data.getSeconds();
	if(segundos<10)
		segundos = '0'+segundos;
	
	return ano+'-'+mes+'-'+dia+' '+hora+':'+minuto+':'+segundos;
}

function DataHoraBr(data){	
	var d = data.split(' ');
	data = d[0].split('-');
	d = d[1].split(':');
	return data[2]+'/'+data[1]+'/'+data[0]+' '+d[0]+':'+d[1]+':'+d[2];
}

$(document).on("select2:open", () => {
	document.querySelector(".select2-container--open .select2-search__field").focus()
})