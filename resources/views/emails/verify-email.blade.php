<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل حسابك</title>
    <!-- رابط خط Tajawal للعربية -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 700;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);
        }

        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.3);
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #888888;
        }

        /* تأثيرات إضافية */
        .container {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>مرحبًا {{ $user->name }},</h2>
        <p>شكرًا لك على التسجيل في موقعنا. يرجى تفعيل حسابك بالنقر على الزر أدناه:</p>
        <p>
            <a href="{{ $url }}" class="btn">تفعيل الحساب</a>
        </p>
        <p class="footer">إذا لم تقم بالتسجيل، يمكنك تجاهل هذا البريد.</p>
    </div>
</body>

</html>
