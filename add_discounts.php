<?php
require_once 'Database.php';
require_once 'Discount.php';

session_start();

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
        $discount->profession = $_POST['profession'];
        $discount->discountAmount = $_POST['discount'];
        $discount->description = $_POST['description'] ?? null;

        if ($discount->addDiscount()) {
            $message = "تمت إضافة الخصم بنجاح.";
        } else {
            $message = "فشل في إضافة الخصم.";
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
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

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
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
