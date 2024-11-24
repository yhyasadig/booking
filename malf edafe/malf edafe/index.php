<?php
session_start(); // بدء الجلسة

// تحقق من أن المستخدم قد قام بتسجيل الدخول وأنه موجود في الجلسة
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

class PaymentMethods {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=booking_system", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function addPaymentMethod($cardNumber, $methodName, $userID) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO PaymentMethods (cardNumber, paymentMethodName, userID) 
                VALUES (:cardNumber, :methodName, :userID)"
            );
            $stmt->bindParam(':cardNumber', $cardNumber);
            $stmt->bindParam(':methodName', $methodName);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            return "تمت إضافة طريقة الدفع بنجاح.";
        } catch (PDOException $e) {
            return "خطأ أثناء إضافة طريقة الدفع: " . $e->getMessage();
        }
    }

    public function getPaymentMethods() {
        try {
            $stmt = $this->db->query("SELECT pm.cardNumber, pm.paymentMethodName, u.userName 
                                      FROM PaymentMethods pm 
                                      JOIN Users u ON pm.userID = u.userID");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

$paymentMethods = new PaymentMethods();

// معالجة الإضافة إذا تم إرسال النموذج
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = $_POST['cardNumber'];
    $methodName = $_POST['paymentMethodName'];
    $userID = $_SESSION['userID']; // جلب userID من الجلسة
    $message = $paymentMethods->addPaymentMethod($cardNumber, $methodName, $userID);

    // إعادة توجيه إلى نفس الصفحة بعد معالجة النموذج
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit;
}

// التحقق من وجود رسالة نجاح
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "تمت إضافة طريقة الدفع بنجاح.";
}

// جلب البيانات من قاعدة البيانات
$methods = $paymentMethods->getPaymentMethods();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طرق الدفع</title>
    <style>
        /* تصميم الصفحة بالكامل */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }

        /* تصميم الهيدر */
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        /* تصميم شريط التنقل */
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

        /* تنسيق المربع الأبيض */
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* تنسيق الصورة */
        .paymentTypeImage {
            max-width: 90%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* النصوص والعناوين */
        h2 {
            color: #007bff;
        }

        /* الحقول والأزرار */
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

        /* قائمة طرق الدفع */
        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* تصميم الفوتر */
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
    <a href="paymentMethods.php">طرق الدفع</a>
</nav>

<div class="container">
    <h2>إضافة طريقة دفع</h2>

    <!-- إضافة الصورة -->
    <div class="paymentType">
        <img src="ض.png" alt="paymentType" class="paymentTypeImage">
    </div>

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

    <h2>قائمة طرق الدفع</h2>
    <ul>
        <?php foreach ($methods as $method): ?>
            <li>
                <strong>رقم البطاقة:</strong> <?php echo $method['cardNumber']; ?> -
                <strong>طريقة الدفع:</strong> <?php echo $method['paymentMethodName']; ?> -
                <strong>اسم المستخدم:</strong> <?php echo $method['userName']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
