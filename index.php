<?php
try {
    require_once 'config.php';
    require_once 'setup_browser.php';
    require_once 'handle_login.php';
    
    // ১. গ্রুপে পোস্ট করা (যদি $doPost = true থাকে)
    if (isset($doPost) && $doPost) {
        require_once 'post_groups.php';
    }
    
    // ২. গ্রুপে অটো-কমেন্ট করা (যদি $doComment = true থাকে)
    if (isset($doComment) && $doComment) {
        require_once 'comment_groups.php';
    }

    // ৩. নির্দিষ্ট পোস্টে অটো-রিপ্লাই করা (যদি $doReply = true থাকে)
    if (isset($doReply) && $doReply) {
        require_once 'reply_comments.php';
    }
    
    echo "\n======================================================\n";
    echo "🎉 আপনার সব কাজ শেষ!\n";
    readline("👉 ব্রাউজার বন্ধ করতে চাইলে ENTER চাপুন: ");
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n❌ বড় কোনো সমস্যা হয়েছে:\n" . $e->getMessage() . "\n";
    readline("👉 টার্মিনাল থেকে বের হতে ENTER চাপুন: ");
    if(isset($driver)) { $driver->quit(); }
}
?>