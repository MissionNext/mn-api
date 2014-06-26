(function($) {
    var App = angular.module('mission-next', ['ngRoute', 'userControllers'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });

    App.controller('SubscriptionController', [ '$http', function ($http) {
        var self = this;

        self.configs = [];
        self.application = null;
        $http.get('/dashboard/ajax/subscription/config')
            .success(function (data) {
                self.configs = data.config;
                self.application = window.CurrentApplication;
            });

    }]);

})(jQuery);







