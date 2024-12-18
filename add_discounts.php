<?php
session_start();

// التحقق من أن المستخدم قد قام بتسجيل الدخول
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

// التحقق من أن المستخدم لديه دور admin
try {
    $db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب دور المستخدم من قاعدة البيانات
    $stmt = $db->prepare("SELECT role FROM users WHERE userid = :userid");
    $stmt->bindParam(':userid', $_SESSION['userID'], PDO::PARAM_INT);
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

// استدعاء الملفات المطلوبة
require_once 'Database.php';
require_once 'Discount.php';

// الاتصال بقاعدة البيانات
try {
    $db = new Database();
    $conn = $db->getConnection();
    $discount = new Discount($conn);
} catch (Exception $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// معالجة إضافة الخصم
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $profession = $_POST['profession'];
        $discountAmount = $_POST['discount'];
        $description = $_POST['description'] ?? null;

        // التحقق من وجود الخصم مسبقًا لنفس المهنة
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM discounts WHERE profession = :profession");
        $checkStmt->bindParam(':profession', $profession, PDO::PARAM_STR);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // إذا كانت القيمة موجودة بالفعل
            $message = "⚠️ المهنة '" . htmlspecialchars($profession) . "' تحتوي بالفعل على خصم.";
        } else {
            // إدخال الخصم إذا لم يكن موجودًا مسبقًا
            $discount->profession = $profession;
            $discount->discountAmount = $discountAmount;
            $discount->description = $description;

            if ($discount->addDiscount()) {
                $message = "✅ تمت إضافة الخصم بنجاح.";
            } else {
                $message = "❌ فشل في إضافة الخصم.";
            }
        }
    } catch (Exception $e) {
        $message = "⚠️ حدث خطأ: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة خصومات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #007bff;
            text-align: center;
        }
        input, select, button, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
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
    <h1>إضافة خصومات</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
    <a href="addEvent.php">إضافة حدث</a>
    <a href="ratings_page.php">التقييمات</a>
</nav>

<div class="container">
    <h2>إضافة خصم جديد</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, '⚠️') !== false ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="profession">اختر المهنة:</label>
        <select name="profession" id="profession" required>
            <option value="">-- اختر المهنة --</option>
            <option value="طالب">طالب</option>
            <option value="عسكري">عسكري</option>
            <option value="موظف">موظف</option>
            <option value="لا يعمل">لا يعمل</option>
        </select>

        <label for="discount">الخصم (%):</label>
        <input type="number" name="discount" id="discount" min="0" max="100" required>

        <label for="description">وصف الخصم:</label>
        <textarea name="description" id="description" rows="3" placeholder="أدخل وصف الخصم (اختياري)"></textarea>

        <button type="submit">إضافة الخصم</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
