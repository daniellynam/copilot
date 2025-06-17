
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
      #banner {
        align-items: center;
        background-color: black;
        color: white;
        display: flex;
        height: 50px;
        padding: 0 20px;
      }
      #container {
        display: flex;
        height: calc(100% - 50px);
        padding: 20px;
      }
      #left-column, #right-column {
        flex: 1;
        padding: 10px;
      }
      #left-column {
        border-right: 1px solid #ccc;
      }
      #right-column {
        border-left: 1px solid #ccc;
      }
      #prompts {
        list-style-type: none;
        padding: 0;
      }
      #prompts li {
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        margin: 5px 0;
        padding: 10px;
        cursor: pointer;
      }
      #pending-actions {
        width: 100%;
        border-collapse: collapse;
      }
      #pending-actions th, #pending-actions td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
      }
      #webchat {
        height: calc(100% - 50px);
        overflow: hidden;
        position: fixed;
        top: 50px;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div id="banner">
      <h1>Senator Workbench</h1>
    </div>
    <div id="container">
      <div id="left-column">
        <h2>Prompts</h2>
        <ul id="prompts">
          <li onclick="sendPrompt('Prompt 1')">Placeholder 1</li>
          <li onclick="sendPrompt('Prompt 2')">Placeholder 2</li>
          <li onclick="sendPrompt('Prompt 3')">Placeholder 3</li>
          <li onclick="sendPrompt('Prompt 4')">Placeholder 4</li>
        </ul>
      </div>
      <div id="right-column">
        <h2>Pending Actions</h2>
        <table id="pending-actions">
          <thead>
            <tr>
              <th>Action</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Action 1</td>
              <td>Pending</td>
            </tr>
            <tr>
              <td>Action 2</td>
              <td>Pending</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="webchat" role="main"></div>
    <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
    <script>
      (async function () {
        const styleOptions = {
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

        // Function to send a predefined prompt message to the chat
        window.sendPrompt = function (prompt) {
          directLine.postActivity({
            from: { id: 'user' },
            name: 'message',
            type: 'message',
            text: prompt
          }).subscribe();
        };
      })();
    </script>
  </body>
</html>
