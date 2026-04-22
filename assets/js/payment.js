// payment.js (optional, updated)
document.addEventListener('DOMContentLoaded', () => {
    // Get the ticket details from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const ticketName = urlParams.get('ticketName');
    const ticketPrice = urlParams.get('ticketPrice');

    // Set the ticket details on the payment page
    if (ticketName && ticketPrice) {
        document.getElementById('ticket-name').textContent = ticketName;
        document.getElementById('ticket-price').textContent = ticketPrice;
    }

    // Populate hidden fields in the form
    if (ticketName) document.getElementById('ticketName').value = ticketName;
    if (ticketPrice) document.getElementById('ticketPrice').value = ticketPrice;
});

let captchaCode = "";

function generateCaptcha() {
  const canvas = document.getElementById("captchaCanvas");
  const ctx = canvas.getContext("2d");

  // Generate random characters
  captchaCode = Math.random().toString(36).substring(2, 8).toUpperCase();

  // Draw on canvas
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.font = "24px Arial";
  ctx.fillStyle = "#000";
  ctx.fillText(captchaCode, 10, 30);
}

function validateCaptcha() {
  const userInput = document.getElementById("captchaInput").value.toUpperCase();
  const result = document.getElementById("captchaResult");

  if (userInput === captchaCode) {
    result.textContent = "✔ CAPTCHA matched!";
    result.style.color = "green";
  } else {
    result.textContent = "✖ Incorrect CAPTCHA!";
    result.style.color = "red";
  }

  // Refresh CAPTCHA
  generateCaptcha();
  document.getElementById("captchaInput").value = "";
}

// Generate on page load
window.onload = generateCaptcha;
