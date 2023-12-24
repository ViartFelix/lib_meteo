<?php

namespace Viartfelix\Freather\Exceptions;

use \Exception;
use \Throwable;

/**
 * A simple custom exception that is used all along the project.
 */
class FreatherException extends Exception {
    /**
     * Constructor of the error
     * @param string $message The error message
     * @param int $code The error code
     * @param Throwable|null $previous
     */
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * To string magic method.
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

?>