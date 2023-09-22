<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        [
            "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css", [
                'integrity'=>"sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==",
                'crossorigin'=>"anonymous",
                'referrerpolicy'=>"no-referrer",
            ]
        ]
    ];
    public $js = [
        'assets/local-js/dropdown-button-ajax.js',
        ["https://kit.fontawesome.com/8e279a0a26.js", ['crossorigin' => "anonymous"]],
        [
            "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js", [
                'integrity'=>"sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==",
                'crossorigin'=>"anonymous",
                'referrerpolicy'=>"no-referrer"
            ]
        ],
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
