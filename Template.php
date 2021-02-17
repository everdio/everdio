<?php
namespace Modules\Everdio {
    class Template extends \Modules\Everdio\Library\ECms\Template {
        public function save() {
            $this->TemplateSlug = $this->slug($this->Template);
            parent::save();
        }
    }
}