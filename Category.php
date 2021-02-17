<?php
namespace Modules\Everdio {
    class Category extends \Modules\Everdio\Library\ECms\Category {
        public function save() : \Components\Core\Adapter\Mapper {
            $this->CategorySlug = $this->slug($this->Category);
            return (object) parent::save();
        }
    }
}