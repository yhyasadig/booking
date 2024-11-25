<?php

session_start();

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
        h1 {
            color: #333;
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
    <a href="Event_Page.php">الأحداث </a>






    <a href="ratings_page.php">تقييم الأحداث</a>
    <a href="addEvent.php">إضافة حدث </a>

</nav>

<div class="container">
    <h2>مرحبًا بك في نظام الحجز!</h2>
    <p>يمكنك حجز التذاكر التي ترغب بها </p>
    <p>اختر أحد الخيارات في الأعلى للبدء.</p>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>ٍ