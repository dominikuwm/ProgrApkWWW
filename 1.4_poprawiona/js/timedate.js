function showtime() {
    let now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();
    let day = now.getDate();
    let month = now.getMonth() + 1;  // Miesiące zaczynają się od 0
    let year = now.getFullYear();
    
    let timeValue = ((hours > 12) ? hours - 12 : hours) || 12;
    timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
    timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
    timeValue += (hours >= 12) ? " PM" : " AM";
    
    let dateValue = `${day}/${month}/${year}`;
    let zegarekElement = document.getElementById("zegarek");

    if (zegarekElement) {
        zegarekElement.innerHTML = `${dateValue} ${timeValue}`;
        setTimeout(showtime, 1000);
    }
}
