<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class Document extends \Modules\Everdio\Library\ECms\Document {
        public function save() : \Components\Core\Adapter\Mapper{
            if (!isset($this->DocumentId)) {
                $document = new $this;
                $document->reset($document->mapping);
                $document->store($this->restore(["EnvironmentId"]));
                $document->DocumentSlug = $this->slug($this->Document);
                $count = $document->count();
                $this->DocumentSlug = (sizeof($count) ? sprintf("%s-%s", $this->slug($this->Document), sizeof($count) + 1) : $this->slug($this->Document));
            }
            
            if (!isset($this->DocumentSlug)) {
                $this->DocumentSlug = $this->slug($this->Document);
            }
                       
            if (isset($this->Content)) {
                $content = $this->Content;
                
                if (isset($this->DocumentId)) {
                    $document = new $this;
                    $document->reset($document->mapping);
                    $document->ParentId = $this->DocumentId;
                    $document->store($this->restore(["EnvironmentId"]));
                    foreach ($document->findAll() as $row) {
                        $document = new $this($row);
                        if (isset($document->Content)) {
                            //$content .= $document->Content;
                        }
                    }
                }
                
                $this->Description = implode(". ", $this->str_limit(explode(". ", strip_tags($content)), 30, 160)) . ". ";                
                $this->Keywords = implode(",", $this->str_limit(array_reverse((array) str_word_count(strip_tags($content), 2)), 6, 200));
            }            
            
            if (empty($this->Order)) {
                $this->Order = count(ECms\Document::construct(array("EnvironmentId" => $this->EnvironmentId, "ParentId" => (!empty($this->ParentId) ? $this->ParentId : 0)))->findAll()) + 1;
            }         
            
            return (object) parent::save();
        }
    }
}

