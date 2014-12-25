app.factory("API", ['$http', '$q', function ($http, $q) {
	var serviceBaseUrl = 'api/';
	return {
		token: false,
		user: false,
		login: function(user) {
			var deferred = $q.defer();
			var that = this;
			var promise = this.request('login', user).then(function(data) {
				console.log('this is from API.login method:', data.$token, data.$user);
				if(data.$token && data.$user) {
					that.token = data.$token;
					that.user = data.$user;
					deferred.resolve(data);
				} else {
					deferred.reject(data);
				}
			});
			return deferred.promise;
		},
		request: function(method, params) {
			var deferred = $q.defer();
			$http({
				method: 'POST',
				url: serviceBaseUrl,
				data: {
					method: method,
					params: params
				}
			}).success(function(data) {
				console.info('API[' + method + ']', data);
				if(! data.error ) {
					deferred.resolve(data.response);
				} else {
					deferred.reject(data);
				}
			}).error(function(data, status, headers, config) {
				console.error('API[' + method + ']', data, status, headers, config);
				deferred.reject(data);
			});
			return deferred.promise;
		}
	};
}]);