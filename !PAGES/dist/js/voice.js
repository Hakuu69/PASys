// =======================
// Azure Speech TTS Setup
// =======================

// Add your Azure Speech credentials here
const speechKey = "1MvxDWJFFC9ZVkGCtU0yxuxT1nINUCQDF9yvx8MATu2Yycr3Ji8KJQQJ99BFACqBBLyXJ3w3AAAYACOGKZgg";
const serviceRegion = "southeastasia"; // e.g., "southeastasia"

// Minimal Azure voice list (expand as needed)
const azureVoices = [
  { name: "en-US-GuyNeural", lang: "en-US", display: "Guy (English US)" },
  { name: "en-US-JennyNeural", lang: "en-US", display: "Jenny (English US)" },
  { name: "en-US-AriaNeural", lang: "en-US", display: "Aria (English US)" },
  { name: "en-US-AmberNeural", lang: "en-US", display: "Amber (English US)" },
  { name: "en-US-AnaNeural", lang: "en-US", display: "Ana (English US)" },
  { name: "en-US-AshleyNeural", lang: "en-US", display: "Ashley (English US)" },
  { name: "en-US-BrandonNeural", lang: "en-US", display: "Brandon (English US)" },
  { name: "en-US-ChristopherNeural", lang: "en-US", display: "Christopher (English US)" },
  { name: "en-US-CoraNeural", lang: "en-US", display: "Cora (English US)" },
  { name: "en-US-DavisNeural", lang: "en-US", display: "Davis (English US)" },
  { name: "en-US-ElizabethNeural", lang: "en-US", display: "Elizabeth (English US)" },
  { name: "en-US-EricNeural", lang: "en-US", display: "Eric (English US)" },
  { name: "en-US-JacobNeural", lang: "en-US", display: "Jacob (English US)" },
  { name: "en-US-MichelleNeural", lang: "en-US", display: "Michelle (English US)" },
  { name: "en-US-MonicaNeural", lang: "en-US", display: "Monica (English US)" },
  { name: "en-US-RogerNeural", lang: "en-US", display: "Roger (English US)" },
  { name: "en-US-SteffanNeural", lang: "en-US", display: "Steffan (English US)" },
  { name: "fil-PH-AngeloNeural", lang: "fil-PH", display: "Angelo (Filipino PH)" },
  { name: "fil-PH-BlessicaNeural", lang: "fil-PH", display: "Blessica (Filipino PH)" },
  // Add more voices as needed
];

// Populate the voice dropdown based on selected language
function populateVoices() {
  const voiceSelect = document.getElementById('voice-select');
  const languageSelect = document.getElementById('language-select');
  const selectedLanguage = languageSelect.value;

  if (!voiceSelect) {
    console.error("Voice select element not found.");
    return;
  }

  voiceSelect.innerHTML = ''; // Clear previous options

  // Filter Azure voices by language
  let filteredVoices;
  if (selectedLanguage === 'fil-PH' || selectedLanguage === 'ilo-PH') {
    filteredVoices = azureVoices.filter(voice =>
      voice.lang.toLowerCase().startsWith('fil') ||
      voice.lang.toLowerCase().startsWith('tl')
    );
  } else {
    filteredVoices = azureVoices.filter(voice =>
      voice.lang.toLowerCase().startsWith(selectedLanguage.toLowerCase())
    );
  }

  if (filteredVoices.length === 0) {
    voiceSelect.innerHTML = '<option value="">No Azure voices available for selected language</option>';
    return;
  }

  filteredVoices.forEach(voice => {
    const option = document.createElement('option');
    option.value = voice.name;
    option.textContent = voice.display + ` (${voice.lang})`;
    voiceSelect.appendChild(option);
  });
}

// Speak the announcement message using Azure Speech
function speakMessage() {
  const announcementText = document.getElementById('announcement-text').value;
  if (!announcementText.trim()) {
    console.warn("Announcement text is empty.");
    return;
  }

  const voiceSelect = document.getElementById('voice-select');
  const selectedVoiceName = voiceSelect.value || "en-US-JennyNeural";

  if (!window.SpeechSDK) {
    alert("Azure Speech SDK not loaded.");
    return;
  }

  const speechConfig = SpeechSDK.SpeechConfig.fromSubscription(speechKey, serviceRegion);
  speechConfig.speechSynthesisVoiceName = selectedVoiceName;
  const audioConfig = SpeechSDK.AudioConfig.fromDefaultSpeakerOutput();
  const synthesizer = new SpeechSDK.SpeechSynthesizer(speechConfig, audioConfig);

  synthesizer.speakTextAsync(
    announcementText,
    result => synthesizer.close(),
    error => {
      console.error(error);
      synthesizer.close();
    }
  );
}

document.addEventListener('DOMContentLoaded', function() {
  const announcementForm = document.getElementById('announcementForm');

  if (announcementForm) {
    announcementForm.addEventListener('submit', function(e) {
      if (document.getElementById('announce-later') && document.getElementById('announce-later').checked) {
        const announceAtInput = document.getElementById('announce_at');

        if (announceAtInput && announceAtInput.value) {
          const selectedDate = new Date(announceAtInput.value);
          const now = new Date();

          // Adjust JavaScript time to match server timezone
          now.setMinutes(now.getMinutes() + now.getTimezoneOffset());
          now.setHours(now.getHours() + 8); // Manila Timezone (UTC+8)

          const fiveMinutesLater = new Date(now.getTime() + 5 * 60000);

          if (selectedDate < fiveMinutesLater) {
            e.preventDefault();
            alert("Please select a time at least 5 minutes ahead.");
          }
        } else {
          e.preventDefault();
          alert("Please select a date and time for the announcement.");
        }
      }
    });
  }

  populateVoices();
  document.getElementById('language-select').addEventListener('change', populateVoices);
  document.getElementById('speak-button').addEventListener('click', speakMessage);
});