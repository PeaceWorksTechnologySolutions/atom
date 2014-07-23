(function ($)
  {
    Drupal.behaviors.sfEcommerceCountrySubdivisions = {
      attach: function (context)
        {
          country_fields = $(context).find('select[name="country"]').change(Drupal.behaviors.sfEcommerceCountrySubdivisions.update);
        },
      update: function (event) 
        {
          var selected_country_code = $(event.target).val();
          var loc = new String(document.location);
          var url = loc.substring( 0, loc.lastIndexOf( "/" ) + 1);
          url += 'countrySubdivisions/id/' + selected_country_code;
          $.ajax({
            dataType: 'json',
            timeout: 20000,
            type: 'GET',
            url: url,
            success: function(data)
              {
                  var province_field = $(event.target).closest('form').find('select[name="province"]');
                  $(province).find('option:gt(0)').remove();
                  for (var i=0; i < data.length; i+=1) {
                    $(province_field).append($("<option></option>").attr("value", data[i]).text(data[i]));
                  }
              }
          });
        },
    }
  })
(jQuery);

