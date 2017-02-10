'use strict';

angular.module('app')
	.factory('FeedLoader', ['$q', '$http', function($q, $http) {
		return {
			loadFor: function(network, token, id) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_load_feeds', {'network': network, 'id': id}),
					method: 'POST',
					data: {token: token}
				}).then(function(resp) {
					console.log(resp);
					deferred.resolve(resp.data);
				}, function() {
					deferred.reject();
				});
				return deferred.promise;
			},
			loadAllSaved: function() {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_feeds'),
					method: 'GET'
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
	.controller('ProfileCtrl', ['Account', '$scope', '$http', '$auth', '$sce', 'FeedLoader', 'GetPredefinedVars', function(Account, $scope, $http, $auth, $sce, FeedLoader, GetPredefinedVars) {
		$scope.languages = [];
		$scope.countries = [];
		$scope.categories = [];
		$scope.postTypes = [];
		$scope.networks = [];
		if ($scope.templatePath === undefined) {
			Account.getProfile().then(function(resp) {
				$scope.user = resp.data;
				$scope.templatePath = Routing.generate('inf_profile', {role: resp.data.role});
				$scope.formPath = Routing.generate('inf_profile_main', {role: resp.data.role});
				$scope.formLoaded = 'main';
				GetPredefinedVars.getIntl().then(function(resp) {
					if (resp.data.countries) {
						$scope.countries = resp.data.countries;
					}
					if (resp.data.languages){
						$scope.languages = resp.data.languages;
					}
				});
				GetPredefinedVars.getCategories().then(function(resp) {
					if (resp.data.categories) {
						$scope.categories = resp.data.categories;
					}
				});
				GetPredefinedVars.getTypes().then(function(resp) {
					if (resp.data.types) {
						angular.forEach(resp.data.types, function(v1, k1) {
							var add = true;
							angular.forEach($scope.user.prices, function(v2, k2) {
								if (v1.tag == v2.tag) {
									add = false;
								}
							});
							if (add) {
								$scope.postTypes.push(v1);
							}
						});
					}
				});
				GetPredefinedVars.getSocialNetworks().then(function(resp) {
					if (resp.data.networks) {
						$scope.networks = resp.data.networks;
					}
				});
			});
		}
		$scope.loadForm = function(form) {
			$scope.formPath = Routing.generate('inf_profile_' + form, {role: $scope.user.role});
			$scope.formLoaded = form;
		};
		$scope.submitMain = function() {
			$http({
				url: Routing.generate('inf_update_user', {'id': $scope.user.id}),
				method: 'POST',
				data: $scope.user
			}).then(function(resp) {
				console.log(resp);
			});
			return false;
		};
		
		$scope.feeds = {
			facebook: [],
			google: [],
			twitter: [],
			instagram: []
		};
		$scope.sizeOf = function(obj) {
		    return Object.keys(obj).length;
		};
		$scope.trustAsHtml = function(value) {
            return $sce.trustAsHtml(value);
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
		$scope.loadFeeds = function() {
			FeedLoader.loadAllSaved().then(function(resp) {
				console.log(resp);
				$scope.feeds = resp;
			});
		};
		$scope.refreshFeed = function(network) {
			var token = $auth.getToken();
			var params = {'link_account': 1};
			if ($scope.user[network] == null) {
				params['link_to_user'] = $scope.user.id;
			}
			var loader = function(network) {
				FeedLoader.loadFor(network, $auth.getToken(), $scope.user.id).then(function(resp) {
					$scope.feeds[network] = resp;
					$auth.setToken(token);
					$scope.loadFeeds();
				}, function() {
					$auth.setToken(token);
				});
			};
			$auth.link(network, params).then(function(resp) {
				if ($scope.user[network] == null) {
					$scope.user[network] = resp.data.profile.id;
				}
				loader(network);
			});
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