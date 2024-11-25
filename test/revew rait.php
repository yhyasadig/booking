<?php

use PHPUnit\Framework\TestCase;

require_once 'Rating.php';

class RatingTest extends TestCase
{
    private $dbMock;
    private $rating;

    protected function setUp(): void
    {
        // إنشاء اتصال وهمي (Mock) بقاعدة البيانات
        $this->dbMock = $this->createMock(PDO::class);
        $this->rating = new Rating($this->dbMock);
    }

    public function testInsertRatingSuccess()
    {
        // إعداد استجابة وهمية لعملية الإعداد
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($stmtMock);

        // اختبار الإدخال الناجح
        $result = $this->rating->insertRating(1, 101, 5, "Great event!");
        $this->assertTrue($result, "Failed to insert rating when it should succeed.");
    }

    public function testInsertRatingFailure()
    {
        // إعداد استجابة وهمية لعملية الإعداد بالفشل
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())->method('execute')->willReturn(false);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($stmtMock);

        // اختبار الإدخال الفاشل
        $result = $this->rating->insertRating(1, 101, 5, "Great event!");
        $this->assertFalse($result, "Expected insertRating to fail, but it succeeded.");
    }

    public function testInsertRatingWithInvalidData()
    {
        // إعداد استجابة وهمية
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->never())->method('execute');

        $this->dbMock->expects($this->never())->method('prepare');

        // اختبار الإدخال ببيانات غير صالحة
        $this->expectException(TypeError::class);
        $this->rating->insertRating(null, 101, 5, "Great event!");
    }

    public function testInsertRatingForNonExistingEvent()
    {
        // إعداد استجابة وهمية لعملية إعداد بيان SQL
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())->method('execute')->willReturn(false); // فشل التنفيذ

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($stmtMock);

        // محاولة إدخال تقييم لحدث غير موجود
        $result = $this->rating->insertRating(1, 999, 5, "Event does not exist");
        $this->assertFalse($result, "Expected insertRating to fail for non-existing event.");
    }
}

?>
