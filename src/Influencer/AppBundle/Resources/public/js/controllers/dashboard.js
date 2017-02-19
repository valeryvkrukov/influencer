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
			loadStatistic: function(period) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_campaigns_stat', {period: period}),
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
			if ($scope.user.role == 'influencer') {
				$scope.statistics = [];
				$scope.campaigns = [];
				$scope.walletStatPeriod = '1Y';
				$scope.filterDisplayTitle = 'all';
				/*$scope.periodText = {
					'1D': 'Last Day',
					'1W': 'Last Week',
					'1M': 'Last Month',
					'1Y': 'Last 12 Months'
				};
				$scope.cmpOptions = {
					renderer: 'bar'	
				};
				$scope.cmpFeatures = {
					hover: {
						xFormatter: function(x) {
							return x;
						},
						yFormatter: function(y) {
							return y;
						}
					}	
				};
				$scope.cmpSeries = [];*/
				/*if ($scope.user.google) {
					LoadStatistics.forAllNetworks().then(function(resp) {
						$scope.statistics = resp;
					});
				}*/
				Campaigns.loadStatistic($scope.walletStatPeriod).then(function(resp) {
					$scope.campaignsStat = resp.data;
					$scope.walletInfo = resp.data.walletInfo;
					//$scope.walletStatData = resp.data.paymentsData;
					//$scope.cmpSeries = resp.data.campaignsInfo;
				});
			}
		}
		$scope.walletStatOptions = {
			chart: {
                type: 'stackedAreaChart',
                height: 400,
                margin: {
                    left: 30,
                    right: 30
                },
                x: function(d) {
                    return d.x
                },
                y: function(d) {
                    return d.y
                },
                color: [
                    $.Pages.getColor('success', .7),
                    $.Pages.getColor('info'),
                    $.Pages.getColor('primary', .87)
                ],
                useInteractiveGuideline: true,
                rightAlignYAxis: true,
                transitionDuration: 500,
                showControls: false,
                clipEdge: true,
                xAxis: {
                    tickFormat: function(d) {
                        return d3.time.format('%Y %b')(new Date((d*1000)));
                    }
                },
                yAxis: {
                    tickFormat: function(d) {
                    	return '$'+d;
                    }
                },
                legend: {
                    margin: {
                        top: 30
                    }
                }
            }
		};
		if ($scope.user.role == 'influencer') {
			$scope.changeWalletStatPeriod = function(period) {
				$scope.walletStatPeriod = period;
				Campaigns.loadStatistic($scope.walletStatPeriod).then(function(resp) {
					$scope.walletStatData = resp.data.campaignsStat.paymentsData;
				});
			};
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
				dtInstance.reloadData();
			};
		}
	}]);