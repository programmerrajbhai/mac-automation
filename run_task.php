<?php
// ওয়েবের টাইমআউট বন্ধ করা এবং লাইভ আউটপুট দেওয়ার সেটিং
header('Content-Type: text/plain; charset=utf-8');
set_time_limit(0); 
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
while (ob_get_level()) ob_end_flush();
ob_implicit_flush(true);

$task = isset($_GET['task']) ? $_GET['task'] : '';

try {
    echo "🚀 সিস্টেম চালু হচ্ছে...\n";
    require_once 'config.php';
    require_once 'setup_browser.php';

    // ১. শুধু লগইন করার কমান্ড আসলে
    if ($task === 'login') {
        echo "🔐 লগইন প্রসেস শুরু হচ্ছে...\n";
        require_once 'handle_login.php';
        echo "\n✅ আপনার আইডি সফলভাবে সেভ হয়েছে!\n";
    } 
    // ২. অন্যান্য কাজের কমান্ড আসলে
    else {
        require_once 'handle_login.php';

        if ($task === 'post') {
            require_once 'post_groups.php';
        } elseif ($task === 'comment') {
            require_once 'comment_groups.php';
        } elseif ($task === 'reply') {
            require_once 'reply_comments.php';
        } else {
            echo "⚠️ কোনো নির্দিষ্ট কাজ সিলেক্ট করা হয়নি!\n";
        }
    }

    // ======================================================
    // কাজ শেষে ব্রাউজার ওপেন রাখার ম্যাজিক
    // ======================================================
    echo "\n======================================================\n";
    echo "🎉 আপনার নির্দেশিত কাজ সফলভাবে শেষ হয়েছে!\n";
    echo "🛑 ব্রাউজারটি এখন আপনার কন্ট্রোলে আছে। আপনি নিজে না কাটা পর্যন্ত এটি ওপেন থাকবে।\n";
    echo "🍪 (চিন্তা করবেন না, ব্রাউজার কাটলেও সব কুকিজ এবং লগইন ডাটা অটোমেটিক সেভ হবে)\n";
    echo "======================================================\n";
    
    // 🟢 ম্যাজিক ট্রিক: যতক্ষণ আপনি নিজে 'X' বাটনে ক্লিক করে ব্রাউজার না কাটবেন, 
    // ততক্ষণ এই লুপটি ব্রাউজারটিকে ধরে রাখবে এবং ড্যাশবোর্ডকে জানিয়ে দেবে যে ব্রাউজার চলছে।
    try {
        while (true) {
            // ব্রাউজারটি এখনো ওপেন আছে কিনা তা চেক করা হচ্ছে
            $driver->getTitle(); 
            sleep(2); // সার্ভারের ওপর চাপ কমাতে ২ সেকেন্ড বিরতি
        }
    } catch (Exception $e) {
        // যখনই আপনি নিজে থেকে ব্রাউজারটি কেটে দেবেন, তখন এই মেসেজটি দেখাবে
        echo "\n✅ আপনি ব্রাউজারটি ম্যানুয়ালি ক্লোজ করেছেন। সিস্টেম এখন সুন্দরভাবে বন্ধ হচ্ছে...\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ বড় কোনো সমস্যা হয়েছে:\n" . $e->getMessage() . "\n";
    if(isset($driver)) { 
        try { $driver->quit(); } catch(Exception $ex) {} 
    }
}
?>