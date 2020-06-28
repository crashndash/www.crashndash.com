(function($) {
  $(document).ready(function() {
    var adjust;
    if (window.innerHeight < window.innerWidth) {
      adjust = window.innerHeight;
    }
    else {
      adjust = window.innerWidth;
    }
    $('.slide.slide1').css('height', adjust);
    $('.slide1 .top-wrapper')
      .css({
        'height': adjust,
        'padding-top': adjust - 300
      });
    $('.top-wrapper-bg').css('height', adjust);
    if ($('.lastupdated').length > 0) {
      // Get that timestamp.
      var $elem = $('.lastupdated');
      var time = $elem.attr('data-time');
      // Oh, and that is only in seconds. And a string.
      time = parseInt(time, 10) * 1000;
      // Let's make it local time.
      var timestring = moment(time).fromNow();
      $elem.text('Last updated ' + timestring);
    }
    if ($('body').hasClass('path-play')) {
      // Some special case here. Ugly as heck.
      var iosSetup = function() {
        jQuery(document).ready(function() {
          // Try to redirect the person.
          var url = $('#iosbutton').attr('href');
          var start = Date.now();
          window.location.href = url;
          setTimeout(function() {
            if (Date.now() - start > 1500) {
              return;
            }
            window.location.href = 'http://itunes.com/apps/crashndash';
          }, 1000);
        });
      };
      var ua = navigator.userAgent;
      var ios = false;
      if (ua.match(/iPhone/i) || ua.match(/iPad/i) || ua.match(/iPod/i)) {
        ios = true;
        setTimeout(iosSetup, 200);
      }

      if (!ios) {
        jQuery(document).ready(function() {
          // Just assume we are on android then. Try to click on the button.
          setTimeout(function() {
            //window.location.href = $('#intenbutton').attr('href');
            //document.getElementById('intentbutton').click();
          }, 200);
        });
      }
    }

  });
  $(document).foundation();
})(jQuery);
