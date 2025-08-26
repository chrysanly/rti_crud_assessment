<?php
$db = require __DIR__ . '/test_db.php';
$params = require __DIR__ . '/params.php';

return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
    ],
    'params' => $params,
];
