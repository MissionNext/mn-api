<?php

return array(

    'title' => 'Users',
    'single' => 'user',
    'model' => \MissionNext\Models\User\User::class,

    /**
     * The display columns
     */
    'columns' => array(
        'id' => array(
            'title' => '#'
        ),
        'username' => array(
            'title' => 'Login',
            'sortable' => true,
        ),
        'password',
        'email' => array(
            'title' => 'E-mail',
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
//        'user-roles' => array(
//            'title' => 'Roles',
//            'relationship' => 'roles',
//            'select' => '(:table).role',
//        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'username' => array(
            'title' => 'Login'
        ),
        'email' => array(
            'title' => 'E-mail',
        ),
        'roles' => array(
            'title' => 'Role',
            'type'  => 'relationship',
            'name_field' => 'role',
//            'select' => '(:table).role'

        ),
        'created_at' => array(
            'type' => 'datetime',
            'title' => 'Created at',
            'date_format' => 'dd.mm.yy',
            'time_format' => 'HH:mm',
        ),
        'updated_at' => array(
            'type' => 'datetime',
            'title' => 'Updated at',
            'date_format' => 'dd.mm.yy',
            'time_format' => 'HH:mm',

        ),
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'username' => array(
            'title' => 'Login',
            'type' => 'text',
        ),
        'email' => array(
            'title' => 'E-mail',
            'type' => 'text',
        ),
        'password' => array(
            'title' => 'Password',
            'type' => 'password',
//            'setter' => true,
        ),
        'roles' => array(
            'title' => 'Role',
            'type'  => 'relationship',
            'name_field' => 'role',

        ),
        'apps' => array(
            'title' => 'Applications',
            'type'  => 'relationship',
            'name_field' => 'name',

        ),

    ),


    /**
     * The validation rules for the form, based on the Laravel validation class
     *
     * @type array
     */
//    'rules' => array(
//        'email' => 'required|email',
//        'username' => 'required',
//        'password' => 'required',
//    ),



);