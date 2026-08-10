<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Control Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --panel-dark: #121824;
            --terminal-bg: #030712;
            --accent: #e27c1d;
            --accent-hover: #ca6b15;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --success: #10b981;
            --border: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* Glassmorphism card */
        .container {
            width: 100%;
            max-width: 900px;
            background-color: var(--panel-dark);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header styling */
        header {
            padding: 25px 30px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(226, 124, 29, 0.05) 0%, rgba(0, 0, 0, 0) 100%);
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title h1 span {
            color: var(--accent);
        }

        .header-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .badge {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Control Panel Grid */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            padding: 30px;
            gap: 25px;
        }

        @media(min-width: 768px) {
            .grid-container {
                grid-template-columns: 320px 1fr;
            }
        }

        /* Sidebar actions */
        .actions-sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 5px;
            font-weight: 700;
        }

        .btn-command {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 14px 18px;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-command:hover {
            background-color: rgba(226, 124, 29, 0.1);
            border-color: var(--accent);
            transform: translateX(4px);
        }

        .btn-command::after {
            content: '→';
            font-size: 18px;
            color: var(--text-muted);
            transition: transform 0.25s;
        }

        .btn-command:hover::after {
            color: var(--accent);
            transform: translateX(2px);
        }

        .btn-command:active {
            transform: scale(0.98);
        }

        /* Terminal Console */
        .console-container {
            display: flex;
            flex-direction: column;
            height: 400px;
            background-color: var(--terminal-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        .console-header {
            background-color: rgba(255, 255, 255, 0.02);
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .console-dots {
            display: flex;
            gap: 6px;
        }

        .console-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .console-dot.red { background-color: #ef4444; }
        .console-dot.yellow { background-color: #f59e0b; }
        .console-dot.green { background-color: #10b981; }

        .console-title {
            font-family: 'Fira Code', monospace;
            font-size: 12px;
            color: var(--text-muted);
        }

        .console-body {
            padding: 20px;
            flex-grow: 1;
            overflow-y: auto;
            font-family: 'Fira Code', monospace;
            font-size: 14px;
            line-height: 1.6;
            color: var(--success);
            white-space: pre-wrap;
            scroll-behavior: smooth;
        }

        .console-body::-webkit-scrollbar {
            width: 6px;
        }

        .console-body::-webkit-scrollbar-thumb {
            background-color: var(--border);
            border-radius: 3px;
        }

        .terminal-line {
            margin-bottom: 8px;
        }

        .terminal-prompt {
            color: var(--accent);
            font-weight: 500;
        }

        .terminal-output {
            color: var(--text-main);
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-top: 2px solid var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .status-idle {
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="header-title">
                <h1>Shiatic <span>Artisan Panel</span></h1>
                <p>Execute administrative commands securely over HTTP</p>
            </div>
            <div class="badge">
                <span style="display:inline-block; width:8px; height:8px; background-color:var(--success); border-radius:50%;"></span>
                SECURE ENVIRONMENT
            </div>
        </header>

        <div class="grid-container">
            <!-- Commands List -->
            <div class="actions-sidebar">
                <div class="section-title">Maintenance Commands</div>
                
                <button class="btn-command" onclick="runCommand('config:clear')">
                    Clear Config Cache
                </button>
                <button class="btn-command" onclick="runCommand('package:discover')">
                    Package Discover
                </button>
                <button class="btn-command" onclick="runCommand('optimize:clear')">
                    Clear All Caches
                </button>
                <button class="btn-command" onclick="runCommand('optimize')">
                    Optimize (Cache All)
                </button>
                
                <div class="section-title" style="margin-top: 15px;">Database Commands</div>
                <button class="btn-command" onclick="runCommand('migrate')">
                    Run Database Migrations
                </button>
            </div>

            <!-- Terminal Output Console -->
            <div class="console-container">
                <div class="console-header">
                    <div class="console-dots">
                        <div class="console-dot red"></div>
                        <div class="console-dot yellow"></div>
                        <div class="console-dot green"></div>
                    </div>
                    <div class="console-title">bash - artisan@shiatic</div>
                    <div class="spinner" id="command-spinner"></div>
                </div>
                <div class="console-body" id="console-output"><span class="status-idle"># Console idle. Select a command on the left to execute.</span></div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const securityKey = urlParams.get('key');

        function appendToConsole(command, output) {
            const consoleBody = document.getElementById('console-output');
            
            // If it has the idle placeholder, clear it
            const idleSpan = consoleBody.querySelector('.status-idle');
            if (idleSpan) {
                consoleBody.innerHTML = '';
            }

            const promptLine = document.createElement('div');
            promptLine.className = 'terminal-line';
            promptLine.innerHTML = `<span class="terminal-prompt">$ php artisan ${command}</span>`;
            consoleBody.appendChild(promptLine);

            const outputLine = document.createElement('div');
            outputLine.className = 'terminal-line terminal-output';
            outputLine.textContent = output;
            consoleBody.appendChild(outputLine);
            
            // Scroll to bottom
            consoleBody.scrollTop = consoleBody.scrollHeight;
        }

        function runCommand(command) {
            if (!securityKey) {
                alert('Access Denied: Missing key parameter in URL.');
                return;
            }

            const spinner = document.getElementById('command-spinner');
            spinner.style.display = 'block';

            // Send POST request
            fetch('/artisan-panel/run', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    command: command,
                    key: securityKey
                })
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                if (data.success) {
                    appendToConsole(command, data.output);
                } else {
                    appendToConsole(command, `ERROR: ${data.message}`);
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                appendToConsole(command, `NETWORK ERROR: Failed to reach the server.`);
            });
        }
    </script>
</body>
</html>
