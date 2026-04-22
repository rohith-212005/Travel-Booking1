document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);

    const fromLocation =
        urlParams.get('from') ||
        localStorage.getItem("fromLocation") ||
        "N/A";

    const toLocation =
        urlParams.get('to') ||
        localStorage.getItem("toLocation") ||
        "N/A";

    const travelDate =
        urlParams.get('date') ||
        localStorage.getItem("travelDate") ||
        "N/A";

    document.getElementById('from-location').textContent = fromLocation;
    document.getElementById('to-location').textContent = toLocation;
    document.getElementById('travel-date').textContent = travelDate;

    
    const ticketData = {
        passengerName: urlParams.get('name') || "Unknown Passenger",
        ticketName: urlParams.get('ticketName') || "Unknown Ticket",
        ticketPrice: urlParams.get('price') || "N/A",
        seatNumber: urlParams.get('seat') || "N/A",
        ticketNumber: urlParams.get('ticket') || "N/A"
    };

    document.getElementById('passenger-name').textContent = ticketData.passengerName;
    document.getElementById('ticket-name').textContent = ticketData.ticketName;
    document.getElementById('ticket-price').textContent = ticketData.ticketPrice;
    document.getElementById('seat-number').textContent = ticketData.seatNumber;
    document.getElementById('ticket-number').textContent = ticketData.ticketNumber;

    // Generate QR Code
    const qrText = `Name: ${ticketData.passengerName}, From: ${fromLocation}, To: ${toLocation}, Date: ${travelDate}, Ticket: ${ticketData.ticketNumber}, Type: ${ticketData.ticketName}, Price: ${ticketData.ticketPrice}, Seat: ${ticketData.seatNumber}`;
    
    try {
        const qrcodeContainer = document.getElementById('qrcode');
        qrcodeContainer.innerHTML = ""; // clear if anything inside
        new QRCode(qrcodeContainer, {
            text: qrText,
            width: 150,
            height: 150,
            colorDark: '#2c3e50',
            colorLight: '#ffffff'
        });
        console.log("QR Code generated.");
    } catch (error) {
        console.error("QR Code generation failed:", error);
    }
});
