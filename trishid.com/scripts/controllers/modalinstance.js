// (function () {
//   'use strict';

//   /**
//   * @ngdoc function
//   * @name trishApp.controller:ModalInstanceCtrl
//   * @description
//   * # ModalinstanceCtrl
//   * Controller of the trishApp
//   */
//   angular
//   .module('trishApp')
//   .controller('ModalInstanceCtrl', ModalInstanceCtrl);

//   /* @ngInject */
//   function ModalInstanceCtrl ($uibModalInstance, slides, $filter) {
//     /* jshint validthis: true */
//     var vm = this;

//     vm.closeModal = closeModal;
//     vm.getActiveSlideTitle = getActiveSlideTitle;

//     _init();

//     function _init () {
//       vm.slides = slides;
//     }

//     function _getActiveSlide () {
//       var result = $filter('filter')(slides, {active: true});
//       return result[0] ? result[0] : null;
//     }

//     function closeModal () {
//       $uibModalInstance.close();
//     }

//     function getActiveSlideTitle () {
//       var slide = _getActiveSlide();
//       if (slide) {
//         if (slide.link) {
//           return slide.text + ' ' + $filter('linky')(slide.link, '_blank');
//         } else {
//           return slide.text;
//         }
//       }
//     }

//   }

// })();
