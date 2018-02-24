var paramModule = ['ui.mask','ui.bootstrap','dialogs','filters','angularTreeview','net.enzey.autocomplete'] ;
if(!(typeof addParamModule == "undefined") && addParamModule.length > 0)
	paramModule = paramModule.concat(addParamModule);
var app = angular.module('HageERP', paramModule , function($httpProvider) {
	// Use x-www-form-urlencoded Content-Type
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

	var param = function(obj) {
		var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

		for(name in obj) {
			value = obj[name];

			if(value instanceof Array) {
				for(i=0; i<value.length; ++i) {
					subValue = value[i];
					fullSubName = name + '[' + i + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += param(innerObj) + '&';
				}
			}
			else if(value instanceof Object) {
				for(subName in value) {
					subValue = value[subName];
					fullSubName = name + '[' + subName + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += param(innerObj) + '&';
				}
			}
			else if(value !== undefined && value !== null)
				query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
		}

		return query.length ? query.substr(0, query.length - 1) : query;
	};

	$httpProvider.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
	}];
 });

angular.module('filters', [])
	.filter('numberFormat', function () {
		return function (number, decimals, dec_point, thousands_sep) {
			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		    var n = !isFinite(+number) ? 0 : +number,
		    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		    s = '',
		    toFixedFix = function(n, prec) {
		      var k = Math.pow(10, prec);
		      return '' + (Math.round(n * k) / k)
		        .toFixed(prec);
		    };
		  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
		   s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		   if (s[0].length > 3) {
		    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		   }
		  if ((s[1] || '')
		    .length < prec) {
		    s[1] = s[1] || '';
		    s[1] += new Array(prec - s[1].length + 1)
		      .join('0');
		  }
		  return s.join(dec);
	    };
	}).filter('Utf8Decode', function () {
		return function (inputFormat) {
			if(inputFormat != undefined)
				return Utf8Decode(inputFormat);
			else
				return null;
		}
	})
	.filter('dateFormat', function () {
		return function (inputFormat,tipo) {
		  	function pad(s) { return (s < 10) ? '0' + s : s; }
		  if (empty(inputFormat)) return "" ;
		  if(inputFormat.length < 6){
		  	return "" ;
		  }else if(inputFormat == '0000-00-00'){
		  	return "" ;
		  }else if(tipo == 'date-m/y'){;
		  	if(inputFormat.length == 6)
		  		return inputFormat.substring(0,2)+'/'+inputFormat.substring(2,6);
		  	else{
		  		var arr = inputFormat.split('-');
		  		return arr[1]+'/'+arr[0];
		  	}
		  }

		  inputFormat = inputFormat.replace(/-/g,"/");
		  var d = new Date(inputFormat);
		  if(tipo == null || tipo == "dateTime"){
		  	var hora = d.getHours() < 10 ? '0'+d.getHours() : d.getHours() ;
		  	var minutos = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes() ;
		  	var segundos = d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds() ;

		  	return pad(d.getDate())+'/'+pad(d.getMonth()+1)+'/'+d.getFullYear()+' '+hora+':'+minutos+':'+segundos;
		  }else if(tipo=="time"){
		  	var hora = d.getHours() < 10 ? '0'+d.getHours() : d.getHours() ;
		  	var minutos = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes() ;
		  	var segundos = d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds() ;
		  	return hora+':'+minutos+':'+segundos;
		  }else if(tipo=='time-HH:mm'){
		  	var hora = d.getHours() < 10 ? '0'+d.getHours() : d.getHours() ;
		  	var minutos = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes() ;
		  	return hora+':'+minutos;
		  }
	      else
	      	return pad(d.getDate())+'/'+pad(d.getMonth()+1)+'/'+d.getFullYear();
	    };
	}).filter('date', function () {
		return function (inputFormat) {
		  	function pad(s) { return (s < 10) ? '0' + s : s; }
		  if (empty(inputFormat)) return "" ;
		  if(inputFormat.length < 6){
		  	return "" ;
		  }

		  inputFormat = inputFormat.replace(/-/g,"/");
		  var d = new Date(inputFormat);
	      return pad(d.getDate())+'/'+pad(d.getMonth()+1)+'/'+d.getFullYear();
	    };
	})
	.filter('maskCpf', function () {
		return function (inputFormat) {
			if(empty(inputFormat) || inputFormat.length != 11 ){
				return inputFormat ;
			}
			var cpf = inputFormat ;
			return cpf.substring(0,3)+"."+cpf.substring(3,6)+"."+cpf.substring(6,9)+"-"+cpf.substring(9,11) ;
	    };
	})
	.filter('maskCnpj', function () {
		return function (inputFormat) {
			if(empty(inputFormat) || inputFormat.length != 14 ){
				return inputFormat ;
			}
			var cnpj = inputFormat ;
			return cnpj.substring(0,2)+"."+cnpj.substring(2,5)+"."+cnpj.substring(5,8)+"/"+cnpj.substring(8,12)+"-"+cnpj.substring(12,14) ;
	    };
	})
	.filter('phoneFormat', function () {
		return function (inputFormat) {
			if(empty(inputFormat)){
				return "" ;
			}
			inputFormat = ""+inputFormat;
			if(empty(inputFormat)){
				return "" ;
			}
			var ddd = inputFormat.substring(0,2);
			var telefone = inputFormat.substring(2,inputFormat.length);
			var part1, part2;

			if(telefone.length == 9) {
				part1 = telefone.substring(0,5);
				part2 = telefone.substring(5,telefone.length);
			} else {
				part1 = telefone.substring(0,4);
				part2 = telefone.substring(4,telefone.length);
			}

			telefone = part1 +"-"+ part2;

			return "("+ ddd +") "+ telefone;
	    };
	})
	.filter('cpfFormat', function () {
		return function (inputFormat,prefix) {
			inputFormat = ""+inputFormat;
			if(inputFormat.length == 11){
				prefix = empty(prefix) ? '' : prefix ;
				if(inputFormat.length != 11 && $.isNumeric(inputFormat)){
					return "" ;
				}
				var cpf = inputFormat.substring(0,3)+'.'+inputFormat.substring(3,6)+'.'+inputFormat.substring(6,9)+'-'+inputFormat.substring(9,11);

				return prefix+cpf ;
			}else{
				return '' ;
			}

	    };
	})
	.filter('cnpjFormat', function () {
		return function (inputFormat,prefix) {
			inputFormat = ""+inputFormat;
			if(inputFormat.length == 14){
				prefix = empty(prefix) ? '' : prefix ;
				var cnpj = inputFormat.substring(0,2)+'.'+inputFormat.substring(2,5)+'.'+inputFormat.substring(5,8)+'/'+inputFormat.substring(8,12)+'-'+inputFormat.substring(12,14);

				return prefix+cnpj ;
			}else{
				return '' ;
			}

	    };
	})
	.directive('thousandsFormatter', function ($filter) {
	    return {
	        require: 'ngModel',
	            link: function (scope, element, attrs, ctrl) {
	            precision = !empty(attrs.precision) && $.isNumeric(attrs.precision)? Number(attrs.precision) : 2 ; 
	            ctrl.$formatters.push(function (data) {
	            	if(data != null && data != undefined){
	            		 precision = !empty(attrs.precision) && $.isNumeric(attrs.precision)? Number(attrs.precision) : 2 ; 
		                var formatted = $filter('numberFormat')(data,precision,',','.');
		                $(element).val(formatted);
		                var emptyDecimal = ""+$filter('numberFormat')(0,precision,',','.') ;
		                if(formatted != emptyDecimal )
		                	return formatted;
		                else{
		                	ctrl.$setViewValue('0');
		                	ctrl.$render();
		                	return emptyDecimal;
		                }
	            	}else
	            		return '';
	            });

	            element.bind('focusout', function (event) {
	            	var emptyDecimal = ""+$filter('numberFormat')(0,precision,',','.') ;
		       		if(element.val() == emptyDecimal)
		       			element.val('');
	            });

	            element.bind('focusin', function (event) {
	            	var emptyDecimal = ""+$filter('numberFormat')(0,precision,',','.') ;
		       		if(element.val() == '')
		       			element.val(emptyDecimal);
	            });

	            ctrl.$parsers.push(function (data) {
	            	precision = !empty(attrs.precision) && $.isNumeric(attrs.precision)? Number(attrs.precision) : 2 ; 
	                var plainNumber = data.replace(/[^\d|\-+|\+]/g, '');
	                var length = plainNumber.length;
	                var intValue = plainNumber.substring(0,length-precision);
	                var decimalValue = plainNumber.substring(length-precision,length)
	                var plainNumberWithDecimal = intValue + '.' + decimalValue;
	                //convert data from view format to model format
	                var formatted = $filter('numberFormat')(plainNumberWithDecimal,precision,',','.');
	                element.val(formatted);

	                return isNaN(Number(plainNumberWithDecimal)) ? 0 : Number(plainNumberWithDecimal);
	            });
	        }
	    };
	})
	.directive('maskMoeda', function ($filter) {
	    var precision = 2;
	    return {
	        require: 'ngModel',
	              link: function (scope, element, attrs, ctrl) {

	            ctrl.$formatters.push(function (data) {
	            	if(data != null && data != undefined){
		                var formatted = $filter('numberFormat')(data,2,',','.');
		                $(element).val(formatted);
		                if(formatted != '0,00' )
		                	return formatted;
		                else{
		                	ctrl.$setViewValue('0');
		                	ctrl.$render();
		                	return '0,00';
		                }
	            	}else
	            		return '';
	            });

	            /*element.bind('focusout', function (event) {
		       		if(element.val() == '0,00')
		       			element.val('');
	            });*/

	            element.bind('focusin', function (event) {
		       		if(Number(ctrl.viewValue) == 0)
		       			element.val('0,00');
	            });

	            ctrl.$parsers.push(function (data) {
	            	if(data == "")
	            		return null ;
	                var plainNumber = data.replace(/[^\d|\-+|\+]/g, '');
	                if(Number(plainNumber) == 0){
	                	element.val('0.00');
	                	return 0 ;
	                }
	                var length = plainNumber.length;
	                var intValue = plainNumber.substring(0,length-precision);
	                var decimalValue = plainNumber.substring(length-precision,length)
	                var plainNumberWithDecimal = intValue + '.' + decimalValue;
	                //convert data from view format to model format
	                var formatted = $filter('numberFormat')(plainNumberWithDecimal,2,',','.');
	                element.val(formatted);

	                return isNaN(Number(plainNumberWithDecimal)) ? 0 : Number(plainNumberWithDecimal);
	            });
	        }
	    };
	})
	.directive('datePicker', function ($filter) {
	    return {
	        require: 'ngModel',
	       	  scope: {
	            options: '=',
	           	 model: '=ngModel'
	       		},
	            link: function (scope, element, attrs, ctrl) {
	            $(element).datepicker(
	            	{	
	            		format: "dd/mm/yyyy",
				        language: "pt-BR",
				        clearBtn:true
				    }
	            ).on('changeDate', function(e) {
	            	ctrl.$viewValue = e.date;
               		ctrl.$commitViewValue();
               		ctrl.$setViewValue(e.date);
               		ctrl.render ;
               		$(this).datepicker('hide');
			    });
			    $(element).parent().children('span').on("click", function(){ $(element).trigger("focus"); })
	            ctrl.$parsers.push(function (data) {
	            	var data = $(element).val();
	            	if(data.length == 10)
	            		return formatDate(data) ;
	            });
	            if(!empty(attrs.stardate))
	           	 $(element).datepicker('setDate', attrs.stardate);

	           	 scope.$watch('model', function(newValue, oldValue) {
	           	 	if(!empty(newValue))
	           	 		$(element).datepicker('setDate', formatDateBR(newValue));
           		 }, true);
	        }
	    };
	}).directive('tooltip', function ($filter) {
	    return {
	            link: function (scope, element, attrs, ctrl) {
	            if(empty(attrs.tooltip))
	           		$(element).tooltip()
	           	else if(attrs.tooltip=='show' || attrs.tooltip == 'hide')
	           		$(element).tooltip(attrs.tooltip);
	        }
	    };
	}).directive('controllTooltip', function ($filter) {
	    return {
	       	 	scope: {
		            options: '=',
		           	 controllTooltip: '='
		       	},
	            link: function (scope, element, attrs, ctrl) {
	            if(typeof scope.controll == 'object' &&  scope.controll.init === true)
	           		$(element).tooltip((attrs.controllTooltip=='show' ? 'show' : null ));

	           	scope.$watch('controllTooltip', function(newValue, oldValue) {
	           	 	if(typeof newValue == 'object' && newValue.init === true){
	           	 		$(element).tooltip('destroy');
	           			$(element).tooltip( {
	           				placement : newValue.placement,
							title : newValue.title,
							trigger : newValue.trigger,
							container: ( empty(newValue.container) ? null : newValue.container )
	           			} );
	           			if(newValue.show === true){
	           				$(element).trigger("focus");
	           			}
	           	 	}else{
	           	 		$(element).tooltip('destroy');
	           	 	}
	       		}, true);
	        }
	    };
	}).filter("emptyToEnd", function () {
	    return function (array, key) {
	        if(!angular.isArray(array)) return;
	        var present = array.filter(function (item) {
	            return item[key];
	        });
	        var empty = array.filter(function (item) {
	            return !item[key]
	        });
	        return present.concat(empty);
	    };
	}).
	directive('controlSizeString', function ($filter) {
	    return {
	    	link:function(scope,element,attrs,ctrl){
	    		var size = Number(attrs.size);
	    		if(attrs.content.length > size){
	    			element.html( attrs.content.substring(0,size)+' <a style="cursor:pointer;color:black" class="controlSizeString-active">...</a>' );
		    		var trigger = empty(attrs.trigger) ? 'hover' : attrs.trigger ;
		    		var container = empty(attrs.container) ? 'body' : attrs.container;
		    		var title     = empty(attrs.title) ? false : attrs.title ;
		    		var placement = empty(attrs.placement) ? 'top' : attrs.placement;

		    		var config = {
						title: ( title==false ? '' : title ) ,
		                placement: placement ,
		                content: attrs.content ,
		                html: true,
		                container: container,
		                trigger  :trigger
		            }
		            if(title == false)
		           	 config.template =  '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
	    			$('.controlSizeString-active',element).popover(config).popover();
	    		}else{
	    			element.html((attrs.content))
	    		}
	    	}
	    }
	}).directive('initPopover', function ($compile,$filter) {
		$(document).on('click',':not(.popover > *)' , function(event){
		   var close = true ;
		   if($(this).hasClass('popover') || $(this).parents('.popover').hasClass('popover')){
		   		close = false ;
		   }
		   var attrInitPopOver = $(this).attr('popover-control-angular') ;
		   var parentAttrInitPopOver = $(this).parents('[popover-control-angular]').attr('popover-control-angular')
		   if ( (typeof attrInitPopOver !== typeof undefined && attrInitPopOver !== false) || (typeof parentAttrInitPopOver !== typeof undefined && parentAttrInitPopOver !== false) ) {
		   		close = false ;
		   }
		   if(close) $('[popover-control-angular]').popover('hide')
		   else{
		   	event.stopPropagation()
		   }
		});
	    return {
	    	link:function(scope,element,attrs,ctrl){
	    			$(element).attr('popover-control-angular','');
		    		var trigger = empty(attrs.trigger) ? 'click' : attrs.trigger ;
		    		var container = empty(attrs.container) ? 'body' : attrs.container;
		    		var title     = empty(attrs.title) ? false : attrs.title ;
		    		var placement = empty(attrs.placement) ? 'top' : attrs.placement;

		    		var config = {
						title: ( title==false ? '' : title ) ,
		                placement: placement ,
		                content:  $compile($(attrs.content))(scope) ,
		                html: true,
		                container: container,
		                trigger  :trigger
		            }
		            if(title == false)
		           	 config.template =  '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
	    			$(element).popover(config).popover();
	    		
	    	}
	    }
	}).directive('preLoadImg', function ($compile,$filter) {
	    return {
	    	scope: {
	            options: '=',
	           	 preLoadImg: '='
	       		},
	    	link:function(scope,element,attrs,ctrl){
			    if(empty(!attrs.preLoadImg)){
			    	 scope.$watch('preLoadImg', function(newValue, oldValue) {
		                	$(element).attr('src',attrs.imgpreload);
							$(element).after('<img style="display:none" class="pre-load-img-cache" src="'+newValue+'"/>')
							$(element).next('.pre-load-img-cache').on('load', function() {
								$(element).attr('src',newValue);
								$(element).next('.pre-load-img-cache').remove();
							})
						    .on('error', function() {
						    	$(element).attr('src',attrs.notimg);
						    })
		            })
			    }else{
				    $(element).attr('src',attrs.imgpreload);
					$(element).after('<img style="display:none" class="pre-load-img-cache" src="'+attrs.datasrc+'"/>')
					$(element).next('.pre-load-img-cache').on('load', function() {
						$(element).attr('src',attrs.datasrc);
						$(element).next('.pre-load-img-cache').remove();
					})
				    .on('error', function() {
				    	$(element).attr('src',attrs.notimg);
				    })
			    }
	    	}
	    }
	}).directive('uploadFile', function ($compile,$filter) {
		var controle = 0 ;
	    return {
	    	link:function(scope,element,attrs,ctrl){
		    	element.bind('click', function (event) {
		    		var idModalUploadFile = $(element).attr('datamodaluploadfile');
		    			idModalUploadFile = 'modal-upload';
		    			$('#'+idModalUploadFile).remove();
		    			var nameInput = !empty($(element).attr('datanameinput')) ? $(element).attr('datanameinput') : 'image_upload_file' ;
			       		var htmlModal = 
				       		'<div class="modal fade modal-upload" id="'+idModalUploadFile+'" style="display:none">'+
								'<div class="modal-dialog error modal-md">'+
									'<div class="modal-content">'+
						  				'<div class="modal-header">'+
						  					'<h4>Upload Imagem</h4>'+
						  				'</div>'+
									    '<div class="modal-body">'+
									    	'<div class="row">'+
									    		'<div class="col-sm-12">'+
									    			'<!-- the avatar markup -->'+
												    '<div id="kv-avatar-errors-" class="center-block" style="width:800px;display:none"></div>'+
												    '<form class="text-center" action="/avatar_upload.php" method="post" enctype="multipart/form-data">'+
												        '<div class="kv-avatar center-block" style="width:200px">'+
												            '<input id="avatar-2" name="'+nameInput+'" type="file" class="file-loading">'+
												        '</div>'+
												    '</form>'+
												'</div>'+
									    	'</div>'+
									    '</div>'+
									    '<div class="modal-footer">'+
									    	'<button type="button" data-loading-text=" Aguarde..."'+
									    		'class="btn btn-md btn-default hide-modal-upload-file">'+
									    		 'OK'+
									    	'</button>'+
								   		'</div>'+
								  	'</div>'+
								'</div>'+
							'</div>' ;
						$('body').append(htmlModal);
						$('#'+idModalUploadFile+' .hide-modal-upload-file').click(function(){
							$(this).parents('.modal').modal('hide');
						});
						
						$('#'+idModalUploadFile).modal('show');
						var preview = '<img style="cursor:pointer" src="img/sem-imagem-produto.jpg" alt="" style="width:160px"><h6 class="text-muted">Selecionar</h6>';
						if(!empty(attrs.defaultPreviewContent))
							preview = '<img style="cursor:pointer" src="'+attrs.defaultPreviewContent+'" alt="" style="width:160px"><h6 class="text-muted">Selecionar</h6>';
						var btnCust = '';
						var config = {
						    language:'pt-BR',
						    overwriteInitial: true,
						    maxFileSize: 1500,
						    showClose: false,
						    showCaption: false,
						    showBrowse: false,
						    browseOnZoneClick: true,
						    removeLabel: '',
						    removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
						    removeTitle: 'Cancelar Alterações',
						    elErrorContainer: '#kv-avatar-errors-2',
						    msgErrorClass: 'alert alert-block alert-danger',
						    defaultPreviewContent:  preview ,
						    layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
						    allowedFileExtensions: ["jpg", "png", "gif"],
						    uploadTitle :'salvar imagem',
						    uploadIcon: '<i class="glyphicon glyphicon-upload"></i>',
						    uploadUrl: ( !empty(attrs.uploadurl) ? attrs.uploadurl : "file-upload-single.php" ), // server upload action
						    uploadAsync: true,
						    maxFileCount: 1,
						    initialPreviewAsData: true,
						    previewZoomSettings:{
						        image: {'max-height': "480px",'height':'auto','width':'auto'},
						    },
						    deleteUrl: ( !empty(attrs.deleteurl) ? attrs.deleteurl : "delete_upload_file.php" ),
						    uploadExtraData : {nome:'jheizer'}

						}

						 if(!empty(attrs.uploadextradata)){
					    	var extra = parseJSON(attrs.uploadextradata);
					    	if(typeof(extra) == 'object')
					    		config.uploadExtraData = extra ;
						  }

						if(!empty(attrs.dataimg)){
							 config.initialPreview=[
						        attrs.dataimg
						    ];
						     config.initialPreviewConfig = [
						        {caption: "", width: "160px", key: null},
						    ];
						    if(!empty(attrs.deleteextradata)){
						    	var extra = parseJSON(attrs.deleteextradata);
						    	if(typeof(extra) == 'object')
						    		config.initialPreviewConfig[0].extra = extra ;
						    }
						}
						$("#avatar-2").fileinput(config);
						$('#avatar-2').on('filezoomshow', function(event, params) {
							 params.modal.appendTo('body');
						});
						$('#avatar-2').on('filedeleteerror', function(event, data, msg) {
						    console.log('File delete error');
						});
						$('#avatar-2').on('fileuploaderror', function(event, data, msg) {
						    var form = data.form, files = data.files, extra = data.extra,
						        response = data.response, reader = data.reader;
						    console.log('File upload error');
						});

	            });
	    		
	    	}
	    }
	}).directive('popover2', function ($compile,$filter) {
		$(document).on('click',':not(.popover > *)' , function(event){
		   var close = true ;
		   if($(this).hasClass('popover') || $(this).parents('.popover').hasClass('popover')){
		   		close = false ;
		   }
		   var attrInitPopOver = $(this).attr('popover-control-angular') ;
		   var parentAttrInitPopOver = $(this).parents('[popover-control-angular]').attr('popover-control-angular')
		   if ( (typeof attrInitPopOver !== typeof undefined && attrInitPopOver !== false) || (typeof parentAttrInitPopOver !== typeof undefined && parentAttrInitPopOver !== false) ) {
		   		close = false ;
		   }
		   if(close) $('[popover-control-angular]').popover('hide')
		   else{
		   	event.stopPropagation()
		   }
		});
	    return {
	    	scope: {
	            options: '=',
	           	model: '=',
	           	func: '='
	       	},
	    	link:function(scope,element,attrs,ctrl){
	    			scope.$watch("model", function(currentValue, previousValue) {
	    				$(element).attr('popover-control-angular','');
	    				$(element).popover('destroy');
	    				var trigger = empty(attrs.trigger) ? 'click' : attrs.trigger ;
			    		var container = empty(attrs.container) ? 'body' : attrs.container;
			    		var title     = empty(attrs.title) ? false : attrs.title ;
			    		var placement = empty(attrs.placement) ? 'top' : attrs.placement;

			    		var config = {
							title: ( title==false ? '' : title ) ,
			                placement: placement ,
			                content:  $compile($( unescape(attrs.content) ))(scope) ,
			                html: true,
			                container: container,
			                trigger  :trigger
			            }
			            if(title == false)
			           	 config.template =  '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
		    			$(element).popover(config).popover();
		    			$(element).on('show.bs.popover', function () {
						   $('[popover-control-angular]').not(element).popover('hide');
						})
		            });
	    	}
	    }
	})

