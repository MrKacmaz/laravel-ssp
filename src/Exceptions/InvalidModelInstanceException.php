<?php

namespace Mrkacmaz\LaravelSsp\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown if a model provided to SSP is not a valid Eloquent model instance.
 */
class InvalidModelInstanceException extends Exception
{
    /**
     * The model class name that caused the exception.
     *
     * @var string
     */
    protected string $modelClass;

    public function __construct(string $model, int $code = 0, ?Throwable $previous = null)
    {
        $this->modelClass = $model;
        $message = "The provided model class '$model' does not exist or is not an instance of Illuminate\Database\Eloquent\Model.";
        parent::__construct($message, $code, $previous);
    }


    /**
     * Get the model class name that caused the exception.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }
}


//SSP-INIT
//SSP-INIT/src
//SSP-INIT/src/Exceptions
//SSP-INIT/src/Exceptions/InvalidModelInstanceException.php
//SSP-INIT/src/Traits
//SSP-INIT/src/Traits/SSP.php
//SSP-INIT/vendor
//SSP-INIT/composer.json
//SSP-INIT/composer.lock

