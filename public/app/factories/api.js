'use strict';

app.factory('API', [ '$rootScope', '$http', '$q', 'ACL','localStorageService', 
function ($rootScope, $http, $q, ACL, localStorageService) {
	var serviceBaseUrl = 'api/';
	return {
		token: false,
		token_key: 'token',
		user: false,
		spinner: false,
		login: function(user) {
			var deferred = $q.defer();
			var that = this;
			var promise = this.request('login', user, true).then(function(data) {
				console.log('this is from API.login method:', data.$token, data.$user);
				if(data.$token && data.$user && data.$acl) {
					deferred.resolve(data);
				} else {
					deferred.reject(data);
				}
			});
			return deferred.promise;
		},
		getSession: function() {
			var that = this;
			var token = localStorageService.get(this.token_key);
			var deferred = $q.defer();
			if(! token) {
				deferred.reject('token-not-found');
			} else {
				this.token = token;
				this.request('session').then(function(data) {
					console.log('this is from API.getSession method:', data);
					if(data.$user && data.$acl) {
						deferred.resolve(data);
					}
				});
			}
			return deferred.promise;
		},
		request: function(method, params, skip_token) {
			var deferred = $q.defer();
			var that = this;
			this.showSpinner();
			//headers
			var headers = {};
			if(! skip_token) {
				headers['Authorization'] = 'Token ' + this.token;
			}
			//send request
			$http({
				method: 'POST',
				url: serviceBaseUrl,
				data: {
					method: method,
					params: params
				},
				headers: headers
			}).success(function(data) {
				console.info('API[' + method + ']', data);
				if(! data.error ) {
					that.internalLogic(data.response);
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
		internalLogic: function(data) {
			for(var x in data) {
				switch(x) {
					case '$user':
						this.user = $rootScope.user = data[x];
					break;
					case '$acl':
						ACL.set(data[x]);
					break;
					case '$token':
						this.token = data[x];
						localStorageService.set(this.token_key, this.token);
					break;
				}
			}
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