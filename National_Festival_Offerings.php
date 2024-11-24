<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذاكر المهرجانات</title>
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
        .event-offerings {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .event-offerings h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .offering-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 20px;
        }
        .offering-box {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .offering-box img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .offering-box h3 {
            margin-bottom: 10px;
        }
        .offering-box p {
            margin-bottom: 20px;
        }
        .booking-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
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
        <a href="register.php">تسجيل مستخدم جديد</a>
        <a href="login.php">تسجيل دخول</a>
    </nav>

    <div class="event-offerings">
        <h2>عروض المهرجانات</h2>
        <div class="offering-container">
            <!-- العروض الثابتة -->
            <div class="offering-box">
                <img src="horse.jpg" alt="عرض الفروسية">
                <h3>عرض الفروسية</h3>
                <p>التاريخ: 2023-07-20</p>
                <a href="book_ticket.php" class="booking-btn">شراء التذاكر</a>
            </div>
            <div class="offering-box">
                <img src="malof.jpg" alt="عرض الفنان الليبي مالوف">
                <h3>عرض الفنان الليبي مالوف</h3>
                <p>التاريخ: 2023-07-22</p>
                <a href="book_ticket.php" class="booking-btn">شراء التذاكر</a>
            </div>

            <!-- العروض من قاعدة البيانات -->
            <?php
            // الاتصال بقاعدة البيانات
            $host = "localhost";
            $dbname = "booking_system";
            $username = "root";
            $password = "";

            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // جلب الأحداث من نوع "عرض مهرجان"
                $query = "SELECT * FROM events WHERE eventType = 'عرض مهرجان'";
                $stmt = $conn->prepare($query);
                $stmt->execute();

                // عرض الأحداث
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                    <div class="offering-box">
                        <img src="uploads/' . htmlspecialchars($row['eventImage']) . '" alt="' . htmlspecialchars($row['eventName']) . '">
                        <h3>' . htmlspecialchars($row['eventName']) . '</h3>
                        <p>التاريخ: ' . htmlspecialchars($row['eventDate']) . '</p>
                        <a href="book_ticket.php" class="booking-btn">شراء التذاكر</a>
                    </div>
                    ';
                }
            } catch (PDOException $e) {
                echo "<p>خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
    </footer>

</body>
</html>
