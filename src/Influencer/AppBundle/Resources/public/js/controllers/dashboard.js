'use strict';

angular.module('app')
	.factory('Campaigns', ['$q', '$http', function($q, $http) {
		return {
			loadAll: function(filter) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_campaigns', {status: filter}),
					method: 'GET'
				}).then(function(resp) {
					deferred.resolve(resp.data);
				})
				return deferred.promise;
			},
			loadStatistic: function() {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_campaigns_stat'),
					method: 'GET'
				}).then(function(resp) {
					deferred.resolve(resp.data);
				})
				return deferred.promise;
			}
		};
	}])
	.controller('DashboardCtrl', ['$rootScope', '$scope', '$http', '$filter', 'LoadStatistics', 'Campaigns', 'localStorageService', 'DTDefaultOptions', 'DTOptionsBuilder', 'DTColumnBuilder', function($rootScope, $scope, $http, $filter, LoadStatistics, Campaigns, localStorageService, DTDefaultOptions, DTOptionsBuilder, DTColumnBuilder) {
		if ($scope.templatePath === undefined) { 
			$scope.user = localStorageService.get('currentUser');
			$scope.templatePath = Routing.generate('inf_dashboard', {role: $scope.user.role});
			$scope.statistics = [];
			$scope.campaigns = [];
			$scope.filterDisplayTitle = 'all';
			if ($scope.user.google) {
				LoadStatistics.forAllNetworks().then(function(resp) {
					$scope.statistics = resp;
				});
			}
			Campaigns.loadStatistic().then(function(resp) {
				$scope.campaignsStat = resp.data;
			});
		}
		DTDefaultOptions.setLoadingTemplate('<div class="progress-circle-indeterminate"></div>');
		$scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
				return Campaigns.loadAll($scope.filterDisplayTitle);
			})
			.withPaginationType('full_numbers')
			.withDOM('ftrip')
			.withBootstrap();
		$scope.dtColumns = [
			DTColumnBuilder.newColumn('name').withTitle('Name').renderWith(function(data, type, full) {
				return '<span class="font-montserrat all-caps fs-12">'+full.name+'</span>';
			}),
			DTColumnBuilder.newColumn('client').withTitle('Client'),
			DTColumnBuilder.newColumn('budget').withTitle('Budget').renderWith(function(data, type, full) {
				return '<span class="font-montserrat fs-18 text-primary">$'+$filter('number')(full.budget, 2)+'</span>';
			}),
			DTColumnBuilder.newColumn('status').withTitle('Status').renderWith(function(data, type, full) {
				var status = '<span class="font-montserrat all-caps fs-12 ';
				switch(full.status) {
					case 'new':
						status += 'text-success'
						break;
					case 'rejected':
						status += 'text-danger';
						break;
					case 'live':
						status += 'text-primary';
						break;
					case 'pending':
						status += 'text-warning';
						break;
					case 'finished':
						status += 'text-master';
						break;
				}
				status += '">'+full.status+'</span>';
				return status;
			}),
			DTColumnBuilder.newColumn('deadline').withTitle('Deadline')
		];
		$scope.dtInstance = {};
		$scope.applyStatusFilter = function(dtInstance, status) {
			$scope.filterDisplayTitle = status;
			dtInstance.reloadData(function() {
				return Campaigns.loadAll(status);
			}, true);
			//dtInstance.rerender();
		};
	}]);