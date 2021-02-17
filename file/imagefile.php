<?php
use \Modules\Everdio;
echo "generating images" . PHP_EOL;
ob_flush();

$imagefile = new Everdio\ImageFile;
$imagefile->reset($imagefile->mapping);
$imagefile->store($this->request->restore());

foreach ($imagefile->findAll() as $row) {
    if (!isset($this->request->quick) || (isset($this->request->quick) && empty($row["Source"]))) {
        try {
            $imagefile = new Everdio\ImageFile($row);
            echo $imagefile->generate() . PHP_EOL;
            ob_flush();
        } catch (\Exception $ex) {   
            echo $ex->getMessage() . PHP_EOL;
            ob_flush();
        }
    }
}
