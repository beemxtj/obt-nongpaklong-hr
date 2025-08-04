<?php
// สร้างไฟล์ php_config_check.php เพื่อตรวจสอบการตั้งค่า

echo "<h2>ตรวจสอบการตั้งค่า PHP สำหรับการอัปโหลดไฟล์</h2>";

// ตรวจสอบการตั้งค่าที่เกี่ยวข้องกับการอัปโหลดไฟล์
$settings = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: sys_get_temp_dir(),
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Recommended</th><th>Status</th></tr>";

// file_uploads
$status = $settings['file_uploads'] ? '✅ OK' : '❌ Disabled';
echo "<tr><td>file_uploads</td><td>" . ($settings['file_uploads'] ? 'On' : 'Off') . "</td><td>On</td><td>$status</td></tr>";

// upload_max_filesize
$upload_size_bytes = return_bytes($settings['upload_max_filesize']);
$status = $upload_size_bytes >= 5242880 ? '✅ OK' : '⚠️ Small'; // 5MB
echo "<tr><td>upload_max_filesize</td><td>{$settings['upload_max_filesize']}</td><td>5M or higher</td><td>$status</td></tr>";

// post_max_size
$post_size_bytes = return_bytes($settings['post_max_size']);
$status = $post_size_bytes >= 8388608 ? '✅ OK' : '⚠️ Small'; // 8MB
echo "<tr><td>post_max_size</td><td>{$settings['post_max_size']}</td><td>8M or higher</td><td>$status</td></tr>";

// max_file_uploads
$status = $settings['max_file_uploads'] >= 20 ? '✅ OK' : '⚠️ Low';
echo "<tr><td>max_file_uploads</td><td>{$settings['max_file_uploads']}</td><td>20 or higher</td><td>$status</td></tr>";

// max_execution_time
$status = $settings['max_execution_time'] >= 30 ? '✅ OK' : '⚠️ Short';
echo "<tr><td>max_execution_time</td><td>{$settings['max_execution_time']}</td><td>30 or higher</td><td>$status</td></tr>";

// memory_limit
$memory_bytes = return_bytes($settings['memory_limit']);
$status = $memory_bytes >= 134217728 ? '✅ OK' : '⚠️ Low'; // 128MB
echo "<tr><td>memory_limit</td><td>{$settings['memory_limit']}</td><td>128M or higher</td><td>$status</td></tr>";

// upload_tmp_dir
$status = is_writable($settings['upload_tmp_dir']) ? '✅ Writable' : '❌ Not Writable';
echo "<tr><td>upload_tmp_dir</td><td>{$settings['upload_tmp_dir']}</td><td>Writable directory</td><td>$status</td></tr>";

echo "</table>";

// ตรวจสอบโฟลเดอร์ uploads
echo "<h3>ตรวจสอบโฟลเดอร์ uploads</h3>";

$uploads_dir = 'uploads/profiles/';
$current_dir = dirname(__FILE__);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Check</th><th>Path</th><th>Status</th></tr>";

// ตรวจสอบว่าโฟลเดอร์มีอยู่หรือไม่
$exists = is_dir($uploads_dir);
$status = $exists ? '✅ Exists' : '❌ Not Found';
echo "<tr><td>Directory Exists</td><td>$uploads_dir</td><td>$status</td></tr>";

if ($exists) {
    // ตรวจสอบสิทธิ์การเขียน
    $writable = is_writable($uploads_dir);
    $status = $writable ? '✅ Writable' : '❌ Not Writable';
    echo "<tr><td>Directory Writable</td><td>$uploads_dir</td><td>$status</td></tr>";
    
    // ตรวจสอบ permissions
    $perms = substr(sprintf('%o', fileperms($uploads_dir)), -4);
    $status = $perms >= '0755' ? '✅ OK' : '⚠️ May need adjustment';
    echo "<tr><td>Permissions</td><td>$perms</td><td>0755 or 0777</td><td>$status</td></tr>";
} else {
    echo "<tr><td>Directory Writable</td><td>-</td><td>❌ Cannot check</td></tr>";
    echo "<tr><td>Permissions</td><td>-</td><td>❌ Cannot check</td></tr>";
}

echo "</table>";

// แสดงวิธีแก้ไขปัญหา
echo "<h3>วิธีแก้ไขปัญหา</h3>";

if (!$settings['file_uploads']) {
    echo "<div style='color: red;'><strong>❌ file_uploads ปิดอยู่:</strong><br>";
    echo "- แก้ไขไฟล์ php.ini: เปลี่ยน file_uploads = Off เป็น file_uploads = On<br>";
    echo "- รีสตาร์ท web server<br></div><br>";
}

if ($upload_size_bytes < 5242880) {
    echo "<div style='color: orange;'><strong>⚠️ upload_max_filesize เล็กเกินไป:</strong><br>";
    echo "- แก้ไขไฟล์ php.ini: เปลี่ยน upload_max_filesize = 5M หรือมากกว่า<br>";
    echo "- รีสตาร์ท web server<br></div><br>";
}

