<?php
class AvailableOffers {
    public $offersnumber;
    public $offerstartdate;
    public $offerenddate;

    public function addOffer() {
        echo "تمت إضافة العرض من " . $this->offerstartdate . " إلى " . $this->offerenddate . ".";
        return true; // أو يمكنك إرجاع false حسب الحاجة
    }

    public function removeOffer() {
        echo "تمت إزالة العرض.";
    }

    public function viewAvailableOffers() {
        echo "عرض العروض المتاحة.";
    }
}
?>