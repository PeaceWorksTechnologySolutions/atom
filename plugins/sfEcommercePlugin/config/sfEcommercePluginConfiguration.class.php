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

class sfEcommercePluginConfiguration extends sfPluginConfiguration
{
  public static
    $summary = 'Enables E-commerce functionality allowing users to purchase digital photos online with payment via PayPal.',
    $version = '0.1.0';

  /**
   * @see sfPluginConfiguration
   */

  public function contextLoadFactories(sfEvent $event)
  {
    $this->create_menu();
    sfContext::getInstance()->response->addStylesheet('/plugins/sfEcommercePlugin/css/cart.css');
  }

  public function setup() // loads handler if needed
  {
    if ($this->configuration instanceof sfApplicationConfiguration)
    {
      $configCache = $this->configuration->getConfigCache();
      $configCache->registerConfigHandler('config/ecommerce.yml', 'sfDefineEnvironmentConfigHandler',
        array('prefix' => 'ecommerce_'));
      $configCache->checkConfig('config/ecommerce.yml');
    }
  }

  public function initialize()
  {
    $enabledModules = sfConfig::get('sf_enabled_modules');
    $enabledModules[] = 'sfEcommercePlugin';
    sfConfig::set('sf_enabled_modules', $enabledModules);

    if ($this->configuration instanceof sfApplicationConfiguration)
    {
      $configCache = $this->configuration->getConfigCache();
      include($configCache->checkConfig('config/ecommerce.yml'));
    }
    $this->dispatcher->connect('context.load_factories', array($this, 'contextLoadFactories'));
  }


  public function create_menu() 
  {
    $criteria = new Criteria;
    $criteria->add(QubitMenu::NAME, 'sfEcommerceUserSettings');
    $menu = QubitMenu::getOne($criteria);
    if (!isset($menu)) {
      $criteria = new Criteria;
      $criteria->add(QubitMenu::NAME, 'users');
      $this->userAclMenu = null;
      if (null !== $parent = QubitMenu::getOne($criteria))
      {
        $menu = new QubitMenu;
        $menu['parentId'] = $parent->getId();
        $menu['label'] = 'Ecommerce Settings';
        $menu['name'] = 'sfEcommerceUserSettings';
        $menu['path'] = 'sfEcommercePlugin/indexUserSettings?slug=%currentSlug%';
        $menu->save();
      }
    }
  }

}
