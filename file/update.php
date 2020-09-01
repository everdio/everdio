<?php
use \Modules\Everdio;

foreach (Everdio\File::construct()->findAll() as $row) {
    $file = new Everdio\File($row);
    if (!$file->update()) {
        echo sprintf("removed %s", $row["File"]) . PHP_EOL;
        ob_flush();
    }
}

