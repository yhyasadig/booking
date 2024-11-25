<?php
require_once 'Database.php';
require_once 'User.php';


session_start(); // بدء الجلسة

// تحقق من أن المستخدم قد قام بتسجيل الدخول وأنه موجود في الجلسة
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

class Events {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=booking_system", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function addEvent($name, $date, $location, $type, $image) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Events (eventName, eventDate, eventLocation, eventType, eventImage) 
                 VALUES (:name, :date, :location, :type, :image)"
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
            $stmt->execute();
            return "تمت إضافة الحدث بنجاح.";
        } catch (PDOException $e) {
            return "خطأ أثناء إضافة الحدث: " . $e->getMessage();
        }
    }
}

$events = new Events();

// معالجة الإضافة إذا تم إرسال النموذج
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['eventName'];
    $date = $_POST['eventDate'];
    $location = $_POST['eventLocation'];
    $type = $_POST['eventType'];
    $image = file_get_contents($_FILES['eventImage']['tmp_name']); // قراءة الصورة

    $message = $events->addEvent($name, $date, $location, $type, $image);

    // إعادة توجيه بعد الإضافة لتجنب تكرار الإدخال
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit;
}

// التحقق من وجود رسالة نجاح
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "تمت إضافة الحدث بنجاح.";
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة حدث</title>
    <style>
        /* نفس تصميم CSS السابق */
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
            max-width: 800px;
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
    <h1>شركة متين للحجوزات</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
   

    


    <a href="ratings_page.php">تقييم الأحداث</a>
   


    
</nav>

<div class="container">
    <h2>إضافة حدث جديد</h2>

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="eventName">اسم الحدث:</label>
        <input type="text" id="eventName" name="eventName" required>

        <label for="eventDate">تاريخ الحدث:</label>
        <input type="date" id="eventDate" name="eventDate" required>

        <label for="eventLocation">مكان الحدث:</label>
        <input type="text" id="eventLocation" name="eventLocation" required>

        <label for="eventType">نوع العرض:</label>
        <select id="eventType" name="eventType" required>
            <option value="عرض طيران">عرض طيران</option>
            <option value="عرض مهرجان">عرض مهرجان</option>
            <option value="عرض كرة قدم">عرض كرة قدم</option>
            <option value="عرض فيلم">عرض فيلم</option>
        </select>

        <label for="eventImage">صورة الحدث:</label>
        <input type="file" id="eventImage" name="eventImage" accept="image/*" required>

        <button type="submit">إضافة الحدث</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 شركة متين للحجوزات. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>