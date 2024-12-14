<?php
require 'Database.php'; // استدعاء كلاس الاتصال بقاعدة البيانات
require 'Notification.php'; // استدعاء كلاس إدارة الإشعارات

$message = ""; // رسالة تأكيد أو خطأ

// إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'] ?? null; // العنوان المدخل
        $messageContent = $_POST['message'] ?? null; // الرسالة المدخلة
        $isGlobal = isset($_POST['is_global']) ? 1 : 0; // التحقق إذا كان الإشعار عامًا

        // التحقق من صحة المدخلات
        if (!$title || !$messageContent) {
            throw new Exception("العنوان والرسالة مطلوبان.");
        }

        // إنشاء كائن الإشعار وحفظه
        $database = new Database();
        $conn = $database->getConnection(); // الاتصال بقاعدة البيانات
        $notification = new Notification($title, $messageContent, $isGlobal);
        $message = $notification->save($conn);
        $database->closeConnection(); // إغلاق الاتصال بعد الحفظ
    } catch (Exception $e) {
        $message = "خطأ: " . $e->getMessage();
    }
}

// استرجاع جميع الإشعارات العامة
$allNotifications = [];
try {
    $database = new Database();
    $conn = $database->getConnection(); // الاتصال بقاعدة البيانات
    $allNotifications = Notification::fetchAll($conn, 1); // استرجاع الإشعارات العامة فقط
    $database->closeConnection(); // إغلاق الاتصال بعد الجلب
} catch (Exception $e) {
    $message = "خطأ أثناء جلب الإشعارات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة إشعار</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        nav {
            background-color: #0056b3;
            padding: 10px 0;
            text-align: center;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav a:hover {
            background-color: #004494;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .notification {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .notification:last-child {
            border-bottom: none;
        }
        .notification h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .notification p {
            margin: 5px 0;
            color: #555;
        }
        .notification span {
            display: block;
            color: #999;
            font-size: 12px;
        }
        form {
            margin-bottom: 20px;
        }
        form input, form textarea, form button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
        }
        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            color: white;
            background-color: green;
        }
        .error {
            background-color: red;
        }
    </style>
</head>
<body>

<header>
    <h1>إدارة الإشعارات</h1>
</header>

<nav>
    <a href="addNotification.php">إضافة إشعار</a>
    <a href="home.php">الصفحة الرئيسية</a>
</nav>

<div class="container">
    <h2>إضافة إشعار جديد</h2>
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'خطأ') !== false ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="title" placeholder="عنوان الإشعار" required>
        <textarea name="message" placeholder="نص الإشعار" required></textarea>
        <label>
            <input type="checkbox" name="is_global"> جعل الإشعار عامًا
        </label>
        <button type="submit">إضافة</button>
    </form>

    <h2>الإشعارات السابقة</h2>
    <?php if (is_array($allNotifications) && count($allNotifications) > 0): ?>
        <?php foreach ($allNotifications as $notification): ?>
            <div class="notification">
                <h3><?= htmlspecialchars($notification['title']) ?></h3>
                <p><?= htmlspecialchars($notification['message']) ?></p>
                <span><?= $notification['created_at'] ?></span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>لا توجد إشعارات لعرضها.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 نظام الإشعارات. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