function Ctrl($scope) {
    $scope.currencyVal;
}

String.prototype.splice = function(idx, rem, s) {
    return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
};

(function(l) {
    l.module("angularTreeview", []).directive("treeModel", function($compile) {
        return {
            restrict: "A",
            link: function(a, g, c) {
                var e = c.treeModel,
                    h = c.nodeLabel || "label",
                    d = c.nodeChildren || "children",
                    k = '<ul><li data-ng-repeat="node in ' + e + '"><i class="collapsed" data-ng-show="node.' + d + '.length && node.collapsed" data-ng-click="selectNodeHead(node, $event)"></i><i class="expanded" data-ng-show="node.' + d + '.length && !node.collapsed" data-ng-click="selectNodeHead(node, $event)"></i><i class="normal" data-ng-hide="node.' +
                    d + '.length"></i> <span data-ng-class="node.selected" data-ng-click="selectNodeLabel(node, $event)">{{node.' + h + '}}</span><div data-ng-hide="node.collapsed" data-tree-model="node.' + d + '" data-node-id=' + (c.nodeId || "id") + " data-node-label=" + h + " data-node-children=" + d + "></div></li></ul>";
                e && e.length && (c.angularTreeview ? (a.$watch(e, function(m, b) {
                    g.empty().html($compile(k)(a))
                }, !1), a.selectNodeHead = a.selectNodeHead || function(a, b) {
                    b.stopPropagation && b.stopPropagation();
                    b.preventDefault && b.preventDefault();
                    b.cancelBubble = !0;
                    b.returnValue = !1;
                    a.collapsed = !a.collapsed
                }, a.selectNodeLabel = a.selectNodeLabel || function(c, b) {
                    b.stopPropagation && b.stopPropagation();
                    b.preventDefault && b.preventDefault();
                    b.cancelBubble = !0;
                    b.returnValue = !1;
                    a.currentNode && a.currentNode.selected && (a.currentNode.selected = void 0);
                    c.selected = "selected";
                    a.currentNode = c
                }) : g.html($compile(k)(a)))
            }
        }
    })
})(angular);

