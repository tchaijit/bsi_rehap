# วิธีเก็บข้อมูลเป็น JSON สำหรับ Dashboard

## ภาพรวมระบบ

ระบบมีการเก็บข้อมูลอยู่แล้ว 2 รูปแบบ:
1. **data.json** - ไฟล์ JSON มาตรฐาน (สำหรับ API)
2. **data.js** - JavaScript file (สำหรับเปิดแบบ Static ไม่มีปัญหา CORS)

ทั้ง 2 ไฟล์จะถูกอัพเดทพร้อมกันอัตโนมัติ

---

## วิธีที่ 1: ส่งข้อมูลผ่าน API (แนะนำ)

### 1.1 ใช้ JavaScript Fetch

```javascript
// ข้อมูลที่ต้องการบันทึก
const testData = {
    timestamp: new Date().toISOString(),
    date: "01/12/2568",
    hn: "67-23-012345",
    age: 35,
    gender: "male",
    weight: 75,
    measurements: {
        handgrip: 48,
        legstrength: 145,
        backstrength: 125,
        flexibility: 12,
        pushup: 30  // optional
    },
    results: {
        handgrip: {
            level: "ดี",
            class: "good"
        },
        legstrength: {
            level: "ดีมาก",
            class: "excellent"
        },
        backstrength: {
            level: "ดีมาก",
            class: "excellent"
        },
        flexibility: {
            level: "ดี",
            class: "good"
        },
        pushup: {
            level: "ดี",
            class: "good"
        }
    }
};

// ส่งไปยัง API
fetch('http://localhost:8000/save_test_data.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(testData)
})
.then(response => response.json())
.then(data => {
    console.log('Success:', data);
    // Output: {success: true, message: "Test data saved successfully", total_records: 7}
})
.catch(error => {
    console.error('Error:', error);
});
```

### 1.2 ใช้ jQuery Ajax

```javascript
$.ajax({
    url: 'http://localhost:8000/save_test_data.php',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify(testData),
    success: function(response) {
        console.log('บันทึกสำเร็จ:', response);
        alert('บันทึกข้อมูลเรียบร้อย! ทั้งหมด ' + response.total_records + ' records');
    },
    error: function(xhr, status, error) {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด: ' + error);
    }
});
```

### 1.3 ใช้ cURL (สำหรับ Command Line)

```bash
curl -X POST http://localhost:8000/save_test_data.php \
  -H "Content-Type: application/json" \
  -d '{
    "timestamp": "2025-12-01T10:30:00.000Z",
    "date": "01/12/2568",
    "hn": "67-23-012345",
    "age": 35,
    "gender": "male",
    "weight": 75,
    "measurements": {
        "handgrip": 48,
        "legstrength": 145,
        "backstrength": 125,
        "flexibility": 12
    },
    "results": {
        "handgrip": {"level": "ดี", "class": "good"},
        "legstrength": {"level": "ดีมาก", "class": "excellent"},
        "backstrength": {"level": "ดีมาก", "class": "excellent"},
        "flexibility": {"level": "ดี", "class": "good"}
    }
}'
```

---

## วิธีที่ 2: แก้ไข JSON โดยตรง

### 2.1 แก้ไข data.json

```bash
# เปิดไฟล์
notepad C:\Apps\Rehap\pdf\report\data.json
```

เพิ่มข้อมูลใหม่ใน array:

```json
[
  {
    "timestamp": "2025-11-27T09:12:00.000Z",
    "date": "27/11/2568",
    "hn": "67-23-012242",
    ...
  },
  {
    "timestamp": "2025-12-01T10:30:00.000Z",
    "date": "01/12/2568",
    "hn": "67-23-NEW001",
    "age": 40,
    "gender": "female",
    "weight": 65,
    "measurements": {
        "handgrip": 28,
        "legstrength": 85,
        "backstrength": 95,
        "flexibility": 15
    },
    "results": {
        "handgrip": {"level": "ดี", "class": "good"},
        "legstrength": {"level": "ปานกลาง", "class": "average"},
        "backstrength": {"level": "ดี", "class": "good"},
        "flexibility": {"level": "ดีมาก", "class": "excellent"}
    }
  }
]
```

### 2.2 อัพเดท data.js ด้วย

