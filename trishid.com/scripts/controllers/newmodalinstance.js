(function () {
  'use strict';

  /**
  * @ngdoc function
  * @name trishApp.controller:NewModalInstanceCtrl
  * @description
  * # ModalinstanceCtrl
  * Controller of the trishApp
  */
  angular
  .module('trishApp')
  .controller('NewModalInstanceCtrl', NewModalInstanceCtrl);

  /* @ngInject */
  function NewModalInstanceCtrl ($uibModalInstance, slides, $filter) {
    /* jshint validthis: true */
    var vm = this;

    vm.closeModal = closeModal;
    vm.getActiveSlideTitle = getActiveSlideTitle;
    vm.getActiveSlideContent = getActiveSlideContent;

    _init();

    function _init () {
      vm.slides = slides;
    }

    function _getActiveSlide () {
      var result = $filter('filter')(slides, {active: true});
      return result[0] ? result[0] : null;
    }

    function closeModal () {
      $uibModalInstance.close();
    }

    function getActiveSlideTitle () {
      var slide = _getActiveSlide();
      if (slide) {
        return slide.title;
      }
    }

    function getActiveSlideContent () {
      var slide = _getActiveSlide();
      if (slide) {
        return slide.content;
      }
    }

  }

})();
