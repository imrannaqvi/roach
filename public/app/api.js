app.factory('API', [ '$rootScope', '$http', '$q', 'localStorageService', 
function ($rootScope, $http, $q, localStorageService) {
	var serviceBaseUrl = 'api/';
	return {
		token: false,
		user: false,
		spinner: false,
		login: function(user) {
			var deferred = $q.defer();
			var that = this;
			var promise = this.request('login', user).then(function(data) {
				console.log('this is from API.login method:', data.$token, data.$user);
				if(data.$token && data.$user) {
					//save references to logged in user
					that.token = data.$token;
					that.user = data.$user;
					$rootScope.user = data.$user;
					//store token to localstorage
					localStorageService.set('token', that.token);
					//callback
					deferred.resolve(data);
				} else {
					deferred.reject(data);
				}
			});
			return deferred.promise;
		},
		request: function(method, params) {
			var deferred = $q.defer();
			var that = this;
			this.showSpinner();
			//send request
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
				that.hideSpinner();
			}).error(function(data, status, headers, config) {
				console.error('API[' + method + ']', data, status, headers, config);
				deferred.reject(data);
				that.hideSpinner();
			});
			return deferred.promise;
		},
		showSpinner: function() {
			//TODO: <div class="overlay" style="position: absolute; background: rgba(0,0,0,0.25); z-index: 10000;"></div>
			var target = document.body;
			if(! this.spinner) {
				this.spinner = new Spinner({color:'#ccc', lines: 8}).spin(target);
			} else {
				this.spinner.spin(target);
			}
		},
		hideSpinner: function() {
			if( this.spinner) {
				this.spinner.stop();
			}
		}
	};
}]);