หลังแก้ data.json ต้องรัน PHP script เพื่ออัพเดท data.js:

```php
<?php
$dataFile = __DIR__ . '/pdf/report/data.json';
$dataJsFile = __DIR__ . '/pdf/report/data.js';

$allData = json_decode(file_get_contents($dataFile), true);

$jsContent = "// Physical Fitness Test Data\n";
$jsContent .= "// This file is automatically updated by save_test_data.php\n";
$jsContent .= "var FITNESS_TEST_DATA = " . json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";\n";

file_put_contents($dataJsFile, $jsContent);

echo "Updated successfully!\n";
?>
```

บันทึกเป็น `update_data_js.php` แล้วรัน:
```bash
php update_data_js.php
```

---

## วิธีที่ 3: ใช้ Form HTML

สร้างไฟล์ `add_test_form.html`:

```html
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลการทดสอบ</title>
    <style>
        body { font-family: 'Noto Sans Thai', Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 150px; font-weight: bold; }
        input, select { padding: 5px; width: 200px; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h2>เพิ่มข้อมูลการทดสอบสมรรถภาพ</h2>
    <form id="testForm">
        <div class="form-group">
            <label>HN:</label>
            <input type="text" name="hn" required>
        </div>
        <div class="form-group">
            <label>อายุ:</label>
            <input type="number" name="age" required>
        </div>
        <div class="form-group">
            <label>เพศ:</label>
            <select name="gender" required>
                <option value="male">ชาย</option>
                <option value="female">หญิง</option>
            </select>
        </div>
        <div class="form-group">
            <label>น้ำหนัก (kg):</label>
            <input type="number" name="weight" required>
        </div>
        <h3>ผลการทดสอบ</h3>
        <div class="form-group">
            <label>Hand Grip:</label>
            <input type="number" name="handgrip" required>
        </div>
        <div class="form-group">
            <label>Leg Strength:</label>
            <input type="number" name="legstrength" required>
        </div>
        <div class="form-group">
            <label>Back Strength:</label>
            <input type="number" name="backstrength" required>
        </div>
        <div class="form-group">
            <label>Flexibility:</label>
            <input type="number" name="flexibility" required>
        </div>
        <div class="form-group">
            <label>Push-up (optional):</label>
            <input type="number" name="pushup">
        </div>
        <button type="submit">บันทึกข้อมูล</button>
    </form>

    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // สร้าง object ตามโครงสร้างที่ต้องการ
            const testData = {
                timestamp: new Date().toISOString(),
                date: new Date().toLocaleDateString('th-TH'),
                hn: data.hn,
                age: parseInt(data.age),
                gender: data.gender,
                weight: parseInt(data.weight),
                measurements: {
                    handgrip: parseInt(data.handgrip),
                    legstrength: parseInt(data.legstrength),
                    backstrength: parseInt(data.backstrength),
                    flexibility: parseInt(data.flexibility)
                },
                results: evaluateResults(data)
            };

            if (data.pushup) {
                testData.measurements.pushup = parseInt(data.pushup);
            }

            // ส่งข้อมูล
            fetch('http://localhost:8000/save_test_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(testData)
            })
            .then(response => response.json())
            .then(result => {
                alert('บันทึกสำเร็จ! ทั้งหมด ' + result.total_records + ' records');
                e.target.reset();
            })
            .catch(error => {
                alert('เกิดข้อผิดพลาด: ' + error);
            });
        });

        function evaluateResults(data) {
            // ตัวอย่างการประเมินผล (ควรปรับตามเกณฑ์จริง)
            return {
                handgrip: evaluateLevel(parseInt(data.handgrip), [30, 40, 50]),
                legstrength: evaluateLevel(parseInt(data.legstrength), [80, 120, 150]),
                backstrength: evaluateLevel(parseInt(data.backstrength), [70, 100, 130]),
                flexibility: evaluateLevel(parseInt(data.flexibility), [5, 10, 15])
            };
        }

        function evaluateLevel(value, thresholds) {
            if (value >= thresholds[2]) return { level: "ดีมาก", class: "excellent" };
            if (value >= thresholds[1]) return { level: "ดี", class: "good" };
            if (value >= thresholds[0]) return { level: "ปานกลาง", class: "average" };
            if (value >= thresholds[0] * 0.7) return { level: "ต่ำ", class: "low" };
            return { level: "ต่ำมาก", class: "very-low" };
        }
    </script>
</body>
</html>
```

