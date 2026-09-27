<?php

// Apenas as opções que diferem do padrão do pacote jeroennoten/laravel-adminlte.
// As demais chaves são mescladas automaticamente a partir da configuração do pacote.

return [

    'title' => 'Oberland JR Vive',
    'title_prefix' => '',
    'title_postfix' => ' | Oberland JR Vive',

    'logo' => '<b>Oberland</b> JR Vive',
    'logo_img_alt' => 'Oberland JR Vive',

    'auth_logo' => [
        'enabled' => false,
    ],

    'preloader' => [
        'enabled' => false,
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',

    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,

    'classes_sidebar' => 'sidebar-dark-primary elevation-4',

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => false,
    'password_reset_url' => false,
    'password_email_url' => false,
    'profile_url' => false,

    'menu' => [
        [
            'text' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],

        ['header' => 'CADASTROS'],
        [
            'text' => 'Pessoas',
            'route' => 'pessoas.index',
            'icon' => 'fas fa-fw fa-users',
            'active' => ['pessoas', 'pessoas/*'],
        ],

        [
            'header' => 'ADMINISTRAÇÃO',
            'can' => 'admin',
        ],
        [
            'text' => 'Usuários',
            'route' => 'users.index',
            'icon' => 'fas fa-fw fa-user-shield',
            'active' => ['users', 'users/*'],
            'can' => 'admin',
        ],
    ],

];
