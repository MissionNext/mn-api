(function(){
    var userControllers = angular.module('userControllers', ['ngSanitize', 'angularUtils.directives.dirPagination']);

    userControllers.config(usersRouter);




    function usersRouter($routeProvider){

        $routeProvider
//        .when(
//            '/', { templateUrl: Routing.buildUrl('/user/list'), controller: "UserListCtl" }
//        )
            .when(
            '/', { templateUrl: Routing.buildTemplateUrl('/users/list.html'), controller: "UserListCtl" }
        )
            .when(
            '/:user', { templateUrl: Routing.buildTemplateUrl('/users/user.html'), controller: "UserCtl" }
        );

    }

    function intersect(a, b) {
        var t;
        if (b.length > a.length) t = b, b = a, a = t; // indexOf to loop over shorter
        return a.filter(function (e) {
            if (b.indexOf(e) !== -1) return true;
        });
    }

//    var a = ['1'];
//
//    var b = ['1', '2'];
//
//    console.log("HERE", intersect(a, b));

    userControllers.controller("UserListCtl",['$scope', '$http', '$sce', '$timeout', 'filterFilter', function($scope, $http, $sce, $timeout, filterFilter){

        $scope.search = {role: null, app: null};

        $scope.userMatchProfile = function(query){

            return function(user) {

                return user.username.match(query) || user.email.match(query);
            };
        };

        $scope.userMatchRole = function(query){

            var roles = query ? query.split('|') : null;

            return function(user) {

                return roles ? $.inArray(user.roleId.toString(), roles)  !== -1 : true;
            }
        };

        $scope.userMatchApp = function(query){

            var apps = query ? query.split('|') : null;

            return function(user) {
                if (apps){
                   var interApps =  intersect(apps, user.appsIds);
                   if (!interApps.length || apps.length !== interApps.length){

                       return false;
                   }

                }
                return true;
            }
        };

        function buildFilterQuery(){

            return 'filters[role]='+($scope.search.role || '')+'&filters[app]='+($scope.search.app || '')+'&filters[profile]='+($scope.search.profile || '');
        }

        $scope.customFiltering = function() {
            console.log($scope.search);
            getResultsPage(1);
        };


        $('#role-select-id').selectize({
            plugins: ['remove_button'],
            delimiter: '|',
            maxItems: null,
            valueField: 'id',
            preload: true,
            openOnFocus: true,
            labelField: 'role',
            searchField: 'role',
            options: [],
            create: false,
            onChange: function(value){
                $scope.$apply(function(){
                    $scope.search.role = value;
                    $scope.customFiltering();
                });
            },
            initUrl: Routing.buildUrl('/filter/roles'),
            remoteUrl:Routing.buildUrl('/filter/roles'),
            load : function(query, callback){
                var selectize = this;
                $http.get(selectize.settings.initUrl).success(function(data){
                    console.log('roles', data);
                    $.each(data, function(idx, el){
                        selectize.addOption(el);
                    });
                });

            }
        });

        $('#apps-select-id').selectize({
            plugins: ['remove_button'],
            delimiter: '|',
            maxItems: null,
            valueField: 'id',
            preload: true,
            openOnFocus: true,
            labelField: 'name',
            searchField: 'name',
            options: [],
            create: false,
            onChange: function(value){
                $scope.$apply(function(){
                    $scope.search.app = value;
                    $scope.customFiltering();
                });
            },
            initUrl: Routing.buildUrl('/filter/apps'),
            remoteUrl:Routing.buildUrl('/filter/apps'),
            load : function(query, callback){
                var selectize = this;
                $http.get(selectize.settings.initUrl).success(function(data){
                    $.each(data, function(idx, el){
                        selectize.addOption(el);
                    });
                });

            }
        });

        $scope.pagination = {
            current: 1
        };
        getResultsPage($scope.pagination.current);

        $scope.pageChanged = function(newPage) {
            getResultsPage(newPage);
        };

        function getResultsPage(pageNumber) {
            // this is just an example, in reality this stuff should be in a service
            $http.get(Routing.buildUrl('/user/list?page='+pageNumber+'&'+buildFilterQuery()))
                .success(function(result) {
                    $scope.users = result.users.data;
                    console.log($scope.users);
                    $.each($scope.users,function(idx, user){
                        $.each(user.appsIds, function(ix, id){
                            $scope.users[idx].appsIds[ix] = id.toString();
                        });
                    });
                    $scope.totalUsers = result.totalUsers;
                    $scope.oldTotalUsers = result.totalUsers;
                    $scope.itemsPerPage = result.itemsPerPage;
                    $scope.oldItemsPerPage = $scope.itemsPerPage;


                });
        }

//        $scope.filter = function() {
//            $timeout(function() {
//                $scope.totalUsers = $scope.filtered.length;
//               console.log($scope.filtered);
//               // $scope.noOfPages = Math.ceil($scope.filtered.length/$scope.entryLimit);
//            });
//        };
//        console.log(filterFilter);
//        $scope.$watch('search', function(term) {
//           var filtered  = filterFilter($scope.users, term);
//            if (filtered) {
//                $scope.totalUsers = filtered.length == $scope.oldItemsPerPage ? $scope.oldTotalUsers : filtered.length;
//            }
//
//            // Then calculate noOfPages
//            //$scope.noOfPages = Math.ceil($scope.users.length/2);
//        })



    }]);

    userControllers.controller("UserCtl",['$scope', '$routeParams', '$http', function($scope, $params, $http){
        $scope.modalShown = false;
        $scope.modalCancelSub = false;

        $scope.toggleModal = function(isActive, sub) {

            $scope.isActiveOnSite = isActive ? { 'label' : 'Activate', value: isActive, sub: sub } :  { 'label' : 'Block', value: isActive, sub: sub };
            $scope.configurator = function(){
                sub.app.is_active = !sub.app.is_active;
            };
            $scope.modalShown = !$scope.modalShown;
        };

        $scope.toggleModalCancelSub = function(sub){
            $scope.cancelSubModel = sub;
            $scope.modalCancelSub = !$scope.modalCancelSub;
        };

        $scope.transactions = [];

        $scope.closeModal = function (isActiveOnSite) {
            isActiveOnSite.sub.app.is_active = !isActiveOnSite.sub.app.is_active;
            $scope.modalShown = !$scope.modalShown;
        };
        $scope.closeModalCancelSub = function(){

            $scope.modalCancelSub = !$scope.modalCancelSub;
        };

        $http.get(Routing.buildUrl('/subscription/manager/transactions/'+$params.user))
            .success(function(data){
                console.log(data);
                $scope.transactions = $.map(data, function(el) { return el; });
            }).error(function(error){
                console.log(error);
        });

        $scope.closeSubscription = function(sub){
            $scope.modalCancelSub = !$scope.modalCancelSub;

            if (sub.is_recurrent && sub.authorize_id){
                $.each($scope.subscriptions, function(idx, s){
                    s.status = 'closed';
                });
            }

            sub.status = 'closed';
            $scope.updateSub(sub, 'status', true);
        };

        $scope.activateOnSite = function(bool)
        {
            var status = bool && $scope.isActiveOnSite.value ? 'enable' : 'disable';
            if (bool && !$scope.isActiveOnSite.value){
                status = 'enable';
                bool = true;
                $scope.isActiveOnSite.value = true;
            }

            $scope.isActiveOnSite.sub.app.is_active = bool && $scope.isActiveOnSite.value;
            $http.get(Routing.buildUrl('/user/app/'+ status+'/'+$scope.isActiveOnSite.sub.user_id+'/'+$scope.isActiveOnSite.sub.app_id))
                .success(function(data){
                    console.log(data);
                }).error(function(error){
                    console.log(error);
                });

            $scope.modalShown = !$scope.modalShown;

        };


        $scope.dateOptions = {
            changeYear: true,
            changeMonth: true,
            dateFormat: 'yy-mm-dd'
        };

        $scope.myDate = "Thursday, 11 October, 2012";

        $scope.user = null;
        $scope.subscriptions = [];
        $scope.showLevel = false;

        $http.get(Routing.buildUrl('/user/'+$params.user)).success(function(data){
            console.log(data.user);
            $scope.user = data.user;
            if ($scope.user.status != 1 &&  $scope.user.is_active){
                $scope.userStatusMessage = 'Access Granted';
            }else if($scope.user.status != 1 &&  !$scope.user.is_active){
                $scope.userStatusMessage = 'Access Denied';
            }
            $scope.showLevel = data.user.role === 'organization';
            $scope.statuses = data.statuses;
        });

        $http.get(Routing.buildUrl('/subscription/manager/'+$params.user)).success(function(data){
            $scope.subscriptions = data;
            console.log($scope.subscriptions);
        });

        $scope.userStatusMessage = 'Pending Approval';

        $scope.setDisabled = function($event){
           $http.get(Routing.buildUrl('/user/disable/'+$scope.user.id))
               .success(function(data){
                    $scope.user.is_active = data.is_active;
                    $scope.user.status = data.status;
                    $scope.userStatusMessage = 'Access Denied';
               });
        };

        $scope.setActive = function($event){
            $http.get(Routing.buildUrl('/user/enable/'+$scope.user.id))
                .success(function(data){
                    $scope.user.is_active = data.is_active;
                    $scope.user.status = data.status;
                    $scope.userStatusMessage = 'Access Granted';
                });
        };

        $scope.updateSub = function(subscription, property, forceClose){
            $http.put(Routing.buildUrl('/subscription/'+ subscription.id), [{ field: property, value : subscription[property], forceClose : forceClose }])
                .success(function(data){
                    console.log(data);
                    subscription.days_left = data.subscription.days_left;
                    subscription.status = data.subscription.status;
                });
        }

    }]);

    App.requires.push('userControllers');

})();