---

## โครงสร้างข้อมูล JSON

### ฟิลด์ที่จำเป็น (Required):
```json
{
    "timestamp": "ISO 8601 timestamp",
    "date": "วัน/เดือน/ปี (Thai format)",
    "hn": "เลข HN",
    "age": "อายุ (number)",
    "gender": "male หรือ female",
    "weight": "น้ำหนัก (number)",
    "measurements": {
        "handgrip": "ค่าที่วัดได้ (number)",
        "legstrength": "ค่าที่วัดได้ (number)",
        "backstrength": "ค่าที่วัดได้ (number)",
        "flexibility": "ค่าที่วัดได้ (number)"
    },
    "results": {
        "handgrip": {
            "level": "ระดับภาษาไทย",
            "class": "excellent/good/average/low/very-low"
        },
        ...
    }
}
```

### ฟิลด์ที่ไม่จำเป็น (Optional):
- `measurements.pushup`
- `results.pushup`

---

## การดู Dashboard

หลังจากบันทึกข้อมูลแล้ว:

1. **เปิดแบบ Static (ไม่ต้องใช้ Server)**:
   ```
   ดับเบิลคลิก: C:\Apps\Rehap\pdf\report\dashboard.html
   ```

2. **เปิดผ่าน PHP Server**:
   ```bash
   cd C:\Apps\Rehap
   php -S localhost:8000
   ```
   เปิด: http://localhost:8000/pdf/report/dashboard.html

3. **Refresh ข้อมูล**:
   - กดปุ่ม "Refresh Data" บน dashboard
   - หรือ Refresh หน้าเว็บ (F5)

---

## ตัวอย่างการใช้งานจริง

### สถานการณ์: มีระบบทดสอบที่มีอยู่แล้ว

ถ้ามี form ทดสอบอยู่แล้ว เพียงเพิ่ม code นี้เมื่อกดบันทึก:

```javascript
// หลังจากประเมินผลเสร็จแล้ว
function saveToAnalytics(testResults) {
    fetch('http://localhost:8000/save_test_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(testResults)
    })
    .then(response => response.json())
    .then(data => console.log('Saved to analytics:', data))
    .catch(error => console.error('Analytics error:', error));
}
```

---

## การ Backup ข้อมูล

### อัตโนมัติ (แนะนำ):
สร้าง script backup:

```php
<?php
// backup_data.php
$source = __DIR__ . '/pdf/report/data.json';
$backupDir = __DIR__ . '/backups';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$backupFile = $backupDir . '/data_backup_' . date('Y-m-d_H-i-s') . '.json';
copy($source, $backupFile);

echo "Backup created: $backupFile\n";
?>
```

รัน backup ทุกวัน:
```bash
php backup_data.php
```

### Manual:
```bash
copy C:\Apps\Rehap\pdf\report\data.json C:\Apps\Rehap\backups\data_backup.json
```

---

## Troubleshooting

### ปัญหา: Dashboard ไม่แสดงข้อมูลใหม่

**แก้ไข:**
1. ตรวจสอบว่า data.json และ data.js อัพเดทหรือไม่
2. กด Refresh Data บน dashboard
3. Clear browser cache (Ctrl + F5)

### ปัญหา: CORS Error

**แก้ไข:**
- ใช้ PHP Server แทนการเปิดไฟล์โดยตรง
- หรือใช้ data.js (ไม่มีปัญหา CORS)

### ปัญหา: JSON Invalid

**แก้ไข:**
1. ตรวจสอบ syntax ด้วย https://jsonlint.com/
2. ตรวจสอบ comma สุดท้าย (ต้องไม่มี)
3. ตรวจสอบ quotes (" ไม่ใช่ ')

---

## สรุป

✅ **วิธีที่แนะนำ**: ใช้ API (save_test_data.php) เพราะ:
- อัพเดท data.json และ data.js พร้อมกัน
- ไม่ต้องกังวลเรื่อง JSON syntax
- ปลอดภัย validation ในตัว

📝 **วิธีสำรอง**: แก้ data.json โดยตรง (เหมาะสำหรับ testing)

🔄 **อัพเดท Dashboard**: กด Refresh Data หรือ F5
