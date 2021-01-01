<?php
namespace Modules\Everdio {
    class Category extends \Modules\Everdio\Library\ECms\Category {
        public function save() {
            $this->CategorySlug = $this->slug($this->Category);
            parent::save();
        }
    }
}