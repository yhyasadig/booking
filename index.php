<?php
session_start(); // بدء الجلسة

class PaymentHandler {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function addPaymentAndBooking($cardNumber, $methodName, $userid, $booking) {
        try {
            $this->db->beginTransaction();

            // إدخال بيانات الدفع إلى جدول paymentmethods
            $stmt = $this->db->prepare(
                "INSERT INTO paymentmethods (cardNumber, paymentMethodName, userid) 
                VALUES (:cardNumber, :methodName, :userid)"
            );
            $stmt->bindParam(':cardNumber', $cardNumber);
            $stmt->bindParam(':methodName', $methodName);
            $stmt->bindParam(':userid', $userid);
            $stmt->execute();

            // إدخال بيانات الحجز إلى جدول bookings
            $stmt = $this->db->prepare(
                "INSERT INTO bookings (userid, eventName, phone, seatType, ticketPrice, ticketCount, created_at) 
                VALUES (:userid, :eventName, :phone, :seatType, :ticketPrice, :ticketCount, NOW())"
            );
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':eventName', $booking['event']);
            $stmt->bindParam(':phone', $booking['phone']);
            $stmt->bindParam(':seatType', $booking['seatType']);
            $stmt->bindParam(':ticketPrice', $booking['ticketprice']);
            $stmt->bindParam(':ticketCount', $booking['ticketcount']);
            $stmt->execute();

            $this->db->commit();
            return "تمت إضافة طريقة الدفع والحجز بنجاح.";
        } catch (PDOException $e) {
            $this->db->rollBack();
            return "خطأ أثناء معالجة الدفع والحجز: " . $e->getMessage();
        }
    }
}

$paymentHandler = new PaymentHandler();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = $_POST['cardNumber'];
    $methodName = $_POST['paymentMethodName'];
    $userid = $_SESSION['userid']; // استدعاء userid من الجلسة
    $booking = $_SESSION['booking']; // استدعاء بيانات الحجز من الجلسة

    $message = $paymentHandler->addPaymentAndBooking($cardNumber, $methodName, $userid, $booking);

    // مسح بيانات الحجز من الجلسة بعد الإضافة
    unset($_SESSION['booking']);

    // عرض رسالة النجاح باستخدام JavaScript
    echo "<script>alert('تمت إضافة طريقة الدفع والحجز بنجاح.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طرق الدفع والحجز</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
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
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            color: #007bff;
        }
        img {
            width: 100%; /* اجعل الصورة بعرض الشاشة */
            height: auto; /* احتفظ بنسبة العرض إلى الارتفاع */
            border-radius: 8px; /* إضافة حواف دائرية */
            margin-bottom: 15px;
        }
        input, select, button {
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
    <h1>طرق الدفع والحجز</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="paymentMethods.php">طرق الدفع</a>
</nav>

<div class="container">
    <h2>إضافة طريقة دفع</h2>
    <!-- الصورة -->
    <img src="ض.png" alt="صورة طريقة الدفع">
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
    <form method="post" action="">
        <label for="cardNumber">رقم البطاقة:</label>
        <input type="text" id="cardNumber" name="cardNumber" maxlength="16" pattern="\d{16}" title="أدخل رقم بطاقة مكون من 16 رقمًا" required>

        <label for="paymentMethodName">طريقة الدفع:</label>
        <select id="paymentMethodName" name="paymentMethodName" required>
            <option value="Sadad">سداد</option>
            <option value="MobiCash">موبي كاش</option>
            <option value="MobiMal">موبي مال</option>
            <option value="BankCard">بطاقة مصرفية</option>
        </select>

        <button type="submit">إضافة</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
