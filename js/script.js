(function($) {
  "use strict";

  $(".portfolio-single-slider").slick({
    infinite: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 2000
  });

  $(".clients-logo").slick({
    infinite: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 2000
  });

  $(".testimonial-wrap").slick({
    slidesToShow: 2,
    slidesToScroll: 2,
    infinite: true,
    dots: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 6000,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 900,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  $(".portfolio-gallery").each(function() {
    $(this)
      .find(".popup-gallery")
      .magnificPopup({
        type: "image",
        gallery: {
          enabled: true
        }
      });
  });

  var map;

  function initialize() {
    var mapOptions = {
      zoom: 13,
      center: new google.maps.LatLng(34.24107, -118.60179)
      // styles: style_array_here
    };
    map = new google.maps.Map(
      document.getElementById("map-canvas"),
      mapOptions
    );
  }

  var google_map_canvas = $("#map-canvas");

  if (google_map_canvas.length) {
    google.maps.event.addDomListener(window, "load", initialize);
  }

  // Counter

  $(".counter-stat").counterUp({
    delay: 10,
    time: 1000
  });

})(jQuery);

// Author - Mike Ross
var TRU = {
  Proposals: {
    _form: null,
    _errors: [],
    Add: function(form) {
      TRU.Proposals._form = form;
      if (TRU.Proposals.Valid()) {
        $(form).submit();
      }
    },
    Valid: function() {
      let valid, required, email;
      valid = true;
      required = $(TRU.Proposals._form).find(".input-required input");
      email = $(TRU.Proposals._form).find(".input-email input");
      
      // Check required
      let allFilled, totalRequired, r;
      allFilled = true;
      totalRequired = required.length;
      for (r = 0; r < totalRequired; r++) {
        let requiredField = $($(required)[r]);
        if (requiredField.val() == "") {
          allFilled = false;
          valid = false;
          TRU.Proposals._errors.push(requiredField.attr("data-label") + " is required");
        }
      }

      // Check email
      if (allFilled) {
        let emailValue = email.val();
        if (!(emailValue.indexOf("@") > -1 && emailValue.indexOf(".") > -1)) {
          valid = false;
          TRU.Proposals._errors.push(email.attr("data-label") + " is not valid");
        }
      }

      if (TRU.Proposals._errors.length > 0) {
        TRU.Proposals.DisplayErrors();
      }
      return valid;
    },
    DisplayErrors: function() {
      let output, totalErrors, e;
      output = "";
      totalErrors = TRU.Proposals._errors.length;
      for (e = 0; e < totalErrors; e++) {
        output += "<p class='error'>" + TRU.Proposals._errors[e] + "</p>";
      }
      $("#form-errors").html(output);
      TRU.Proposals._errors = [];
    }
  }
};