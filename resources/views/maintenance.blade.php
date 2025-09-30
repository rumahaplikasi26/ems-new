<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ $system_name ?? 'EMS System' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .maintenance-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .maintenance-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1e3c72, #2a5298, #3b82f6, #1d4ed8);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .ems-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2em;
            font-weight: bold;
        }

        .maintenance-icon {
            font-size: 80px;
            color: #1e3c72;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .maintenance-title {
            font-size: 2.5em;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .maintenance-subtitle {
            font-size: 1.2em;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .maintenance-description {
            font-size: 1em;
            color: #718096;
            margin-bottom: 40px;
            line-height: 1.8;
        }

        .estimated-time {
            background: #fef5e7;
            border: 1px solid #f6e05e;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .estimated-time h3 {
            color: #d69e2e;
            font-size: 1.1em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .estimated-time p {
            color: #744210;
            font-size: 0.95em;
        }

        .contact-info {
            background: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .contact-info h3 {
            color: #38a169;
            font-size: 1.1em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 8px 0;
            color: #276749;
            font-size: 0.95em;
        }

        .contact-item i {
            width: 20px;
            text-align: center;
        }

        .refresh-button {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 20px 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .refresh-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 60, 114, 0.3);
        }

        .refresh-button:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.9em;
        }

        .footer a {
            color: #1e3c72;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 40px 20px;
                margin: 20px;
            }

            .maintenance-title {
                font-size: 2em;
            }

            .maintenance-subtitle {
                font-size: 1.1em;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="ems-logo">EMS</div>
        
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        
        <h1 class="maintenance-title">System Maintenance</h1>
        
        <p class="maintenance-subtitle">
            We're currently performing scheduled maintenance to improve your EMS experience
        </p>
        
        <p class="maintenance-description">
            {{ $message ?? 'Our team is working hard to enhance the Employee Management System performance and add new features. We apologize for any inconvenience this may cause.' }}
        </p>

        <div class="estimated-time">
            <h3>
                <i class="fas fa-clock"></i>
                Estimated Completion Time
            </h3>
            <p>We expect to complete maintenance within the next <strong>{{ $estimated_completion ?? '2-3 hours' }}</strong></p>
        </div>

        <div class="contact-info">
            <h3>
                <i class="fas fa-headset"></i>
                Need Immediate Assistance?
            </h3>
            @if($contact_phone)
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>{{ $contact_phone }}</span>
            </div>
            @endif
            @if($contact_email)
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $contact_email }}</span>
            </div>
            @endif
            <div class="contact-item">
                <i class="fab fa-whatsapp"></i>
                <span>+62 812 3456 7890 (WhatsApp)</span>
            </div>
        </div>

        <button class="refresh-button pulse" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i>
            Check Again
        </button>

        <div class="footer">
            <p>
                &copy; {{ date('Y') }} <strong>{{ $system_name ?? 'Employee Management System' }}</strong>. All rights reserved.
                <br>
                <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
            </p>
        </div>
    </div>

    <script>
        // Auto refresh every 5 minutes
        setTimeout(() => {
            window.location.reload();
        }, 5 * 60 * 1000);
    </script>
</body>
</html>
