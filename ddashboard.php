<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة مراقبة الزوار - منبه الدخول فقط</title>
    <link rel="stylesheet" href="https://jsdelivr.net"/>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f4f6f9; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #2c3e50; color: white; }
        .dot { height: 12px; width: 12px; border-radius: 50%; display: inline-block; margin-left: 8px; }
        .active-dot { background-color: #2ecc71; } 
        .inactive-dot { background-color: #e74c3c; } 
        .status-container { display: flex; align-items: center; justify-content: center; }
        .alert-btn { background: #e67e22; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>📊 لوحة مراقبة الزوار (منبه عند الدخول فقط 🔔)</h2>
        <button id="audio-auth" class="alert-btn" onclick="enableAudio()">🔔 تفعيل صوت دخول الزوار</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>كود الزائر</th>
                <th>الدولة</th>
                <th>المنطقة / المدينة</th>
                <th>الصفحة الحالية (الاسم)</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody id="visitors-table"></tbody>
    </table>

    <script>
        let previousVisitors = {};
        let audioEnabled = false;

        // نغمة رنين مميزة وقوية مخصصة لدخول زائر جديد للموقع
        function playNewVisitorSound() {
            if (!audioEnabled) return;
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const now = audioCtx.currentTime;

                // رنين مزدوج سريع وملفت للانتباه (Ding-Dong)
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now); // نغمة E5 مرتفعة
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.2);

                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.15); // نغمة A5 حادة
                gain2.gain.setValueAtTime(0.2, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.45);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.45);

            } catch (e) {
                console.log("خطأ في تشغيل الصوت:", e);
            }
        }

        function enableAudio() {
            audioEnabled = true;
            const btn = document.getElementById('audio-auth');
            btn.innerHTML = '✅ منبه الدخول يعمل الآن';
            btn.style.background = '#2ecc71';
            playNewVisitorSound(); 
        }

        async function updateDashboard() {
            try {
                const res = await fetch('track.php');
                const visitors = await res.json();
                const tbody = document.getElementById('visitors-table');
                tbody.innerHTML = '';

                let hasNewVisitor = false;
                let currentVisitorsMap = {};

                visitors.forEach(visitor => {
                    const isActive = visitor.status === 'نشط';
                    const dotClass = isActive ? 'active-dot' : 'inactive-dot';
                    const region = visitor.region ? visitor.region : 'غير مححدد';

                    currentVisitorsMap[visitor.code] = visitor;

                    // فحص شرط الدخول فقط: إذا كان الزائر نشطاً ولم يكن موجوداً إطلاقاً في الدورة السابقة
                    if (isActive && !previousVisitors[visitor.code]) {
                        hasNewVisitor = true;
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${visitor.code}</strong></td>
                            <td><span class="fi fi-${visitor.countryCode}"></span></td>
                            <td><span style="color: #7f8c8d; font-weight: bold;">${region}</span></td>
                            <td>${visitor.pageTitle}</td>
                            <td>
                                <div class="status-container">
                                    <span class="dot ${dotClass}"></span>
                                    <span>${visitor.status}</span>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                // إطلاق الصوت فقط في حال رصد زائر جديد تماماً دخل الموقع
                if (hasNewVisitor) {
                    playNewVisitorSound();
                }

                previousVisitors = currentVisitorsMap;

            } catch (e) {}
        }

        updateDashboard();
        setInterval(updateDashboard, 2000); // فحص التحديثات كل ثانيتين
    </script>
</body>
</html>
