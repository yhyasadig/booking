<?php
class Event {
    public $eventid;
    public $eventname;
    public $eventdate;
    public $eventlocation;

    public function createEvent() {
        try {
            // تحقق من صحة البيانات المطلوبة
            if (empty($this->eventname) || empty($this->eventdate) || empty($this->eventlocation)) {
                throw new Exception("اسم الحدث، تاريخ الحدث، وموقع الحدث مطلوبة.");
            }

            echo "تم إنشاء الحدث: " . $this->eventname . " بتاريخ " . $this->eventdate . " في " . $this->eventlocation . ".";
            return true;
        } catch (Exception $exception) {
            echo "خطأ في إنشاء الحدث: " . $exception->getMessage();
            return false;
        }
    }

    public function viewEventDetails() {
        try {
            echo "عرض تفاصيل الحدث.";
        } catch (Exception $exception) {
            echo "خطأ في عرض تفاصيل الحدث: " . $exception->getMessage();
        }
    }
}
?>