app.directive('syncFocusWith', function($timeout, $rootScope) {
    return {
        restrict: 'A',
        scope: {
            focusValue: "=syncFocusWith"
        },
        link: function($scope, $element, attrs) {
            $scope.$watch("focusValue", function(currentValue, previousValue) {
                if (currentValue === true && !previousValue) {
                    $element[0].focus();
                } else if (currentValue === false && previousValue) {
                    $element[0].blur();
                }
            })
        }
    }
});

app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});

app.directive('somenteNumeros', function () {
    return function (scope, element, attrs) {
        element.bind("keypress", function (event) {
           var tecla=(window.event)?event.keyCode:e.which;
		    if((tecla>47 && tecla<58)) return true;
		    else{
		        if (tecla==8 || tecla==0) return true;
		    else  return false;
		    }
        });
    };
});

app.directive('keyPressFalseTagsInputCategorias', function () {
    return function (scope, element, attrs) {
        element.bind("keypress", function (event) {
          scope.showCategorias();
		  return false;
		    
        });
    };
});

app.directive('keyPressFalseTagsInputFabricantes', function () {
    return function (scope, element, attrs) {
        element.bind("keypress", function (event) {
          scope.modalFabricantes(0,10);
		  return false;
		    
        });
    };
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});

app.directive('integracao', function($timeout) {
  	return {
		link: function(scope, element, attrs) {
		    if (scope.$last){
                scope.integracao();
		    }
		  }
 	}
})

