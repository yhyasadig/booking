<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['userID'])) {
    header('Location: login.php'); // إعادة التوجيه إذا لم يتم تسجيل الدخول
    exit;
}

// الاتصال بقاعدة البيانات
require 'Database.php';

$db = new Database();
$conn = $db->getConnection();

// عرض الإشعار العام إذا كان موجودًا
$notification = "";
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']); // إزالة الإشعار بعد عرضه
}

// جلب جميع الإشعارات العامة والخاصة بالمستخدم
$userId = $_SESSION['userID'];
$notifications = [];
try {
    $query = "
        SELECT title, message, created_at 
        FROM notifications 
        WHERE is_global = 1 OR user_id = :user_id
        ORDER BY created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $notification = "خطأ أثناء جلب الإشعارات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة الرئيسية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
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
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .notification {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .user-notification {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .user-notification h3 {
            margin: 0;
            color: #007bff;
        }
        .user-notification p {
            margin: 5px 0;
            color: #333;
        }
        .user-notification span {
            color: #666;
            font-size: 0.9em;
        }
        h2 {
            color: #007bff;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

<header>
    <h1>نظام الحجز</h1>
</header>

<nav>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
    <a href="Event_Page.php">الأحداث</a>
    <a href="add_discounts.php">إضافة خصم</a>
    <a href="ratings_page.php">تقييم الأحداث</a>
    <a href="addEvent.php">إضافة حدث</a>
    <a href="addNotification.php">إضافة إشعار</a>
</nav>

<div class="container">
    <!-- عرض الإشعار العام إذا كان موجودًا -->
    <?php if ($notification): ?>
        <div class="notification">
            <?= htmlspecialchars($notification); ?>
        </div>
    <?php endif; ?>

    <h2>مرحبًا بك في نظام الحجز!</h2>
    <p>يمكنك حجز التذاكر التي ترغب بها.</p>
    <p>اختر أحد الخيارات في الأعلى للبدء.</p>

    <h2>إشعاراتك:</h2>
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="user-notification">
                <h3><?= htmlspecialchars($notif['title']); ?></h3>
                <p><?= htmlspecialchars($notif['message']); ?></p>
                <span>تم الإضافة بتاريخ: <?= htmlspecialchars($notif['created_at']); ?></span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>لا توجد إشعارات مرتبطة بحسابك حاليًا.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
