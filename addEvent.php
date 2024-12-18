<?php
require_once 'Database.php';

session_start();



///////



// تحقق من أن المستخدم قد قام بتسجيل الدخول
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

// التحقق من أن المستخدم لديه دور admin
try {
    $db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب دور المستخدم من قاعدة البيانات
    $stmt = $db->prepare("SELECT role FROM users WHERE userid = :userid");
    $stmt->bindParam(':userid', $_SESSION['userID']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // إذا كان المستخدم ليس admin
    if (!$user || $user['role'] != 'admin') {
        echo "<script>alert('غير مصرح لك بالوصول إلى هذه الصفحة.'); window.location.href='home.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}



/////
class Events {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=booking_system2", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function addEventWithSeats($name, $date, $location, $type, $image, $price, $seatsData) {
        try {
            // ابدأ معاملة قاعدة البيانات
            $this->db->beginTransaction();

            // إدخال الحدث في جدول events
            $stmt = $this->db->prepare(
                "INSERT INTO events (eventName, eventDate, eventLocation, eventType, eventImage, ticketPrice) 
                 VALUES (:name, :date, :location, :type, :image, :price)"
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
            $stmt->bindParam(':price', $price);
            $stmt->execute();

            // جلب eventID للحدث الذي تم إضافته
            $eventId = $this->db->lastInsertId();

            // إدخال بيانات المقاعد في جدول seats
            $seatsStmt = $this->db->prepare(
                "INSERT INTO seats (seatsnumber, seatstype, price, eventid) 
                 VALUES (:seatsnumber, :seatstype, :price, :eventid)"
            );
            foreach ($seatsData as $seat) {
                $seatsStmt->bindParam(':seatsnumber', $seat['seatsnumber']);
                $seatsStmt->bindParam(':seatstype', $seat['seatstype']);
                $seatsStmt->bindParam(':price', $seat['price']);
                $seatsStmt->bindParam(':eventid', $eventId);
                $seatsStmt->execute();
            }

            // التزام المعاملة
            $this->db->commit();

            return $eventId; // إرجاع معرف الحدث
        } catch (PDOException $e) {
            // إرجاع المعاملة إذا حدث خطأ
            $this->db->rollBack();
            // طباعة تفاصيل الخطأ للمساعدة في تحديد المشكلة
            echo "خطأ: " . $e->getMessage();
            return false;
        }
    }
}

$events = new Events();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['eventName'];
    $date = $_POST['eventDate'];
    $location = $_POST['eventLocation'];
    $type = $_POST['eventType'];
    $price = $_POST['ticketPrice'];

    // تحقق من رفع الصورة
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['eventImage']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $image = file_get_contents($_FILES['eventImage']['tmp_name']); // قراءة الصورة

            // بيانات المقاعد
            $seatsData = [
                [
                    'seatsnumber' => $_POST['firstClassSeats'],
                    'seatstype' => 'درجة أولى',
                    'price' => $_POST['firstClassPrice']
                ],
                [
                    'seatsnumber' => $_POST['secondClassSeats'],
                    'seatstype' => 'درجة ثانية',
                    'price' => $_POST['secondClassPrice']
                ],
                [
                    'seatsnumber' => $_POST['thirdClassSeats'],
                    'seatstype' => 'درجة ثالثة',
                    'price' => $_POST['thirdClassPrice']
                ]
            ];

            $eventId = $events->addEventWithSeats($name, $date, $location, $type, $image, $price, $seatsData);

            if ($eventId) {
                // إعادة التوجيه بناءً على نوع العرض
                switch ($type) {
                    case 'عرض طيران':
                        header("Location: Flight_Event_Offerings.php");
                        break;
                    case 'عرض مهرجان':
                        header("Location: National_Festival_Offerings.php");
                        break;
                    case 'عرض كرة قدم':
                        header("Location: LaLigaMatchOfferings.php");
                        break;
                    case 'عرض فيلم':
                        header("Location: Cinema_Event_Offerings.php");
                        break;
                    default:
                        header("Location: Event_Page.php");
                        break;
                }
                exit;
            } else {
                $message = "حدث خطأ أثناء إضافة الحدث والمقاعد.";
            }
        } else {
            $message = "الرجاء رفع ملف صورة بصيغة صحيحة (JPEG/PNG/GIF).";
        }
    } else {
        $message = "يرجى رفع صورة للحدث.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة حدث</title>
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
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
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

        <label for="ticketPrice">سعر التذكرة:</label>
        <input type="number" id="ticketPrice" name="ticketPrice" step="0.01" required>

        <label for="firstClassSeats">عدد مقاعد الدرجة الأولى:</label>
        <input type="number" id="firstClassSeats" name="firstClassSeats" required>

        <label for="firstClassPrice">سعر مقعد الدرجة الأولى:</label>
        <input type="number" id="firstClassPrice" name="firstClassPrice" step="0.01" required>

        <label for="secondClassSeats">عدد مقاعد الدرجة الثانية:</label>
        <input type="number" id="secondClassSeats" name="secondClassSeats" required>

        <label for="secondClassPrice">سعر مقعد الدرجة الثانية:</label>
        <input type="number" id="secondClassPrice" name="secondClassPrice" step="0.01" required>

        <label for="thirdClassSeats">عدد مقاعد الدرجة الثالثة:</label>
        <input type="number" id="thirdClassSeats" name="thirdClassSeats" required>

        <label for="thirdClassPrice">سعر مقعد الدرجة الثالثة:</label>
        <input type="number" id="thirdClassPrice" name="thirdClassPrice" step="0.01" required>

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
