'use strict';

var app = angular.module('app');

app.config(function($stateProvider, $urlRouterProvider, $authProvider, $ocLazyLoadProvider, localStorageServiceProvider) {
	var controllersRoot = (Routing.generate('inf_root')).replace('app_dev.php/', '') + 'bundles/influencerapp/js/controllers/';
	localStorageServiceProvider
		.setPrefix('incluencer_app')
		.setStorageType('localStorage')
		.setNotify(true, true);
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
			resolve: lazyLoad(['home'], ['isotope', 'ngImgCrop'])
		})
		.state('app.me', {
			url: '/me',
			controller: 'HomeCtrl',
			templateUrl: Routing.generate('inf_home'),
			resolve: lazyLoad(['home'], [])
		})
		.state('app.feeds', {
			url: '/feeds',
			controller: 'FeedCtrl',
			templateUrl: Routing.generate('inf_feed'),
			resolve: lazyLoad(['feed'], ['isotope'])
		})
		.state('app.profile', {
			url: '/profile',
			templateUrl: Routing.generate('inf_profile'),
			controller: 'ProfileCtrl',
			resolve: lazyLoad(['profile'], ['wysihtml5', 'select', 'tagsInput', 'dropzone', 'inputMask', 'ngImgCrop'])
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
			resolve: lazyLoad(['signup'], ['datepicker', 'select', 'wizard', 'tagsInput', 'dropzone', 'inputMask', 'ngImgCrop'])
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
		scope: ['profile', 'email', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtube.force-ssl', 'https://www.googleapis.com/auth/youtube.readonly', 'https://www.googleapis.com/auth/youtubepartner']
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

app.factory('userService', ['Account', 'localStorageService', function(Account, localStorageService) {
	return {
		getCurrent: function() {
			var user;
			if (!localStorageService.get('currentUser')) {
				Account.getProfile().then(function(resp) {
					localStorageService.set('currentUser', resp.data);
				    user = resp.data;
				});
			} else {
				user = localStorageService.get('currentUser');
			}
			return user;
		}
	}
}]);

app.factory('ImageUtils', function() {
	return {
		isDataUrl: function(input) {
			var regex = /^\s*data:([a-z]+\/[a-z]+(;[a-z\-]+\=[a-z\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
			return !!input.match(regex);
		},
		toDataUrl: function(src, callback) {
			var img = new Image();
			img.crossOrigin = 'Anonymous';
			img.onload = function() {
				var canvas = document.createElement('CANVAS');
				var ctx = canvas.getContext('2d');
				var dataURL;
				canvas.height = this.height;
			    canvas.width = this.width;
			    ctx.drawImage(this, 0, 0);
			    dataURL = canvas.toDataURL('image/png');
			    callback(dataURL);
			};
			img.src = src;
		}
	};
});

app.controller('AppCtrl', ['$scope', '$rootScope', '$state', '$stateParams', '$auth', 'userService', function($scope, $rootScope, $state, $stateParams, $auth, userService) {
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
    $scope.user = userService.getCurrent();
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