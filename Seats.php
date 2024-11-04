<?php
class Seats {
    public $seatsnumber;
    public $seatstype;
    public $price;
    public $eventid;

    public function addSeat() {
        try {
            // تحقق من صحة البيانات المطلوبة
            if (empty($this->seatstype) || empty($this->price) || empty($this->eventid)) {
                throw new Exception("نوع المقعد، السعر، ومعرف الحدث مطلوبة.");
            }

            echo "تمت إضافة مقعد من نوع " . $this->seatstype . " بسعر " . $this->price . " لحدث ID " . $this->eventid . ".";
            return true;
        } catch (Exception $exception) {
            echo "خطأ في إضافة المقعد: " . $exception->getMessage();
            return false;
        }
    }

    public function bookSeat() {
        try {
            echo "تم حجز المقعد.";
        } catch (Exception $exception) {
            echo "خطأ في حجز المقعد: " . $exception->getMessage();
        }
    }

    public function viewSeats() {
        try {
            echo "عرض المقاعد.";
        } catch (Exception $exception) {
            echo "خطأ في عرض المقاعد: " . $exception->getMessage();
        }
    }
}
?>