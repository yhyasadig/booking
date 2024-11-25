<?php
$username = $_GET['username'];
$phone = $_GET['phone'];
$ticketcount = $_GET['ticketcount'];
$names = explode(',', $_GET['names']);
$ticketprice = $_GET['ticketprice'];
$seatType = $_GET['seatType'];
$event = $_GET['event']; // استقبل الحدث
$totalPrice = $ticketprice * $ticketcount;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل التذكرة</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px;
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
        h1 {
            color: #007bff;
        }
        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<header>
    <h1>نظام الحجز</h1>
</header>

<nav>
    <a href="index.php">الصفحة الرئيسية</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
    


    <a href="ratings_page.php">تقييم الأحداث</a>
    <a href="addEvent.php">إضافة حدث </a>

</nav>

<h1>تفاصيل التذكرة</h1>

<div class="details">
    <p><strong>الاسم:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($phone); ?></p>
    <p><strong>عدد التذاكر:</strong> <?php echo htmlspecialchars($ticketcount); ?></p>
    <p><strong>أسماء المسافرين:</strong></p>
    <ul>
        <li><?php echo htmlspecialchars($username); ?></li> <!-- الاسم الأول هو الاسم المدخل -->
        <?php for ($i = 2; $i <= $ticketcount; $i++): ?>
            <li><?php echo htmlspecialchars($names[$i - 1]); ?></li>
        <?php endfor; ?>
    </ul>
    <p><strong>نوع المقعد:</strong> <?php echo htmlspecialchars($seatType); ?></p>
    <p><strong>اسم الحدث:</strong> <?php echo htmlspecialchars($event); ?></p> <!-- عرض اسم الحدث -->
    <p><strong>إجمالي السعر:</strong> <?php echo htmlspecialchars($totalPrice); ?> دينار</p>
</div>

<form action="index.php" method="get" style="text-align: center;">
    <button type="submit">متابعة إلى الدفع</button>
</form>

</body>
</html>
