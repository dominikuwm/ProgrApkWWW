function getTheDate() {
    let Todays = new Date();
    let TheDate = (Todays.getMonth() + 1) + "/" + Todays.getDate() + "/" + Todays.getFullYear(); // Use getFullYear()
    document.getElementById("data").innerHTML = TheDate;
}

var timerID = null;
var timerRunning = false;

function stopclock() {
    if (timerRunning) {
        clearTimeout(timerID);
        timerRunning = false;
    }
}

function startclock() {
    stopclock();
    getTheDate();
    showtime();
}

function showtime() {
    let now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();
    let timeValue = "" + ((hours > 12) ? hours - 12 : hours);
    timeValue = (timeValue == "0") ? 12 : timeValue; // Handle midnight and noon properly
    timeValue += (minutes < 10 ? ":0" : ":") + minutes;
    timeValue += (seconds < 10 ? ":0" : ":") + seconds;
    timeValue += (hours >= 12) ? " P.M." : " A.M.";
    
    document.getElementById("zegarek").innerHTML = timeValue; // Ensure the correct element ID
    timerID = setTimeout(showtime, 1000);
    timerRunning = true;
}