app.controller('MasterController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged = UserService.getUserLogado();
	ng.meusEmpreendimentos = UserService.getMeusEmpreendimentos(ng.userLogged.id);
	
	ng.logout = function() {
		UserService.clearSessionData();
		window.location.href = "logout.php";
	}

	ng.changeEmpreendimento = function(empSelected) {
		var url = "util/login/login.php?";
			url += "id_empreendimento=" 	+ empSelected.id;
			url += "&nome_empreendimento=" 	+ empSelected.nome_empreendimento;
			url += "&nickname=" 			+ empSelected.nickname;
			url += "&nme_logo=" 			+ empSelected.nme_logo;
			url += "&id_perfil=" 			+ ng.userLogged.id_perfil;
			url += "&id=" 					+ ng.userLogged.id;
			url += "&end_email=" 			+ ng.userLogged.end_email;
			url += "&nme_usuario=" 			+ ng.userLogged.nme_usuario;

		aj.get(url)
			.success(function(data, status, headers, config) {
				UserService.clearSessionData();
				window.location.reload();
			})
			.error(function(data, status, headers, config) {
				alert('Desculpe, ocorreu um erro inesperado !!!');
			});

		return false;
	};
	
	ng.openModalMeusEmpreendimentos = function(){
		$(".modal.meus-empreendimentos").modal("show");
	}
});

