<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class Routing extends \Modules\Everdio\Library\ECms\Routing {
        public function save() : \Components\Core\Adapter\Mapper {
            $values = $this->restore($this->mapping);
            
            $this->reset($this->mapping);
            
            $this->LocaleId = $values["LocaleId"];
            $this->EnvironmentId = $values["EnvironmentId"];
            $this->ExecuteId = $values["ExecuteId"];
            $this->DocumentId = $values["DocumentId"];
            $this->find();
            
            $routing = (isset($this->Routing) ? $this->Routing : false);
            
            $this->reset($this->mapping);
            $this->store($values);
            
            $environment = new ECms\Environment;
            $environment->EnvironmentId = $this->EnvironmentId;
            unset ($environment->Status);
            $environment->find();

            $environment_locale = new ECms\EnvironmentLocale;
            $environment_locale->EnvironmentId = $this->EnvironmentId;
            $environment_locale->LocaleId = $this->LocaleId;
            $environment_locale->find();

            if (!isset($this->Routing)) {
                $document = new ECms\Document;
                $document->DocumentId = $this->DocumentId;
                $document->find();
                
                if ($document->ParentId !== 0) {                
                    $parent = new ECms\Routing;
                    $parent->LocaleId = $this->LocaleId;
                    $parent->EnvironmentId = $this->EnvironmentId;
                    $parent->DocumentId = $document->ParentId;
                    unset ($parent->Status);
                    unset ($parent->Display);                
                    $parent->find();
                    if (isset($parent->Routing)) {
                        $this->Routing = $environment_locale->Path . basename($parent->Routing, $environment->Extension) . DIRECTORY_SEPARATOR . $this->slug($document->Document) . $environment->Extension;                    
                    } else {
                        $this->Routing = $environment_locale->Path . $this->slug($document->Document) . $environment->Extension;
                    }
                } else {
                    $this->Routing = $environment_locale->Path . $this->slug($document->Document) . $environment->Extension;
                }
            }
            
            if ($routing && $routing !== $this->Routing) {
                
                $redirect = new ECms\Redirect;
                $redirect->Redirect = $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . $environment_locale->Path . $routing;
                $redirect->Routing = $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . $environment_locale->Path . $this->Routing;
                $redirect->Status = 302;
                $redirect->save();
                $redirect->reset($redirect->mapping);
                $redirect->Redirect = $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . $environment_locale->Path . $this->Routing;
                $redirect->delete();
            }            
    
            return (object) parent::save();
        }
    }
}

