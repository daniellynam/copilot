
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
        text-align: center;
      }
      .header {
        font-size: 16px;
        margin: 20px 0;
      }
      .top-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
      }
      .section {
        width: 30%;
        padding: 10px;
      }
      .prompts {
        display: flex;
        justify-content: space-around;
      }
      .prompt {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
      }
      .gauge {
        background-color: #e0e0e0;
        padding: 20px;
        border-radius: 5px;
      }
      .pending-actions {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
      }
      .chat-container {
        width: 80%;
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">Senator Workbench</div>
      <div class="top-section">
        <div class="section prompts">
          <div class="prompt">Top 5 projects ordered by project spend</div>
          <div class="prompt">Biggest program issue</div>
          <div class="prompt">Inflight projects</div>
        </div>
        <div class="section gauge">
          <div>Gauge: 80% Complete</div>
        </div>
        <div class="section pending-actions">
          <table>
            <tr>
              <th>Pending Actions</th>
            </tr>
            <tr>
              <td>Action 1</td>
            </tr>
            <tr>
              <td>Action 2</td>
            </tr>
          </table>
        </div>
      </div>
      <div class="chat-container">
        <div id="webchat" role="main"></div>
      </div>
    </div>
    <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
    <script>
      (async function () {
        const styleOptions = {
          hideUploadButton: true,
          bubbleBackground: 'white',
          bubbleBorderRadius: 10,
          bubbleBorderWidth: 0
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
      })();
    </script>
  </body>
</html>
