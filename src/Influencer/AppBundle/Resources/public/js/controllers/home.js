'use strict';

angular.module('app')
	.controller('HomeCtrl', ['$rootScope', '$scope', '$http', '$timeout', 'Account', 'LoadData', 'LoadStatistics', 'localStorageService', 'ImageUtils', function($rootScope, $scope, $http, $timeout, Account, LoadData, LoadStatistics, localStorageService, ImageUtils) {
		if ($scope.templatePath === undefined) { 
			$scope.user = localStorageService.get('currentUser');
			
			$scope.templatePath = Routing.generate('inf_home', {role: $scope.user.role});
			LoadData.get($scope.user).then(function(data) {
				$scope.feeds = data;
			});
			$scope.statistics = [];
			if ($scope.user.google) {
				LoadStatistics.forAllNetworks().then(function(resp) {
					$scope.statistics = resp;
				});
			}
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
						localStorageService.set('currentUser', $scope.user);
						$rootScope.$broadcast('profile-update', 'cover');
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
						localStorageService.set('currentUser', $scope.user);
						$rootScope.$broadcast('profile-update', 'avatar');
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
			}).then(function(resp) {
				if (resp.status == 200) {
					localStorageService.set('currentUser', $scope.user);
				}
			});
		};
		$scope.cropProfileImage = function($flow) {
			$scope.croppedImage = '';
			if (ImageUtils.isDataUrl($scope.user.profileImage)) {
				var fileReader = new FileReader();
				fileReader.onload = function(event) {
					$scope.croppedImage = event.target.result;
				};
				fileReader.readAsDataURL($scope.user.profileImage);
			} else {
				ImageUtils.toDataUrl($scope.croppedImage, function(image) {
					$scope.croppedImage = image;
				});
			}
		};
        $scope.saveCroppedImage = function(croppedImage) {
        	$http({
				url: Routing.generate('inf_user_update_field'),
				method: 'POST',
				data: {user: $scope.user.id, field: 'profileImage', value: croppedImage}
			}).then(function(resp) {
				if (resp.status == 200) {
					$scope.user.profileImage = resp.data.profileImage;
					localStorageService.set('currentUser', $scope.user);
				}
			});
        };
        $scope.getTwitterFollowersCount = function(id) {
        	$http({
				url: Routing.generate('inf_user_get_stat', {'network': 'twitter', 'id': id}),
				method: 'GET'
			}).then(function(resp) {
				console.log('RESP: ', resp);
				if (resp.status == 200) {
					$scope.twitterFollowersCount = resp;
				}
			});
        };
        
        $scope.getGoogleStat = function() {
        	
        };
	}]);