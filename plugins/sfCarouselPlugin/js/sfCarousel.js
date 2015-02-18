(function ($)
    {
        Drupal.behaviors.sfCarousel_init = {
            attach: function (context)
                {
                    $('#carousel').show();
                    $('#carousel').carouFredSel({
                        responsive: true,
                        auto                : 7000,
                        width: '86%',
                        height:         250,
                        items: {
                            visible: 3,
                            minimum: 4,
                            start: "random"
                        },
                        direction           : "left",
                        scroll : {
                            items           : 3,
                            duration        : 1000,                         
                            pauseOnHover    : true,
                        },
                        prev : '#carousel_left_arrow',
                        next : '#carousel_right_arrow',
                    });
                } 
    };
})(jQuery);

