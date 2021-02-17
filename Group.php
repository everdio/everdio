<?php
namespace Modules\Everdio {
    class Group extends \Modules\Everdio\Library\ECms\Group {
        public function save() : \Components\Core\Adapter\Mapper {
            $this->GroupSlug = $this->slug($this->Group);
            return (object) parent::save();
        }
    }
}