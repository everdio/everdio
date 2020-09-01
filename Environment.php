<?php
namespace Modules\Everdio {
    class Environment extends \Modules\Everdio\Library\ECms\Environment {
        public function __construct(string $host) {
            parent::__construct();            
            unset ($this->Extension);
            $this->Host = $host;
            $this->Status = "active";
            $this->find();
        }
        
        public function save() {
            if (!isset($this->EnvironmentId)) {
                $this->EnvironmentSlug = $this->slug($this->environment);
            }            
            parent::save();
        }        
        
        public function scheme(string $scheme, string $arguments) {
            if ($this->Scheme !== "//" && $this->Scheme !== $scheme) {
                die(header("Location:" . $this->Scheme . $this->Host . DIRECTORY_SEPARATOR . $arguments, true, 301));
            }                
        }
        
        public function redirect(array $arguments) {
            $redirect = new Library\ECms\Redirect;
            $redirect->reset($redirect->mapping);
            $redirect->Redirect = $this->Scheme . $this->Host . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arguments);
            $redirect->find();
            if (isset($redirect->RedirectId)) {        
                $redirect->Hits = $redirect->Hits + 1;
                $redirect->save();
                die(header("Location:" . $redirect->Routing, true, $redirect->Status));
            }               
        }
    }
}