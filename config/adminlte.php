<?php

// Apenas as opções que diferem do padrão do pacote jeroennoten/laravel-adminlte.
// As demais chaves são mescladas automaticamente a partir da configuração do pacote.

return [

    'title' => 'Oberland Jr. Vive',
    'title_prefix' => '',
    'title_postfix' => ' | Oberland Jr. Vive',

    'google_fonts' => [
        'allowed' => false,
    ],

    'logo' => '<b>Oberland</b> Jr. Vive',
    'logo_img' => 'img/povrosto.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Projeto Oberland Jr. Vive',

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'img/povlogo.png',
            'alt' => 'Projeto Oberland Jr. Vive',
            'class' => '',
            'width' => 240,
            'height' => 198,
        ],
    ],

    'preloader' => [
        'enabled' => false,
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',

    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_btn' => 'btn-flat btn-primary',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_topnav' => 'navbar-white navbar-light',

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
            'text' => 'Painel',
            'route' => 'dashboard',
            'icon' => 'fas fa-fw fa-home',
        ],

        ['header' => 'ATENDIMENTO'],
        [
            'text' => 'Beneficiários',
            'route' => 'beneficiarios.index',
            'icon' => 'fas fa-fw fa-hands-helping',
            'active' => ['beneficiarios', 'regex:@^beneficiarios/(?!create)@'],
        ],
        [
            'text' => 'Novo cadastro',
            'route' => 'beneficiarios.create',
            'icon' => 'fas fa-fw fa-user-plus',
        ],
        [
            'text' => 'Atendimentos',
            'route' => 'atendimentos.index',
            'icon' => 'fas fa-fw fa-hand-holding-heart',
            'active' => ['atendimentos', 'atendimentos/*'],
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

    // Tema do projeto, carregado em todas as páginas (inclusive login)
    'plugins' => [
        'TemaPov' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/pov.css',
                ],
            ],
        ],
    ],

];
