<?php
use Facebook\WebDriver\WebDriverBy;

echo "\n🚀 গ্রুপে পোস্টিং শুরু হচ্ছে...\n";

foreach ($groupUrls as $url) {
    echo "\n👉 প্রবেশ করা হচ্ছে: $url\n";
    $driver->get($url);
    sleep(8); 

    try {
        // ১. পোস্ট বক্সে ক্লিক
        $createPostBox = $driver->findElement(WebDriverBy::xpath("//div[@role='button'][.//span[contains(text(), 'Write something') or contains(text(), 'কিছু লিখুন')]]"));
        $createPostBox->click();
        
        echo "পোস্ট বক্স ওপেন হয়েছে। মেসেজ লেখা হচ্ছে...\n";
        sleep(4); 

        // ২. মেসেজ টাইপ
        $activeElement = $driver->switchTo()->activeElement();
        $activeElement->sendKeys($postMessage);
        sleep(3); 

        // ৩. সাবমিট
        $postButton = $driver->findElement(WebDriverBy::xpath("//div[@aria-label='Post' or @aria-label='পোস্ট করুন']"));
        $postButton->click();

        echo "✅ এই গ্রুপে পোস্ট সফলভাবে সম্পন্ন হয়েছে!\n";
        sleep(6); 

    } catch (Exception $e) {
        echo "❌ এই গ্রুপে পোস্ট করতে সমস্যা হয়েছে। (হয়তো পারমিশন নেই বা এলিমেন্ট মেলেনি)\n";
    }
}