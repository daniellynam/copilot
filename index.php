<!DOCTYPE html>
<html lang="en">
<head>
 <title>Care Agent Workbench</title>
 <style>
 html, body {
 height: 100%;
 margin: 0;
 font-family: "Segoe UI", Arial, sans-serif;
 background-color: #f3f2f1;
 }

.copilot-button-modern {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 14px 20px;
  background: linear-gradient(145deg, #ffffff, #f3f3f3);
  border: 1px solid #d0d0d0;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  color: #1b1a19;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  cursor: pointer;
  transition: all 0.25s ease;
  width: 100%;
  text-align: left;
}

.copilot-button-modern:hover {
  background: linear-gradient(145deg, #e6f0fa, #ffffff);
  border-color: #0078d4;
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
  color: #005a9e;
}

.copilot-button-modern .icon {
  font-size: 1.25rem;
}

.copilot-button-modern .label {
  flex: 1;
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
 .section h3 {
 font-size: 1.0rem;
 color: #201f1e;
 margin-bottom: 1rem;
 }

 .prompt-buttons {
 display: flex;
 gap: 1rem;
 }

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

<style>
  .chart-container {
    position: relative;
    width: 300px;
    height: 150px;
    margin-top: 1rem;
  }

  .center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -30%);
    font-size: 24px;
    font-weight: bold;
    color: #333;
  }
</style>
</head>
<body>
 <header>Welcome Daniel</header>
 <div class="workbench">
 <div class="main-content">
 <div class="section prompts">
 <h2>Top Prompts</h2>

<div class="prompt-buttons">
  <button class="copilot-button-modern" onclick="sendMessage('Identify the top 10 residents with the highest clinical risks')">
    <span class="icon">🧠</span>
    <span class="label">Identify Top 10 Clinical Risk Residents</span>
  </button>
  <button class="copilot-button-modern" onclick="sendMessage('List the residents with pending appointments')">
    <span class="icon">📅</span>
    <span class="label">View Pending Appointments</span>
  </button>
  <button class="copilot-button-modern" onclick="sendMessage('Conduct compliance checks against progress notes')">
    <span class="icon">✅</span>
    <span class="label">Compliance Check - Progress Notes</span>
  </button>
</div>
 </div>

 <div class="section pending-actions">
   <h2>AI Recommended Priorities</h2>
   <h3>Please review these 10 residents</h3>
   <table id="clinicalRiskTable">
     <thead>
       <tr>
         <th>Resident</th>
         <th>UID</th>
         <th>Risk Level</th>
         <th>Observations</th>
         <th>Action Required</th>
         <th>Notes</th>
       </tr>
     </thead>
     <tbody>
       <!-- Rows will be inserted here -->
     </tbody>
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

directLine.activity$.subscribe(activity => {
  console.log("🔔 Activity received:", activity);

  if (activity.type === 'message') {
    const sender = activity.from?.role || 'unknown';
    const text = activity.text || '[No text content]';

    console.log(`💬 Message from ${sender}:`, text);

    // Only parse if it's from the bot and contains expected content
    if (sender === 'bot' && text.includes('Risk Level')) {
      const residents = parseClinicalRiskData(text);
      console.log("📊 Parsed residents:", residents);
      updateClinicalRiskTable(residents);
    }
  }
});

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

// 👇 Parse structured clinical risk text
function parseClinicalRiskData(text) {
  const entries = text.split(/\n\d+\.\s+\*\*/); // Split by numbered entries like "1. **"
  const residents = [];

  entries.forEach(entry => {
    // Re-add the "**" prefix that was removed during split
    entry = "**" + entry.trim();

    const nameMatch = entry.match(/\*\*(.*?)\s+\(UID\s*(.*?)\)\*\*/);
    const riskMatch = entry.match(/- Risk Level:\s*(.*)/);
    const obsMatch = entry.match(/- Observations:\s*(.*)/);
    const actionMatch = entry.match(/- Action Required:\s*(.*)/);
    const notesMatch = entry.match(/- Notes:\s*(.*)/);

    if (nameMatch) {
      // Remove any leading numbering like "1. " from the name
      const rawName = nameMatch[1].trim();
      const cleanedName = rawName
        .replace(/^\d+\.\s*/, '')  // Remove "1. " if present
        .replace(/\*\*/g, '')      // Remove all "**"

      residents.push({
        // name: nameMatch[1].trim(),
        name: cleanedName,
        uid: nameMatch[2].trim(),
        risk: riskMatch?.[1]?.trim() || '',
        observations: obsMatch?.[1]?.trim() || '',
        action: actionMatch?.[1]?.trim() || '',
        notes: notesMatch?.[1]?.trim() || ''
      });
    }
  });

  return residents;
}


// 👇 Update the table with parsed data
function updateClinicalRiskTable(residents) {
  const tableBody = document.querySelector('#clinicalRiskTable tbody');
  if (!tableBody) return;

  tableBody.innerHTML = ''; // Clear existing rows

  residents.forEach(resident => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${resident.name}</td>
      <td>${resident.uid}</td>
      <td>${resident.risk}</td>
      <td>${resident.observations}</td>
      <td>${resident.action}</td>
      <td>${resident.notes}</td>
    `;
    tableBody.appendChild(row);
  });
}
</script>


 <script src="https://cdn.jsdelivr.net/npm/chart.js"></rt configuration script -->
 <script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('halfPieChart').getContext('2d');

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Compliance', 'Remaining'],
        datasets: [{
          data: [95, 5],
          backgroundColor: ['#4CAF50', '#e0e0e0'],
          borderWidth: 0
        }]
      },
      options: {
        rotation: Math.PI,
        circumference: Math.PI,
        cutout: '70%',
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false }
        }
      }
    });
  });
</script>

</body>
</html>
