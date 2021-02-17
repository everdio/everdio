<?php
namespace Modules\Everdio {
    use \Components\Validation;
    use \Components\Validator; 
    abstract class Frontend extends \Components\Core\Controller\Model\Browser {
        public function __construct(array $server, array $request, \Components\Parser $parser) {
            parent::__construct($server, $request, $parser);
            $this->add("environment", new Validation(false, [new Validator\IsString]));
            $this->add("document", new Validation(false, [new Validator\IsString]));
            $this->add("templates", new Validation(false, [new Validator\IsArray]));
        }
        
        public function display(string $path) : string {
            //fetching settings from db based on current host
            $environment = new Library\ECms\Environment;
            unset ($environment->Extension);
            unset ($environment->Arguments);
            unset ($environment->Scheme);
            $environment->Host = $this->host;
            $environment->Status = "active";
            $environment->find();
            
            if ($environment->isStrict()) {
                if (!isset($this->arguments)) {
                    $this->arguments = ["index.html"];
                }
                
                //scheme redirect
                if ($environment->Scheme !== "//" && $environment->Scheme !== $this->scheme) {
                    die(header("Location:" . $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . implode(\DIRECTORY_SEPARATOR, $this->arguments), true, 301));
                }             

                //page redirect
                $redirect = new Library\ECms\Redirect;
                $redirect->reset($redirect->mapping);
                $redirect->Redirect = $environment->Scheme . $environment->Host . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $this->arguments);
                $redirect->find();
                if (isset($redirect->RedirectId)) {        
                    $redirect->Hits = $redirect->Hits + 1;
                    $redirect->save();
                    die(header("Location:" . $redirect->Routing, true, $redirect->Status));
                }           

                
                
                try {
                    
                    $document = new Documents($environment, implode(DIRECTORY_SEPARATOR, $this->arguments));
                    
                    
                    if (isset($document->DocumentId)) {
                        setlocale(LC_ALL, $document->Locale);
                        $this->document = $document->export($document->mapping);
                        http_response_code(200);
                    } else {
                        $document = new Documents($environment, $environment->PageNotFound);    
                        $document->Content = sprintf($document->Content, $environment->Scheme, $environment->Host, implode(DIRECTORY_SEPARATOR, $this->arguments));
                        $this->document = $document->export($document->mapping);
                        http_response_code(404);        
                    }    
                } catch (\RuntimeException $event) {
                    http_response_code(500);
                    $document = new Documents($environment, $environment->PageEvent);    
                    $document->Content = sprintf($document->Content, $event->getMessage(), $event->getTraceAsString());
                    $this->document = $document->export($document->mapping);
                }              
                
                
                //displaying one of the 3 above, error, not found or a page
                return (string) $document->display($this->execute($path . DIRECTORY_SEPARATOR . $document->Route));                    
            } else {
                http_response_code(500);
                return (string) "something isn't ok right now and I am not sure if it's you or me? Let's ask my creator via the log files ...";
            }
        }
    }
}
