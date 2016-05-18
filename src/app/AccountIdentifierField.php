<?php
namespace groupcash\socialhours\app;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\model\AccountIdentifier;
use groupcash\socialhours\projections\AccountList;
use rtens\domin\delivery\web\fields\AutoCompleteField;
use rtens\domin\Parameter;
use watoki\reflect\type\ClassType;

class AccountIdentifierField extends AutoCompleteField {

    /** @var AccountList */
    private $accounts;

    /**
     * @param object|AccountList $accounts
     */
    public function __construct(AccountList $accounts) {
        $this->accounts = $accounts;
    }

    /**
     * @param Parameter $parameter
     * @return array With captions indexed by values
     */
    protected function getOptions(Parameter $parameter) {
        $options = [];
        foreach ($this->accounts->getAddresses() as $address) {
            $options[$this->accounts->getEmail($address)] = $this->accounts->getEmail($address);
        }
        return $options;
    }

    /**
     * @param Parameter $parameter
     * @return bool
     */
    public function handles(Parameter $parameter) {
        return $parameter->getType() == new ClassType(AccountIdentifier::class);
    }

    /**
     * @param Parameter $parameter
     * @param array $serialized
     * @return Binary
     */
    public function inflate(Parameter $parameter, $serialized) {
        return new AccountIdentifier($serialized);
    }

    /**
     * @param Parameter $parameter
     * @param AccountIdentifier $value
     * @return string
     */
    public function render(Parameter $parameter, $value) {
        return parent::render($parameter, $value ? $value->getEmail() : null);
    }
}