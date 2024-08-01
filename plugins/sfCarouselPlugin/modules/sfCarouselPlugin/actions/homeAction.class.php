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

class SfCarouselPluginHomeAction extends DefaultBrowseAction
{
  public function execute($request)
  {
    $this->total = 100;

    // Set up StaticPage
    // copied from StaticPageIndexAction (we inherit from DefaultBrowseAction instead).
    $this->resource = $this->getRoute()->resource;
    $this->content = $this->getStaticPageContent();
    

    // Set up query for recently updated photos

    // Force number of hits per page
    $request->limit = 30;

    parent::execute($request);

    $this->response->setTitle(sfConfig::get('app_siteTitle'));

    // Create query object
    $this->queryBool = new \Elastica\Query\BoolQuery;
    $this->queryBool->addMust(new \Elastica\Query\Term(array('hasDigitalObject' => true)));

    // Set sort and limit
    $this->query = new \Elastica\Query();
    $this->query->setSort(array('updatedAt' => 'desc'));

    $this->query->setQuery($this->queryBool);

    // Filter drafts
    QubitAclSearch::filterDrafts($this->queryBool);

    $resultSet = QubitSearch::getInstance()->index->getType('QubitInformationObject')->search($this->query);
    $this->results = $resultSet->getResults();
  }


  // copied from StaticPageIndexAction::getPurifiedStaticPageContent
  protected function getStaticPageContent()
  {
    $culture = sfContext::getInstance()->getUser()->getCulture();
    $cacheKey = 'staticpage:'.$this->resource->id.':'.$culture;
    $cache = QubitCache::getInstance();

    if (null === $cache)
    {
      return;
    }

    if ($cache->has($cacheKey))
    {
      return $cache->get($cacheKey);
    }

    $content = $this->resource->getContent(array('cultureFallback' => true));
    //$content = QubitHtmlPurifier::getInstance()->purify($content);
    sfContext::getInstance()->getLogger()->warning('staticpage indexaction');


    $cache->set($cacheKey, $content);

    return $content;
  }





}
