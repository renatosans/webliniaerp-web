jQuery.fn.extend({
		  fixedHeadTable: function(top,capsula,scroll) {
		  	var tr = $("thead",this);
		  	var table = this ;
		   	var firstScroll = true;
		   	var dotopo = 0 ;
		   	var dataTable = [];
		   	var topo_tabela = "";
		   	var clone = null ;
		   	$(window).scroll(function(){
			   	if(firstScroll){
				   	$.each(tr,function(i,v){
						 $.each($("th",v),function(x,y){ 
						 	var width = $(y).outerWidth();
						 	var height = $(y).outerHeight();
						 	dataTable.push({ ele:$(y), width: width+"px" , height:height+"px"});
						 }); 
					});
					$.each(dataTable,function(x,y){
						y.ele.css({width:y.width ,height:y.height });
					});
						
					if(capsula != null){
						var n_start = capsula.indexOf('</');
						var ini_capsula = capsula.substring(0,n_start);
						var fim_capsula = capsula.substring(n_start);
						clone = tr.clone().html();
						topo_tabela = "<div id=\"topo_fixo_tabela\" style=\"display:none;position: fixed;top: "+top+"px;\">"+ini_capsula+"<table class=\""+( table.attr("class") == undefined ? "" : table.attr("class") )+"\" style=\"margin-bottom:0px;padding-bottom:0px;background: #FFF;"+( table.attr("style") == undefined ? "" : table.attr("style") )+"\" ><thead class=\""+(tr.clone().attr("class") == undefined ? "" : tr.clone().attr("class"))+"\" style=\""+( tr.clone().attr("style") == undefined ? "" : tr.clone().attr("style") )+"\" >"+clone+"</thead></table>"+fim_capsula+"</div>";
					}else{
						topo_tabela  = "<table class=\""+( table.attr("class") == undefined ? "" : table.attr("class") )+"\" style=\"position: fixed;top: "+top+"px;background: #FFF;"+( table.attr("style") == undefined ? "" : table.attr("style") )+"\" ><thead class=\""+(tr.clone().attr("class") == undefined ? "" : tr.clone().attr("class"))+"\" style=\""+( tr.clone().attr("style") == undefined ? "" : tr.clone().attr("style") )+"\" >"+clone+"</thead></table>";
					}

					table.before(topo_tabela);

					if(scroll != null){

						if(typeof scroll == "object"){
							$.each(scroll,function(x,y){
								$.each(scroll,function(i,v){
										if(y != v){
											$(y).scroll(function(){
												$(v).scrollLeft($(y).scrollLeft());
											});
											console.log(v+" "+v);
										}
									
								});
							});
						}else{
							$(scroll).scroll(function(){
								var scroll_left = $(this).scrollLeft() ;
								var position_left = scroll_left > 0 ? "-"+scroll_left+"px" : "0px" ;
								$("#topo_fixo_tabela table").css("margin-left",position_left);
							});	
						}
					}

					firstScroll = false;
			   	}
		   		 dotopo = $("#tabela-lancamentos").offset().top - $(this).scrollTop();
		   		 if(dotopo <= top){
		   		 	$("#topo_fixo_tabela").show();
		   		 }else{
		   		 	$("#topo_fixo_tabela").hide();
		   		 }
		  		 
		    });
		  }
		});
