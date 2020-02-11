(function () {
  'use strict';

  angular
    .module('trishApp')
    .service('MyWork', MyWork);

  /* @ngInject */
  function MyWork (Spreadsheet) {

    this.slides = [];

    Spreadsheet.get().then(function (result) {
      this.slides = result.data;
    }.bind(this));

  }

})();