app.controller('AlertasController', function($scope, $http, $window, UserService, ConfigService) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.itensVencidos = [];
	ng.itensVencer = [];
	ng.itensEstoqueMinimo = [];
	ng.paginacao = {};
	ng.alertas = [];
	ng.count = {
		orcamentos : 0
	};
	ng.config = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	
	if(!empty(ng.config.dta_validade_certificado_digital) 
		&& !empty(ng.config.qtd_dias_antecedencia_alerta_vencimento_certificado_digital)) {
		var today = moment();
		var dta_validade_certificado_digital = moment(ng.config.dta_validade_certificado_digital, 'DD/MM/YYYY');
		var qtd_dias_vencer_certificado_digital = dta_validade_certificado_digital.diff(today, 'days');
		
		if(qtd_dias_vencer_certificado_digital <= ng.config.qtd_dias_antecedencia_alerta_vencimento_certificado_digital) {
			if(today <= dta_validade_certificado_digital){
				ng.alertas.push({
					type: 'warning',
					message: "Seu certificado digital expira em "+ qtd_dias_vencer_certificado_digital +" dia(s)!"
				});
			} else if(today >= dta_validade_certificado_digital){
				ng.alertas.push({
					type: 'danger',
					message: "Seu certificado digital expirou há "+ - qtd_dias_vencer_certificado_digital +" dia(s)!"
				});
			}
		}
	}

	var mojsShow = function (promise) {
		var n = this
		var Timeline = new mojs.Timeline()
		var body = new mojs.Html({
			el: n.barDom,
			x: {
				500: 0, 
				delay: 0, 
				duration: 500, 
				easing: 'elastic.out'
			},
			isForce3d: true,
			onComplete: function () {
				promise(function (resolve) {
					resolve()
				})
			}
		})

		var parent = new mojs.Shape({
			parent: n.barDom,
			width: 200,
			height: n.barDom.getBoundingClientRect().height,
			radius: 0,
			x: {
				[150]: -150
			},
			duration: 1.2 * 500,
			isShowStart: true
		})

		n.barDom.style['overflow'] = 'visible'
		parent.el.style['overflow'] = 'hidden'

		var burst = new mojs.Burst({
			parent: parent.el,
			count: 10,
			top: n.barDom.getBoundingClientRect().height + 75,
			degree: 90,
			radius: 75,
			angle: {[-90]: 40},
			children: {
				fill: '#EBD761',
				delay: 'stagger(500, -50)',
				radius: 'rand(8, 25)',
				direction: -1,
				isSwirl: true
			}
		})

		var fadeBurst = new mojs.Burst({
			parent: parent.el,
			count: 2,
			degree: 0,
			angle: 75,
			radius: {0: 100},
			top: '90%',
			children: {
				fill: '#EBD761',
				pathScale: [.65, 1],
				radius: 'rand(12, 15)',
				direction: [-1, 1],
				delay: .8 * 500,
				isSwirl: true
			}
		})

		Timeline.add(body, burst, fadeBurst, parent)
		Timeline.play()
	}

	if((!empty(ng.config.flg_pagamento_pendente)) && (parseInt(ng.config.flg_pagamento_pendente, 10) === 1)) {
		setTimeout(function() {
			new Noty({
				type: 'warning',	
				text: '<h4>Olá, tudo bom?</h4><br/>Não conseguimos identificar o pagamento da fatura esse mês, caso já tenha realizado você pode nos enviar o comprovante de pagamento pelo WhatsApp, <a target="_blank" href="https://api.whatsapp.com/send?phone=5511968697611&text=Ol%C3%A1%20gostaria%20de%20comunicar%20o%20pagamento%20da%20fatura%20do%20empreendimento%20'+ ng.userLogged.nome_empreendimento +'">clicando aqui</a> ou pelo e-mail financeiro@webliniaerp.com.br.<br/><br/>Ah, já ia me esquecendo: caso não identificarmos o pagamento dentro de 5 dias após o vencimento, o acesso ao sistema poderá ser bloqueado.',
				progressBar: false,
				timeout: false,
				theme: 'mint',
				animation: {
					open: 'animated bounceInUp' /*mojsShow*/
				}
			}).show();
		}, 2000);
	}

	ng.loadCountOrcamentos = function(first_date,last_date) {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_orcamentos/dashboard?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.count.orcamentos = data.total_orcamentos  ;

				if(ng.count.orcamentos > 0) {
					ng.alertas.push({
						type: 'danger',
						message: "Você tem "+ ng.count.orcamentos +" orçamentos para validar!",
						link: "vendas.php?status=orcamento"
					});
				}
			})
			.error(function(data, status, headers, config) {
				ng.count.orcamentos  = 0 ;
			});
	}

	ng.loadCountOrcamentosByEmpreendimento = function() {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_orcamentos_by_empreendimento/dashboard/"+ng.userLogged.id_empreendimento+"/"+ng.userLogged.id)
			.success(function(data, status, headers, config) {
				var orcamentos = data  ;
				$.each(orcamentos,function(i,v){
					ng.alertas.push({
						type: 'danger',
						message: "Você tem "+ v.total_orcamentos +" orçamentos para validar no emp. "+v.nome_empreendimento+"!"
					});
				});
			})
			.error(function(data, status, headers, config) {
				if(status != 404)
					console.log('erro ao buscar url '+baseUrlApi()+"count_orcamentos_by_empreendimento/dashboard/"+ng.userLogged.id_empreendimento+"/"+ng.userLogged.id);
			});
	}

	ng.loadProdutosVencidos = function() {
		aj.get(baseUrlApi()+"produtos/vencidos/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.itensVencidos = [];

				$.each(data.produtos, function(i, produto) {
					ng.itensVencidos.push(produto);
				});

				if(ng.itensVencidos.length > 0) {
					var msg;

					if(ng.itensVencidos.length == 1) {
						msg = '1 produto venceu há '+ ng.itensVencidos[0].qtd_dias_vencido +' dias';
					}
					else {
						var qtd = 0;

						$.each(ng.itensVencidos, function(i, item) {
							qtd += item.qtd_item;
						});

						msg = qtd + ' produtos vencidos no estoque';
					}

					ng.alertas.push({
						type: 'danger',
						message: msg,
						link: "rel_produtos_vencidos.php"
					});
				}
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.loadProdutosVencer = function() {
		aj.get(baseUrlApi()+"produtos/vencer/"+ ng.userLogged.id_empreendimento +"/30")
			.success(function(data, status, headers, config) {
				ng.itensVencer = Object.keys(data.produtos).map(function(key){return data.produtos[key]});
				//ng.itensVencer = data.produtos;

				if(ng.itensVencer.length > 0) {
					var qtd = 0;

					$.each(ng.itensVencer, function(i, item) {
						qtd += item.qtd_item;
					});

					var msg = qtd +' prod. vence nos próx. 30 dias';;

					ng.alertas.push({
						type: 'warning',
						message: msg,
						link: "rel_produtos_vencer.php"
					});
				}
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.loadProdutosEstoqueMinimo = function() {
		aj.get(baseUrlApi()+"produtos/estoque/minimo?id_empreendimento="+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.itensEstoqueMinimo = data;

				if(ng.itensEstoqueMinimo.length > 0) {
					var msg = ng.itensEstoqueMinimo.length +' prod. abaixo do estoque mínimo';;

					ng.alertas.push({
						type: 'warning',
						message: msg,
						link: "rel_produtos_estoque_minimo.php"
					});
				}
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.loadPedidosTransferenciaRecebido = function() {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"transferencias/estoque/?id_empreendimento_transferencia="+ng.userLogged.id_empreendimento+"&id_status_transferencia=1")
			.success(function(data, status, headers, config) {
				if(data.length > 0) {
					ng.alertas.push({
						type: 'warning',
						message: "Você tem "+ data.length +" "+ (data.length == 1 ? 'pedido' : 'pedidos' ) + " de transferência de produtos",
						link: "pedido_transferencia_recebido.php"
					});
				}
			})
			.error(function(data, status, headers, config) {
				ng.count.orcamentos  = 0 ;
			});
	}

	ng.loadPedidosTransferenciaTransporte = function() {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"transferencias/estoque/?id_empreendimento_pedido="+ng.userLogged.id_empreendimento+"&id_status_transferencia=2")
			.success(function(data, status, headers, config) {
				if(data.length > 0) {
					ng.alertas.push({
						type: 'warning',
						message: "Você tem "+ data.length +" "+ (data.length == 1 ? 'pedido' : 'pedidos' ) + " de transferência de produtos em transporte",
						link: "pedido_transferencia.php"
					});
				}
			})
			.error(function(data, status, headers, config) {
				ng.count.orcamentos  = 0 ;
			});
	}

	ng.loadCountOrcamentos(formatDate(getFirstDateOfMonthString()), formatDate(getLastDateOfMonthString()));
	ng.loadCountOrcamentosByEmpreendimento();
	ng.loadProdutosVencidos();
	ng.loadProdutosVencer();
	ng.loadProdutosEstoqueMinimo();
	ng.loadPedidosTransferenciaRecebido();
	ng.loadPedidosTransferenciaTransporte();
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});


/*app.factory('httpRequestInterceptor',function () {
  var user = angular.fromJson(sessionStorage.user);
  console.log(user);
  return {
    request: function (config) {
      //config.headers['Authorization'] = 'Basic d2VudHdvcnRobWFuOkNoYW5nZV9tZQ==';
      config.headers['Empreendimento'] = user.id_empreendimento;
      return config;
    }
  };
});

app.config(function ($httpProvider) {
  $httpProvider.interceptors.push('httpRequestInterceptor');
});*/
