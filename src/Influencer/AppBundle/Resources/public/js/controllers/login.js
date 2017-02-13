'use strict';

angular.module('app')
	.controller('LoginCtrl', ['$scope', '$state', '$auth', 'Account', 'localStorageService', function($scope, $state, $auth, Account, localStorageService) {
		$scope.login = function() {
			$auth.login($scope.user).then(function() {
				//toastr.success('You have successfully signed in!');
				//$location.path('/');
				Account.getProfile().then(function(resp) {
					localStorageService.set('currentUser', resp.data);
				    $scope.user = resp.data;
				    $state.transitionTo('app.home');
				});
			}).catch(function(error) {
				//toastr.error(error.data.message, error.status);
				$scope.error = error.data.message;
			});
		};
		$scope.authenticate = function(provider) {
			$auth.authenticate(provider).then(function() {
				//toastr.success('You have successfully signed in with ' + provider + '!');
				//$location.path('/');
				$state.transitionTo('app.home');
				
			}).catch(function(error) {
				if (error.message) {
					//toastr.error(error.message);
					$scope.error = error.message;
				} else if (error.data) {
					//toastr.error(error.data.message, error.status);
					$scope.error = error.data.message;
				} else {
					//toastr.error(error);
					$scope.error = error;
				}
			});
		};
	}]);