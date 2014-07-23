(function ($)
    {
        var i = 0;

        function toggle_cart_colour() {
          i += 1;
          jQuery('div#ecommerce-cart-menu a').toggleClass('ecommerce-cart-highlight');
          if (i < 6) {
            window.setTimeout(toggle_cart_colour, 200);
          }
        }


        Drupal.behaviors.sfEcommerce_cart = {
            attach: function (context)
                {
                  var query = window.location.search.substring(1);
                  if (query.indexOf('cart=added') != -1) {
                    window.setTimeout(toggle_cart_colour, 200);
                  }
                } 
    };
})(jQuery);
