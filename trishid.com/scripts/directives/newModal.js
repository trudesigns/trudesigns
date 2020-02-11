(function () {
  'use strict';

  angular
    .module('trishApp')
    .directive('newModal', newModal);

    function newModal () {

      return {
        controller: controller,
        controllerAs: 'NewModalCtrl',
        link: link,
        restrict: 'A'
      };

      function link (scope, element, attributes, ctrl) {
        function onClick () {
          ctrl.openModal(
            // attributes.filter
          );
        }
        angular.element(element[0]).bind('click', onClick);
      }

    }

    /* @ngInject */
    function controller ($uibModal, $filter, NewContent) {
      /* jshint validthis: true */
      var vm = this;

      vm.openModal = openModal;

      function openModal (filterOn) {
        $uibModal.open({
          controller: 'NewModalInstanceCtrl',
          controllerAs: 'NewModalInstanceCtrl',
          resolve: {
            slides: function () {
              var filter = $filter('filter');
              var slides = filter(NewContent.slides, {image: filterOn});
              return angular.copy(slides);
            }
          },
          size: 'lg',
          templateUrl: 'newModalContent.html'
        }).result.then();
      }

    }

})();
