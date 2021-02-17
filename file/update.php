<?php
use \Modules\Everdio;

foreach (Everdio\File::construct()->findAll() as $row) {
    $file = new Everdio\File($row);
    $file->update();
    if (!isset($file->File)) {
        echo sprintf("removed %s", $row["File"]) . PHP_EOL;
        ob_flush();
    }
}

