<?php
namespace Modules\Everdio {
    class Regional extends \Modules\Everdio\Library\ECms\Regional {
        public function save() : \Components\Core\Adapter\Mapper {
            $this->RegionalSlug = $this->slug($this->Regional);
            return (object) parent::save();
        }
    }
}