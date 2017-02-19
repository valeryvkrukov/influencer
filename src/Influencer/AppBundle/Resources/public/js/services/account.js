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
				$scope.selected = resp.data;
			});
			return deferred.promise;
		}
	};
}])