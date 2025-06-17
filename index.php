<!doctype html>
<html lang="en">
  <head>
    <title>Contoso Sample Web Chat</title>
    <!--
      This styling is for Web Chat demonstration purposes
      For larger projects, we recommend you move style to a separate file
      Learn more about Web Chat at https://github.com/microsoft/BotFramework-WebChat
    -->
    <style>
      html,
      body {
        height: 100%;
      }

      body {
        margin: 0;
      }

      h1 {
        color: whitesmoke;
        font-family: Segoe UI;
        font-size: 16px;
        line-height: 20px;
        margin: 0;
        padding: 0 20px;
      }

      #banner {
        align-items: center;
        background-color: black;
        display: flex;
        height: 50px;
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
    <div>
      <div id="banner">
        <h1>Contoso agent name</h1>
      </div>
      <div id="webchat" role="main"></div>
    </div>

    <!--
      This sample uses the latest version of Web Chat
      In a production environment, the version number should be pinned and version bump should be done frequently
      Review the changelog at https://github.com/microsoft/BotFramework-WebChat/tree/main/CHANGELOG.md
    -->
    <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>

    <script>
      (async function () {
        // Specifies style options to customize the Web Chat canvas
        // For customization samples, visit https://microsoft.github.io/BotFramework-WebChat
        const styleOptions = {
          // Hides the upload button
          hideUploadButton: true
        };

        // Specifies the token endpoint for your agent
        // To get this value, go to the Channels page for your agent in Copilot Studio, and open the Email config panel
        const tokenEndpointURL = new URL('https://748bab4fa737e24aa461e28516a505.4a.environment.api.powerplatform.com/powervirtualagents/botsbyschema/cr4b6_parliamentarySenateEstimatesAssistant/directline/token?api-version=2022-03-01-preview');

        // Specifies the language the agent and Web Chat should display in:
        // - (Recommended) To match the page language, set it to document.documentElement.lang
        // - To use current user language, set it to navigator.language with a fallback language
        // - To use another language, set it to supported Unicode locale
        // Setting page language is highly recommended
        // When page language is set, browsers use native font for the specified language

        const locale = document.documentElement.lang || 'en'; // Uses language specified in <html> element and fallback to English (United States).
        // const locale = navigator.language || 'ja-JP'; // Uses user preferred language and fallback to Japanese.
        // const locale = 'zh-HAnt'; // Always use Chinese (Traditional).

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

        // The "token" variable is the credentials for accessing the current conversation.
        // To maintain conversation across page navigation, save and reuse the token.

        // The token could have access to sensitive information about the user.
        // It must be treated like user password.

        const directLine = WebChat.createDirectLine({ domain: new URL('v3/directline', directLineURL), token });

        // Sends "startConversation" event when the connection is established.

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

              // Only send the event once, unsubscribe after the event is sent.
              subscription.unsubscribe();
            }
          }
        });

        WebChat.renderWebChat({ directLine, locale, styleOptions }, document.getElementById('webchat'));
      })();
    </script>
  </body>
</html>
