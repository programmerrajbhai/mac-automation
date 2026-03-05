<?php
try {
    // ১. কনফিগারেশন লোড করা
    require_once 'config.php';
    
    // ২. ব্রাউজার সেটআপ ও স্টার্ট করা
    require_once 'setup_browser.php';
    
    // ৩. লগইন চেক করা
    require_once 'handle_login.php';
    
    // ৪. গ্রুপগুলোতে পোস্ট করা
    require_once 'post_groups.php';
    
    // ৫. কাজ শেষ
    echo "\n======================================================\n";
    echo "🎉 আপনার সব কাজ শেষ!\n";
    readline("👉 ব্রাউজার বন্ধ করতে চাইলে ENTER চাপুন: ");
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n❌ বড় কোনো সমস্যা হয়েছে:\n" . $e->getMessage() . "\n";
    readline("👉 টার্মিনাল থেকে বের হতে ENTER চাপুন: ");
    if(isset($driver)) { $driver->quit(); }
}