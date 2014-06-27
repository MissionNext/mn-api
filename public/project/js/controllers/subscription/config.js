(function(){
    var subscriptionControllers = angular.module('subscriptionControllers', []);

    subscriptionControllers.controller('SubscriptionController', [ '$http', function ($http) {
        var self = this;

        self.configs = [];
        self.application = null;
        $http.get('/dashboard/ajax/subscription/config')
            .success(function (data) {
                self.configs = data.config;
                self.application = window.CurrentApplication;
        });

        self.editPrice = function(mainIndex, priceIndex){
            self.editing = [];
            self.editing[mainIndex] = [];
            self.editing[mainIndex][priceIndex] = 0;
            self.editing[mainIndex][priceIndex] = self.configs[mainIndex].partnership[priceIndex].price;
            if (! self.editing[mainIndex][priceIndex]){
                self.editing[mainIndex][priceIndex] = 1;
            }
            if (!self.configs[mainIndex].partnership[priceIndex].price){
                self.configs[mainIndex].partnership[priceIndex].price = 0;
            }
            console.log(self.configs);
        };

        self.blurPrice = function(){
             console.log('sdf');
        }

    }]);

    App.requires.push('subscriptionControllers');
})();


