<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'local_xml' => [
            'driver' => 'local',
            'root' => storage_path('../../xml_file/'),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'sftp_026' => [
            'driver' => 'sftp',
            'host' => '172.17.2.45',
            'port' => 22,
            'username' => 'gen026',
            //'password' => 'N!cEar.39',
	    'password' => 'We1comE.sFim.9172',
            /*'privateKey' => 'path/to/or/contents/of/privatekey',*/
            //'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen026/', //'/apps/ePaymentFiles/gen026/',
	    'root' => '/gen026/',
            'timeout' => 20,
        ],

        'sftp_027' => [
            'driver' => 'sftp',
            'host' => '172.17.2.45',
            'port' => 22,
            'username' => 'gen027',
            //'password' => 'Ca!$d.07#4',
	    'password' => 'We1c0mE.iFsm.7294',
            'root' => '/gen027/',
            'timeout' => 20,
        ],

        'sftp_028' => [
            'driver' => 'sftp',
            'host' => '172.17.2.45',
            'port' => 22,
            'username' => 'gen028',
            //'password' => 'n*cE@r.&6',
	    'password' => 'W@1c0mE#!fms.3815',
	    'root' => '/gen028/',
            //'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen028/',
            'timeout' => 20,
        ],

        'sftp_030' => [
            'driver' => 'sftp',
            'host' => '172.17.2.45',
            'port' => 22,
            'username' => 'gen030',
            //'password' => 'nuc!e@R.6472',
	    'password' => 'W@1c0me#IFmS.2846',
	    'root' => '/gen030/',
            //'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen030/',
            'timeout' => 20,
        ],

        'sftp_031' => [
            'driver' => 'sftp',
            'host' => '172.17.2.45',
            'port' => 22,
            'username' => 'gen031',
            //'password' => 'nuc!e@R.8351',
	    'password' => 'We!cOme#IfSm.6621',
	    'root' => '/gen031/',
            //'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen031/',
            'timeout' => 20,
        ],

        'sftp_033' => [
            'driver' => 'sftp',
            'host' => '172.17.2.53',
            'port' => 22,
            'username' => 'gen033',
            //'password' => 'We!c0me.FIsm.4016',
	    'password' => 'We!c0me.FIsm.4016',
	    'root' => '/gen033/',
           // 'root' => '/apps/ePaymentFiles/gen033/', //'/ifms_web_cache/ekuber/ePaymentFiles/gen031/'
            'timeout' => 20,
        ],

        'sftp_sbi' => [
            'driver' => 'sftp',
            'host' => '103.209.96.231',
            'port' => 2201,
            'username' => 'WB003',
            'password' => 'User#123',
            'root' => '/',
            'timeout' => 10,
        ],

    ],

];
