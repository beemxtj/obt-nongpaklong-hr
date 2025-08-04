<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - ไม่พบหน้า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 6rem; /* Adjust as needed */
            color: #ef4444; /* Red-500 */
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        h2 {
            font-size: 1.875rem; /* text-3xl */
            color: #1f2937; /* Gray-800 */
            margin-bottom: 1rem;
        }
        p {
            font-size: 1rem; /* text-base */
            color: #4b5563; /* Gray-600 */
            margin-bottom: 2rem;
        }
        a {
            display: inline-block;
            background-color: #4f46e5; /* Indigo-600 */
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #4338ca; /* Indigo-700 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>ไม่พบหน้าที่คุณกำลังมองหา</h2>
        <p>ขออภัย, หน้าที่คุณพยายามเข้าถึงอาจถูกลบ เปลี่ยนชื่อ หรือไม่มีอยู่จริง</p>
        <a href="<?php echo (defined('BASE_URL') ? BASE_URL : ''); ?>/dashboard">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>