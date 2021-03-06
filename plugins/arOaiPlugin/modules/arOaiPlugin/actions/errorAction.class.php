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
 * Generate the OAI-PMH response
 *
 * @package    AccesstoMemory
 * @subpackage oai
 * @author     Mathieu Fortin Library and Archives Canada <mathieu.fortin@lac-bac.gc.ca>
 */
class arOaiPluginErrorAction extends sfAction
{
  /**
   * Executes action
   *
   * @param sfRequest $request A request object
   */
  public function execute($request)
  {
    $request->setRequestFormat('xml');
    $this->date = QubitOai::getDate();
    $this->path = $this->request->getUriPrefix().$this->request->getPathInfo();
    $this->attributes = $this->request->getGetParameters();

    $this->attributesKeys = array_keys($this->attributes);
    $this->requestAttributes = '';
    foreach ($this->attributesKeys as $key)
    {
      $this->requestAttributes .= ' '.$key.'="'.$this->attributes[$key].'"';
    }
    $this->sets = array();

    foreach (QubitInformationObject::getCollections() as $el)
    {
      $this->sets[] = new sfIsadPlugin($el);
    }
    if (isset($this->request->verb))
    {
      switch($this->request->verb)
      {
        case 'Identify':
          $this->verb = 'identify';
          break;
        case 'ListMetadataFormats':
          $this->verb = 'listMetadataFormats';
          break;
        case 'ListSets':
          $this->verb = 'listSets';
          break;
        case 'ListRecords':
          $this->verb = 'listRecords';
          break;
        case 'ListIdentifiers':
          $this->verb = 'listIdentifiers';
          break;
        case 'GetRecord':
          $this->verb = 'getRecord';
          break;
        default:
          $this->verb = 'badVerb';
      }
    } else
    {
      $this->verb = 'badVerb';
    }
  }
}
