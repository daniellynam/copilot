
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Aged Care Workbench</title>
  <style>
    /* Base styles for page layout and typography */
    html, body {
      height: 100%;
      margin: 0;
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: #f3f2f1;
    }

    /* Header styling */
    header {
      background-color: #ffffff;
      padding: 1rem 2rem;
      font-size: 1.5rem;
      font-weight: 600;
      color: #323130;
      border-bottom: 1px solid #e1dfdd;
    }

    /* Main layout container */
    .workbench {
      display: flex;
      height: calc(100% - 64px); /* Subtract header height */
    }

    /* Left panel: main content area */
    .main-content {
      width: 66.66%; /* Two-thirds of the screen */
      padding: 2rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      overflow-y: auto;
    }

    /* Right panel: chat window */
    .chat-window {
      width: 33.33%; /* One-third of the screen */
      border-left: 1px solid #e1dfdd;
      padding: 2rem;
      background-color: #ffffff;
      overflow-y: auto;
    }

    /* Section styling for cards/containers */
    .section {
      background-color: #ffffff;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Section title styles */
    .section h2 {
      font-size: 1.2rem;
      color: #201f1e;
      margin-bottom: 1rem;
    }

    /* Section subtitle styles */
    .section h3 {
      font-size: 1.0rem;
      color: #201f1e;
      margin-bottom: 1rem;
    }

    /* Layout for prompt buttons */
    .prompt-buttons {
      display: flex;
      gap: 1rem;
    }

    /* Individual prompt button styling */
    .prompt-button {
      flex: 1;
      padding: 10px;
      background-color: #c7e0f4;
      border: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      font-weight: 500;
    }

    /* Hover effect for prompt buttons */
    .prompt-button:hover {
      background-color: #a0d1f5;
    }

    /* Table styling for resident data */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #edebe9;
      padding: 8px;
      text-align: left;
    }

    /* Chat container height */
    .webchat {
      height: 100%;
    }

    /* Remove borders and shadows from chat bubbles */
    .webchat__bubble--from-user .webchat__bubble__content,
    .webchat__bubble--from-bot .webchat__bubble__content {
      border: none !important;
      box-shadow: none !important;
    }

    /* User message bubble styling */
    .webchat .webchat__bubble--from-user {
      text-align: right;
      background-color: #e0e0e0;
    }

    /* Bot message bubble styling */
    .webchat .webchat__bubble--from-bot {
      text-align: left;
      background-color: #ffffff;
    }
  </style>
</head>
<body>
  <!-- Page header -->
  <header>Welcome Daniel</header>

  <!-- Main layout: content + chat -->
  <div class="workbench">
    <!-- Left panel: main content -->
    <div class="main-content">
      <!-- Section: Prompt buttons -->
      <div class="section prompts">
        <h2>Top Prompts</h2>
        <div class="prompt-buttons">
          <!-- Each button sends a predefined message to the bot -->
          <button class="prompt-button" onclick="sendMessage('Identify the top 10 residents with the highest clinical risks')">Identify the top 10 residents with the highest clinical risks</button>
          <button class="prompt-button" onclick="sendMessage('List the residents with pending appointments')">List the residents with pending appointments</button>
          <button class="prompt-button" onclick="sendMessage('Conduct compliance checks against progress notes')">Conduct compliance checks against progress notes</button>
        </div>
      </div>

      <!-- Section: Compliance gauge -->
      <div class="section gauge">
        <h2>Care Minutes Compliance</h2>
        <div>95% Compliant</div>
      </div>

      <!-- Section: AI recommendations -->
      <div class="section pending-actions">
        <h2>AI Recommended Priorities</h2>
        <h3>Apply these recommendations to improve resident care minutes</h3>
        <table>
          <tr><th>Resident</th><th>Care Minutes (Actuals)</th><th>Actions</th></tr>
          <tr><td>Resident 1</td><td>100min (55%)</td><td>Pending</td></tr>
          <tr><td>Resident 2</td><td>200min (75%)</td><td>Pending</td></tr>
        </table>
      </div>
    </div>

    <!-- Right panel: chat window -->
    <div class="chat-window">
      <div id="webchat" class="webchat" role="main"></div>
    </div>
  </div>

  <!-- Load Bot Framework Web Chat -->
  <script crossorigin="anonymous" src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></nc function () {
      // Web Chat style options
      const styleOptions = { hideUploadButton: true };

      // Token endpoint for bot authentication
      const tokenEndpointURL = new URL('https://748bab4fa737e24aa461e28516a505.4a.environment.api.powerplatform.com/powervirtualagents/botsbyschema/cr4b6_parliamentarySenateEstimatesAssistant/directline/token?api-version=2022-03-01-preview');

      // Get locale and API version
      const locale = document.documentElement.lang || 'en';
      const apiVersion = tokenEndpointURL.searchParams.get('api-version');

      // Fetch Direct Line URL and token in parallel
      const [directLineURL, token] = await Promise.all([
        fetch(new URL(`/powervirtualagents/regionalchannelsettings?api-version=${apiVersion}`, tokenEndpointURL))
          .then(response => response.json())
          .then(({ channelUrlsById: { directline } }) => directline),
        fetch(tokenEndpointURL)
          .then(response => response.json())
          .then(({ token }) => token)
      ]);

      // Initialize Direct Line connection
      directLine = WebChat.createDirectLine({ domain: new URL('v3/directline', directLineURL), token });

      // Start conversation when connected
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

      // Render Web Chat in the chat window
      WebChat.renderWebChat({ directLine, locale, styleOptions }, document.getElementById('webchat'));
    })();

    // Function to send a message to the bot
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
