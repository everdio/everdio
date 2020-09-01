<?php
namespace Modules\Everdio {
    class Account extends \Modules\Everdio\Library\ECms\Account {
        public function savePassword(string $realm, string $password) {
            if (isset($this->AccountId)) {
                $this->Password =  md5(sprintf("%s:%s:%s", $this->Account, $realm, $password));
                parent::save();
            }
        }
    }
}