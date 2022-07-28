<?php
namespace core\extension\parser\env;
use core\extension\parser\env\exception\InvalidCallbackException;
use core\extension\parser\env\exception\ValidationException;

/**
 * This is the validator class.
 * It's responsible for applying validations against a number of variables.
 */
class Validator {
    protected $variables;
    protected $loader;

    public function __construct(array $variables,Loader $loader) {
        $this->variables = $variables;
        $this->loader    = $loader;

        $this->assertCallback(function($value) {
            return $value !== null;
        },'is missing');
    }

    public function notEmpty() {
        return $this->assertCallback(function($value) {
            return strlen(trim($value)) > 0;
        },'is empty');
    }

    public function isInteger() {
        return $this->assertCallback(function($value) {
            return ctype_digit($value);
        },'is not an integer');
    }


    public function isBoolean() {
        return $this->assertCallback(function($value) {
            if ($value === '') {
                return false;
            }
            return (filter_var($value,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE) !== null);
        },'is not a boolean');
    }

    public function allowedValues(array $choices) {
        return $this->assertCallback(function($value) use ($choices) {
            return in_array($value,$choices);
        },'is not an allowed value');
    }

    protected function assertCallback($callback,$message = 'failed callback assertion') {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('The provided callback must be callable.');
        }

        $variablesFailingAssertion = [];
        foreach($this->variables as $variableName) {
            $variableValue = $this->loader->getEnvironmentVariable($variableName);
            if ($callback($variableValue) === false) {
                $variablesFailingAssertion[] = $variableName." $message";
            }
        }

        if (count($variablesFailingAssertion) > 0) {
            throw new ValidationException(sprintf('One or more environment variables failed assertions: %s.',implode(', ',$variablesFailingAssertion)));
        }
        return $this;
    }
}
