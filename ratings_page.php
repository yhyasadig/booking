<?php
// الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "booking_system2";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// جلب جميع الأحداث من جدول events
$query = "SELECT * FROM events";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// معالجة إرسال التقييم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من أن المستخدم قام بتسجيل الدخول
    session_start();
    if (!isset($_SESSION['userID'])) {
        echo "<script>alert('يرجى تسجيل الدخول لتقييم الأحداث.'); window.location.href = 'login.php';</script>";
        exit;
    }

    $userID = $_SESSION['userID']; // معرّف المستخدم من الجلسة
    $eventID = $_POST['eventID'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    try {
        $insertQuery = "INSERT INTO ratings (userID, eventID, rating, review) VALUES (:userID, :eventID, :rating, :review)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':userID', $userID);
        $insertStmt->bindParam(':eventID', $eventID);
        $insertStmt->bindParam(':rating', $rating);
        $insertStmt->bindParam(':review', $review);
        $insertStmt->execute();

        echo "<script>alert('تم إرسال تقييمك بنجاح!'); window.location.href = 'ratings_page.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('حدث خطأ أثناء إرسال التقييم.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييم الأحداث</title>
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
        .event-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }
        .event-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .event-box img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .event-box h3 {
            margin: 15px 0;
        }
        .rating-container {
            margin: 15px 0;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 25px;
            color: #ccc;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating input:hover ~ label {
            color: #FFD700;
        }
        .star-rating input:hover ~ label:hover ~ label {
            color: #FFD700;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <h1>تقييم الأحداث</h1>
</header>

<nav>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
    <a href="Event_Page.php">الأحداث</a>
    <a href="ratings_page.php">التقييمات</a>
    
    <a href="addEvent.php">إضافة حدث </a>

</nav>

<div class="event-container">
    <?php foreach ($events as $event): ?>
        <div class="event-box">
            <img src="uploads/<?php echo htmlspecialchars($event['eventImage']); ?>" alt="<?php echo htmlspecialchars($event['eventName']); ?>">
            <h3><?php echo htmlspecialchars($event['eventName']); ?></h3>
            <p><?php echo htmlspecialchars($event['eventDate']); ?></p>

            <form method="POST">
                <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">

                <div class="rating-container">
                    <div class="star-rating">
                        <input type="radio" id="star5-<?php echo $event['eventID']; ?>" name="rating" value="5" required>
                        <label for="star5-<?php echo $event['eventID']; ?>">&#9733;</label>

                        <input type="radio" id="star4-<?php echo $event['eventID']; ?>" name="rating" value="4">
                        <label for="star4-<?php echo $event['eventID']; ?>">&#9733;</label>

                        <input type="radio" id="star3-<?php echo $event['eventID']; ?>" name="rating" value="3">
                        <label for="star3-<?php echo $event['eventID']; ?>">&#9733;</label>

                        <input type="radio" id="star2-<?php echo $event['eventID']; ?>" name="rating" value="2">
                        <label for="star2-<?php echo $event['eventID']; ?>">&#9733;</label>

                        <input type="radio" id="star1-<?php echo $event['eventID']; ?>" name="rating" value="1">
                        <label for="star1-<?php echo $event['eventID']; ?>">&#9733;</label>
                    </div>
                </div>

                <textarea name="review" placeholder="أضف تعليقك هنا (اختياري)" rows="4"></textarea>
                <button type="submit">إرسال التقييم</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
