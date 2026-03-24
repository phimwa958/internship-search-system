<?php
require_once __DIR__ . '/../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

$isSearchIsEmpty = false;
if (
    empty($faculty) &&
    empty($program) &&
    empty($major) &&
    empty($province) &&
    empty($academicYear)
) {
    $isSearchIsEmpty = true;
}

?>

<!-- Download report buttons -->
<?php if (!$isSearchIsEmpty): ?>
    <form action="<?php echo $baseUrl; ?>/actions/pdf_report_filter.php" method="POST">
        <input type="hidden" name="faculty" value="<?= htmlspecialchars($faculty) ?>">
        <input type="hidden" name="program" value="<?= htmlspecialchars($program) ?>">
        <input type="hidden" name="major" value="<?= htmlspecialchars($major) ?>">
        <input type="hidden" name="province" value="<?= htmlspecialchars($province) ?>">
        <input type="hidden" name="academic-year" value="<?= htmlspecialchars($academicYear) ?>">
        <button
            class="flex h-11 bg-red-500 hover:bg-red-600 text-white rounded-md px-4 text-center justify-center items-center"
            type="submit">
            ดาวน์โหลดรายการที่เลือก (.pdf)
        </button>
    </form>
<?php else: ?>
    <button
        class="flex h-11 bg-red-500 hover:bg-red-600 rounded-md text-white px-4 text-center justify-center items-center">
        <a href="<?php echo $baseUrl; ?>/actions/pdf_report_all.php">ดาวน์โหลดทั้งหมด (.pdf)</a>
    </button>
<?php endif; ?>