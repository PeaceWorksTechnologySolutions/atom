<?php

/*
 */

class sfEcommerceTaxes 
{

  public static function determine_taxes($sale, $saleResource, $repo)
  {
    $taxes = array();

    /* Applicable taxes could depend on:
      * which repository is making the sale
      * the country and/or region of the customer

      Check legal requirements with your bookkeeper or tax consultant.

      Then implement appropriate logic for your situation.
      Note that the sample code below is INCOMPLETE.

      Append applicable taxes and rates to the $taxes array
      and return it.  
    */

    /*
    $hst_provinces = array(
      'New Brunswick' => '13',
      'Newfoundland and Labrador' => '13',
      'Nova Scotia' => '15',
      'Ontario' => '13',
      'Prince Edward Island' => '14',
    );

    $repository = $repo->slug;

    $supply_country = $sale->country;
    if ($supply_country != 'CA') {
      // no applicable taxes if customer is outside Canada
      return $taxes;
    }
    $supply_province = $sale->province;

    if ($repository == 'test123') {
      if (array_key_exists($supply_province, $hst_provinces)) {
        $taxes['HST'] = $hst_provinces[$supply_province];
      } else {
        $taxes['GST'] = '5';
      }
    } */
    return $taxes;
  }
}
