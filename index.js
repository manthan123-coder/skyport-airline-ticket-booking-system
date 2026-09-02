// ==============================
// SWAP ORIGIN & DESTINATION CITIES
// ==============================
function swapOriginDestination() {
    const fromEl = document.getElementById('fromCitySelect') || document.querySelector('select[name="from_city"]');
    const toEl = document.getElementById('toCitySelect') || document.querySelector('select[name="to_city"]');
    if (fromEl && toEl) {
        const temp = fromEl.value;
        fromEl.value = toEl.value;
        toEl.value = temp;
        if (typeof window.syncCityOptions === 'function') {
            window.syncCityOptions();
        }
    }
}

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

// ==============================
// MANDATORY FIELD & DYNAMIC SAME CITY DROPDOWN HIDING
// ==============================
document.addEventListener("DOMContentLoaded", function () {
    const flightSearchForm = document.getElementById("flightSearchForm");
    const depDateInput = document.getElementById("departureDate");
    const retDateInput = document.getElementById("returnDate");

    const allFromSelects = document.querySelectorAll('select[name="from_city"]');
    const allToSelects = document.querySelectorAll('select[name="to_city"]');

    allFromSelects.forEach((fSel, idx) => {
        const tSel = allToSelects[idx] || allToSelects[0];
        if (!fSel || !tSel) return;

        // Store master list of original options
        const fromMaster = Array.from(fSel.options).map(o => ({ value: o.value, text: o.text }));
        const toMaster = Array.from(tSel.options).map(o => ({ value: o.value, text: o.text }));

        function syncCityOptions() {
            const selectedFrom = (fSel.value || "").trim();
            const selectedTo = (tSel.value || "").trim();

            // 1. Rebuild Destination (To) dropdown, removing selected From city from DOM
            const currentToVal = tSel.value;
            tSel.innerHTML = "";
            toMaster.forEach(opt => {
                if (opt.value && selectedFrom !== "" && opt.value.trim().toLowerCase() === selectedFrom.toLowerCase()) {
                    return; // Omit option from combobox
                }
                const newOpt = document.createElement("option");
                newOpt.value = opt.value;
                newOpt.textContent = opt.text;
                if (opt.value === currentToVal && opt.value.trim().toLowerCase() !== selectedFrom.toLowerCase()) {
                    newOpt.selected = true;
                }
                tSel.appendChild(newOpt);
            });

            // 2. Rebuild Origin (From) dropdown, removing selected To city from DOM
            const currentFromVal = fSel.value;
            fSel.innerHTML = "";
            fromMaster.forEach(opt => {
                if (opt.value && selectedTo !== "" && opt.value.trim().toLowerCase() === selectedTo.toLowerCase()) {
                    return; // Omit option from combobox
                }
                const newOpt = document.createElement("option");
                newOpt.value = opt.value;
                newOpt.textContent = opt.text;
                if (opt.value === currentFromVal && opt.value.trim().toLowerCase() !== selectedTo.toLowerCase()) {
                    newOpt.selected = true;
                }
                fSel.appendChild(newOpt);
            });
        }

        // Expose function globally for swapOriginDestination
        window.syncCityOptions = syncCityOptions;

        fSel.addEventListener("change", syncCityOptions);
        tSel.addEventListener("change", syncCityOptions);

        // Run initial sync on page load
        syncCityOptions();
    });

    if (flightSearchForm) {
        flightSearchForm.addEventListener("submit", function (e) {
            const currentFrom = document.getElementById("fromCitySelect") || document.querySelector('select[name="from_city"]');
            const currentTo = document.getElementById("toCitySelect") || document.querySelector('select[name="to_city"]');

            if (!currentFrom || !currentFrom.value.trim()) {
                e.preventDefault();
                alert("⚠️ Please select an Origin City (From City).");
                if (currentFrom) currentFrom.focus();
                return false;
            }

            if (!currentTo || !currentTo.value.trim()) {
                e.preventDefault();
                alert("⚠️ Please select a Destination City (To City).");
                if (currentTo) currentTo.focus();
                return false;
            }

            if (!depDateInput || !depDateInput.value.trim()) {
                e.preventDefault();
                alert("⚠️ Please select a Departure Date.");
                if (depDateInput) depDateInput.focus();
                return false;
            }

            if (roundTripRadio && roundTripRadio.checked && (!retDateInput || !retDateInput.value.trim())) {
                e.preventDefault();
                alert("⚠️ Please select a Return Date for Round Trip.");
                if (retDateInput) retDateInput.focus();
                return false;
            }

            if (currentFrom.value.trim().toLowerCase() === currentTo.value.trim().toLowerCase()) {
                e.preventDefault();
                alert("⚠️ Origin and Destination cities cannot be the same. Please select a different destination.");
                return false;
            }
        });
    }
});

// ==============================
// SCROLL REVEAL OBSERVER (SMOOTH FADE-IN)
// ==============================
document.addEventListener("DOMContentLoaded", function () {
    const revealTargets = document.querySelectorAll(".card, .news-card, .flight-card-v2, article");
    if (revealTargets.length > 0 && "IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        revealTargets.forEach(el => {
            el.classList.add("reveal-on-scroll");
            observer.observe(el);
        });
    }
});


