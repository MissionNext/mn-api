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
        $scope.user = null;
        $scope.subscriptions = [];
        $http.get(Routing.buildUrl('/user/'+$params.user)).success(function(data){
            console.log($params.user, data.user);
            $scope.user = data.user;
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

    }]);

    App.requires.push('userControllers');

})();



