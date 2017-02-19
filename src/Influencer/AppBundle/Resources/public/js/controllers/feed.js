'use strict';

angular.module('app')
	.factory('LoadFeedsData', ['$q', '$http', function($q, $http) {
		return {
			get: function(user) {
				var deferred = $q.defer();
				try {
					var url = Routing.generate('inf_load_user_feed', {'role': user.role});
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
	.controller('FeedCtrl', ['$scope', '$http', 'Account', 'LoadFeedsData', 'localStorageService', function($scope, $http, Account, LoadFeedsData, localStorageService) {
		if ($scope.templatePath === undefined) {
			$scope.user = localStorageService.get('currentUser');
			$scope.templatePath = Routing.generate('inf_feed', {role: $scope.user.role});
			LoadFeedsData.get($scope.user).then(function(data) {
				$scope.feeds = data;
			});
		}
		$scope.feedFilter = '';
		$scope.activeTab = '';
		$scope.setFeedFilter = function(network) {
			if (network == '') {
				$scope.feedFilter = '';
				$scope.activeTab = '';
			} else {
				$scope.feedFilter = {network: network};
				$scope.activeTab = network;
			}
		};
	}]);