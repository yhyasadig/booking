<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <style>
        /* CSS styles go here */
        .event-details {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .event-details img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .event-details h2 {
            margin-bottom: 10px;
        }
        .event-details p {
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
    </style>
</head>
<body>
    <div class="event-details">
        <img src="flight.jpg" alt="Flight Event">
        <h2>Flight Event</h2>
        <p>Date: 2023-06-15</p>
        <p>Experience the thrill of a lifetime with our flight event!</p>
        <h3>Upcoming Flights</h3>
        <div class="flight-options">
            <div class="flight-option">
                <h4>Istanbul</h4>
                <p>Date: 2023-06-20 - 2023-06-25</p>
                <a href="#" class="booking-btn">Book Now</a>
            </div>
            <div class="flight-option">
                <h4>Amsterdam</h4>
                <p>Date: 2023-07-01 - 2023-07-07</p>
                <a href="#" class="booking-btn">Book Now</a>
            </div>
            <div class="flight-option">
                <h4>Sharm El-Sheikh</h4>
                <p>Date: 2023-08-15 - 2023-08-22</p>
                <a href="#" class="booking-btn">Book Now</a>
            </div>
        </div>
    </div>
</body>
</html>