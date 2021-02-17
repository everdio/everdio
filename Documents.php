<?php
namespace Modules\Everdio {
    use \Modules\Everdio\Library\ECms;
    class Documents extends \Modules\Everdio\Library\ECms\Documents {
        public function __construct(Library\ECms\Environment $environment, string $arguments) {
            parent::__construct();
            $this->reset($this->mapping);            
            $this->EnvironmentId = $environment->EnvironmentId;
            $this->Routing = $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . $arguments;                        
            $this->find();            
        }
        
        public function display(string $template) {
            $tpl = new \Components\Core\Template;

            $properties = new ECms\Properties;
            $properties->DocumentId = $this->DocumentId;
            $properties->EnvironmentId = $this->EnvironmentId;

            foreach ($properties->findAll() as $row) {
                $property = new ECms\Properties($row);
                $tpl->{$property->Property} = $property->Content;                        
            }   
            
            $translations = new ECms\Translations;
            $translations->LanguageId = $this->LanguageId;
            $translations->EnvironmentId = $this->EnvironmentId;
            foreach ($translations->findAll() as $row) {
                $translation = new ECms\Translations($row);
                $tpl->{$translation->Property} = $translation->Content;                        
            }
            
            return (string) $tpl->display($template);
        }     
    }
}


