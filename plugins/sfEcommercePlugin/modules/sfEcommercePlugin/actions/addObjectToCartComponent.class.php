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

class sfEcommercePluginAddObjectToCartComponent extends sfComponent
{
  public function execute($request)
  {
    $this->may_disseminate = $this->right_is_allowed(true);
    sfContext::getInstance()->getLogger()->warning('foobar');
  }

  public function right_is_allowed($default) 
  {
    $ancestors = $this->resource->ancestors->andSelf()->orderBy('rgt');
    foreach ($ancestors as $item) {
        foreach ($item->getRights() as $right) {
            $right_obj = $right->object;
            if ($right_obj->actId == QubitTerm::RIGHT_ACT_DISSEMINATE_ID) {
                return $right_obj->restriction;
            }
        }
    }
    return $default;
  }
}
