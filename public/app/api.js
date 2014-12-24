app.factory("API", ['$http', function ($http) {
	var serviceBaseUrl = 'api/';
	return {
		login: function(user, callback) {
			this.request('login', user, callback);
		},
		request: function(method, params, callback){
			$http({
				method: 'POST',
				url: serviceBaseUrl,
				data: {
					method: method,
					params: params
				}
			}).success(function(data) {
				console.info('API[' + method + ']', data);
				if(! data.error && typeof(callback) === typeof(function(){})) {
					callback(data.response);
				}
			}).error(function(data, status, headers, config) {
				console.error('API[' + method + ']', data, status, headers, config);
			});
		}
	};
}]);