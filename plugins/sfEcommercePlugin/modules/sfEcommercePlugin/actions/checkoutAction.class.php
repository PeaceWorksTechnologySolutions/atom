<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Controller for editing repository information.
 *
 * @package    AccesstoMemory
 * @subpackage repository
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Jack Bates <jack@nottheoilrig.com>
 * @author     David Juhasz <david@artefactual.com>
 */

function sfe_array_get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}


class sfEcommercePluginCheckoutAction extends DefaultEditAction
{
  public static
    $NAMES = array(
      'firstName',
      'lastName',
      'address1',
      'address2',
      'city',
      'province',
      'postalCode',
      'country',
      'email',
      'phone',
      'non_commercial',
      );

  protected function earlyExecute()
  {
    $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
    $this->resource = new QubitSale;
  }

  public function logMessage($message, $priority = 'info')
  {
    if (isset($this->resource)) {
      $message = "Order " . $this->resource->getId() . " " . $message;
      $message = str_replace("\n", " ", $message);
    }
    parent::logMessage($message, $priority);
  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'firstName':
      case 'lastName':
      case 'address1':
      case 'city':
      case 'postalCode':
      case 'email':
        $this->form->setDefault($name, $this->resource[$name]);
        $this->form->setValidator($name, new sfValidatorString(array('required' => true), array('required' => $this->context->i18n->__('This field is required.'))));
        $this->form->setWidget($name, new sfWidgetFormInput);

        break;

      case 'address2':
      case 'phone':
        $this->form->setDefault($name, $this->resource[$name]);
        $this->form->setValidator($name, new sfValidatorString);
        $this->form->setWidget($name, new sfWidgetFormInput);
        break;

      case 'country':
        $this->form->setValidator('country', new sfValidatorI18nChoiceCountry);
        $this->form->setWidget('country', new sfWidgetFormI18nChoiceCountry(array('add_empty' => true, 'culture' => $this->context->user->getCulture())));
        $this->form->setDefault('country', sfe_array_get($this->resource, 'country', sfConfig::get("ecommerce_default_country")));
        break;

      case 'province':
        $country = sfe_array_get($this->resource, 'country', sfConfig::get("ecommerce_default_country"));
        $choices = array('' => 'Select...');
        foreach (sfEcommercePlugin::country_subdivisions($country) as $region) {
          $choices[$region] = $region;
        }
        $this->form->setDefault('province', $this->resource['province']);
        $this->form->setValidator('province', new sfValidatorString(array('required' => true), array('required' => $this->context->i18n->__('This field is required.'))));
        $this->form->setWidget('province', new sfWidgetFormSelect(array('choices' => $choices, 'multiple' => false)));
        break;

      case 'non_commercial':
        $this->form->setValidator($name, new sfValidatorBoolean(array('required' => true), array('required' => $this->context->i18n->__('This system only allows purchase of photos for non-commercial use.  If you require photos for commercial use, please contact the archives directly.'))));
        $this->form->setWidget($name, new sfWidgetFormInputCheckbox);
        $this->form->setDefault($name, false);
        break;

      default:

        return parent::addField($name);
    }
  }

  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'non_commercial':
        sfContext::getInstance()->getLogger()->warning('processField!');
        sfContext::getInstance()->getLogger()->warning($this->form->getValue('non_commercial'));

      default:
        return parent::processField($field);
    }
  }

  public function execute($request)
  {
    parent::execute($request);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());
      if ($this->form->isValid())
      {
        $this->processForm();

        $this->resource['processingStatus'] = 'pending_payment';

        $this->resource->save();

        $this->logMessage('Created order', 'notice');

        $cart_contents = $this->getUser()->getAttribute('cart_contents');
        $i = 0;
        foreach (sfEcommercePlugin::fetch_cart_resources($cart_contents) as $resource) {
            $repo = $resource->getRepository(array('inherit' => true));

            $sale_resource = new QubitSaleResource();
            $sale_resource->setSale($this->resource);
            $sale_resource->setResource($resource);
            $sale_resource->setRepository($resource->getRepository(array('inherit' => true)));
            $sale_resource->setPrice(sfEcommercePlugin::resource_price($resource));
            $sale_resource->setProcessingStatus('new');

            $sale_resource->save();
            $i += 1;
        }
        sfEcommercePlugin::set_applicable_taxes($this->resource);

        $this->logMessage("Created $i sale_resource records", 'notice');

        $my_sales = $this->getUser()->getAttribute('my_sales', NULL);
        if (empty($my_sales)) {
          $my_sales = array();
        }
        $my_sales[] = $this->resource->getId();
        $this->getUser()->setAttribute('my_sales', $my_sales);

        $this->redirect(array('module' => 'sfEcommercePlugin', 'action' => 'payment', 'id' => $this->resource->getId()));
      }
    } else {

        if (sfConfig::get("ecommerce_fill_checkout")) {
            $this->form->setDefaults(array(
                'firstName' => 'Jason',
                'lastName' => 'Hildebrand',
                'address1' => '198 Home Street',
                'city' => 'Winnipeg',
                'province' => 'Manitoba',
                'postalCode' => 'r3g 1x1',
                'country' => 'CA',
                'email' => 'jason@peaceworks.ca',
                'phone' => '204 775 1212',
                'non_commercial' => true,
            ));
        }
    }
  }
}
