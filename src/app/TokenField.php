<?php
namespace groupcash\socialhours\app;

use groupcash\socialhours\model\Token;
use rtens\domin\delivery\web\fields\StringField;
use rtens\domin\Parameter;
use watoki\reflect\type\ClassType;

class TokenField extends StringField {

    /** @var Session */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function handles(Parameter $parameter) {
        return $parameter->getType() == new ClassType(Token::class);
    }

    public function inflate(Parameter $parameter, $serialized) {
        return new Token(parent::inflate($parameter, $serialized));
    }

    public function render(Parameter $parameter, $value) {
        if (!(string)$value && $this->session->isStarted()) {
            $value = $this->session->getToken();
        }
        return parent::render($parameter, $value);
    }
}