<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FB Automation Pro Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* কনসোল বক্সের ডিজাইন */
        #console {
            background-color: #1e1e1e;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap; /* লাইন ব্রেক ঠিক রাখার জন্য */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-5xl mx-auto mt-10 p-5">
        
        <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg mb-8 text-center">
            <h1 class="text-3xl font-bold">🚀 Facebook Automation Pro</h1>
            <p class="text-blue-100 mt-2">আপনার সকল ফেসবুক মার্কেটিং কাজ এক ক্লিকেই কন্ট্রোল করুন</p>
        </div>


        <div class="bg-white p-6 rounded-lg shadow border-t-4 border-yellow-500 text-center">
                <h2 class="text-xl font-bold mb-4 text-gray-700">🔐 Login to Facebook</h2>
                <p class="text-sm text-gray-500 mb-6">প্রথমে এখানে ক্লিক করে আপনার ফেসবুক আইডিতে লগইন করে নিন।</p>
                <button onclick="startTask('login')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded transition">
                    Start Login
                </button>
            </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-blue-500 text-center">
                <h2 class="text-xl font-bold mb-4 text-gray-700">📝 Auto Group Post</h2>
                <p class="text-sm text-gray-500 mb-6">টার্গেটেড গ্রুপগুলোতে স্বয়ংক্রিয়ভাবে ছবি, ভিডিও বা টেক্সট পোস্ট করুন।</p>
                <button onclick="startTask('post')" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded transition">
                    Start Auto Post
                </button>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-green-500 text-center">
                <h2 class="text-xl font-bold mb-4 text-gray-700">💬 Auto Comment</h2>
                <p class="text-sm text-gray-500 mb-6">গ্রুপের নতুন পোস্টগুলোতে মানুষের মতো স্প্যাম-ফ্রি কমেন্ট করুন।</p>
                <button onclick="startTask('comment')" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded transition">
                    Start Auto Comment
                </button>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-purple-500 text-center">
                <h2 class="text-xl font-bold mb-4 text-gray-700">🔁 Auto Reply</h2>
                <p class="text-sm text-gray-500 mb-6">নির্দিষ্ট পোস্টের কমেন্টে গিয়ে সবার কমেন্টের অটোমেটিক রিপ্লাই দিন।</p>
                <button onclick="startTask('reply')" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded transition">
                    Start Auto Reply
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-3 text-gray-700">🖥️ Live Activity Log</h3>
            <div id="console">সিস্টেম রেডি। ওপর থেকে যেকোনো একটি বাটনে ক্লিক করুন...
</div>
        </div>

    </div>

    <script>
        async function startTask(taskName) {
            const consoleBox = document.getElementById('console');
            consoleBox.innerHTML += `\n\n[System] 🟡 Starting task: ${taskName.toUpperCase()}...\n`;
            consoleBox.scrollTop = consoleBox.scrollHeight;

            try {
                // Fetch API দিয়ে ব্যাকএন্ডে রিকোয়েস্ট পাঠানো (Streaming mode)
                const response = await fetch(`run_task.php?task=${taskName}`);
                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    
                    // পিএইচপি থেকে আসা লাইভ ডাটা কনসোলে দেখানো
                    const chunk = decoder.decode(value, { stream: true });
                    consoleBox.innerHTML += chunk;
                    consoleBox.scrollTop = consoleBox.scrollHeight; // অটো স্ক্রল ডাউন
                }
            } catch (error) {
                consoleBox.innerHTML += `\n[System] ❌ Error: ${error.message}\n`;
            }
        }
    </script>
</body>
</html>