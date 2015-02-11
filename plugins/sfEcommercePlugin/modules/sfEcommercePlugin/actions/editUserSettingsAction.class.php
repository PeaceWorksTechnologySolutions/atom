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


class sfEcommercePluginEditUserSettingsAction extends DefaultEditAction
{
  public static
    $NAMES = array(
      'repository',
      'vacationEnabled',
      'vacationMessage',
      );

  protected function earlyExecute()
  {
    $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);


    if (isset($this->getRoute()->resource))
    {
      $this->user = $this->getRoute()->resource;
      $this->is_own_record = ($this->user->id == $this->context->user->getAttribute('user_id'));
      $this->resource = $this->user->userEcommerceSettingss[0];
    }
    if (!isset($this->resource)) {
      $this->resource = new QubitUserEcommerceSettings;
      $this->is_own_record = FALSE;
    }
  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'repository':
        if (!$this->context->user->isAdministrator()) {
          return;
        }
        $this->form->setDefault('repository', $this->resource['repositoryId']);
        $this->form->setValidator('repository', new sfValidatorString);

        $choices = array();
        foreach (QubitRepository::getAll() as $item)
        {
          $choices[$item->getId()] = $item->__toString();
        }

        $this->form->setWidget('repository', new sfWidgetFormSelect(array('choices' => $choices)));

        break;

      case 'vacationEnabled':
        $this->form->setValidator($name, new sfValidatorBoolean());
        $this->form->setWidget($name, new sfWidgetFormInputCheckbox);
        if ($this->resource->vacationEnabled) {
          $this->form->setDefault($name, true);
        }
        break;

      case 'vacationMessage':
        $this->form->setDefault('vacationMessage', $this->resource->vacationMessage);
        $this->form->setValidator('vacationMessage', new sfValidatorString);
        $this->form->setWidget('vacationMessage', new sfWidgetFormTextarea);
        break;

      default:

        return parent::addField($name);
    }
  }

  protected function processField($field)
  {
    switch ($field->getName())
    {

      case 'repository':
        if ($this->context->user->isAdministrator()) {
          $this->resource->setRepository(QubitRepository::getById($this->form->getValue('repository')));
        }
        break;

      case 'vacationEnabled':
        if ($this->form->getValue('vacationEnabled') == 1) {
          $this->resource->setVacationEnabled(true);
        }
        break;

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
        $this->resource->setVacationEnabled(false); // initialize to false.  processField may set to true. 
        $this->processForm();

        $this->resource->setUser($this->user);
        $this->resource->save();

        $this->redirect(array('module' => 'sfEcommercePlugin', 'action' => 'indexUserSettings', 'slug' => $this->user->slug));
      }
    }
  }
}
