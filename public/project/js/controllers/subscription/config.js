(function(){
    var subscriptionControllers = angular.module('subscriptionControllers', []);

    subscriptionControllers.controller('SubscriptionController', [ '$http', '$scope', function ($http, $scope) {
        var self = this;

        var watchPrice = function(p, period){
            $scope.$watch(function(){
                return p['price_'+period];
            }, function(newVal, oldVal){
                if (!newVal){
                    p['price_'+period] = 0;
                }
                var editing = 'editing'+ period.ucfirst();
                self[editing] = null;
                console.log(oldVal, newVal);
            });
        };

        self.configs = [];
        self.application = null;
        $http.get('/dashboard/ajax/subscription/config')
            .success(function (data) {
                self.configs = data.config;
                self.application = window.CurrentApplication;

                angular.forEach(self.configs, function(config, indexMain){
                   angular.forEach(config.partnership, function(p, index){
                        watchPrice(p, 'month');
                        watchPrice(p, 'year');
                   });
                });
        });



        self.editPrice = function(mainIndex, priceIndex, period){
            var editing = 'editing'+ period.ucfirst();
            self[editing] = [];
            self[editing][mainIndex] = [];
            self[editing][mainIndex][priceIndex] = self.configs[mainIndex].partnership[priceIndex]['price_'+ period];
            if (! self[editing][mainIndex][priceIndex]){
                self[editing][mainIndex][priceIndex] = 1;
            }

        };

    }]);

    App.requires.push('subscriptionControllers');
})();


