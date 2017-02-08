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
	.controller('HomeCtrl', ['$scope', '$http', 'LoadData', function($scope, $http, LoadData) {
		$scope.init = function() {
			LoadData.get($scope.user).then(function(data) {
				console.log(data);
			});
		};
	}]);