if ($post_size_bytes < 8388608) {
    echo "<div style='color: orange;'><strong>⚠️ post_max_size เล็กเกินไป:</strong><br>";
    echo "- แก้ไขไฟล์ php.ini: เปลี่ยน post_max_size = 8M หรือมากกว่า<br>";
    echo "- post_max_size ต้องมากกว่า upload_max_filesize<br>";
    echo "- รีสตาร์ท web server<br></div><br>";
}

if (!$exists) {
    echo "<div style='color: red;'><strong>❌ โฟลเดอร์ uploads/profiles/ ไม่มี:</strong><br>";
    echo "- สร้างโฟลเดอร์: mkdir -p uploads/profiles/<br>";
    echo "- หรือใช้ PHP: mkdir('uploads/profiles/', 0755, true);<br></div><br>";
}

if ($exists && !is_writable($uploads_dir)) {
    echo "<div style='color: red;'><strong>❌ โฟลเดอร์ไม่มีสิทธิ์เขียน:</strong><br>";
    echo "- Linux/Mac: chmod 755 uploads/profiles/<br>";
    echo "- หรือ: chmod 777 uploads/profiles/ (less secure)<br>";
    echo "- Windows: คลิกขวา > Properties > Security > แก้ไขสิทธิ์<br></div><br>";
}

// ทดสอบการสร้างไฟล์
echo "<h3>ทดสอบการสร้างไฟล์</h3>";

if ($exists && is_writable($uploads_dir)) {
    $test_file = $uploads_dir . 'test_' . time() . '.txt';
    $content = "Test file created at " . date('Y-m-d H:i:s');
    
    if (file_put_contents($test_file, $content)) {
        echo "✅ สามารถสร้างไฟล์ในโฟลเดอร์ได้<br>";
        // ลบไฟล์ทดสอบ
        unlink($test_file);
        echo "✅ สามารถลบไฟล์ได้<br>";
    } else {
        echo "❌ ไม่สามารถสร้างไฟล์ในโฟลเดอร์ได้<br>";
    }
} else {
    echo "❌ ไม่สามารถทดสอบได้เนื่องจากโฟลเดอร์ไม่มีหรือไม่มีสิทธิ์<br>";
}

// Helper function
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

// แสดงข้อมูลเพิ่มเติม
echo "<h3>ข้อมูลเพิ่มเติม</h3>";
echo "Current working directory: " . getcwd() . "<br>";
echo "Script directory: " . dirname(__FILE__) . "<br>";
echo "Temporary directory: " . sys_get_temp_dir() . "<br>";
echo "PHP version: " . phpversion() . "<br>";
echo "Web server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

// สำหรับ Debug - แสดงข้อมูล $_FILES เมื่อมีการส่งไฟล์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Debug Information ($_FILES)</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
}

// Form สำหรับทดสอบการอัปโหลด
echo "<h3>ทดสอบการอัปโหลดไฟล์</h3>";
echo '<form method="post" enctype="multipart/form-data">';
echo '<input type="file" name="test_file" accept="image/*" required><br><br>';
echo '<input type="submit" value="ทดสอบอัปโหลด">';
echo '</form>';

// ถ้ามีการส่งไฟล์ทดสอบ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h4>ผลการทดสอบ:</h4>";
    
    $file = $_FILES['test_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "✅ ไฟล์ถูกอัปโหลดสำเร็จ<br>";
        echo "ชื่อไฟล์: " . htmlspecialchars($file['name']) . "<br>";
        echo "ขนาด: " . number_format($file['size']) . " bytes<br>";
        echo "ประเภท: " . htmlspecialchars($file['type']) . "<br>";
        echo "Temp file: " . htmlspecialchars($file['tmp_name']) . "<br>";
        
        // ตรวจสอบว่าไฟล์ temp มีอยู่จริง
        if (file_exists($file['tmp_name'])) {
            echo "✅ ไฟล์ temporary มีอยู่<br>";
            
            // ลองย้ายไฟล์
            if ($exists && is_writable($uploads_dir)) {
                $target = $uploads_dir . 'test_upload_' . time() . '_' . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    echo "✅ ไฟล์ถูกย้ายไปยัง $target สำเร็จ<br>";
                    // ลบไฟล์ทดสอบ
                    unlink($target);
                    echo "✅ ไฟล์ทดสอบถูกลบแล้ว<br>";
                } else {
                    echo "❌ ไม่สามารถย้ายไฟล์ได้<br>";
                }
            } else {
                echo "❌ โฟลเดอร์ปลายทางไม่พร้อมใช้งาน<br>";
            }
        } else {
            echo "❌ ไฟล์ temporary ไม่มีอยู่<br>";
        }
    } else {
        echo "❌ เกิดข้อผิดพลาดในการอัปโหลด: ";
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "ไฟล์ใหญ่เกิน upload_max_filesize";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "ไฟล์ใหญ่เกิน MAX_FILE_SIZE";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "ไฟล์อัปโหลดไม่สมบูรณ์";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "ไม่ได้เลือกไฟล์";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "ไม่พบ temporary directory";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "ไม่สามารถเขียนไฟล์ลงดิสก์";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "การอัปโหลดถูกหยุดโดย extension";
                break;
            default:
                echo "ข้อผิดพลาดไม่ทราบสาเหตุ (รหัส: {$file['error']})";
                break;
        }
        echo "<br>";
    }
}
?>