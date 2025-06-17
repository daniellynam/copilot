<!doctype html>
<html lang="en">
  <head>
    <title>Senator Workbench</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
      }

      .container {
        width: 80%;
        margin: 0 auto;
        margin-top: 50vh;
        transform: translateY(-50%);
      }

      .header {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
      }

      .top-section {
        width: 80%;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
      }

      .section {
        flex: 1;
        padding: 10px;
        box-sizing: border-box;
      }

      .prompts, .gauge, .pending-actions {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
      }

      .prompts h2, .gauge h2, .pending-actions h2 {
        font-size: 16px;
        margin-bottom: 10px;
      }

      .prompt-button {
        display: block;
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #d3d3d3;
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
      }

      .pending-actions table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
      }

      .pending-actions th, .pending-actions td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
      }

      .chat-container {
        width: 80%;
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
      }

      .webchat {
        height: 500px;
      }

      .webchat__bubble--from-user,
      .webchat__bubble--from-bot {
        border: none !important;
        box-shadow: none !important;
      }

      .webchat .webchat__bubble--from-user {
        text-align: right;
        background-color: #e0e0e0;
        border: none;
        border: none !important;
        box-shadow: none !important;
      }

     .webchat .webchat__bubble--from-bot {
        text-align: left;
        background-color: #ffffff;
        border: none;
        border: none !important;
        box-shadow: none !important;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">Senator Workbench</div>
      <div class="top-section">
        <div class="section prompts">
          <h2>Top Prompts</h2>
          <button class="prompt-button" onclick="sendMessage('Prompt 1')">Top 3 projects costs</button>
          <button class="prompt-button" onclick="sendMessage('Prompt 2')">Major program issues</button>
          <button class="prompt-button" onclick="sendMessage('Prompt 3')">Delayed projects</button>
        </div>
        <div class="section gauge">
          <h2>Gauge</h2>
          <div>80% Complete</div>
        </div>
        <div class="section pending-actions">
          <h2>Pending Actions</h2>
          <table>
            <tr>
              <th>Action</th>
              <th>Status</th>
            </tr>
            <tr>
              <td>Action 1</td>
              <td>Pending</td>
            </tr>
            <tr>
              <td>Action 2</td>
              <td>Pending</td>
            </tr>
          </table>
        </div>
      </div>
      <div class="chat-container">
        <div id="webchat" class="webchat" role="main"></div>
      </div>
    </div>

    <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
    <script>
      let directLine;

      (async function () {
        const styleOptions = {
          hideUploadButton: true
        };

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
