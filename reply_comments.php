<?php
use Facebook\WebDriver\WebDriverKeys; 

if (!isset($doReply) || !$doReply) {
    return; 
}

echo "\n🔁 প্রো-লেভেল অটো-রিপ্লাই শুরু হচ্ছে (100% JS Injection Mode)...\n";

foreach ($postUrls as $url) {
    echo "\n👉 প্রবেশ করা হচ্ছে: $url\n";
    $driver->get($url);
    sleep(10); // পোস্ট এবং কমেন্টগুলো লোড হওয়ার জন্য সময়

    $replyCount = 0;
    $scrollAttempts = 0;

    while ($replyCount < $maxReplies && $scrollAttempts < 30) {
        try {
            // ---------------------------------------------------------
            // ১. "আরও মন্তব্য দেখুন" লোড করা (সরাসরি JS দিয়ে)
            // ---------------------------------------------------------
            $driver->executeScript("
                let moreBtns = Array.from(document.querySelectorAll('span')).filter(el => el.textContent.includes('View more comments') || el.textContent.includes('আরও মন্তব্য দেখুন') || el.textContent.includes('View previous comments'));
                for(let btn of moreBtns) {
                    if(btn.offsetParent !== null) { btn.click(); }
                }
            ");
            sleep(2);

            // ---------------------------------------------------------
            // ২. দ্য মাস্টারমাইন্ড JS ফাইন্ডার (সঠিক বাটন খোঁজা)
            // ---------------------------------------------------------
            // এই স্ক্রিপ্টটি পুরো পেজ খুঁজে একদম ফ্রেশ একটি Reply বাটন বের করবে,
            // সেটিকে মার্ক করবে, মাঝখানে আনবে এবং PHP এর কাছে পাঠিয়ে দেবে।
            $js_finder = "
                let buttons = Array.from(document.querySelectorAll('span')).filter(el => el.textContent.trim() === 'Reply' || el.textContent.trim() === 'রিপ্লাই');
                for(let btn of buttons) {
                    // চেক করা হচ্ছে বাটনটি ভিজিবল কিনা এবং আগে কাজ করা হয়েছে কিনা
                    if(!btn.hasAttribute('data-done-reply') && btn.offsetParent !== null) {
                        btn.setAttribute('data-done-reply', 'true'); // সাথে সাথে মার্ক করা হলো
                        btn.scrollIntoView({behavior: 'smooth', block: 'center'});
                        return btn; // PHP কে বাটনটি পাঠিয়ে দেওয়া হলো
                    }
                }
                return null; // নতুন কোনো বাটন নেই
            ";

            $freshButton = $driver->executeScript($js_finder);

            // ---------------------------------------------------------
            // ৩. যদি নতুন ফ্রেশ বাটন পাওয়া যায়
            // ---------------------------------------------------------
            if ($freshButton) {
                echo "\n👉 নতুন মানুষের কমেন্ট পাওয়া গেছে, Reply বাটনে ক্লিক করা হলো...\n";
                sleep(2); // স্ক্রল শেষ হওয়ার জন্য সময়

                // বাটনটিতে ক্লিক করা
                $driver->executeScript("arguments[0].click();", [$freshButton]);
                sleep(3); // রিপ্লাই বক্স ওপেন ও ফোকাস হওয়ার সময়

                $activeElement = $driver->switchTo()->activeElement();
                $role = $activeElement->getAttribute('role');

                if ($role === 'textbox' || $activeElement->getAttribute('contenteditable') === 'true') {
                    
                    // টাইপিং এবং এন্টার
                    $messagesList = isset($replyMessages) ? $replyMessages : ["ধন্যবাদ!"];
                    $randomReply = $messagesList[array_rand($messagesList)];

                    echo "✍️ রিপ্লাই লেখা হচ্ছে: '$randomReply'\n";
                    $activeElement->sendKeys($randomReply);
                    sleep(2); 

                    $activeElement->sendKeys(WebDriverKeys::ENTER);
                    echo "✅ ENTER চাপা হয়েছে!\n";

                    echo "⏳ কমেন্ট পাবলিশ হওয়ার জন্য ৬ সেকেন্ড অপেক্ষা...\n";
                    sleep(6); 

                    // আনফোকাস করা (কোনো ESCAPE বাটন নেই, তাই পোস্ট ক্লোজ হবে না)
                    echo "❌ বক্স আনফোকাস করা হচ্ছে...\n";
                    $driver->executeScript("if(document.activeElement) document.activeElement.blur();");

                    // 🔴 অ্যান্টি-লুপ হ্যাক: সদ্য তৈরি হওয়া নিজের কমেন্টের Reply বাটনগুলোকেও ব্লক করা 🔴
                    $driver->executeScript("
                        let allBtns = Array.from(document.querySelectorAll('span')).filter(el => el.textContent.trim() === 'Reply' || el.textContent.trim() === 'রিপ্লাই');
                        for(let b of allBtns) { b.setAttribute('data-done-reply', 'true'); }
                    ");

                    $replyCount++;
                    $scrollAttempts = 0; // সফল হলে স্ক্রল কাউন্ট রিস্টার্ট হবে

                    $sleepTime = rand(20, 35); 
                    echo "🛑 সিকিউরিটির জন্য $sleepTime সেকেন্ড অপেক্ষা...\n";
                    sleep($sleepTime);

                } else {
                    echo "⚠️ রিপ্লাই বক্স ফোকাস হয়নি, স্কিপ করা হচ্ছে...\n";
                    $driver->executeScript("if(document.activeElement) document.activeElement.blur();");
                    sleep(2);
                }

            } 
            // ---------------------------------------------------------
            // ৪. যদি নতুন বাটন না পাওয়া যায়, তবে নিচে স্ক্রল করা
            // ---------------------------------------------------------
            else {
                echo "⬇️ আরও নতুন কমেন্ট খোঁজার জন্য নিচে স্ক্রল করা হচ্ছে...\n";
                // পপ-আপ এবং মেইন বডি উভয়ের জন্যই পারফেক্ট স্ক্রল লজিক
                $driver->executeScript("
                    let dialog = document.querySelector('div[role=\"dialog\"]');
                    if (dialog) {
                        // ফেসবুকের পপ-আপের ভেতরের স্ক্রলবার খোঁজা
                        let scrollable = dialog.querySelector('div[style*=\"overflow-y\"]') || dialog;
                        scrollable.scrollBy({top: 600, behavior: 'smooth'});
                    } else {
                        window.scrollBy({top: 600, behavior: 'smooth'});
                    }
                ");
                $scrollAttempts++;
                sleep(4);
            }

        } catch (Exception $e) {
            echo "⚠️ ছোটখাটো এরর, স্কিপ করে নিচে নামা হচ্ছে...\n";
            $driver->executeScript("window.scrollBy(0, 300);");
            sleep(3);
        }
    }

    if ($replyCount >= $maxReplies) {
        echo "\n🎯 এই পোস্টের টার্গেট ($maxReplies টি রিপ্লাই) 100% পূর্ণ হয়েছে!\n";
    } else {
        echo "\n⚠️ আর নতুন কমেন্ট লোড হয়নি। মোট $replyCount টি রিপ্লাই সম্পন্ন হয়েছে।\n";
    }
}
?>