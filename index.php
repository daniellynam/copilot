
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

      #container {
        display: flex;
        flex-direction: column;
        height: 100%;
      }

      #top-section {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        background-color: #f8f9fa;
      }

      #prompts, #pending-actions {
        width: 48%;
      }

      #prompts h2, #pending-actions h2 {
        margin-top: 0;
      }

      #prompts ul {
        list-style-type: none;
        padding: 0;
      }

      #prompts li {
        background-color: #007bff;
        color: white;
        padding: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        border-radius: 5px;
        text-align: center;
      }

      #prompts li:hover {
        background-color: #0056b3;
      }

      #pending-actions table {
        width: 100%;
        border-collapse: collapse;
      }

      #pending-actions th, #pending-actions td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
      }

      #pending-actions th {
        background-color: #e9ecef;
      }

      #chat-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-color: #f1f1f1;
      }

      #chat-window {
        width: 60%;
        height: 500px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        overflow: hidden;
      }

      .webchat__bubble__content {
        background-color: transparent !important;
      }
    </style>
  </head>
  <body>
    <div id="container">
      <div id="top-section">
        <div id="prompts">
          <h2>Prompts</h2>
          <ul>
            <li onclick="sendPrompt('Placeholder 1')">Placeholder 1</li>
            <li onclick="sendPrompt('Placeholder 2')">Placeholder 2</li>
            <li onclick="sendPrompt('Placeholder 3')">Placeholder 3</li>
            <li onclick="sendPrompt('Placeholder 4')">Placeholder 4</li>
          </ul>
        </div>
        <div id="pending-actions">
          <h2>Pending Actions</h2>
          <table>
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
      <div id="chat-container">
        <div id="chat-window">
          <iframe src="https://copilotstudio.microsoft.com/environments/748bab4f-a737-e24a-a461-e28516a5054a/bots/cr4b6_parliamentarySenateEstimatesAssistant/webchat?__version__=2" frameborder="0" style="width: 100%; height: 100%;"></iframe>
        </div>
      </div>
    </div>

    <script>
      function sendPrompt(prompt) {
        const iframe = document.querySelector('#chat-window iframe');
        const message = {
          type: 'message',
          text: prompt
        };
        iframe.contentWindow.postMessage(message, '*');
      }
    </script>
  </body>
</html>
