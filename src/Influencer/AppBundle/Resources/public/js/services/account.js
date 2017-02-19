'use strict';

var app = angular.module('app')

app.factory('Account', ['$rootScope', '$http', 'localStorageService', function($rootScope, $http, localStorageService) {
	return {
		getProfile: function() {
			return $http.get(Routing.generate('inf_me'));
		},
		updateProfile: function(profileData) {
			return $http.put(Routing.generate('inf_me'), profileData);
		}
	};
}]);

app.factory('LoadData', ['$q', '$http', function($q, $http) {
	return {
		get: function(user) {
			var deferred = $q.defer();
			try {
				var url = Routing.generate('inf_load_user_home', {'role': user.role});
				$http.get(url).then(function(resp) {
					deferred.resolve(resp.data);
				});
			} catch(e) {
				deferred.reject('Route not exists');
			}
			return deferred.promise;
		}
	};
}]);

app.factory('LoadStatistics', ['$q', '$http', function($q, $http) {
	return {
		forAllNetworks: function() {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_get_user_statistics', {'network': 'all'}),
				method: 'GET'
			}).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		}
	};
}]);

app.factory('UserEdit', ['$q', '$http', function($q, $http) {
	return {
		getInCurrentState: function(id) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_admin_data_user_edit', {id: id}),
				method: 'GET'
			}).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		},
		updateSelectedUser: function(user) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_admin_data_user_update', {'id': user.id}),
				method: 'POST',
				data: user
			}).then(function(resp) {
				deferred.resolve(resp.data);
				$scope.selected = resp.data;
			});
			return deferred.promise;
		},
		createNewUser: function(user) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_admin_data_user_create'),
				method: 'POST',
				data: user
			}).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		}
	};
}]);

app.factory('Users', ['$q', '$http', function($q, $http) {
	return {
		loadAll: function(field, filter) {
			var deferred = $q.defer();
			var params = {};
			if (field !== undefined && filter !== undefined && filter !== 'all') {
				params = {field: field, filter: filter};
			}
			$http({
				url: Routing.generate('inf_admin_data_users_list'),
				method: 'POST',
				data: params
			}).then(function(resp) {
				deferred.resolve(resp.data);
			})
			return deferred.promise;
		},
		disable: function(user) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_user_update_field'),
				method: 'POST',
				data: {user: user.id, field: 'enabled', value: (user.enabled == 1?0:1)}
			}).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		},
		deleteUser: function(user) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_admin_data_user_delete'),
				method: 'POST',
				data: {user: user.id}
			}).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		}
	};
}]);

app.factory('CheckForUnique', ['$q', '$http', function($q, $http) {
	return {
		checkForUsername: function(username) {
			return $http({
    			url: Routing.generate('inf_check_for_username'),
    			method: 'POST',
    			data: {
    				'username': username
    			}
    		});
		},
		checkForEmail: function(email) {
			return $http({
    			url: Routing.generate('inf_check_for_email'),
    			method: 'POST',
    			data: {
    				'email': email
    			}
    		});
		}
	};
}]);

app.factory('Register', ['$q', '$http', function($q, $http) {
	return {
		submitInfluencerData: function(data) {
			var deferred = $q.defer();
			$http({
				url: Routing.generate('inf_registration_save'),
				method: 'POST',
				data: data
			}).then(function(resp) {
				console.log(resp);
				deferred.resolve(resp.data);
			}, function() {
				deferred.reject();
			});
			return deferred.promise;
		}
	};
}])
