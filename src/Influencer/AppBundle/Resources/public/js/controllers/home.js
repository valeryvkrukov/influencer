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
				LoadData.get(resp.data).then(function(data) {
					$scope.feeds = data;
				});
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
		$scope.sizeOf = function(obj) {
		    return Object.keys(obj).length;
		};
		$scope.changeCover = function($file, $event, $flow) {
			var cover = '';
			var fileReader = new FileReader();
			fileReader.onload = function(event) {
				cover = event.target.result;
				$http({
					url: Routing.generate('inf_user_update_field'),
					method: 'POST',
					data: {user: $scope.user.id, field: 'profileCover', value: cover}
				}).then(function(resp) {
					if (resp.status == 200) {
						$scope.user.profileCover = cover;
					}
				});
			};
			fileReader.readAsDataURL($file.file);
		};
		$scope.changeAvatar = function($file, $event, $flow) {
			var avatar = '';
			var fileReader = new FileReader();
			fileReader.onload = function(event) {
				avatar = event.target.result;
				$http({
					url: Routing.generate('inf_user_update_field'),
					method: 'POST',
					data: {user: $scope.user.id, field: 'profileImage', value: avatar}
				}).then(function(resp) {
					if (resp.status == 200) {
						$scope.user.profileImage = avatar;
					}
				});
			};
			fileReader.readAsDataURL($file.file);
		};
		$scope.updateProfileField = function(field) {
			$http({
				url: Routing.generate('inf_user_update_field'),
				method: 'POST',
				data: {user: $scope.user.id, field: field, value: $scope.user[field]}
			});
		};
	}]);