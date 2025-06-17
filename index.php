
<!doctype html>
<html lang="en">
  <head>
    <title>Senator Workbench</title>
    <style>
      html,
      body {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
      }

      #container {
        display: flex;
        flex-direction: column;
        height: 100%;
      }

      #top-section {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
      }

      #prompts {
        display: flex;
        flex-direction: column;
      }

      .prompt {
        margin: 5px 0;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
      }

      #pending-actions {
        display: flex;
        flex-direction: column;
      }

      #pending-actions table {
        width: 100%;
        border-collapse: collapse;
      }

      #pending-actions th,
      #pending-actions td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
      }

      #chat-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-color: #e9ecef;
      }

      #webchat {
        width: 60%;
        height: 500px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background-color: white;
      }
    </style>
  </head>
  <body>
    <div id="container">
      <div id="top-section">
        <div id="prompts">
          <div class="prompt" onclick="sendPrompt('Placeholder 1')">Placeholder 1</div>
          <div class="prompt" onclick="sendPrompt('Placeholder 2')">Placeholder 2</div>
          <div class="prompt" onclick="sendPrompt('Placeholder 3')">Placeholder 3</div>
          <div class="prompt" onclick="sendPrompt('Placeholder 4')">Placeholder 4</div>
        </div>
        <div id="pending-actions">
          <h3>Pending Actions</h3>
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
      <div id="chat-container">
        <div id="webchat" role="main"></div>
      </div>
    </div>

    <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
    <script>
      (async function () {
        const styleOptions = {
          bubbleBackground: 'white',
          bubbleBorderColor: 'transparent',
          bubbleBorderRadius: 10,
          bubbleFromUserBackground: '#007bff',
          bubbleFromUserTextColor: 'white',
          hideUploadButton: true
        };

        const tokenEndpointURL = new URL('https://748bab4fa737e24aa461e28516a505.4a.environment.api.powerplatform.com/powervirtualagents/botsbyschema/cr4b6_parliamentarySenateEstimatesAssistant/directline/token?api-version=2022-03-01-preview');
        const locale = document.documentElement.lang || 'en';

        const apiVersion = tokenEndpointURL.searchParams.get('api-version');

        const [directLineURL, token] = await Promise.all([
          fetch(new URL(`/powervirtualagents/regionalchannelsettings?api-version=${apiVersion}`, tokenEndpointURL))
            .then(response => {
              if (!response.ok) {
                throw new Error('Failed to retrieve regional channel settings.');
              }
              return response.json();
            })
            .then(({ channelUrlsById: { directline } }) => directline),
          fetch(tokenEndpointURL)
            .then(response => {
              if (!response.ok) {
                throw new Error('Failed to retrieve Direct Line token.');
              }
              return response.json();
            })
            .then(({ token }) => token)
        ]);

        const directLine = WebChat.createDirectLine({ domain: new URL('v3/directline', directLineURL), token });

        const subscription = directLine.connectionStatus$.subscribe({
          next(value) {
            if (value === 2) {
              directLine
                .postActivity({
                  localTimezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                  locale,
                  name: 'startConversation',
                  type: 'event'
                })
                .subscribe();
              subscription.unsubscribe();
            }
          }
        });

        WebChat.renderWebChat({ directLine, locale, styleOptions }, document.getElementById('webchat'));

        window.sendPrompt = function (prompt) {
          directLine.postActivity({
            from: { id: 'user1', name: 'User' },
            type: 'message',
            text: prompt
          }).subscribe();
        };
      })();
    </script>
  </body>
</html>
