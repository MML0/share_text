chrome.runtime.onInstalled.addListener(() => {
  chrome.contextMenus.create({
    id: "sendText",
    title: "MML-share",
    contexts: ["selection"],

  });
});



chrome.contextMenus.onClicked.addListener((info, tab) => {
  if (info.menuItemId === "sendText") {
    const selectedText = info.selectionText;

    // Prepare form data
    const formData = new URLSearchParams();
    formData.append("content", selectedText);

    // Send POST request
    fetch("http://mml-dev.ir/t/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "User-Agent": navigator.userAgent,
        "Origin": "http://mml-dev.ir",
        "Referer": "http://mml-dev.ir/t/index.php"
      },
      body: formData.toString()
    })
      .then(res => res.text())
      .then(html => {
        console.log("✅ Sent successfully");
        // Optional: show notification in browser
        chrome.scripting.executeScript({
          target: { tabId: tab.id },
          func: () => console.log("Text sent to server!")
        });
      })
      .catch(err => console.error("❌ Error sending:", err));
  }
});
