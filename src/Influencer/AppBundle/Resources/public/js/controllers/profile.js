'use strict';

angular.module('app')
	.factory('FeedLoader', ['$q', '$http', function($q, $http) {
		return {
			loadFor: function(network, token, payload, id) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_load_feeds', {'network': network, 'id': id}),
					method: 'POST',
					data: {token: token, payload: payload}
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
	.controller('ProfileCtrl', ['$scope', '$http', '$auth', 'FeedLoader', function($scope, $http, $auth, FeedLoader) {
		$scope.feeds = {
			facebook: [],
			google: [],
			twitter: [],
			instagram: []
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
		$scope.refreshFeed = function(network) {
			var token = $auth.getToken();
			var id = $scope.user.id;
			var loader = function(network, id) {
				FeedLoader.loadFor(network, $auth.getToken(), id).then(function(resp) {
					$scope.feeds[network] = resp;
					//$auth.setToken(token);
				});
			};
			//if (!$auth.isAuthenticated()) {
				$auth.link(network, {'link_account': 1}).then(function(resp) {
					console.log(resp)
					loader(network, id);
				});
			/*} else {
				loader(network);
			}*/
		};
		$scope.linkSocial = function(network) {
			
		};
		$scope.unlinkSocial = function(network) {
			$http({
				url: Routing.generate('inf_user_update_field'),
				method: 'POST',
				data: {user: $scope.user.id, field: network}
			}).then(function(resp) {
				if (resp.status === 200) {
					$scope.user[network] = null;
				}
			});
		};
	}]);