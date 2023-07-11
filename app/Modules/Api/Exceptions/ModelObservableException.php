<?php


namespace App\Modules\Api\Exceptions;


class ModelObservableException extends \Exception
{

    const ON_SAVED = 1;

    const ON_CREATED = 2;

}
