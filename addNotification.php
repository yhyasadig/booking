<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

// الاتصال بقاعدة البيانات
require 'Database.php';

try {
    // إنشاء اتصال بقاعدة البيانات
    $db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب دور المستخدم من قاعدة البيانات
    $stmt = $db->prepare("SELECT role FROM users WHERE userid = :userid");
    $stmt->bindParam(':userid', $_SESSION['userID']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // إذا كان المستخدم ليس admin
    if (!$user || $user['role'] != 'admin') {
        echo "<script>alert('غير مصرح لك بالوصول إلى هذه الصفحة.'); window.location.href='home.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// جلب بيانات الإشعارات وإدارتها
require 'Notification.php';

$message = ""; // رسالة تأكيد أو خطأ

// إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'] ?? null;
        $messageContent = $_POST['message'] ?? null;
        $isGlobal = isset($_POST['is_global']) ? 1 : 0;

        if (!$title || !$messageContent) {
            throw new Exception("العنوان والرسالة مطلوبان.");
        }

        $database = new Database();
        $conn = $database->getConnection();

        $notification = new Notification($title, $messageContent, $isGlobal);
        $message = $notification->save($conn);
        $database->closeConnection();
    } catch (Exception $e) {
        $message = "خطأ: " . $e->getMessage();
    }
}

// استرجاع جميع الإشعارات العامة
$allNotifications = [];
try {
    $database = new Database();
    $conn = $database->getConnection();
    $allNotifications = Notification::fetchAll($conn, 1);
    $database->closeConnection();
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
</div>

<footer>
    <p>&copy; 2024 نظام الإشعارات. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
