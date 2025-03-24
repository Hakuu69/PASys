let availableVoices = [];

// Function to populate the voice dropdown based on the selected language
function populateVoices() {
  const voiceSelect = document.getElementById('voice-select');
  const languageSelect = document.getElementById('language-select');
  const selectedLanguage = languageSelect.value; // Get selected language

  if (!voiceSelect) {
    console.error("Voice select element not found.");
    return;
  }

  availableVoices = window.speechSynthesis.getVoices();
  // console.log("Loaded voices:", availableVoices); CONSOLE LOG FOR VOICES AVAILABILITY

  voiceSelect.innerHTML = ''; // Clear previous options

  // Filter voices based on selected language.
  let filteredVoices;
  if (selectedLanguage === 'fil-PH' || selectedLanguage === 'ilo-PH') {
    // If Iloko is selected, map it to Filipino (`fil-PH` or `tl-PH`)
    filteredVoices = availableVoices.filter(voice =>
      voice.lang.toLowerCase().startsWith('fil') ||
      voice.lang.toLowerCase().startsWith('tl')
    );
  } else {
    filteredVoices = availableVoices.filter(voice =>
      voice.lang.toLowerCase().startsWith(selectedLanguage.toLowerCase())
    );
  }

  console.log("Filtered voices:", filteredVoices);

  // If no voices are found for the selected language, warn the user.
  if (filteredVoices.length === 0) {
    voiceSelect.innerHTML = '<option value="">No voices available for selected language</option>';
    return;
  }

  // Populate the dropdown with matching voices
  filteredVoices.forEach(voice => {
    const option = document.createElement('option');
    option.value = voice.name;
    option.textContent = `${voice.name} (${voice.lang})${voice.default ? ' -- DEFAULT' : ''}`;
    voiceSelect.appendChild(option);
  });
}

// Function to speak the announcement message using the selected voice
function speakMessage() {
  const announcementText = document.getElementById('announcement-text').value;
  if (!announcementText.trim()) {
    console.warn("Announcement text is empty.");
    return;
  }

  const voiceSelect = document.getElementById('voice-select');
  const selectedVoiceName = voiceSelect.value;
  const utterance = new SpeechSynthesisUtterance(announcementText);

  const selectedVoice = availableVoices.find(voice => voice.name === selectedVoiceName);
  if (selectedVoice) {
    utterance.voice = selectedVoice;
  } else {
    console.warn("Selected voice not found, using default.");
  }

  window.speechSynthesis.speak(utterance);
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
                  
                  console.log("Selected Date:", selectedDate.toISOString());
                  console.log("Server Adjusted Time:", now.toISOString());
                  console.log("Minimum Allowed Time:", fiveMinutesLater.toISOString());

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
});

// Check if the browser supports the Web Speech API
if ('speechSynthesis' in window) {
  // When voices are loaded/changed, repopulate the list.
  speechSynthesis.onvoiceschanged = populateVoices;

  // Initial population attempt.
  populateVoices();

  // Repopulate voices when language selection changes.
  document.getElementById('language-select').addEventListener('change', populateVoices);

  // Add event listener for the speak button.
  document.getElementById('speak-button').addEventListener('click', speakMessage);

  // ✅ Minimal Additional Block: Second Attempt After 2 Seconds
  setTimeout(() => {
    console.log("Second attempt to populate voices...");
    populateVoices();
  }, 1);

} else {
  console.error("Web Speech API is not supported in this browser.");
}
