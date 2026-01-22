<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error | Drift Nimbus</title>
    <style>
        body {
            background-color: #05070a;
            color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 2rem;
        }

        h1 {
            color: #fca5a5;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        p {
            color: #9ca3af;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background-color: #ffffff;
            color: #000000;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .error-code {
            font-family: monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            text-align: left;
            overflow-x: auto;
            color: #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Critical System Malfunction</h1>
        <p>
            The dashboard has encountered an unexpected termination sequence.
            Please contact support immediately to resolve this technical anomaly.
        </p>

        <?php if (getenv('APP_DEBUG') === 'true' && isset($errorMessage)): ?>
            <div class="error-code">
                <strong>Debug Info:</strong><br>
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <a href="mailto:support@driftnimbus.com" class="btn">Contact Technical Support</a>
    </div>
</body>

</html>