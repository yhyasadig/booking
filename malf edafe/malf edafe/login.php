<?php
session_start(); // بدء الجلسة

include_once 'Database.php';
include_once 'User.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($conn);
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    if ($user->login()) {
        // تخزين userID في الجلسة
        $_SESSION['userID'] = $user->getUserID(); // استدعاء الدالة التي تعيد userID
        echo "<script>alert('تم تسجيل الدخول بنجاح.'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('فشل تسجيل الدخول. يرجى التحقق من البيانات.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول</title>
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
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #007bff;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
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
    <a href="register.php">تسجيل مستخدم جديد</a>
</nav>

<div class="container">
    <form action="login.php" method="POST">
        <h2>تسجيل دخول</h2>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit">دخول</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
