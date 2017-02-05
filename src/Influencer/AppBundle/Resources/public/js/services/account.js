'use strict';

angular.module('app')
	.factory('Account', function($http) {
		return {
			getProfile: function() {
				return $http.get(Routing.generate('inf_me'));
			},
			updateProfile: function(profileData) {
				return $http.put(Routing.generate('inf_me'), profileData);
			}
		};
	});