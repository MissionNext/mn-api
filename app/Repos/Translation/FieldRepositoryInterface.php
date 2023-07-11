<?php


namespace App\Repos\Translation;


interface FieldRepositoryInterface
{
    const KEY = 'trans_field';

    /**
     * @param $type
     * @return $this
     */
    public function fieldsOfType($type);
}
