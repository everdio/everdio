<?php
namespace Modules\Everdio {
    use \Components\Validation;
    use \Components\Validator; 
    use \Modules\Everdio\Library\ECms;
    abstract class Frontend extends \Components\Core\Controller\Model\Browser {
        public function __construct(array $server, array $request, \Components\Parser $parser) {
            parent::__construct($server, $request, $parser);
            $this->add("environment", new Validation(false, [new Validator\IsString]));
            $this->add("document", new Validation(false, [new Validator\IsString]));
            $this->add("locale", new Validation(false, [new Validator\IsString]));
        }
        
        public function display(string $path) : string {
            $environment = new Environment($this->host);
            if (!isset($this->arguments)) {
                $this->arguments = explode(DIRECTORY_SEPARATOR, $environment->Arguments);
            }

            $environment->scheme($this->scheme, implode(DIRECTORY_SEPARATOR, $this->arguments));
            $environment->redirect($this->arguments);
            
            $this->environment = $environment->export($environment->mapping);
            

            $locale_environment = new ECms\EnvironmentLocale(["EnvironmentId" => $environment->EnvironmentId]);
            $locale_environment->find();

            $locale = new ECms\Locale(["LocaleId" => $locale_environment->LocaleId]);
            $locale->find();
            
            $this->locale = $locale->export($locale->mapping);
            
            setlocale(LC_ALL, $locale->Locale);
            
            try {
                $document = new Documents($environment, implode(DIRECTORY_SEPARATOR, $this->arguments));
                if (isset($document->DocumentId)) {
                    $this->document = $document->export($document->mapping);
                    http_response_code(200);
                } else {
                    $document = new Documents($environment, $environment->PageNotFound);    
                    $document->Content = sprintf($document->Content, $environment->Scheme, $environment->Host, implode(DIRECTORY_SEPARATOR, $this->arguments));
                    $this->document = $document->export($document->mapping);
                    http_response_code(404);        
                }    
            } catch (\Components\Event $event) {
                http_response_code(500);
                $document = new Documents($environment, $environment->PageEvent);    
                $document->Content = sprintf($document->Content, $event->getMessage(), $event->getTraceAsString());
                $this->document = $document->export($document->mapping);
            }
            
            return (string) $document->display($this->execute($path . DIRECTORY_SEPARATOR . $document->Route));              
        }
    }
}
