'use strict';

/**
 * @ngdoc service
 * @name trishApp.Spreadsheet
 * @description
 * # Spreadsheet
 * Service in the trishApp.
 */
angular.module('trishApp')
  .service('Spreadsheet', function Spreadsheet($http) {

    this.data = [];

    // $http.get('https://spreadsheets.google.com/feeds/list/14AFYYn59_NA7d0tiyO1kezNQQ0AryvGM7AlWXgG3_Uk/od6/public/values?alt=json')
    // https://docs.google.com/spreadsheets/d/1-IoeykgHEa4mFbrb65qji8nyJ_Q-BkME8Wk8DFJ38Is/pub?gid=0&single=true&output=csv

    this.get = function () {
      var url = 'https://spreadsheets.google.com/feeds/list/1-IoeykgHEa4mFbrb65qji8nyJ_Q-BkME8Wk8DFJ38Is/od6/public/values?alt=json';
      return $http.get(url, {
        transformResponse: function (d) {

          var collection = angular.fromJson(d).feed.entry || [],
              item,
              i;

          this.data = []; // Reset

          for (i = 0; i < collection.length; i++) {
            item = collection[i];
            this.data.push({
              text : item.gsx$text.$t || null,
              link : item.gsx$link.$t || null,
              image : item.gsx$image.$t || null
            });
          }

          return this.data;

        }.bind(this)
      });
    };

    // this.getItemById = function (id) {
    //   var returnItem = {};
    //   angular.forEach(this.data, function (item) {
    //     if (item.id === id) {
    //       returnItem = item;
    //     }
    //   });
    //   return returnItem;
    // };

  });
