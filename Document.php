<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class Document extends \Modules\Everdio\Library\ECms\Document {
        public function save() {
            if (!isset($this->DocumentId)) {
                $document = new $this;
                $document->store($this->restore(["EnvironmentId"]));
                $document->DocumentSlug = $this->slug($this->Document);
                $results = $document->findAll();
                $this->DocumentSlug = (sizeof($results) ? sprintf("%s-%s", $this->slug($this->Document), sizeof($results) + 1) : $this->slug($this->Document));
            } else {
                $this->DocumentSlug = $this->slug($this->Document);
            }
            
            if (isset($this->Content)) {
                $this->Description = implode(". ", $this->str_limit(explode(". ", strip_tags($this->Content)), 30, 160)) . ".";                
                $this->Keywords = implode(",", $this->str_limit(array_reverse((array) str_word_count(strip_tags($this->Content), 2)), 6, 200));
            }            
            
            if (empty($this->Order)) {
                $this->Order = count(ECms\Document::construct(array("EnvironmentId" => $this->EnvironmentId, "ParentId" => (!empty($this->ParentId) ? $this->ParentId : 0)))->findAll()) + 1;
            }         
            
            parent::save();
        }
    }
}

