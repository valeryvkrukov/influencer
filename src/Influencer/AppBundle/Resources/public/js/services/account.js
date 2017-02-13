'use strict';

angular.module('app')
	.factory('Account', ['$rootScope', '$http', 'localStorageService', function($rootScope, $http, localStorageService) {
		return {
			getProfile: function() {
				return $http.get(Routing.generate('inf_me'));
			},
			updateProfile: function(profileData) {
				return $http.put(Routing.generate('inf_me'), profileData);
			}
		};
	}]);