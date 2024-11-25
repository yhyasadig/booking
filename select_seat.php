<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختيار المقعد</title>
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
        .container {
            max-width: 400px;
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
        select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
        .price, .available-seats {
            text-align: center;
            margin: 10px 0;
            font-size: 1.2em;
            color: #28a745;
        }
    </style>
</head>
<body>

<header>
    <h1>نظام الحجز</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
    <a href="Event_Page.php">الأحداث </a>
    


    <a href="ratings_page.php">تقييم الأحداث</a>
    <a href="addEvent.php">إضافة حدث </a>

</nav>

<div class="container">
    <h2>اختيار المقعد</h2>
    <form action="submit_selection.php" method="POST">
        <label for="seatType">اختر نوع المقعد:</label>
        <select id="seatType" name="seatType" onchange="updatePriceAndSeats()">
            <option value="" disabled selected>اختر هنا</option>
            <option value="200,10">درجة أولى - 200 دينار (10 مقاعد متاحة)</option>
            <option value="140,70">درجة ثانية - 140 دينار (70 مقعد متاحة)</option>
            <option value="250,6">درجة سياحية - 250 دينار (6 مقاعد متاحة)</option>
        </select>

        <div class="price" id="priceDisplay">السعر: 0 دينار</div>
        <div class="available-seats" id="seatsDisplay">المقاعد المتاحة: 0</div>
        
        <button type="submit">OK</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

<script>
    function updatePriceAndSeats() {
        const select = document.getElementById('seatType');
        const priceDisplay = document.getElementById('priceDisplay');
        const seatsDisplay = document.getElementById('seatsDisplay');

        const selectedOption = select.value.split(',');
        const price = selectedOption[0] || 0;
        const availableSeats = selectedOption[1] || 0;

        priceDisplay.innerText = "السعر: " + price + " دينار";
        seatsDisplay.innerText = "المقاعد المتاحة: " + availableSeats;
    }
</script>

</body>
</html>