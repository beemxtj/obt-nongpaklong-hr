<?php
// layouts/alert.php

// ตรวจสอบว่ามี session_start() แล้วใน index.php หรือ Controller
// ถ้ายังไม่มี สามารถเพิ่ม session_start(); ที่นี่ได้ แต่โดยปกติควรอยู่ในจุดเริ่มต้นของแอปพลิเคชัน
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// ตรวจสอบและแสดงข้อความ Success
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['success_message']);
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    unset($_SESSION['success_message']); // ลบข้อความออกจาก session หลังจากแสดงแล้ว
}

// ตรวจสอบและแสดงข้อความ Error
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['error_message']);
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    unset($_SESSION['error_message']); // ลบข้อความออกจาก session หลังจากแสดงแล้ว
}
?>