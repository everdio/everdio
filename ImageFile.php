<?php
namespace Modules\Everdio {
    class ImageFile extends \Modules\Everdio\Library\ECms\ImageFile {
        public function generate() : string {
    
            $environment = new Library\ECms\Environment;
            $environment->reset($environment->mapping);
            $environment->EnvironmentId = $this->EnvironmentId;
            $environment->find();
            
            $image = new Image;
            $image->ImageId = $this->ImageId;
            $image->find();

            $file = new File;
            $file->FileId = $this->FileId;
            $file->find();
            
            if (isset($file->FileId) && isset($environment->EnvironmentId)) {
                $path = new \Components\Path($environment->RootPath . DIRECTORY_SEPARATOR . $environment->MainPath . $environment->WwwPath . $image->ImagePath);
                $imagefile = new \Components\File(sprintf("%s/%s%s.%s", $path->getPath(), $image->Prefix, $this->slug($this->Content), $image->Output), "w");
                
                if ($imagefile->isWritable() && in_array(strtolower($file->Extension), $image->Input)) {
                    switch (strtolower($file->Extension)) {
                        case "jpg":
                        case "jpeg":                
                            $input = new \Modules\Image\Jpeg;
                            break;
                        case "png":
                            $input = new \Modules\Image\Png;
                            break;
                        case "gif":
                            $input = new \Modules\Image\Gif;
                            break;
                        case "webp":
                            $input = new \Modules\Image\Webp;
                            break;
                        default:
                            throw new Event(sprintf("unknown input %s", strtolower($file->Extension)));
                    }
                    
                    $input->input($file->File);

                    if (isset($this->Rotate)) {
                        $input->rotate($this->Rotate);
                    }

                    $input->resize((in_array("width", $image->Resample) ? $image->Width : false), (in_array("height", $image->Resample) ? $image->Height : false));

                    if ($image->Scale) {
                        $input->scale($image->Scale);
                    }
                    if ($image->Width && $image->Height) {
                        $input->crop($image->Width, $image->Height, $this->Top, $this->Left);
                    }
                    
                    switch ($image->Output) {
                        case "jpg":
                        case "jpeg":                
                            $output = new \Modules\Image\Jpeg;
                            break;
                        case "png":
                            $output = new \Modules\Image\Png;
                            break;
                        case "gif":
                            $output = new \Modules\Image\Gif;
                            break;
                        case "webp":
                            $output = new \Modules\Image\Webp;
                            break;
                        default:
                            throw new Event(sprintf("unknown output for %s", strtolower($file->Extension)));
                    }
                    
                    if ($output->export($input, $imagefile, $image->Compression)) {
                        $this->Source = $environment->Scheme . $environment->Host . $image->ImagePath . DIRECTORY_SEPARATOR . $imagefile->getBasename();
                        $this->Content = $this->sanitize($this->Content);
                        $this->save();
                        
                        return (string) sprintf("%s (%sx%s %s)", $this->Source, $image->Width, $image->Height, $this->formatsize($imagefile->getSize())); 
                    } else {
                        throw new Event(sprintf("export failed %s", $file->File));
                    }
                } else {
                    throw new Event(sprintf("invalid input %s: %s", $file->File, $this->dehydrate($image->Input)));
                }
            } else {
                throw new Event(sprintf("invalid file %s", $file->File));
            }
        }
        
        public function delete() {
            if (isset($this->ImageFileId)) {
                $imagefilegallery = new Library\ECms\ImageFileGallery;
                $imagefilegallery->reset($imagefilegallery->mapping);
                $imagefilegallery->ImageFileId = $this->ImageFileId;
                $imagefilegallery->delete();           
            }
            
            parent::delete();
        }
    }
}