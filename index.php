<?php
try {
    require_once 'config.php';
    require_once 'setup_browser.php';
    require_once 'handle_login.php';
    
    // যদি config-এ $doPost = true থাকে, তবেই পোস্ট করবে
    if (isset($doPost) && $doPost) {
        require_once 'post_groups.php';
    }
    
    // কমেন্ট করার ফাইলটি যুক্ত করা হলো
    require_once 'comment_groups.php';
    
    echo "\n======================================================\n";
    echo "🎉 আপনার সব কাজ শেষ!\n";
    readline("👉 ব্রাউজার বন্ধ করতে চাইলে ENTER চাপুন: ");
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n❌ বড় কোনো সমস্যা হয়েছে:\n" . $e->getMessage() . "\n";
    readline("👉 টার্মিনাল থেকে বের হতে ENTER চাপুন: ");
    if(isset($driver)) { $driver->quit(); }
}