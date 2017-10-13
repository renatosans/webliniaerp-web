

	

	app.controller('autoCompeteExample', function($scope, $timeout, $rootScope, $q) {
	
	
		

		var arrayOfStuff = [];
		var arrayOfObjs = [];
		arrayOfStuff.forEach(function(text) {
			arrayOfObjs.push({foo: {bar: text}});
		});

		$scope.searchFunctionStaticData = function (inputText) {
			var arrayOfStuff
			$.ajax({
			 	url: "get_arr.php",
			 	async: false,
			 	success: function(data) {
			 		 arrayOfStuff = data;
			 	},
			 	error: function(error) {
			 		console.log(error);
		 		}
			});
			
			var deferredFn = $q.defer();
			if (!inputText || inputText.length < 1) {
				deferredFn.resolve(arrayOfStuff);
				return deferredFn.promise;
			}

			var regex = new RegExp(inputText, 'i');
			var results = [];
			arrayOfStuff.forEach(function(text) {
				if (regex.test(text)) {results.push(text);}
			});

			deferredFn.resolve(results);
			return deferredFn.promise;
		}

	});

