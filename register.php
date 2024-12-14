<?php
include_once 'Database.php';
include_once 'User.php';

session_start();
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($conn);
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->profession = $_POST['profession']; // تخزين المهنة

    // التحقق من وجود البريد الإلكتروني مسبقًا
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email', $user->email);
    $stmt->execute();
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        // إذا كان البريد الإلكتروني موجودًا بالفعل
        echo "<script>alert('البريد الإلكتروني مسجل مسبقًا. يرجى استخدام بريد إلكتروني آخر.');</script>";
    } else {
        // البحث عن DiscountID بناءً على المهنة
        $stmt = $conn->prepare("SELECT DiscountID FROM Discounts WHERE Profession = :profession");
        $stmt->bindParam(":profession", $user->profession);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $user->discountID = $result['DiscountID'];
        } else {
            $user->discountID = null; // لا يوجد خصم
        }

        // تسجيل المستخدم في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, profession, DiscountID) VALUES (:username, :email, :password, :profession, :discountID)");
        $hashed_password = password_hash($user->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':profession', $user->profession);
        $stmt->bindParam(':discountID', $user->discountID);

        if ($stmt->execute()) {
            $_SESSION['userid'] = $conn->lastInsertId(); // الحصول على معرف المستخدم الجديد
            echo "<script>alert('تم تسجيل المستخدم بنجاح.'); window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('فشل التسجيل. حاول مرة أخرى.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل مستخدم جديد</title>
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
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input, select, button {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
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
    <h1>شركة متين للحجوزات</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
    <a href="login.php">تسجيل دخول</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="ratings_page.php">تقييم الأحداث</a>
    <a href="addEvent.php">إضافة حدث</a>
</nav>

<div class="container">
    <form action="register.php" method="POST">
        <h2>تسجيل مستخدم جديد</h2>
        <input type="text" name="username" placeholder="اسم المستخدم" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <select name="profession" required>
            <option value="">اختر مهنة...</option>
            <option value="طالب">طالب</option>
            <option value="عسكري">عسكري</option>
            <option value="موظف">موظف</option>
            <option value="لا يعمل">لا يعمل</option>
        </select>
        <button type="submit">تسجيل</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
