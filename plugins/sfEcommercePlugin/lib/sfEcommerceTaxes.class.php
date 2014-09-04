<?php

/*
 */

class sfEcommerceTaxes 
{
  public static $hst_provinces = array(
    'New Brunswick' => '13',
    'Newfoundland and Labrador' => '13',
    'Nova Scotia' => '15',
    'Ontario' => '13',
    'Prince Edward Island' => '14',
  );

  public static function determine_taxes($sale, $saleResource, $repo)
  {
    $taxes = array();

    if ($repo->slug == 'jasonarc') {
      $repo_prov = 'Manitoba';
    } else if ($repo->slug == 'jasonarc-2') {
      $repo_prov = 'Ontario';
    }

    $supply_country = $sale->country;
    if ($supply_country != 'CA') {
      return $taxes;
    }
    $supply_province = $sale->province;

    if ($repo_prov == 'Ontario') {
      if (array_key_exists($supply_province, self::$hst_provinces)) {
        $hstprovs = sfEcommerceTaxes::$hst_provinces;
        $taxes['HST'] = $hstprovs[$supply_province];
      } else {
        $taxes['GST'] = '5';
      }
    } elseif ($repo_prov == 'Manitoba') {
      $taxes['GST'] = '5';
      if ($supply_province == 'Manitoba') {
        $taxes['PST'] = '8';
      }
    }
    return $taxes;
  }
}
