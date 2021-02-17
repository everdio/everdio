<?php
namespace Modules\Everdio {
    class Account extends \Modules\Everdio\Library\ECms\Account {
        public function save() : \Components\Core\Adapter\Mapper {
            if (isset($this->Realm) && isset($this->AccountId) && isset($this->Password)) {
                $this->Password =  md5(sprintf("%s:%s:%s", $this->Account, $this->Realm, $this->Password));
                return (object) parent::save();
            }
        }
    }
}