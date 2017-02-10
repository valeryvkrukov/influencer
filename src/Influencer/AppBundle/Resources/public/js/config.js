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
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.home', {
			url: '/home',
			controller: 'HomeCtrl',
			templateUrl: Routing.generate('inf_home'),
			resolve: lazyLoad(['home'], ['isotope'])
		})
		.state('app.me', {
			url: '/me',
			controller: 'HomeCtrl',
			templateUrl: Routing.generate('inf_home'),
			resolve: lazyLoad(['home'], [])
		})
		.state('app.profile', {
			url: '/profile',
			templateUrl: Routing.generate('inf_profile'),
			controller: 'ProfileCtrl',
			resolve: lazyLoad(['profile'], ['wysihtml5', 'select', 'tagsInput', 'dropzone', 'inputMask'])
		})
		.state('app.campaign', {
			url: '/campaign',
			controller: 'CampaignCtrl',
			templateUrl: Routing.generate('inf_campaign'),
			resolve: lazyLoad(['campaign'], ['select', 'wizard'])
		})
		.state('app.settings', {
			abstract: true,
			url: '/settings',
			template: '<div class="full-height" ui-view></div>',
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.settings.signup', {
			url: '/signup',
			controller: 'AdminSettingsSignupCtrl',
			templateUrl: Routing.generate('inf_admin_settings_signup'),
			resolve: lazyLoad(['admin/settings/signup'], [])
		})
		.state('app.users', {
			abstract: true,
			url: '/users',
			template: '<div class="full-height" ui-view></div>',
			resolve: {
				loginRequired: loginRequired
			}
		})
		.state('app.users.list', {
			url: '/list',
			controller: 'AdminUsersListCtrl',
			templateUrl: Routing.generate('inf_admin_users_list'),
			resolve: lazyLoad(['admin/users/list'], ['dataTables'])
		})
		.state('app.users.create', {
			url: '/create',
			controller: 'AdminUsersCreateCtrl',
			templateUrl: Routing.generate('inf_admin_users_create'),
			resolve: lazyLoad(['admin/users/create'], [])
		})
		.state('access', {
			abstract: true,
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
		clientId: '1230641216984423',
		authorizationEndpoint: 'https://www.facebook.com/v2.8/dialog/oauth',
		scope: ['email', 'public_profile', 'user_friends']
	});
	$authProvider.google({
		clientId: '758902806102-sh9om8tu0bbbbgvecsokav3uimkmaekj.apps.googleusercontent.com',
		scope: ['profile', 'email', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtube.force-ssl', 'https://www.googleapis.com/auth/youtube.readonly']
	});
	$authProvider.instagram({
		clientId: '0328e45f47f944ceb589dc0f1879d82b',
		scope: ['basic', 'public_content']
	});
	$authProvider.twitter({
		url: '/auth/twitter'
	});
});

angular.module('ui.select').config(function($provide) {
    $provide.decorator('uiSelectChoicesDirective', function($delegate) {
        var directive = $delegate[0];

        var templateUrl = directive.templateUrl;

        directive.templateUrl = function(tElement) {
            tElement.addClass('ui-select-choices');
            return templateUrl(tElement);
        };

        return $delegate;
    });

    $provide.decorator('uiSelectMatchDirective', function($delegate) {
        var directive = $delegate[0];

        var templateUrl = directive.templateUrl;

        directive.templateUrl = function(tElement) {
            tElement.addClass('ui-select-match')
            return templateUrl(tElement);
        };

        return $delegate;
    });
});

app.factory('GetPredefinedVars', ['$q', '$http', function($q, $http) {
	return {
		getIntl: function() {
			var deferred = $q.defer();
			$http.get(Routing.generate('inf_get_intl_vars')).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		},
		getTypes: function() {
			var deferred = $q.defer();
			$http.get(Routing.generate('inf_get_post_types')).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		},
		getSocialNetworks: function() {
			var deferred = $q.defer();
			$http.get(Routing.generate('inf_get_social_networks')).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		},
		getCategories: function() {
			var deferred = $q.defer();
			$http.get(Routing.generate('inf_get_categories')).then(function(resp) {
				deferred.resolve(resp.data);
			});
			return deferred.promise;
		}
	};
}]);

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
    	if ($rootScope.user === undefined) {
	    	Account.getProfile().then(function(resp) {
	    		$rootScope.user = resp.data;
	    	});
    	}
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

app.filter('datetime', function() {
	return function(input) {
		var date = new Date(input);
		return date.toDateString() + ' @ ' + date.getHours() + ':' + date.getMinutes();
	};
});

app.filter('trustHTML', ['$sce',function($sce) {
	return function(value, type) {
		return $sce.trustAs(type || 'html', value);
	};
}]);