<?php
require_once __DIR__ . '/../../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';
$baseDashboardUrl = $baseUrl . '/dashboard';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];

$fullUrl = $protocol . $host . $requestUri;
?>

<!-- Backdrop สำหรับ mobile (คลุมพื้นหลังตอน sidebar เปิด) -->
<div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/40 z-30 lg:hidden">
</div>

<aside id="sidebar" class="
        fixed inset-y-0 left-0 z-40
        w-64 min-w-[16rem] bg-sky-500 text-white flex flex-col shadow-lg
        transform transition-transform duration-300
        -translate-x-full
        lg:translate-x-0
        lg:relative lg:flex lg:min-h-screen
    ">

    <!-- Brand + Toggle -->
    <div class="flex items-center justify-between px-4 py-4 border-b border-sky-400/70 gap-3">
        <a href="index.php" class="sidebar-brand flex items-center gap-3">
            <div class="flex items-center justify-center flex-shrink-0">
                <img src="../public/images/SDU Logo.png" alt="SDU Logo" class="h-10 w-auto">
            </div>
            <div class="sidebar-text text-base font-semibold tracking-wide">
                ระบบจัดการฐานข้อมูล
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 space-y-3">
        <!-- Interface Heading -->
        <div class="sidebar-heading px-4 text-xs text-center font-semibold tracking-wide uppercase text-sky-100/80">
            เมนู
        </div>

        <!-- Dashboard -->
        <div class="px-2">
            <a href="index.php" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                <?php echo $fullUrl === $baseDashboardUrl . '/index.php'
                    ? 'bg-sky-600/90 shadow-sm'
                    : 'hover:bg-sky-400/70'; ?>">
                <i class="fas fa-fw fa-tachometer-alt text-sm"></i>
                <span class="sidebar-text">แดชบอร์ด</span>
            </a>
        </div>

        <!-- Users -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="px-2">
                <a href="user.php" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                    <?php echo $fullUrl === $baseDashboardUrl . '/user.php'
                        ? 'bg-sky-600/90 shadow-sm'
                        : 'hover:bg-sky-400/70'; ?>">
                    <i class="fas fa-fw fa-users text-sm"></i>
                    <span class="sidebar-text">ตารางข้อมูลผู้ใช้</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Insert Faculty -->
        <div class="px-2">
            <a href="faculty.php" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                <?php echo $fullUrl === $baseDashboardUrl . '/faculty.php'
                    ? 'bg-sky-600/90 shadow-sm'
                    : 'hover:bg-sky-400/70'; ?>">
                <i class="fas fa-university"></i>
                <span class="sidebar-text">เพิ่มข้อมูลคณะ</span>
            </a>
        </div>

        <!-- View feedback page -->
        <div class="px-2">
            <a href="feedback.php" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                <?php echo $fullUrl === $baseDashboardUrl . '/feedback.php'
                    ? 'bg-sky-600/90 shadow-sm'
                    : 'hover:bg-sky-400/70'; ?>">
                <i class="fa-solid fa-comments"></i>
                <span class="sidebar-text">ข้อเสนอแนะ</span>
            </a>
        </div>

        <!-- Insert Excel -->
        <div class="px-2">
            <a href="insert_excel.php" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                <?php echo $fullUrl === $baseDashboardUrl . '/insert_excel.php'
                    ? 'bg-sky-600/90 shadow-sm'
                    : 'hover:bg-sky-400/70'; ?>">
                <i class="fas fa-fw fa-file-excel text-sm"></i>
                <span class="sidebar-text">เพิ่มข้อมูล Excel</span>
            </a>
        </div>

        <!-- Divider -->
        <div class="sidebar-divider border-t border-sky-400/60 mx-3"></div>

        <!-- ปุ่มย่อ/ขยาย sidebar -->
        <div class="hidden md:flex items-center justify-center">
            <!-- ปุ่มนี้เอาไว้ใช้เฉพาะ desktop จะได้ไม่ชนกับ mobile off-canvas -->
            <button id="sidebarToggle" type="button"
                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-sky-400/80 hover:bg-sky-400/60 border border-sky-300/60 transition">
                <i class="fas fa-angle-double-left text-xs" id="toggleIcon"></i>
            </button>
        </div>
    </nav>

</aside>

<script src="../public/js/components/sidebar.js" defer></script>