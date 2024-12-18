<?php
session_start();
require_once 'Database.php';

// الاتصال بقاعدة البيانات
$db = new Database();
$conn = $db->getConnection();

// جلب الأحداث من قاعدة البيانات
$query = "SELECT eventID, eventName, ticketPrice FROM events";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب أنواع المقاعد
$query = "SELECT seatstype, price FROM seats";
$stmt = $conn->prepare($query);
$stmt->execute();
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$discount = 0; // نسبة الخصم الافتراضية

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];

    // جلب المهنة بناءً على المستخدم
    $query = "
        SELECT u.Profession, d.DiscountAmount 
        FROM users u
        LEFT JOIN discounts d ON u.Profession = d.Profession
        WHERE u.userid = :userid";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // الحصول على الخصم بناءً على المهنة
        $discount = $result['DiscountAmount'] ?? 0;  // في حال لم يوجد خصم للمهنة
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['userid'])) {
        echo "<script>alert('يرجى تسجيل الدخول أولاً لإتمام الحجز.'); window.location.href='login.php';</script>";
        exit;
    }

    $userid = $_SESSION['userid'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $eventID = $_POST['event'];
    $seatType = $_POST['seatType'];
    $ticketCount = intval($_POST['ticketcount']);
    $ticketPrice = floatval($_POST['ticketprice']);
    $totalPrice = $ticketCount * $ticketPrice;

    try {
        $query = "SELECT eventName FROM events WHERE eventID = :eventID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        $stmt->execute();
        $eventName = $stmt->fetchColumn();

        // تطبيق الخصم عند حساب السعر
        $discountedPrice = $totalPrice - ($totalPrice * $discount / 100);

        // إدخال الحجز في قاعدة البيانات
        $query = "INSERT INTO bookings (userid, phone, seatType, eventName, ticketPrice, ticketCount, totalPriceAfterDiscount)
                  VALUES (:userid, :phone, :seatType, :eventName, :ticketPrice, :ticketCount, :discountedPrice)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':seatType', $seatType, PDO::PARAM_STR);
        $stmt->bindParam(':eventName', $eventName, PDO::PARAM_STR);
        $stmt->bindParam(':ticketPrice', $ticketPrice, PDO::PARAM_STR);
        $stmt->bindParam(':ticketCount', $ticketCount, PDO::PARAM_INT);
        $stmt->bindParam(':discountedPrice', $discountedPrice, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['last_booking_id'] = $conn->lastInsertId();

        echo "<script>alert('تم الحجز بنجاح!'); window.location.href='index.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "خطأ أثناء الحجز: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز تذكرة</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 0; }
        header { background-color: #007bff; color: white; padding: 15px 0; text-align: center; }
        nav { background-color: #0056b3; padding: 10px 0; text-align: center; }
        nav a { margin: 0 15px; text-decoration: none; color: white; font-weight: bold; padding: 10px 15px; border-radius: 5px; }
        nav a:hover { background-color: #004494; }
        .container { max-width: 400px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; }
        button { background-color: #007bff; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background-color: #0056b3; }
        footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #666; }
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
    <form action="book_ticket.php" method="POST">
        <h2>حجز تذكرة</h2>

        <label for="username">اسمك:</label>
        <input type="text" id="username" name="username" placeholder="أدخل اسمك" required>

        <label for="phone">رقم الهاتف:</label>
        <input type="tel" id="phone" name="phone" placeholder="أدخل رقم الهاتف" required>

        <label for="event">اختر الحدث:</label>
        <select id="event" name="event" onchange="calculatePrice()" required>
            <option value="" disabled selected>اختر حدثًا</option>
            <?php foreach ($events as $event): ?>
                <option value="<?php echo $event['eventID']; ?>" data-price="<?php echo $event['ticketPrice']; ?>">
                    <?php echo htmlspecialchars($event['eventName']) . " - " . $event['ticketPrice'] . " دينار"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="seatType">اختر نوع المقعد:</label>
        <select id="seatType" name="seatType" onchange="calculatePrice()" required>
            <option value="" disabled selected>اختر نوع المقعد</option>
            <?php foreach ($seats as $seat): ?>
                <option value="<?php echo $seat['price']; ?>">
                    <?php echo htmlspecialchars($seat['seatstype']) . " - " . $seat['price'] . " دينار"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="ticketcount">عدد التذاكر:</label>
        <input type="number" id="ticketcount" name="ticketcount" min="1" value="1" onchange="calculatePrice()" required>

        <label for="ticketprice">السعر لكل تذكرة:</label>
        <input type="text" id="ticketprice" name="ticketprice" readonly required>

        <p id="priceBeforeDiscount">السعر الإجمالي قبل الخصم: </p>
        <p id="priceAfterDiscount">السعر الإجمالي بعد الخصم: </p>

        <button type="submit">حجز</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 نظام الحجز. جميع الحقوق محفوظة.</p>
</footer>

<script>
    function calculatePrice() {
        const event = document.getElementById('event');
        const seatType = document.getElementById('seatType');
        const ticketCount = parseInt(document.getElementById('ticketcount').value || 1);
        const ticketPriceInput = document.getElementById('ticketprice');
        const priceBeforeDiscount = document.getElementById('priceBeforeDiscount');
        const priceAfterDiscount = document.getElementById('priceAfterDiscount');

        if (!event.value || !seatType.value) return;

        const eventPrice = parseFloat(event.options[event.selectedIndex].getAttribute('data-price'));
        const seatPrice = parseFloat(seatType.value);
        const totalPrice = (eventPrice + seatPrice) * ticketCount;

        ticketPriceInput.value = eventPrice + seatPrice;

        const discount = <?php echo $discount; ?>; // الخصم من قاعدة البيانات
        const discountedPrice = totalPrice - (totalPrice * discount / 100);

        priceBeforeDiscount.innerText = `السعر الإجمالي قبل الخصم: ${totalPrice.toFixed(2)} دينار`;
        priceAfterDiscount.innerText = `السعر الإجمالي بعد الخصم: ${discountedPrice.toFixed(2)} دينار`;
    }
</script>

</body>
</html>
