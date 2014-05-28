<?php

return array(

    'title' => 'Users',

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'password' => array(
            'before_save' => function($data)
                {
                    dd($data);
                    return $data['password'] = Hash::make($data['password']);
                },
        )
    ),

    /**
     * This is run prior to saving the JSON form data
     *
     * @type function
     * @param array     $data
     *
     * @return string (on error) / void (otherwise)
     */
//    'before_save' => function($data)
//        {
//            dd($data);
//            return $data['password'] = Hass::make($data['password']);
//        },
);
