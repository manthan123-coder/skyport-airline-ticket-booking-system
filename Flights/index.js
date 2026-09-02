// ==============================
// CITY DROPDOWN & SEARCH
// ==============================
document.querySelectorAll(".custom-dropdown").forEach(dropdown => {
    const input = dropdown.querySelector(".citySearch");
    const list = dropdown.querySelector(".cityList");
    if (!input || !list) return;

    const items = list.querySelectorAll(".item");

    input.addEventListener("click", function (e) {
        e.stopPropagation();
        closeAllDropdowns();
        list.style.display = "block";
    });

    input.addEventListener("keyup", function () {
        const value = input.value.toUpperCase();
        items.forEach(item => {
            item.style.display = item.innerText.toUpperCase().includes(value) ? "" : "none";
        });
    });

    items.forEach(item => {
        item.addEventListener("click", function () {
            input.value = item.innerText;
            list.style.display = "none";
        });
    });
});

function closeAllDropdowns() {
    document.querySelectorAll(".cityList").forEach(list => {
        list.style.display = "none";
    });
}

document.addEventListener("click", function (e) {
    if (!e.target.closest(".custom-dropdown")) {
        closeAllDropdowns();
    }
});

// ==============================
// PASSENGERS AND CLASS DROPDOWN
// ==============================
const passengerBtn = document.getElementById("passengerDropdownBtn");
const passengerMenu = document.getElementById("passengerMenu");

if (passengerBtn && passengerMenu) {
    passengerBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        passengerMenu.style.display = (passengerMenu.style.display === "block") ? "none" : "block";
    });

    passengerMenu.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    document.addEventListener("click", function (e) {
        const pBox = document.getElementById("passengerBox");
        if (pBox && !pBox.contains(e.target)) {
            passengerMenu.style.display = "none";
        }
    });
}

function changeCount(type, change) {
    const el = document.getElementById(type);
    if (!el) return;
    let count = parseInt(el.innerText) || 0;
    if (type === "adult") {
        count = Math.max(1, count + change);
    } else {
        count = Math.max(0, count + change);
    }
    el.innerText = count;
    updateSummary();
}

function updateSummary() {
    const adultEl = document.getElementById("adult");
    const childEl = document.getElementById("child");
    const classEl = document.getElementById("travelClass");
    const summaryEl = document.getElementById("summary");
    const hiddenEl = document.getElementById("passenger_class");

    if (!adultEl || !summaryEl) return;

    let adults = parseInt(adultEl.innerText) || 1;
    let child = parseInt(childEl ? childEl.innerText : "0") || 0;
    let cls = classEl ? classEl.value : "Economy";

    let txt = adults + (adults > 1 ? " Adults" : " Adult");
    if (child > 0) {
        txt += ", " + child + (child > 1 ? " Children" : " Child");
    }
    txt += ", " + cls;

    summaryEl.innerText = txt;
    if (hiddenEl) hiddenEl.value = txt;
}

const doneBtn = document.getElementById("donePassenger");
if (doneBtn) {
    doneBtn.onclick = function (e) {
        if (e) e.preventDefault();
        updateSummary();
        if (passengerMenu) passengerMenu.style.display = "none";
    };
}

// ==============================
// FLATPICKR DATE PICKERS
// ==============================
let returnDatePicker = null;
let departureDatePicker = null;

if (document.getElementById("departureDate")) {
    departureDatePicker = flatpickr("#departureDate", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        minDate: "today",
        disableMobile: true,
        allowInput: false,
        clickOpens: true,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0 && returnDatePicker) {
                returnDatePicker.set("minDate", selectedDates[0]);
            }
        }
    });
}

if (document.getElementById("returnDate")) {
    returnDatePicker = flatpickr("#returnDate", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        minDate: "today",
        disableMobile: true,
        allowInput: false,
        clickOpens: true
    });
}

// ==============================
// ONE WAY / ROUND TRIP TOGGLE
// ==============================
const oneWayRadio = document.getElementById("oneway");
const roundTripRadio = document.getElementById("roundtrip");
const returnBox = document.getElementById("returnBox");

function toggleReturnDate() {
    if (!returnBox) return;
    if (oneWayRadio && oneWayRadio.checked) {
        returnBox.style.display = "none";
        const rInput = document.getElementById("returnDate");
        if (rInput) rInput.value = "";
    } else if (roundTripRadio && roundTripRadio.checked) {
        returnBox.style.display = "block";
    }
}

if (oneWayRadio) oneWayRadio.addEventListener("change", toggleReturnDate);
if (roundTripRadio) roundTripRadio.addEventListener("change", toggleReturnDate);

// Ensure correct display state on initial script load
document.addEventListener("DOMContentLoaded", function () {
    toggleReturnDate();
});
toggleReturnDate();
