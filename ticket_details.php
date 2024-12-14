<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

// الحصول على معرف المستخدم ومعرف الحجز
$userid = $_SESSION['userid'];
$bookingId = isset($_GET['bookingId']) ? intval($_GET['bookingId']) : null;

// التحقق من أن معرف الحجز موجود وصحيح
if (!$bookingId || $bookingId <= 0) {
    echo "<script>alert('رقم معرف الحجز غير موجود أو غير صالح.'); window.location.href='book_ticket.php';</script>";
    exit;
}

try {
    // الاتصال بقاعدة البيانات
    $db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب بيانات الحجز من جدول bookings
    $stmt = $db->prepare("
        SELECT 
            b.bookingID, 
            b.eventName, 
            b.phone, 
            b.seatType, 
            b.ticketPrice, 
            b.ticketCount, 
            b.created_at, 
            u.username, 
            u.email 
        FROM bookings b
        INNER JOIN users u ON b.userid = u.userid
        WHERE b.bookingID = :bookingId AND b.userid = :userid
    ");
    $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    // تحقق من وجود بيانات الحجز
    if (!$booking) {
        echo "<script>alert('لا توجد بيانات للحجز المطلوب. تأكد من صحة رقم الحجز.'); window.location.href='book_ticket.php';</script>";
        exit;
    }

    // استخراج البيانات
    $eventName = $booking['eventName'];
    $phone = $booking['phone'];
    $seatType = $booking['seatType'];
    $ticketPrice = $booking['ticketPrice'];
    $ticketCount = $booking['ticketCount'];
    $createdAt = $booking['created_at'];
    $username = $booking['username'];
    $email = $booking['email'];

    // حساب السعر الإجمالي
    $totalPrice = $ticketPrice * $ticketCount;

} catch (PDOException $e) {
    // عرض رسالة خطأ إذا كان هناك مشكلة في الاتصال بقاعدة البيانات
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
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
</nav>

<h1>تفاصيل التذكرة</h1>

<div class="details">
    <p><strong>اسم المستخدم:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>اسم الحدث:</strong> <?php echo htmlspecialchars($eventName); ?></p>
    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($phone); ?></p>
    <p><strong>نوع المقعد:</strong> <?php echo htmlspecialchars($seatType); ?></p>
    <p><strong>عدد التذاكر:</strong> <?php echo htmlspecialchars($ticketCount); ?></p>
    <p><strong>السعر لكل تذكرة:</strong> <?php echo htmlspecialchars($ticketPrice); ?> دينار</p>
    <p><strong>السعر الإجمالي:</strong> <?php echo htmlspecialchars($totalPrice); ?> دينار</p>
    <p><strong>تاريخ الحجز:</strong> <?php echo htmlspecialchars($createdAt); ?></p>
</div>

<form action="index.php" method="get" style="text-align: center;">
    <button type="submit">متابعة إلى الدفع</button>
</form>

</body>
</html>
