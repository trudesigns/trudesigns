(function () {
  'use strict';

  /**
   * @ngdoc service
   * @name trishApp.alert
   * @description
   * # alert
   * Service in the trishApp.
   */
  angular.module('trishApp')
    .service('Alert', Alert);

    /* @ngInject */
    function Alert ($uibModal) {
      // AngularJS will instantiate a singleton by calling "new" on this function

      /**
       * [show description]
       * @param  {[type]} message [description]
       * @return {[type]}         [description]
       */
      this.show = function (alertMessage) {

        var template =
          '<div class="modal-header">' +
          '  <h3 class="modal-title">{{ AlertCtrl.message }}</h3>' +
          '</div>';

        var modalInstance = $uibModal.open({
          template: template,
          controller: controller,
          controllerAs: 'AlertCtrl'
        });

        function controller () {
          /* jshint validthis: true */
          this.message = alertMessage;
        }

        return modalInstance.result;
      };

    }

})();
