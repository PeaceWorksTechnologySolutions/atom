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

class sfEcommercePluginDownloadAction extends sfAction
{
  public function execute($request)
  {
    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, $request->getParameter('id'));
    $this->resource = QubitObject::get($criteria)->__get(0);

    $hash = $request->getParameter('hash');
    if ($hash != $this->resource->hash()) {
      throw new sfError404Exception();
    }
    $this->hash = $hash;

    $age = time() - strtotime($this->resource->lastProcessedAt);
    sfContext::getInstance()->getLogger()->warning($age);
    if ($this->resource->processingStatus == 'processed' && $age > 24 * 3600 * 10) {
      throw new sfError404Exception();
    }

    $this->resources = array();
    foreach ($this->resource->saleResources as $saleResource) {
      // only photos which have been accepted should be made available to download.
      if ($saleResource->processingStatus == 'accepted') {
        $this->resources[] = $saleResource->resource;
      }
    }

    if ($request->getParameter('photo')) {
      $this->serve_photo($request->getParameter('photo'));
    } elseif ($request->getParameter('zip')) {
      $this->serve_zip(); 
    }
  }

  public function serve_photo($resource_id)
  {
    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, $resource_id);
    $photo = QubitObject::get($criteria)->__get(0);

    $filepath = $photo->digitalObjects[0]->getAbsolutePath();
    $file = basename($filepath);
    header('Content-Description: File Transfer');
    header("Content-type:  application/octet-stream");
    header("Content-disposition: attachment; filename= ".$file."");
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
  }

  public function serve_zip()
  {
    // Prepare File
    $zipfile = tempnam("tmp", "zip");
    $zip = new ZipArchive();
    $zip->open($zipfile, ZipArchive::OVERWRITE);

    // Stuff with content
    foreach ($this->resources as $photo) {
      $filepath = $photo->digitalObjects[0]->getAbsolutePath();
      $file = basename($filepath);
      $zip->addFile($filepath, $file);
    }

    // Close and send to users
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($zipfile));
    header('Content-Disposition: attachment; filename="photos.zip"'); // FIXME - choose better name
    readfile($zipfile);
    unlink($zipfile);
  }

}
