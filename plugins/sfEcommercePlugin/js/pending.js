(function ($)
    {
        Drupal.behaviors.sfEcommerce_pendingPayment = {
            attach: function (context)
                {
                    window.setTimeout(function() {
                        location.reload();
                    }, 5000);
                } 
    };
})(jQuery);
