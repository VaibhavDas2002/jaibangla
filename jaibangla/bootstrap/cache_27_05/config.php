<?php return array (
  'scout' => 
  array (
    'driver' => 'algolia',
    'prefix' => '',
    'queue' => true,
    'algolia' => 
    array (
      'id' => '',
      'secret' => '',
    ),
  ),
  'app' => 
  array (
    'name' => 'Case Report',
    'env' => 'local',
    'debug' => true,
    'url' => 'https://jaibangla.wb.gov.in',
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'base64:rHADX81cRG01qex+zS2W/jmuzk2oQHRWM4+S4Fk1/+4=',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'log_level' => 'debug',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'OwenIt\\Auditing\\AuditingServiceProvider',
      23 => 'Mews\\Captcha\\CaptchaServiceProvider',
      24 => 'Intervention\\Image\\ImageServiceProvider',
      25 => 'Yajra\\DataTables\\DataTablesServiceProvider',
      26 => 'Laravel\\Tinker\\TinkerServiceProvider',
      27 => 'App\\Providers\\AppServiceProvider',
      28 => 'App\\Providers\\AuthServiceProvider',
      29 => 'App\\Providers\\EventServiceProvider',
      30 => 'App\\Providers\\RouteServiceProvider',
      31 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      32 => 'Barryvdh\\DomPDF\\ServiceProvider',
      33 => 'Elibyy\\TCPDF\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'PDF' => 'Elibyy\\TCPDF\\Facades\\TCPDF',
      'Captcha' => 'Mews\\Captcha\\Facades\\Captcha',
      'Image' => 'Intervention\\Image\\Facades\\Image',
      'DataTables' => 'Yajra\\DataTables\\Facades\\DataTables',
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.wbsdc.in',
    'port' => '25',
    'from' => 
    array (
      'address' => 'no-reply@jaibangla.wb.gov.in',
      'name' => 'Jai Bangla, Government of West Bengal',
    ),
    'encryption' => NULL,
    'username' => NULL,
    'password' => NULL,
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/jaibangla/var/www/html/jaibangla/resources/views/vendor/mail',
      ),
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/jaibangla/var/www/html/jaibangla/resources/views',
    ),
    'compiled' => '/jaibangla/var/www/html/jaibangla/storage/framework/views',
  ),
  'constants' => 
  array (
    'gender' => 
    array (
      'Male' => 'Male',
      'Female' => 'Female',
      'Other' => 'Other',
    ),
    'caste' => 
    array (
      'SC' => 'SC',
      'ST' => 'ST',
      'General' => 'General',
    ),
    'category_purohit' => 
    array (
      'SC' => 'SC',
      'ST' => 'ST',
      'OTHERS' => 'OTHERS',
    ),
    'caste_lb' => 
    array (
      'SC' => 'SC',
      'ST' => 'ST',
      'OBC' => 'OBC',
      'General' => 'General',
    ),
    'purohit_phase' => 
    array (
      'Phase-I' => 'Phase-I',
      'Phase-II' => 'Phase-II',
      'Phase-III' => 'Phase-III',
    ),
    'temple_type' => 
    array (
      'Temple Purohit' => 'Temple Purohit',
      'Tribal Religious Place Purohit' => 'Tribal Religious Place Purohit',
      'Community Purohit' => 'Community Purohit',
    ),
    'user_level' => 
    array (
      'State' => 'State',
      'District' => 'District',
      'Block' => 'Block',
      'Subdiv' => 'Sub Division',
      'Municipality' => 'Municipality',
      'Gram Panchayet' => 'Gram Panchayet',
    ),
    'disablity_type' => 
    array (
      'Orthopedically Handicapped' => 'Orthopedically Handicapped',
      'Visually Handicapped' => 'Visually Handicapped',
      'Mental illness' => 'Mental illness',
      'Mental Retardation' => 'Mental Retardation',
      'Mutiple Disablities' => 'Mutiple Disablities',
      'Leprosy Cured' => 'Leprosy Cured',
      'Nervous Disorder' => 'Nervous Disorder',
      'Others' => 'Others',
    ),
    'document_group' => 
    array (
      1 => 'Date of Birth Identification',
      2 => 'Caste Identification',
      3 => 'Document Group for Manabik',
      4 => 'Date of Birth Identification for OAP',
      5 => 'Date of Birth Identification for WP',
    ),
    'marital_status' => 
    array (
      'Unmarried' => 'Unmarried',
      'Married' => 'Married',
      'Seperated' => 'Seperated',
      'Widow' => 'Widow',
      'Widower' => 'Widower',
    ),
    'ration_cat' => 
    array (
      'AAY' => 'AAY',
      'OHH' => 'OHH',
      'RKSY 1' => 'RKSY 1',
      'RKSY 2' => 'RKSY 2',
      'SPHH' => 'SPHH',
      'PHH' => 'PHH',
      'GEN' => 'GEN',
    ),
    'rural_urban' => 
    array (
      2 => 'Rural',
      1 => 'Urban',
    ),
    'pension_body' => 
    array (
      'Central Govt' => 'Central Govt',
      'State Govt' => 'State Govt',
      'Local Administration' => 'Local Administration',
      'Govt. Aided Organization' => 'Govt. Aided Organization',
    ),
    'social_pension_cat' => 
    array (
      'NSAP Old Age' => 'NSAP Old Age',
      'NSAP Widow Pension' => 'NSAP Widow Pension',
      'NSAP Disability Pension' => 'NSAP Disability Pension',
      'Old Age Pension' => 'Old Age Pension',
      'Widow Pension' => 'Widow Pension',
      'Disability Pension' => 'Disability Pension',
      'Lok Prasar Prakalpa' => 'Lok Prasar Prakalpa',
      'Fisherman\'s Old Age Pension' => 'Fisherman\'s Old Age Pension',
      'Farmers Old Age Pension' => 'Farmers Old Age Pension',
      'Artisan/Weaver Old Age Pension' => 'Artisan/Weaver Old Age Pension',
    ),
    'fin_year' => 
    array (
      '2020-2021' => '2020-2021',
      '2021-2022' => '2021-2022',
    ),
    'monthlist' => 
    array (
      'April' => 'APRIL',
      'May' => 'MAY',
      'June' => 'JUNE',
      'July' => 'JULY',
      'August' => 'AUGUST',
      'September' => 'SEPTEMBER',
      'October' => 'OCTOBER',
      'November' => 'NOVEMBER',
      'December' => 'DECEMBER',
      'January' => 'JANUARY',
      'February' => 'FEBRUARY',
      'March' => 'MARCH',
    ),
    'month_list' => 
    array (
      '01' => 'January',
      '02' => 'February',
      '03' => 'March',
      '04' => 'April',
      '05' => 'May',
      '06' => 'June',
      '07' => 'July',
      '08' => 'August',
      '09' => 'September',
      10 => 'October',
      11 => 'November',
      12 => 'December',
    ),
    'monthval' => 
    array (
      1 => 'January',
      2 => 'February',
      3 => 'March',
      4 => 'April',
      5 => 'May',
      6 => 'June',
      7 => 'July',
      8 => 'August',
      9 => 'September',
      10 => 'October',
      11 => 'November',
      12 => 'December',
    ),
    'category' => 
    array (
      'ALL' => 'ALL',
      'GENERAL' => 'GENERAL',
      'SC' => 'SC',
      'ST' => 'ST',
    ),
    'lot_size' => 
    array (
      10 => '10',
      20 => '20',
      50 => '50',
      100 => '100',
      500 => '500',
      1000 => '1000',
      5000 => '5000',
      10000 => '10000',
    ),
    'schemecodeStatic' => 
    array (
      'purohitmonthly' => 
      array (
        'scheme_code' => '17',
        'name' => 'Monthly Financial Assistance',
        'slug' => 'monthly',
        'maintable' => 'PensionPurohitMonthlyICAD',
        'doctable' => 'BenDocsPurohitMonthlyICAD',
        'docarctable' => 'BenDocsArcPurohitMonthlyICAD',
      ),
      'purohithousing' => 
      array (
        'scheme_code' => '18',
        'name' => 'Both Monthly Pension and One time Housing Scheme',
        'slug' => 'housing',
        'maintable' => 'PensionPurohitHousingICAD',
        'doctable' => 'BenDocsPurohitHousingICAD',
        'docarctable' => 'BenDocsArcPurohitHousingICAD',
      ),
    ),
    'user_audit_trail_code' => 
    array (
      'Update' => 1,
      'Delete' => 2,
    ),
    'errormsg' => 
    array (
      'roolback' => 'Error Occur .. Please try later..',
      'frmjsonnexists' => 'Error Occur .. Please try later..',
      'notValid' => 'is Not Valid',
      'notFound' => 'Not Found',
      'notauthorized' => 'You are not Authorized',
      'applicationidnotfound' => 'Application Id not Found',
      'applicationalreadyverified' => 'Application already verified.. you cannot edit it.',
      'sessiontimeOut' => 'Something wrong..may be session timeout. please logout and then login again',
      'exceedcapacity' => 'Scheme Data has been exceeded to the Capacity',
    ),
    'scheme_code_map' => 
    array (
      1 => 
      array (
        'scheme_id' => '1',
        'model_name' => 'PensionSt',
        'scheme_name' => 'Jai Johar(for ST)',
      ),
      2 => 
      array (
        'scheme_id' => '2',
        'model_name' => 'PensionManabikWCD',
        'scheme_name' => 'Manabik for WCD',
      ),
      3 => 
      array (
        'scheme_id' => '3',
        'model_name' => 'PensionSc',
        'scheme_name' => 'Toposili Bandhu(for SC)',
      ),
      5 => 
      array (
        'scheme_id' => '5',
        'model_name' => 'PensionFisherman',
        'scheme_name' => 'Old Age Pension for Fishermen',
      ),
      6 => 
      array (
        'scheme_id' => '6',
        'model_name' => 'PensionMSME',
        'scheme_name' => 'MSME Pension',
      ),
      7 => 
      array (
        'scheme_id' => '7',
        'model_name' => 'PensionTextile',
        'scheme_name' => 'Textile Pension',
      ),
      10 => 
      array (
        'scheme_id' => '10',
        'model_name' => 'PensionOAPWCD',
        'scheme_name' => 'Old Age Pension WCD',
      ),
      11 => 
      array (
        'scheme_id' => '11',
        'model_name' => 'PensionWPWCD',
        'scheme_name' => 'Widow Pension WCD',
      ),
      17 => 
      array (
        'scheme_id' => '17',
        'model_name' => 'PensionPurohitMonthlyICAD',
        'scheme_name' => 'Purohits Monthly Financial Assistance',
      ),
      13 => 
      array (
        'scheme_id' => '13',
        'model_name' => 'PensionOAPFarmer',
        'scheme_name' => 'Farmer Old Age Pension',
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/jaibangla/var/www/html/jaibangla/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/jaibangla/var/www/html/jaibangla/storage/app/public',
        'url' => 'https://jaibangla.wb.gov.in/storage',
        'visibility' => 'public',
      ),
      'local_xml' => 
      array (
        'driver' => 'local',
        'root' => '/jaibangla/var/www/html/jaibangla/storage/../../xml_file/',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
      ),
      'sftp_026' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.45',
        'port' => 22,
        'username' => 'gen026',
        'password' => 'N!cEar.39',
        'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen026/',
        'timeout' => 20,
      ),
      'sftp_027' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.45',
        'port' => 22,
        'username' => 'gen027',
        'password' => 'Ca!$d.07#4',
        'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen027/',
        'timeout' => 20,
      ),
      'sftp_028' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.45',
        'port' => 22,
        'username' => 'gen028',
        'password' => 'n*cE@r.&6',
        'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen028/',
        'timeout' => 20,
      ),
      'sftp_030' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.45',
        'port' => 22,
        'username' => 'gen030',
        'password' => 'nuc!e@R.6472',
        'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen030/',
        'timeout' => 20,
      ),
      'sftp_031' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.45',
        'port' => 22,
        'username' => 'gen031',
        'password' => 'nuc!e@R.8351',
        'root' => '/ifms_web_cache/ekuber/ePaymentFiles/gen031/',
        'timeout' => 20,
      ),
      'sftp_033' => 
      array (
        'driver' => 'sftp',
        'host' => '172.17.2.53',
        'port' => 22,
        'username' => 'gen033',
        'password' => 'Nuc1e@r.5285',
        'root' => '/apps/ePaymentFiles/gen033/',
        'timeout' => 20,
      ),
      'sftp_sbi' => 
      array (
        'driver' => 'sftp',
        'host' => '103.209.96.231',
        'port' => 2201,
        'username' => 'WB003',
        'password' => 'User#123',
        'root' => '/',
        'timeout' => 10,
      ),
    ),
  ),
  'excel' => 
  array (
    'cache' => 
    array (
      'enable' => true,
      'driver' => 'memory',
      'settings' => 
      array (
        'memoryCacheSize' => '32MB',
        'cacheTime' => 600,
      ),
      'memcache' => 
      array (
        'host' => 'localhost',
        'port' => 11211,
      ),
      'dir' => '/jaibangla/var/www/html/jaibangla/storage/cache',
    ),
    'properties' => 
    array (
      'creator' => 'Maatwebsite',
      'lastModifiedBy' => 'Maatwebsite',
      'title' => 'Spreadsheet',
      'description' => 'Default spreadsheet export',
      'subject' => 'Spreadsheet export',
      'keywords' => 'maatwebsite, excel, export',
      'category' => 'Excel',
      'manager' => 'Maatwebsite',
      'company' => 'Maatwebsite',
    ),
    'sheets' => 
    array (
      'pageSetup' => 
      array (
        'orientation' => 'portrait',
        'paperSize' => '9',
        'scale' => '100',
        'fitToPage' => false,
        'fitToHeight' => true,
        'fitToWidth' => true,
        'columnsToRepeatAtLeft' => 
        array (
          0 => '',
          1 => '',
        ),
        'rowsToRepeatAtTop' => 
        array (
          0 => 0,
          1 => 0,
        ),
        'horizontalCentered' => false,
        'verticalCentered' => false,
        'printArea' => NULL,
        'firstPageNumber' => NULL,
      ),
    ),
    'creator' => 'Maatwebsite',
    'csv' => 
    array (
      'delimiter' => ',',
      'enclosure' => '"',
      'line_ending' => '
',
      'use_bom' => false,
    ),
    'export' => 
    array (
      'autosize' => true,
      'autosize-method' => 'approx',
      'generate_heading_by_indices' => true,
      'merged_cell_alignment' => 'left',
      'calculate' => false,
      'includeCharts' => false,
      'sheets' => 
      array (
        'page_margin' => false,
        'nullValue' => NULL,
        'startCell' => 'A1',
        'strictNullComparison' => false,
      ),
      'store' => 
      array (
        'path' => '/jaibangla/var/www/html/jaibangla/storage/exports',
        'returnInfo' => false,
      ),
      'pdf' => 
      array (
        'driver' => 'DomPDF',
        'drivers' => 
        array (
          'DomPDF' => 
          array (
            'path' => '/jaibangla/var/www/html/jaibangla/vendor/dompdf/dompdf/',
          ),
          'tcPDF' => 
          array (
            'path' => '/jaibangla/var/www/html/jaibangla/vendor/tecnick.com/tcpdf/',
          ),
          'mPDF' => 
          array (
            'path' => '/jaibangla/var/www/html/jaibangla/vendor/mpdf/mpdf/',
          ),
        ),
      ),
    ),
    'filters' => 
    array (
      'registered' => 
      array (
        'chunk' => 'Maatwebsite\\Excel\\Filters\\ChunkReadFilter',
      ),
      'enabled' => 
      array (
      ),
    ),
    'import' => 
    array (
      'heading' => 'slugged',
      'startRow' => 1,
      'separator' => '_',
      'includeCharts' => false,
      'to_ascii' => true,
      'encoding' => 
      array (
        'input' => 'UTF-8',
        'output' => 'UTF-8',
      ),
      'calculate' => true,
      'ignoreEmpty' => false,
      'force_sheets_collection' => false,
      'dates' => 
      array (
        'enabled' => true,
        'format' => false,
        'columns' => 
        array (
        ),
      ),
      'sheets' => 
      array (
        'test' => 
        array (
          'firstname' => 'A2',
        ),
      ),
    ),
    'views' => 
    array (
      'styles' => 
      array (
        'th' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'strong' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'b' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'i' => 
        array (
          'font' => 
          array (
            'italic' => true,
            'size' => 12,
          ),
        ),
        'h1' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 24,
          ),
        ),
        'h2' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 18,
          ),
        ),
        'h3' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 13.5,
          ),
        ),
        'h4' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'h5' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 10,
          ),
        ),
        'h6' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 7.5,
          ),
        ),
        'a' => 
        array (
          'font' => 
          array (
            'underline' => true,
            'color' => 
            array (
              'argb' => 'FF0000FF',
            ),
          ),
        ),
        'hr' => 
        array (
          'borders' => 
          array (
            'bottom' => 
            array (
              'style' => 'thin',
              'color' => 
              array (
                0 => 'FF000000',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'audit' => 
  array (
    'implementation' => 'OwenIt\\Auditing\\Models\\Audit',
    'user' => 
    array (
      'primary_key' => 'id',
      'foreign_key' => 'user_id',
      'model' => 'App\\User',
      'resolver' => 'App\\user',
    ),
    'resolver' => 
    array (
      'user' => 'OwenIt\\Auditing\\Resolvers\\UserResolver',
      'ip_address' => 'OwenIt\\Auditing\\Resolvers\\IpAddressResolver',
      'user_agent' => 'OwenIt\\Auditing\\Resolvers\\UserAgentResolver',
      'url' => 'OwenIt\\Auditing\\Resolvers\\UrlResolver',
    ),
    'events' => 
    array (
      0 => 'created',
      1 => 'updated',
      2 => 'deleted',
      3 => 'restored',
    ),
    'strict' => false,
    'timestamps' => false,
    'threshold' => 0,
    'redact' => false,
    'driver' => 'database',
    'drivers' => 
    array (
      'database' => 
      array (
        'table' => 'audits',
        'connection' => NULL,
      ),
    ),
    'console' => false,
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/jaibangla/var/www/html/jaibangla/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'pgsql',
      'table' => 'failed_jobs',
    ),
  ),
  'database' => 
  array (
    'default' => 'pgsql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => '/jaibangla/var/www/html/jaibangla/database/database.sqlite',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '5432',
        'database' => 'forge',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => NULL,
      ),
      'pgsql_sp' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.107',
        'port' => '5432',
        'database' => 'sneherporosh',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'sslmode' => 'prefer',
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'pgsql2' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'bandhu',
        'sslmode' => 'prefer',
      ),
      'pgsql3' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'johar',
        'sslmode' => 'prefer',
      ),
      'pgsql4' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'manabik',
        'sslmode' => 'prefer',
      ),
      'pgsql5' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'pension',
        'sslmode' => 'prefer',
      ),
      'pgsql6' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'fisherman_oap',
        'sslmode' => 'prefer',
      ),
      'pgsql7' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'prachesta',
        'sslmode' => 'prefer',
      ),
      'pgsql8' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'sbi',
        'sslmode' => 'prefer',
      ),
      'pgsql9' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'msme',
        'sslmode' => 'prefer',
      ),
      'pgsql10' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'weavers',
        'sslmode' => 'prefer',
      ),
      'pgsql_mis' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.62',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'sslmode' => 'prefer',
      ),
      'pgsql_legacy' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'legacy',
        'sslmode' => 'prefer',
      ),
      'pgsql_ifsc' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'ifsc',
        'sslmode' => 'prefer',
      ),
      'pgsql_report' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.62',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'pension',
        'sslmode' => 'prefer',
      ),
      'pgsql11' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'oap_st_wcd',
        'sslmode' => 'prefer',
      ),
      'pgsql12' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'oap_wcd',
        'sslmode' => 'prefer',
      ),
      'pgsql13' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'wp_wcd',
        'sslmode' => 'prefer',
      ),
      'pgsql14' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'lokprasar_retainer',
        'sslmode' => 'prefer',
      ),
      'pgsql15' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'lokprasar_pensioner',
        'sslmode' => 'prefer',
      ),
      'pgsql16' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'farmer',
        'sslmode' => 'prefer',
      ),
      'pgsqlpurohitmonthly' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'purohit_monthly',
        'sslmode' => 'prefer',
      ),
      'pgsqlpurohithousing' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'purohit_housing',
        'sslmode' => 'prefer',
      ),
      'pgsql20' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'lk_wcd',
        'sslmode' => 'prefer',
      ),
      'pgsqllbtemp' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'lb_wcd',
        'sslmode' => 'prefer',
      ),
      'pgsql_ifms' => 
      array (
        'driver' => 'pgsql',
        'host' => '172.20.60.95',
        'port' => '5432',
        'database' => 'jaibangla',
        'username' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'ifms',
        'sslmode' => 'prefer',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 20,
    'expire_on_close' => true,
    'encrypt' => false,
    'files' => '/jaibangla/var/www/html/jaibangla/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
  ),
  'captcha' => 
  array (
    'characters' => '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ',
    'default' => 
    array (
      'length' => 5,
      'width' => 120,
      'height' => 36,
      'quality' => 90,
    ),
    'flat' => 
    array (
      'length' => 6,
      'width' => 160,
      'height' => 46,
      'quality' => 90,
      'lines' => 6,
      'bgImage' => false,
      'bgColor' => '#ecf2f4',
      'fontColors' => 
      array (
        0 => '#2c3e50',
        1 => '#c0392b',
        2 => '#16a085',
        3 => '#c0392b',
        4 => '#8e44ad',
        5 => '#303f9f',
        6 => '#f57c00',
        7 => '#795548',
      ),
      'contrast' => -5,
    ),
    'mini' => 
    array (
      'length' => 3,
      'width' => 60,
      'height' => 32,
    ),
    'inverse' => 
    array (
      'length' => 5,
      'width' => 120,
      'height' => 36,
      'quality' => 90,
      'sensitive' => true,
      'angle' => 12,
      'sharpen' => 10,
      'blur' => 2,
      'invert' => true,
      'contrast' => -5,
    ),
  ),
  'image' => 
  array (
    'driver' => 'gd',
  ),
  'datatables' => 
  array (
    'search' => 
    array (
      'smart' => true,
      'multi_term' => true,
      'case_insensitive' => true,
      'use_wildcards' => false,
    ),
    'index_column' => 'DT_RowIndex',
    'engines' => 
    array (
      'eloquent' => 'Yajra\\DataTables\\EloquentDataTable',
      'query' => 'Yajra\\DataTables\\QueryDataTable',
      'collection' => 'Yajra\\DataTables\\CollectionDataTable',
      'resource' => 'Yajra\\DataTables\\ApiResourceDataTable',
    ),
    'builders' => 
    array (
    ),
    'nulls_last_sql' => '%s %s NULLS LAST',
    'error' => NULL,
    'columns' => 
    array (
      'excess' => 
      array (
        0 => 'rn',
        1 => 'row_num',
      ),
      'escape' => '*',
      'raw' => 
      array (
        0 => 'action',
      ),
      'blacklist' => 
      array (
        0 => 'password',
        1 => 'remember_token',
      ),
      'whitelist' => '*',
    ),
    'json' => 
    array (
      'header' => 
      array (
      ),
      'options' => 0,
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => '/jaibangla/var/www/html/jaibangla/storage/fonts/',
      'font_cache' => '/jaibangla/var/www/html/jaibangla/storage/fonts/',
      'temp_dir' => '/tmp',
      'chroot' => '/jaibangla/var/www/html/jaibangla',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => false,
    ),
  ),
  'tcpdf' => 
  array (
    'page_format' => 'A4',
    'page_orientation' => 'P',
    'page_units' => 'mm',
    'unicode' => true,
    'encoding' => 'UTF-8',
    'font_directory' => '',
    'image_directory' => '',
    'tcpdf_throw_exception' => false,
    'tcpdf_calls_in_html' => true,
  ),
  'tinker' => 
  array (
    'dont_alias' => 
    array (
    ),
  ),
);
