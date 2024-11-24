<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الأحداث</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e9ecef;
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
            display: flex;
            flex-direction: column; /* Stack the boxes vertically */
            gap: 20px; /* Space between boxes */
            max-width: 800px;
            margin: 20px auto;
        }
        .event-box {
            display: flex; /* Flexbox for horizontal layout */
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            align-items: center; /* Center items vertically */
        }
        .event-box img {
            width: 250px; /* Increased width for the image */
            height: 200px; /* Increased height for the image */
            object-fit: cover; /* Maintain aspect ratio */
            margin-left: auto; /* Push image to the right */
        }
        .event-details {
            flex: 1; /* Take the remaining space */
            text-align: left; /* Align text to the left */
        }
        .event-details h3 {
            margin: 0; /* Remove default margin */
        }
        .booking-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px; /* Space above the button */
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

    <h1>الأحداث القادمة</h1>
    <div class="event-container">
        <div class="event-box">
            <div class="event-details">
                <h3>حدث الطيران</h3>
                <p>التاريخ: 2023-06-15</p>
                <p>أحداث طيران مثيرة مع عروض رائعة!</p>
                <a href="Flight_Event_Offerings.php" class="booking-btn">عرض هذا الحدث</a>
            </div>
            <img src="flight.jpg" alt="حدث الطيران">
        </div>
        <div class="event-box">
            <div class="event-details">
                <h3>المهرجان الوطني</h3>
                <p>التاريخ: 2023-07-20</p>
                <p>انضم إلينا للاحتفال بهويتنا الوطنية!</p>
                <a href="National_Festival_Offerings.php" class="booking-btn">عرض هذا الحدث</a>
            </div>
            <img src="festival.jpg" alt="المهرجان الوطني">
        </div>
        <div class="event-box">
            <div class="event-details">
                <h3>مباريات لا ليغا</h3>
                <p>التاريخ: 2023-08-01</p>
                <p>استمتع بحماس المباريات المباشرة!</p>
                <a href="LaLigaMatchOfferings.php" class="booking-btn">عرض هذا الحدث</a>
            </div>
            <img src="football.jpg" alt="مباريات لا ليغا">
        </div>
        <div class="event-box">
            <div class="event-details">
                <h3>أفلام شباك التذاكر</h3>
                <p>جاري العرض</p>
                <p>استمتع بأحدث الأفلام الناجحة!</p>
                <a href="Cinema_Event_Offerings.php" class="booking-btn">عرض هذا الحدث</a>
            </div>
            <img src="cinema.jpg" alt="أفلام شباك التذاكر">
        </div>
    </div>

    <footer>
        <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
    </footer>

</body>
</html> 