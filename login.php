<?php
session_start();

include_once 'Database.php';
include_once 'User.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($conn);
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    if ($user->login()) {
        // تخزين معرف المستخدم في الجلسة
        $_SESSION['userID'] = $user->userid;

        // جلب مهنة المستخدم
        $query = "SELECT profession FROM users WHERE userid = :userid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userid', $user->userid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $profession = $result['profession'] ?? null;

        // جلب الخصم بناءً على المهنة
        if ($profession) {
            $query = "SELECT DiscountAmount, Description FROM discounts WHERE Profession = :profession";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':profession', $profession);
            $stmt->execute();
            $discount = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($discount) {
                $discountAmount = $discount['DiscountAmount'];
                $description = $discount['Description'];
                echo "<script>alert('تم تسجيل الدخول بنجاح. لديك خصم $discountAmount%. السبب: $description.'); window.location.href='home.php';</script>";
            } else {
                echo "<script>alert('تم تسجيل الدخول بنجاح. لا يوجد خصم لهذه المهنة.'); window.location.href='home.php';</script>";
            }
        } else {
            echo "<script>alert('تم تسجيل الدخول بنجاح. لا يمكن تحديد مهنتك.'); window.location.href='home.php';</script>";
        }
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
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: center;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 24px;
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
            margin: 50px auto;
            background: white;
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            font-size: 0.9em;
            color: #666;
        }

        footer p {
            margin: 0;
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
        <label for="email">البريد الإلكتروني:</label>
        <input type="email" id="email" name="email" placeholder="البريد الإلكتروني" required>
        
        <label for="password">كلمة المرور:</label>
        <input type="password" id="password" name="password" placeholder="كلمة المرور" required>
        
        <button type="submit">دخول</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
