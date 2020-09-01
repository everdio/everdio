<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class File extends ECms\File {
        public function save() {
            try {
                $file = new \Components\File($this->File, "r");
                $this->Size = $file->getSize();
                $this->Basename = $file->getBasename("." . $file->getExtension());
                $this->Extension = $file->getExtension();        
                $this->Size = $file->getSize();
                parent::save();           
            } catch (Exception $ex) {
                throw new Event("file doesn't exist");
            }
        }
        
        public function update() {
            try {
                $info = new \Components\File($this->File, "r");
                $this->Size = $info->getSize();
                $this->Basename = $info->getBasename("." . $info->getExtension());
                $this->Extension = $info->getExtension();        
                $this->Size = $info->getSize();
                $this->save();            
                return (bool) true;
            } catch(\Exception $ex) {
                $imagefile = new ImageFile;
                $imagefile->reset($imagefile->mapping);
                $imagefile->FileId = $this->FileId;
                $imagefile->find();                
                $imagefile->delete();
                
                $this->delete();
                
                return (bool) false;
            }            
        }
        
        public function delete() {
            if (isset($this->File)) {
                try {
                    $file = new \Components\File($this->File, "r");
                    $file->delete();                         
                } catch (\RuntimeException $ex) {
                    //we simply ignore this and delete it frmo the database
                }
            }

            parent::delete();
        }
    }
}