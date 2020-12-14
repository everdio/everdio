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
            
            if (in_array(strtolower($file->Extension), \Modules\Image\Jpeg::IMAGE_EXTENSION)) {
                $input = new \Modules\Image\Jpeg;
            } elseif (in_array(strtolower($file->Extension), \Modules\Image\Png::IMAGE_EXTENSION)) {
                $input = new \Modules\Image\Png;
            } elseif (in_array(strtolower($file->Extension), \Modules\Image\Gif::IMAGE_EXTENSION)) {
                $input = new \Modules\Image\Gif;
            } elseif (\in_array(strtolower($file->Extension), \Modules\Image\Webp::IMAGE_EXTENSION)) {
                $input = new \Modules\Image\Webp;
            } else {
                throw new \LogicException(sprintf("unknown input extension %s", $file->File));
            }

            if (in_array(strtolower($image->Output), \Modules\Image\Jpeg::IMAGE_EXTENSION)) {
                $output = new \Modules\Image\Jpeg;
            } elseif (in_array(strtolower($image->Output), \Modules\Image\Png::IMAGE_EXTENSION)) {
                $output = new \Modules\Image\Png;
            } elseif (in_array(strtolower($image->Output), \Modules\Image\Gif::IMAGE_EXTENSION)) {
                $output = new \Modules\Image\Gif;
            } elseif (\in_array(strtolower($image->Output), \Modules\Image\Webp::IMAGE_EXTENSION)) {
                $output = new \Modules\Image\Webp;
            } else {
                throw new \LogicException(sprintf("unknown output extension %s", $file->File));
            }

            $path = new \Components\Path($environment->RootPath . DIRECTORY_SEPARATOR . $environment->MainPath . $environment->WwwPath . $image->ImagePath);
            $imagefile = new \Components\File(sprintf("%s/%s%s.%s", $path->getPath(), $image->Prefix, $this->slug($this->Content), $image->Output), "w");

            if ($imagefile->isWritable()) {
                $input->input($file->File);
                //rotating image if set
                if (isset($this->Rotate)) {
                    $input->rotate($this->Rotate);
                }
                //resizing image
                $input->resize((in_array("width", $image->Resample) ? $image->Width : false), (in_array("height", $image->Resample) ? $image->Height : false));                    
                //scale image if set
                if ($image->Scale) {
                    $input->scale($image->Scale);
                }
                //crop image if set
                if ($image->Width && $image->Height) {
                    $input->crop($image->Width, $image->Height, $this->Top, $this->Left);
                }
                //create image based on $output
                if ($output->export($input, $imagefile, $image->Compression)) {                        
                    $this->Source = $environment->Scheme . $environment->Host . $image->ImagePath . DIRECTORY_SEPARATOR . $imagefile->getBasename();
                    $this->Content = $this->sanitize($this->Content);
                    $this->save();
                    //done
                    return (string) sprintf("%s (%sx%s %s)", $this->Source, $image->Width, $image->Height, $this->formatsize($imagefile->getSize())); 
                } else {
                    throw new \LogicException(sprintf("export failed %s", $file->File));
                }                    
            } else {
                throw new \LogicException(sprintf("invalid file permission %s", $path->getPath()));
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