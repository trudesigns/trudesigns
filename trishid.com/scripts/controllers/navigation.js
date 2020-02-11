(function () {
  'use strict';

  angular
    .module('trishApp')
    .controller('NavigationCtrl', NavigationCtrl);

  /* @ngInject */
  function NavigationCtrl ($scope, $document, $window, $timeout) {
    /* jshint validthis: true */
    var vm = this;

    var _currentAnchorOffset;
    var _isChecking = null;
    var _previousAnchorOffset;
    var anchorElement = document.getElementById('anchorBeginIsScrolledDown');
    var navElement = document.getElementById('navbar');

    vm.isCollapsed = true;
    vm.isScrolledDown = null;

    vm.scrollToTop = scrollToTop;
    vm.toggleNav = toggleNav;

    _init();

    function _init () {
      angular.element($window).bind('scroll', _onScroll);
      _setIsScrolledDown();
    }

    function _onScroll () {
      if (!_isChecking) {
        _isChecking = true;
        $scope.$evalAsync(function () {
          _setIsScrolledDown();
          _isChecking = false;
        });
      }
    }

    /**
    * For when scrolling down past navbar while it's open in mobile view.
    */
    function _setAnchorMarginBottom (newValue) {
      var marginValue = newValue ? newValue : vm.isScrolledDown ? navElement.offsetHeight : 0;
      angular.element(anchorElement).css('margin-bottom', (marginValue) + 'px');
    }

    function _setIsScrolledDown () {
      _currentAnchorOffset = anchorElement.getBoundingClientRect().top;
      if (_currentAnchorOffset > 0) { // If scrolled above the splash-page
        vm.isScrolledDown = false;
      } else { // Else is scrolled past splash-page
        vm.isScrolledDown = true;
      }
      if (vm.isScrolledDown && _currentAnchorOffset > _previousAnchorOffset) { // If scrolled down and scrolling downwards
        _setAnchorMarginBottom();
      } else {
        _setAnchorMarginBottom(0);
      }
      _previousAnchorOffset = _currentAnchorOffset;
    }

    function toggleNav (collapse) {
      $timeout(_setAnchorMarginBottom, 10);
      vm.isCollapsed = collapse || !vm.isCollapsed;
    }

    function scrollToTop () {
      $document.scrollTop(0, 500).then(function () {
        // do something...
      });
    }

  }

})();
