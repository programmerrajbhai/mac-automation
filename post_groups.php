<?php
use Facebook\WebDriver\WebDriverBy;

echo "\n🚀 গ্রুপে পোস্টিং শুরু হচ্ছে...\n";

foreach ($groupUrls as $url) {
    echo "\n👉 প্রবেশ করা হচ্ছে: $url\n";
    $driver->get($url);
    sleep(8); // গ্রুপের পেজ পুরোপুরি লোড হওয়ার জন্য সময়

    try {
        // ১. "Write something..." বক্সে ক্লিক করা
        $createPostBox = $driver->findElement(WebDriverBy::xpath("//div[@role='button'][.//span[contains(text(), 'Write something') or contains(text(), 'কিছু লিখুন')]]"));
        $createPostBox->click();
        
        echo "পোস্ট বক্স ওপেন হয়েছে...\n";
        sleep(5); // পপ-আপ (Dialog) পুরোপুরি ওপেন হওয়ার জন্য একটু বেশি সময়

        // ২. মিডিয়া (ছবি/ভিডিও) আপলোড (টেক্সটের আগে মিডিয়া আপলোড করা বেশি নিরাপদ)
        if ($attachMedia === true) {
            if (file_exists($mediaPath)) {
                echo "মিডিয়া ফাইল আপলোড করা হচ্ছে: " . $mediaFileName . "\n";
                
                // 🔴 ১০০% ফিক্স: শুধুমাত্র ওপেন হওয়া পপ-আপ (dialog) এর ভেতরের ফাইল ইনপুট খুঁজবে
                $fileInputs = $driver->findElements(WebDriverBy::xpath("//div[@role='dialog']//input[@type='file']"));
                
                if (count($fileInputs) > 0) {
                    // ডায়ালগের ভেতরের ইনপুটটিতে ফাইল পাঠানো
                    $fileInputs[0]->sendKeys($mediaPath);
                    
                    $waitTime = ($mediaType === 'video') ? 45 : 15; 
                    echo "ফাইলটি আপলোড হওয়ার জন্য $waitTime সেকেন্ড অপেক্ষা করা হচ্ছে...\n";
                    sleep($waitTime);
                } else {
                    echo "⚠️ আপলোড বাটন খুঁজে পাওয়া যায়নি! (এই গ্রুপে হয়তো মিডিয়া পোস্ট অফ করা আছে)\n";
                }
            } else {
                echo "⚠️ এরর: '$mediaFileName' ফাইলটি 'uploads' ফোল্ডারে পাওয়া যায়নি!\n";
            }
        }

        // ৩. টেক্সট/ক্যাপশন লেখা
        if ($postMessage !== "") {
            echo "ক্যাপশন লেখা হচ্ছে...\n";
            
            // মিডিয়া আপলোডের পর টেক্সট বক্সে ফোকাস করা
            $activeElement = $driver->switchTo()->activeElement();
            $activeElement->sendKeys($postMessage);
            sleep(3); 
        }

        // ৪. Post বাটনে ক্লিক করা (আরও নিখুঁত XPath)
        echo "Post বাটনে ক্লিক করা হচ্ছে...\n";
        $postButton = $driver->findElement(WebDriverBy::xpath("//div[@role='dialog']//div[@aria-label='Post' or @aria-label='পোস্ট করুন'][@role='button']"));
        $postButton->click();

        echo "✅ এই গ্রুপে পোস্ট সফলভাবে সম্পন্ন হয়েছে!\n";
        sleep(10); // পোস্ট পাবলিশ হওয়ার পর পরবর্তী গ্রুপে যাওয়ার আগে একটু বিরতি

    } catch (Exception $e) {
        echo "❌ এই গ্রুপে পোস্ট করতে সমস্যা হয়েছে। (অ্যাডমিন অ্যাপ্রুভাল বা পারমিশন ইস্যু)।\n";
    }
}