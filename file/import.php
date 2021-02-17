<?php
use \Modules\Everdio;

$environment = new Everdio\Environment;
$environment->reset($environment->mapping);
$environment->EnvironmentSlug = $this->request->environment;
$environment->find();

if (isset($environment->EnvironmentId)) {
    $path = new \Components\Path($environment->RootPath . \DIRECTORY_SEPARATOR . $environment->MainPath . $environment->WwwPath . $environment->UploadPath);
    foreach ($path as $object) {
        if ($object->isFile()) {
            $file = new Everdio\File;
            $file->File = $object->getRealPath();
            $file->find();
            if (!isset($file->FileId)) {
                $file->save();
                echo sprintf("file %s saved", $file->Basename) . PHP_EOL;
            }
        }
    }
}