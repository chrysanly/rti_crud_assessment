<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        // ✅ Select2 CSS
        
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
         'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css', // SweetAlert CSS
    ];
    public $js = [
        'js/main.js',
        'js/task.js',
         'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', // SweetAlert JS
        // ✅ Select2 JS
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
