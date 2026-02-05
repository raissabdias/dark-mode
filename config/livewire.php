<?php

return [ // <--- TEM QUE TER A PALAVRA 'return'
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'components.layouts.app',
    'lazy_placeholder' => null,
    
    // A configuração que queremos
    'asset_url' => env('APP_URL', null),

    'temporary_file_upload' => [
        'disk' => 'local', 
        'rules' => 'file|max:12288',
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => ['png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg', 'jpeg', 'mpga', 'webp', 'wma'],
        'max_upload_time' => 5,
    ],

    'inject_assets' => true,
    'inject_morph_markers' => true,
    'navigate' => [
        'show_progress_bar' => true,
    ],
];