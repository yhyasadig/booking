<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استخدم كائنًا بسيطًا لجمع بيانات الحجز
    $booking = new stdClass();
    $booking->username = $_POST['username'];
    $booking->phone = $_POST['phone'];
    $booking->ticketcount = $_POST['ticketcount'];
    $booking->ticketprice = $_POST['ticketprice'];
    $booking->seatType = $_POST['seatType'];
    $booking->userid = $_SESSION['userid']; // تأكد من أن المستخدم مسجل دخوله

    // جمع أسماء المسافرين
    $names = [];
    for ($i = 1; $i <= $booking->ticketcount; $i++) {
        $names[] = $_POST["name$i"];
    }
    $booking->names = $names;

    // فقط عرض رسالة ناجحة بدون إدخال البيانات في قاعدة البيانات
    echo "<script>alert('تم حجز التذكرة بنجاح.'); window.location.href='ticket_details.php?username={$booking->username}&phone={$booking->phone}&ticketcount={$booking->ticketcount}&names=" . implode(',', $names) . "&ticketprice={$booking->ticketprice}&seatType={$booking->seatType}&event=" . urlencode($_POST['event']) . "';</script>";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز تذكرة</title>
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
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
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
    <h1>نظام الحجز</h1>
</header>

<nav>
    <a href="home.php">الصفحة الرئيسية</a>
    <a href="register.php">تسجيل مستخدم جديد</a>
    <a href="login.php">تسجيل دخول</a>
</nav>

<div class="container">
    <form action="book_ticket.php" method="POST" id="bookingForm">
        <h2>حجز تذكرة</h2>

        <label for="username">اسمك:</label>
        <input type="text" id="username" name="username" placeholder="أدخل اسمك" required>

        <label for="phone">رقم الهاتف:</label>
        <input type="tel" id="phone" name="phone" placeholder="أدخل رقم الهاتف" required>

        <label for="event">اختر الحدث:</label>
        <select id="event" name="event" onchange="setTicketPrice()" required>
            <option value="" disabled selected>اختر حدثًا</option>
            <option value="1500">حدث طيران إسطنبول - 1500 دينار</option>
            <option value="3500">حدث طيران شيكاغو - 3500 دينار</option>
            <option value="3000">حدث طيران لندن - 3000 دينار</option>
            <option value="5200">حدث طيران سنغافورة - 5200 دينار</option>
            <option value="200">تذكرة برشلونة ضد ريال مدريد - 200 دينار</option>
            <option value="120">تذكرة أتلتيكو مدريد ضد فالنسيا - 120 دينار</option>
            <option value="340">فيلم Lord of Rings - 340 دينار</option>
            <option value="200">فيلم Beekeeper - 200 دينار</option>
        </select>

        <label for="seatType">اختر نوع المقعد:</label>
        <select id="seatType" name="seatType" onchange="updateSeatPrice()" required>
            <option value="" disabled selected>اختر هنا</option>
            <option value="200,10">درجة أولى - 200 دينار (10 مقاعد متاحة)</option>
            <option value="140,70">درجة ثانية - 140 دينار (70 مقعد متاحة)</option>
            <option value="250,6">درجة سياحية - 250 دينار (6 مقاعد متاحة)</option>
        </select>

        <label for="ticketcount">عدد التذاكر:</label>
        <input type="number" id="ticketcount" name="ticketcount" min="1" value="1" required onchange="updateNameFields()">

        <div id="nameFields"></div>

        <input type="text" id="ticketprice" name="ticketprice" placeholder="سعر التذكرة" readonly required>

        <button type="submit">حجز</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

<script>
    function setTicketPrice() {
        const select = document.getElementById('event');
        const priceInput = document.getElementById('ticketprice');
        priceInput.value = select.value;
    }

    function updateSeatPrice() {
        const select = document.getElementById('seatType');
        const priceInfo = select.value.split(',');
        const priceInput = document.getElementById('ticketprice');
        const ticketPrice = parseInt(priceInput.value) + parseInt(priceInfo[0]);
        priceInput.value = ticketPrice;
    }

    function updateNameFields() {
        const ticketCount = document.getElementById('ticketcount').value;
        const nameFieldsContainer = document.getElementById('nameFields');
        nameFieldsContainer.innerHTML = ''; // مسح المحتوى السابق

        for (let i = 2; i <= ticketCount; i++) {
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'name' + i;
            input.placeholder = 'اسم رقم ' + i;
            nameFieldsContainer.appendChild(input);
        }
    }

    // تعيين السعر الأولي عند تحميل الصفحة
    window.onload = function() {
        setTicketPrice();
    };
</script>

</body>
</html>