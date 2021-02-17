<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class File extends ECms\File {
        public function save() : \Components\Core\Adapter\Mapper {
            try {
                $file = new \Components\File($this->File, "r");
                $this->Size = $file->getSize();
                $this->Basename = $file->getBasename("." . $file->getExtension());
                $this->Extension = $file->getExtension();        
                $this->Size = $file->getSize();
                return (object) parent::save();           
            } catch (Exception $ex) {
                throw new \LogicException(sprintf("file does not exist %s", $ex->getMessage));
            }
        }
        
        public function fetch(string $url) {
            $curl = new \Components\Core\Caller\Curl;
            $curl->setopt_array([CURLOPT_FOLLOWLOCATION => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $url]);

            $response = new \Components\File($this->File, "w");
            $response->store($curl->execute());          
            
            $this->save();
        }
        
        public function update() {
            try {
                $info = new \Components\File($this->File, "r");
                $this->Size = $info->getSize();
                $this->Basename = $info->getBasename("." . $info->getExtension());
                $this->Extension = $info->getExtension();        
                $this->Size = $info->getSize();
                $this->save();            
            } catch(\Exception $ex) {
                $imagefile = new ImageFile;
                $imagefile->reset($imagefile->mapping);
                $imagefile->FileId = $this->FileId;
                $imagefile->find();                
                $imagefile->delete();
                
                $this->delete();
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