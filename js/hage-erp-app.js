var app = angular.module('HageERP', ['filters'], function() {});

// FILTERS
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
	});

// DIRECTIVES

app.directive('barchart', function() {
	return {
		// required to make it work as an element
		restrict: 'E',
		template: '<div></div>',
		replace: true,
		// observe and manipulate the DOM
		link: function(scope, element, attrs) {
			scope.$watch(attrs.data, function(newValue, oldValue) {
				if (newValue.length > 0) {;
					var xkey = scope[attrs.xkey],
						ykeys = scope[attrs.ykeys],
						labels = scope[attrs.labels],
						gridTextColor = attrs.gridtextcolor;

					element.css("height", attrs.height);

					Morris.Bar({
						element: element,
						data: newValue,
						xkey: xkey,
						ykeys: ykeys,
						labels: labels,
						gridTextColor: gridTextColor
					});
				}
			}, true);
		}
	};
});

app.directive('donutchart', function() {
	return {
		// required to make it work as an element
		restrict: 'E',
		template: '<div></div>',
		replace: true,
		// observe and manipulate the DOM
		link: function(scope, element, attrs) {
			scope.$watch(attrs.data, function(newValue, oldValue) {
				if (newValue.length > 0) {
					element.css("height", attrs.height);

					Morris.Donut({
						element: element,
						data: newValue
					});
				}
			}, true);
		}
	};
});
