<?php

return array(

    'title' => 'Application',
    'single' => 'application',
    'model' => \MissionNext\Models\Application\Application::class,

    /**
     * The display columns
     */
    'columns' => array(
        'id' => array(
            'title' => '#'
            ),
        'name' => array(
            'title' => 'Name',
            'sortable' => true,
            ),
        'public_key' => array(
            'title' => 'Public key',
            'sortable' => true,
            ),
        'private_key' => array(
            'title' => 'Private key',
            'sortable' => true,
        ),
        'created_at' => array(
            'title' => 'Created at',
            'output' => function($value) {
                    return date("d.m.Y H:i",strtotime($value));
                }
            ),
        'updated_at' => array(
            'title' => 'Updated at',
            'output' => function($value) {
                    return date("d.m.Y H:i",strtotime($value));
                }
        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'id',
        'name',
        'public_key',
        'private_key',
        'created_at' => array(
            'type' => 'datetime',
            'date_format' => 'dd.mm.yy',
            'time_format' => 'HH:mm',
            ),
        'updated_at' => array(
            'type' => 'datetime',
            'date_format' => 'dd.mm.yy',
            'time_format' => 'HH:mm',

            ),
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'name' => array(
            'title' => 'Name',
            'type' => 'text',
            ),
        'public_key' => array(
            'title' => 'Public key',
            'type' => 'text',
            ),
        'private_key' => array(
            'title' => 'Private key',
            'type' => 'text',
            ),
    ),

);