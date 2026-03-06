<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys; 

if (!isset($doComment) || !$doComment) {
    return; 
}

echo "\n💬 সুপার হিউম্যান অটো-কমেন্টিং শুরু হচ্ছে (100% Safe Mode)...\n";

foreach ($groupUrls as $url) {
    echo "\n👉 প্রবেশ করা হচ্ছে: $url\n";
    $driver->get($url);
    sleep(10); // পেজ পুরোপুরি লোড হওয়ার জন্য একটু বেশি সময়

    echo "পোস্ট লোড করার জন্য কীবোর্ড দিয়ে ইনিশিয়াল স্ক্রল করা হচ্ছে...\n";
    $body = $driver->findElement(WebDriverBy::tagName('body')); 
    
    // পেজ লোড করার জন্য প্রথমে ২-৩ বার পেজ ডাউন করা
    for ($i = 0; $i < 3; $i++) {
        $body->sendKeys(WebDriverKeys::PAGE_DOWN);
        sleep(2);
    }
    // আবার উপরে ফিরে যাওয়া
    $driver->executeScript("window.scrollTo(0, 300);");
    sleep(3);

    $commentCount = 0;
    $scrollAttempts = 0;

    while ($commentCount < $maxComments && $scrollAttempts < 30) {
        try {
            // Comment বাটন খোঁজা
            $xpath = "//div[@role='button' and (contains(@aria-label, 'Comment') or contains(@aria-label, 'Leave a comment') or contains(@aria-label, 'মন্তব্য') or .//span[contains(text(), 'Comment') or contains(text(), 'মন্তব্য')])]";
            $commentButtons = $driver->findElements(WebDriverBy::xpath($xpath));

            $clickedInThisScroll = false;

            foreach ($commentButtons as $button) {
                $isProcessed = $driver->executeScript("return arguments[0].getAttribute('data-bot-commented');", [$button]);

                if (!$isProcessed && $button->isDisplayed()) {
                    
                    // মার্ক করে দেওয়া যাতে দ্বিতীয়বার এই বাটনে ক্লিক না করে
                    $driver->executeScript("arguments[0].setAttribute('data-bot-commented', 'true');", [$button]);
                    
                    // বাটনটি স্ক্রিনের মাঝখানে আনা
                    $driver->executeScript("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", [$button]);
                    sleep(3); // মানুষের মতো দেখার বিরতি

                    // ---------------------------------------------------------
                    // স্টেপ ১: কমেন্ট বক্সে ক্লিক করা
                    // ---------------------------------------------------------
                    $driver->executeScript("arguments[0].click();", [$button]);
                    echo "\n👉 নতুন পোস্ট পাওয়া গেছে, Comment বাটনে ক্লিক করা হলো...\n";
                    sleep(3); // বক্স ওপেন ও ফোকাস হওয়ার সময়

                    $activeElement = $driver->switchTo()->activeElement();
                    $role = $activeElement->getAttribute('role');
                    
                    if ($role === 'textbox' || $activeElement->getAttribute('contenteditable') === 'true') {
                        
                        // ---------------------------------------------------------
                        // স্টেপ ২: টাইপিং এবং এন্টার (কমেন্ট সাবমিট)
                        // ---------------------------------------------------------
                        $messagesList = isset($commentMessages) ? $commentMessages : [$commentMessage];
                        $randomComment = $messagesList[array_rand($messagesList)];

                        echo "✍️ টাইপ করা হচ্ছে: '$randomComment'\n";
                        $activeElement->sendKeys($randomComment);
                        sleep(2); // টাইপ করার পর একটু থামা
                        
                        $activeElement->sendKeys(WebDriverKeys::ENTER);
                        echo "✅ ENTER চাপা হয়েছে!\n";

                        // ---------------------------------------------------------
                        // স্টেপ ৩: কমেন্ট পাবলিশ হওয়ার জন্য অপেক্ষা এবং ক্লোজ করা
                        // ---------------------------------------------------------
                        echo "⏳ কমেন্ট পোস্ট হওয়ার জন্য ৫ সেকেন্ড অপেক্ষা...\n";
                        sleep(5); // ফেসবুকের সার্ভারে কমেন্ট সেভ হওয়ার সময়
                        
                        echo "❌ কমেন্ট বক্স আনফোকাস/ক্লোজ করা হচ্ছে (ESC)...\n";
                        $body->sendKeys(WebDriverKeys::ESCAPE);
                        sleep(2); // ক্লোজ হওয়ার পর বিরতি

                        $commentCount++;
                        $clickedInThisScroll = true;

                        // ---------------------------------------------------------
                        // স্টেপ ৪: স্প্যাম ফিল্টার বাইপাস (দীর্ঘ বিরতি)
                        // ---------------------------------------------------------
                        $sleepTime = rand(25, 45); // ২৫ থেকে ৪৫ সেকেন্ড গ্যাপ
                        echo "🛑 সিকিউরিটির জন্য $sleepTime সেকেন্ড অপেক্ষা করা হচ্ছে...\n";
                        sleep($sleepTime);
                        
                        // একটি কাজ শেষ, এখন ভেতরের লুপ ভেঙে বের হয়ে আবার স্ক্রল করবে
                        break; 
                    } else {
                        echo "⚠️ কমেন্ট বক্স ঠিকমতো আসেনি, বক্স ক্লোজ করে স্কিপ করা হচ্ছে...\n";
                        $body->sendKeys(WebDriverKeys::ESCAPE);
                        sleep(2);
                    }
                }
            }

            // ---------------------------------------------------------
            // স্টেপ ৫: কাজ শেষ হলে বা নতুন পোস্ট না পেলে নিচে স্ক্রল করা
            // ---------------------------------------------------------
            echo "⬇️ পরবর্তী পোস্টের জন্য নিচে স্ক্রল করা হচ্ছে (PAGE DOWN)...\n";
            $body->sendKeys(WebDriverKeys::PAGE_DOWN); 
            $scrollAttempts++;
            sleep(4); // স্ক্রল করার পর লোডিং টাইম

        } catch (Exception $e) {
            echo "⚠️ ছোটখাটো এরর, একটু নিচে নামা হচ্ছে...\n";
            $body->sendKeys(WebDriverKeys::PAGE_DOWN);
            sleep(3);
        }
    }

    if ($commentCount >= $maxComments) {
        echo "\n🎯 এই গ্রুপের টার্গেট ($maxComments টি কমেন্ট) 100% পূর্ণ হয়েছে!\n";
    } else {
        echo "\n⚠️ আর নতুন পোস্ট লোড হয়নি। মোট $commentCount টি কমেন্ট সম্পন্ন হয়েছে।\n";
    }
}
?>