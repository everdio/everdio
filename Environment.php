<?php
namespace Modules\Everdio {
    class Environment extends \Modules\Everdio\Library\ECms\Environment {
        public function save() : \Components\Core\Adapter\Mapper {
            if (!isset($this->EnvironmentId)) {
                $this->EnvironmentSlug = $this->slug($this->environment);
            }            
            return (object) parent::save();
        }        
     }
}