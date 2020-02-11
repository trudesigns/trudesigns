(function () {
  'use strict';

  angular
    .module('trishApp')
    .service('NewContent', NewContent);

  /* @ngInject */
  function NewContent (Contentsource) {

    this.slides = Contentsource.get();

  }

})();
