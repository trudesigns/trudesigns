/* global WOW, Typed */

(function () {
  'use strict';

  /**
  * @ngdoc function
  * @name trishApp.controller:MainCtrl
  * @description
  * # MainCtrl
  * Controller of the trishApp
  */
  angular
  .module('trishApp')
  .controller('MainCtrl', MainCtrl);

  /* @ngInject */
  function MainCtrl (Contact, Alert) {
    /* jshint validthis: true */
    var vm = this;

    vm.contact = contact;

    _init();

    function _init () {
      _resetForm();
      // WOW
      new WOW().init();
      //I edited this for vs3 to take out the type effect on the banner on all the other pages sep.12.2018
      // // Typed
      // Typed.new('.trishtype', {
      //   strings: ["LIFE WITHOUT THE EXPERIENCE IS JUST MEANINGLESS."],
      //   typeSpeed: 0,
      //   showCursor: false
      // });
    }

    function _resetForm () {
      vm.form = {
        "name": null,
        "email": null,
        "message": null
      };
    }

    function contact () {
      Contact.post(vm.form).then(function () {
        Alert.show('YAY! Your message has been sent!');
      }, function () {
        // Alert.show('Oops! Your message could not be sent!');
      });
      _resetForm();
    }

  }

})();


