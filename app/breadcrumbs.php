<?php

Breadcrumbs::register('adminHomepage', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('adminHomepage'));
});

Breadcrumbs::register('applications', function($breadcrumbs) {
    $breadcrumbs->parent('adminHomepage');
    $breadcrumbs->push('Applications', route('applications'));
});
Breadcrumbs::register('applicationCreate', function($breadcrumbs) {
    $breadcrumbs->parent('applications');
    $breadcrumbs->push('Create application', route('applicationCreate'));
});
Breadcrumbs::register('applicationEdit', function($breadcrumbs) {
    $breadcrumbs->parent('applications');
    $breadcrumbs->push('Update application', route('applicationEdit'));
});

Breadcrumbs::register('users', function($breadcrumbs) {
    $breadcrumbs->parent('adminHomepage');
    $breadcrumbs->push('Users', route('users'));
});
Breadcrumbs::register('userCreate', function($breadcrumbs) {
    $breadcrumbs->parent('users');
    $breadcrumbs->push('Create user', route('userCreate'));
});
Breadcrumbs::register('userEdit', function($breadcrumbs) {
    $breadcrumbs->parent('users');
    $breadcrumbs->push('Update user', route('userEdit'));
});
