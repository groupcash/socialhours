<?php
namespace groupcash\socialhours\app;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\projections\AccountList;
use groupcash\socialhours\projections\OrganisationList;
use rtens\domin\delivery\web\Element;
use rtens\domin\delivery\web\WebRenderer;

class AddressRenderer implements WebRenderer {

    /** @var AccountList */
    private $accounts;
    /** @var OrganisationList */
    private $organisations;

    /**
     * @param object|AccountList $accounts
     * @param object|OrganisationList $organisations
     */
    public function __construct(AccountList $accounts, OrganisationList $organisations) {
        $this->accounts = $accounts;
        $this->organisations = $organisations;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function handles($value) {
        return $value instanceof Binary;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function render($value) {
        $caption = substr(trim($value, '='), -12);

        foreach ($this->accounts->getAddresses() as $address) {
            if ($address == $value) {
                if ($this->accounts->getName($address)) {
                    $caption = $this->accounts->getName($address);
                } else {
                    $caption = $this->accounts->getEmail($address);
                }
            }
        }

        foreach ($this->organisations->getAddresses() as $address) {
            if ($address == $value) {
                $caption = $this->organisations->getName($address);
            }
        }

        return new Element('abbr', ['title' => $value], [$caption]);
    }

    /**
     * @param mixed $value
     * @return array|Element[]
     */
    public function headElements($value) {
        return [];
    }
}