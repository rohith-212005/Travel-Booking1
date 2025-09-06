document.addEventListener('DOMContentLoaded', () => {
    // Get stored search details
    const fromLocation = localStorage.getItem("fromLocation") || "N/A";
    const toLocation = localStorage.getItem("toLocation") || "N/A";
    const travelDate = localStorage.getItem("travelDate") || "N/A";

    // Set values in ticket pag
    document.getElementById('from-location').textContent = fromLocation;
    document.getElementById('to-location').textContent = toLocation;
    document.getElementById('travel-date').textContent = travelDate;

    const urlParams = new URLSearchParams(window.location.search);
    const ticketData = {
        passengerName: urlParams.get('name') || "Unknown Passenger",
        ticketName: urlParams.get('ticketName') || "Unknown Ticket",
        ticketPrice: urlParams.get('price') || "N/A",

        seatNumber: urlParams.get('seat') || "N/A",
        ticketNumber: urlParams.get('ticket') || "N/A"
    };

    // Update ticket details on the page
    document.getElementById('passenger-name').textContent = ticketData.passengerName;
    document.getElementById('ticket-name').textContent = ticketData.ticketName;
    document.getElementById('ticket-price').textContent = ticketData.ticketPrice;

    document.getElementById('seat-number').textContent = ticketData.seatNumber;
    document.getElementById('ticket-number').textContent = ticketData.ticketNumber;

    // Generate QR code
    const qrText = `Name: ${ticketData.passengerName},From: ${fromLocation}, To: ${toLocation}, Date: ${travelDate},Ticket: ${ticketData.ticketNumber}, Type: ${ticketData.ticketName}, Price: ${ticketData.ticketPrice}, Seat: ${ticketData.seatNumber}`;
    new QRCode(document.getElementById('qrcode'), {
        text: qrText,
        width: 150,
        height: 150,
        colorDark: '#2c3e50',
        colorLight: '#ffffff'
    });
});
// ticket