<?php
namespace groupcash\socialhours\app;

use groupcash\socialhours\model\OrganisationIdentifier;
use groupcash\socialhours\projections\OrganisationList;
use rtens\domin\delivery\web\fields\AutoCompleteField;
use rtens\domin\Parameter;
use watoki\reflect\type\ClassType;

class OrganisationIdentifierField extends AutoCompleteField {

    /** @var OrganisationList */
    private $organisations;

    /**
     * @param object|OrganisationList $organisations
     */
    public function __construct(OrganisationList $organisations) {
        $this->organisations = $organisations;
    }

    /**
     * @param Parameter $parameter
     * @return array With captions indexed by values
     */
    protected function getOptions(Parameter $parameter) {
        $options = [];
        foreach ($this->organisations->getAddresses() as $address) {
            $options[$this->organisations->getName($address)] = $this->organisations->getName($address);
        }
        return $options;
    }

    /**
     * @param Parameter $parameter
     * @return bool
     */
    public function handles(Parameter $parameter) {
        return $parameter->getType() == new ClassType(OrganisationIdentifier::class);
    }

    public function inflate(Parameter $parameter, $serialized) {
        return new OrganisationIdentifier(parent::inflate($parameter, $serialized));
    }

    /**
     * @param Parameter $parameter
     * @param OrganisationIdentifier|null $value
     * @return string
     */
    public function render(Parameter $parameter, $value) {
        return parent::render($parameter, $value ? $value->getName() : null);
    }
}