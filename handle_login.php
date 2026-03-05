<?php
use Facebook\WebDriver\WebDriverBy;

echo "ফেসবুকে প্রবেশ করা হচ্ছে...\n";
$driver->get('https://www.facebook.com/');
sleep(6); // লোড হওয়ার জন্য সময়

// লগইন বক্স আছে কিনা চেক করা
$loginBoxes = $driver->findElements(WebDriverBy::id('email'));

if (count($loginBoxes) > 0) {
    echo "\n======================================================\n";
    echo "⚠️ আপনাকে ম্যানুয়ালভাবে লগইন করতে হবে ⚠️\n";
    echo "======================================================\n";
    echo "১. ওপেন হওয়া ব্রাউজারে লগইন করুন।\n";
    
    readline("👉 লগইন সম্পন্ন হয়ে হোমপেজ আসলে এখানে (Terminal-এ) ENTER চাপুন: ");
    echo "✅ লগইন প্রোফাইল সেভ হয়েছে!\n";
} else {
    echo "✅ প্রোফাইল থেকে সফলভাবে অটো-লগইন হয়েছে!\n";
}