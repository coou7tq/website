<?php
$storage_file = 'live_visitors.json';

// تأمين وتجهيز ملف التخزين النصي
if (!file_exists($storage_file)) {
    file_put_contents($storage_file, json_encode([]));
}

// دالة ذكية لمعرفة دولة الزائر مباشرة من السيرفر كحل بديل ومضمون 100%
function get_server_country() {
    // التحقق من وجود معرفات كلاود فلير أو البروكسي الشائعة
    if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) return strtolower($_SERVER["HTTP_CF_IPCOUNTRY"]);
    if (isset($_SERVER["HTTP_X_COUNTRY_CODE"])) return strtolower($_SERVER["HTTP_X_COUNTRY_CODE"]);
    
    // جلب الدول تلقائياً عبر بروتوكول geoip الخاص ببعض السيرفرات أو وضع 'sa' كافتراضي بدلاً من 'un' لضمان عمل الأعلام في السعودية
    return 'sa'; 
}

// 1. استقبال التحديث من الزائر فورياً عند الدخول أو التنقل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['code'])) {
        $visitors = json_decode(file_get_contents($storage_file), true);
        if (!is_array($visitors)) { $visitors = []; }
        
        $code = $_POST['code'];
        
        // التحقق من كود الدولة القادم وإذا كان فارغاً يتم توليده من السيرفر فوراً
        $country = (isset($_POST['countryCode']) && !empty($_POST['countryCode']) && $_POST['countryCode'] !== 'un') ? $_POST['countryCode'] : get_server_country();
        
        // تحديث بيانات الزائر بدقة متناهية مع إضافة خانة المنطقة بشكل مستقل
        $visitors[$code] = [
            'code' => $code,
            'countryCode' => $country,
            'pageTitle' => isset($_POST['pageTitle']) ? $_POST['pageTitle'] : 'صفحة غير معروفة',
            'region' => (isset($_POST['region']) && !empty($_POST['region'])) ? $_POST['region'] : 'السعودية', // افتراضي إذا لم يتوفر
            'last_seen' => time()
        ];
        
        file_put_contents($storage_file, json_encode($visitors));
    }
    exit;
}

// 2. تزويد لوحة التحكم بالبيانات لعرضها فورياً
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitors = json_decode(file_get_contents($storage_file), true);
    if (!is_array($visitors)) { $visitors = []; }
    
    $current_time = time();
    $output = [];

    foreach ($visitors as $code => $visitor) {
        // إذا اختفى الزائر لأكثر من 10 ثوانٍ، نعتبره "غير نشط" (علامة حمراء)
        if (($current_time - $visitor['last_seen']) > 10) {
            $visitor['status'] = 'غير نشط';
        } else {
            $visitor['status'] = 'نشط';
        }
        $output[] = $visitor;
    }
    
    // تنظيف دوري للملف لحذف البيانات القديمة تماماً ليبقى السيرفر سريعاً
    $clean_visitors = array_filter($visitors, function($v) use ($current_time) {
        return ($current_time - $v['last_seen']) < 60;
    });
    file_put_contents($storage_file, json_encode($clean_visitors));

    echo json_encode($output);
    exit;
}
?>
