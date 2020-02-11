(function () {
  'use strict';

  angular
    .module('trishApp')
    .directive('trishModal', trishModal);

    function trishModal () {

      return {
        controller: controller,
        controllerAs: 'ModalCtrl',
        link: link,
        restrict: 'A'
      };

      function link (scope, element, attributes, ctrl) {
        function onClick () {
          ctrl.openModal(attributes.filter);
        }
        angular.element(element[0]).bind('click', onClick);
      }

    }

    /* @ngInject */
    function controller ($uibModal, $filter, MyWork) {
      /* jshint validthis: true */
      var vm = this;

      vm.openModal = openModal;

      function openModal (filterOn) {
        $uibModal.open({
          controller: 'ModalInstanceCtrl',
          controllerAs: 'ModalInstanceCtrl',
          resolve: {
            slides: function () {
              var filter = $filter('filter');
              var slides = filter(MyWork.slides, {image: filterOn});
              return angular.copy(slides);
            }
          },
          size: 'lg',
          templateUrl: 'myModalContent.html'
        }).result.then();
      }

    }

})();
