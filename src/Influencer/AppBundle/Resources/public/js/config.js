'use strict';

var app = angular.module('app');

app.config(function($stateProvider, $urlRouterProvider, $authProvider, $ocLazyLoadProvider) {
	var controllersRoot = (Routing.generate('inf_root')).replace('app_dev.php/', '') + 'bundles/influencerapp/js/controllers/';
	var skipIfLoggedIn = function($q, $auth) {
		var deferred = $q.defer();
		if ($auth.isAuthenticated()) {
			deferred.reject();
		} else {
			deferred.resolve();
		}
		return deferred.promise;
	};
	var loginRequired = function($q, $location, $auth) {
		var deferred = $q.defer();
		if ($auth.isAuthenticated()) {
			deferred.resolve();
		} else {
			$location.path('/access/login');
		}
		return deferred.promise;
	};
	var lazyLoad = function(ctrl, plugins) {
		var controllers = [];
		angular.forEach(ctrl, function(val, key) {
    		controllers.push(controllersRoot + val + '.js');
    	});
		return {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load(plugins, {insertBefore: '#lazyload_placeholder'}).then(function() {
                	return $ocLazyLoad.load(controllers);
                });
            }]
        };
	};
	$stateProvider
		.state('app', {
			abstract: true,
			url: '/app',
			templateUrl: Routing.generate('inf_app'),
			resolve: lazyLoad(['header', 'search', 'sidebar'], [
            	'nvd3',
                'mapplic',
                'rickshaw',
                'metrojs',
                'sparkline',
                'skycons',
                'switchery',
                'wysihtml5'
            ])
		})
		.state('app.home', {
			url: '/home',
			controller: 'HomeCtrl',
			templateUrl: Routing.generate('inf_home'),
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.me', {
			url: '/me',
			controller: 'HomeCtrl',
			templateUrl: Routing.generate('inf_home'),
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.profile', {
			url: '/profile',
			templateUrl: Routing.generate('inf_profile'),
			controller: 'ProfileCtrl',
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.campaign', {
			url: '/campaign',
			controller: 'CampaignCtrl',
			templateUrl: Routing.generate('inf_campaign'),
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('access', {
			url: '/access',
	    	template: '<div class="full-height" ui-view></div>',
			resolve: {
				skipIfLoggedIn: skipIfLoggedIn
			}
		})
		.state('access.login', {
			url: '/login',
			controller: 'LoginCtrl',
			templateUrl: Routing.generate('inf_login')
		})
		.state('access.signup', {
			url: '/signup',
			templateUrl: Routing.generate('inf_signup'),
			controller: 'SignupCtrl',
			resolve: lazyLoad(['signup'], ['select', 'wizard', 'tagsInput', 'dropzone', 'inputMask'])
		})
		.state('logout', {
			url: '/logout',
			template: null,
			controller: 'LogoutCtrl'
		});
	$urlRouterProvider.otherwise('/access/login');
	$authProvider.facebook({
		clientId: '1223551571026721',
		authorizationEndpoint: 'https://www.facebook.com/v2.8/dialog/oauth'
	});
	$authProvider.google({
		clientId: '758902806102-sh9om8tu0bbbbgvecsokav3uimkmaekj.apps.googleusercontent.com'
	});
	$authProvider.instagram({
		clientId: '0328e45f47f944ceb589dc0f1879d82b'
	});
	$authProvider.twitter({
		url: '/auth/twitter'
	});
});

app.controller('AppCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'Account', function($scope, $rootScope, $state, $stateParams, Account) {
	$scope.app = {
		name: 'Influence',
		description: 'Influence by insydo',
		layout: {
			menuPin: false,
			menuBehind: false
		},
		author: ''
	};
	$scope.is = function(name) {
		return $state.is(name);
    };
	$scope.includes = function(name) {
		return $state.includes(name);
    };
    $rootScope.getUserData = function(key) {
    	Account.getProfile().then(function(resp) {
    		$rootScope.user = resp.data;
    	});
    };
}]);

app.directive('includeReplace', function() {
	return {
        require: 'ngInclude',
        restrict: 'A',
        link: function(scope, el, attrs) {
            el.replaceWith(el.children());
        }
    };
});

app.directive('bsModal', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			scope.dismiss = function() {
				element.modal('hide');
			};
		}
	};
});

app.directive('fileinput', [function() {
	return {
		scope: {
			fileinput: '=',
	        filepreview: '='
		},
		link: function(scope, element, attributes) {
			element.bind('change', function(changeEvent) {
				scope.fileinput = changeEvent.target.files[0];
				var reader = new FileReader();
				reader.onload = function(loadEvent) {
					scope.$apply(function() {
						scope.filepreview = loadEvent.target.result;
					});
				};
				reader.readAsDataURL(scope.fileinput);
			});
		}
	};
}]);

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});