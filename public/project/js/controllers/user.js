(function(){
    var userControllers = angular.module('userControllers', []);

    userControllers.config(usersRouter);





    function usersRouter($routeProvider){

        $routeProvider
        .when(
            '/', { templateUrl: Routing.buildUrl('/user/list'), controller: "UserListCtl" }
        ).when(
            '/:user', { templateUrl: Routing.buildTemplateUrl('/users/user.html'), controller: "UserCtl" }
        );

    }

    userControllers.controller("UserListCtl",['$scope',function($scope){
        console.log("user list");

    }]);

    userControllers.controller("UserCtl",['$scope', '$routeParams', '$http', function($scope, $params, $http){
        $scope.modalShown = false;
        $scope.toggleModal = function(isActive, sub) {
            $scope.isActiveOnSite = isActive ? { 'label' : 'Activate', value: isActive, sub: sub } :  { 'label' : 'Block', value: isActive, sub: sub };

            $scope.modalShown = !$scope.modalShown;
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
            console.log($params.user, data.user);
            $scope.user = data.user;
            $scope.showLevel = data.user.role === 'organization';
            $scope.statuses = data.statuses;
        });

        $http.get(Routing.buildUrl('/subscription/manager/'+$params.user)).success(function(data){
            console.log(data);
            $scope.subscriptions = data;
        });

        $scope.setDisabled = function($event){
           $http.get(Routing.buildUrl('/user/disable/'+$scope.user.id))
               .success(function(data){
                   console.log(data);
                    $scope.user.is_active = data.is_active;
                    $scope.user.status = data.status;
               });
        };

        $scope.setActive = function($event){
            $http.get(Routing.buildUrl('/user/enable/'+$scope.user.id))
                .success(function(data){
                    $scope.user.is_active = data.is_active;
                    $scope.user.status = data.status;
                });
        };

        $scope.updateSub = function(subscription, property){
            $http.put(Routing.buildUrl('/subscription/'+ subscription.id), [{ field: property, value : subscription[property] }])
                .success(function(data){
                    subscription.days_left = data.subscription.days_left;

                  console.log(data);
                });

        }

    }]);

    App.requires.push('userControllers');

})();



