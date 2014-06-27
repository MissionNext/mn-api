(function(){

    var userControllers = angular.module('userControllers', []);

    userControllers.config(usersRouter);

    function usersRouter($routeProvider){

        $routeProvider
        .when(
            '/', { templateUrl: '/dashboard/ajax/user/list', controller: "UserListCtl" }
        ).when(
            '/:user', { templateUrl: '/project/views/partials/users/user.html', controller: "UserCtl" }
        );

    }

    userControllers.controller("UserListCtl",['$scope',function($scope){
        console.log("user list");

    }]);

    userControllers.controller("UserCtl",['$scope', '$routeParams',function($scope, $params){
        console.log($params.user);

    }]);

    App.requires.push('userControllers');

})();



