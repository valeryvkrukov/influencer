'use strict';

angular.module('app')
	.factory('LoadData', ['$q', '$http', function($q, $http) {
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
	}])
	.controller('HomeCtrl', ['$scope', '$http', 'Account', 'LoadData', function($scope, $http, Account, LoadData) {
		if ($scope.templatePath === undefined) {
			Account.getProfile().then(function(resp) {
				console.log(resp.data.role);
				$scope.templatePath = Routing.generate('inf_home', {role: resp.data.role});
				if ($scope.feeds === undefined) {
					LoadData.get(resp.data).then(function(data) {
						$scope.feeds = data;
					});
				}
			});
		}
		/*if ($scope.feeds === undefined) {
			console.log($scope.user);
			LoadData.get($scope.user).then(function(data) {
				console.log(data);
			});
		}
		/*$scope.init = function() {
			console.log($scope.user);
			LoadData.get($scope.user).then(function(data) {
				console.log(data);
			});
		};*/
	}]);