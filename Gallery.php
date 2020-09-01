<?php
namespace Modules\Everdio {
    class Gallery extends \Modules\Everdio\Library\ECms\Gallery {
        public function save() {
            $this->GallerySlug = $this->slug($this->Gallery);
            parent::save();
        }        
        
        public function delete() {
            if (isset($this->GalleryId)) {
                foreach(Library\ECms\ImageFileGallery::construct(["GalleryId" => $this->GalleryId, "Order" => false])->findAll() as $row) {
                    $imagefilegallery = new Library\ECms\ImageFileGallery($row);
                    $imagefilegallery->find();
                    
                    if (isset($imagefilegallery->ImageFileId)) {
                        $imagefile = new Library\ECms\ImageFile;
                        $imagefile->reset($imagefile->mapping);
                        $imagefile->ImageFileId = $imagefilegallery->ImageFileId;
                        $imagefile->find();
                        
                        if (isset($imagefile->FileId)) {
                            $file = new Library\ECms\File;
                            $file->reset($file->mapping);
                            $file->FileId = $imagefile->FileId;
                            $file->find(); 
                            $file->delete();
                        }
                        
                        $imagefile->delete();
                    }
                    
                    $imagefilegallery->delete();
                }

                parent::delete();
            }
        }
    }
}