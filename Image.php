<?php
namespace Modules\Everdio {
    class Image extends \Modules\Everdio\Library\ECms\Image {
        public function save() : \Components\Core\Adapter\Mapper {
            $this->ImageSlug = $this->slug($this->Image);
            return (object) parent::save();
        }
    }
}