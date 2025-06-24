
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Senator Workbench</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: #f3f2f1;
    }

    header {
      background-color: #ffffff;
      padding: 1rem 2rem;
      font-size: 1.5rem;
      font-weight: 600;
      color: #323130;
      border-bottom: 1px solid #e1dfdd;
    }

    .workbench {
      display: flex;
      height: calc(100% - 64px); /* Adjust for header height */
    }

    .main-content {
      width: 66.66%;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      overflow-y: auto;
    }

    .chat-window {
      width: 33.33%;
      border-left: 1px solid #e1dfdd;
      padding: 2rem;
      background-color: #ffffff;
      overflow-y: auto;
    }

    .section {
      background-color: #ffffff;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .section h2 {
      font-size: 1.2rem;
      color: #201f1e;
      margin-bottom: 1rem;
    }

    .prompt-button {
      display: block;
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #c7e0f4;
      border: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      font-weight: 500;
    }

    .prompt-button:hover {
      background-color: #a0d1f5;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #edebe9;
      padding: 8px;
      text-align: left;
    }

    .webchat {
      height: 100%;
    }

    .webchat__bubble--from-user .webchat__bubble__content,
    .webchat__bubble--from-bot .webchat__bubble__content {
      border: none !important;
      box-shadow: none !important;
    }

    .webchat .webchat__bubble--from-user {
      text-align: right;
      background-color: #e0e0e0;
    }

    .webchat .webchat__bubble--from-bot {
      text-align: left;
      background-color: #ffffff;
    }
  </style>
</head>
<body>
  <header>Welcome Daniel</header>
  <div class="workbench">
    <div class="main-content">
      <div class="section prompts">
        <h2>Top Prompts</h2>
        <button class="prompt-button" onclick="sendMessage('Top 3 projects costs')">Top 3 projects costs</button>
        <button class="prompt-button" onclick="sendMessage('Major program issues')">Major program issues</button>
        <button class="prompt-button" onclick="sendMessage('Delayed projects')">Delayed projects</button>
      </div>
      <div class="section gauge">
        <h2>Gauge</h2>
        <div>80% Complete</div>
      </div>
      <div class="section pending-actions">
        <h2>Pending Actions</h2>
        <table>
          <tr><th>Action</th><th>Status</th></tr>
          <tr><td>Action 1</td><td>Pending</td></tr>
          <tr><td>Action 2</td><td>Pending</td></tr>
        </table>
      </div>
    </div>
    <div class="chat-window">
      <div id="webchat" class="webchat" role="main"></div>
    </div>
  </div>

  <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
  <script>
    let directLine;

    (async function () {
      const styleOptions = { hideUploadButton: true };
      const tokenEndpointURL = new URL('https://748bab4fa737e24aa461e28516a505.4a.environment.api.powerplatform.com/powervirtualagents/botsbyschema/cr4b6_parliamentarySenateEstimatesAssistant/directline/token?api-version=2022-03-01-preview');
      const locale = document.documentElement.lang || 'en';
      const apiVersion = tokenEndpointURL.searchParams.get('api-version');

      const [directLineURL, token] = await Promise.all([
        fetch(new URL(`/powervirtualagents/regionalchannelsettings?api-version=${apiVersion}`, tokenEndpointURL))
          .then(response => response.json())
          .then(({ channelUrlsById: { directline } }) => directline),
        fetch(tokenEndpointURL)
          .then(response => response.json())
          .then(({ token }) => token)
      ]);

      directLine = WebChat.createDirectLine({ domain: new URL('v3/directline', directLineURL), token });

      const subscription = directLine.connectionStatus$.subscribe({
        next(value) {
          if (value === 2) {
            directLine.postActivity({
              localTimezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
              locale,
              name: 'startConversation',
              type: 'event'
            }).subscribe();
            subscription.unsubscribe();
          }
        }
      });

      WebChat.renderWebChat({ directLine, locale, styleOptions }, document.getElementById('webchat'));
    })();

    function sendMessage(message) {
      if (directLine) {
        directLine.postActivity({
          from: { id: 'user', name: 'User' },
          type: 'message',
          text: message
        }).subscribe();
      } else {
        console.error('DirectLine not initialized yet.');
      }
    }
  </script>
</body>
</html>
