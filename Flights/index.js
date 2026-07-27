// ==============================
// CITY DROPDOWN
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

            item.style.display =
                item.innerText.toUpperCase().includes(value)
                    ? ""
                    : "none";

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


//passengers and class =======================
// ================= PASSENGER =================
// ==============================
const passengerBtn = document.getElementById("passengerDropdownBtn");

const passengerMenu = document.getElementById("passengerMenu");

passengerBtn.addEventListener("click", function (e) {

    e.preventDefault();
    e.stopPropagation();

    if (passengerMenu.style.display === "block") {
        passengerMenu.style.display = "none";
    }
    else {
        passengerMenu.style.display = "block";
    }

});


passengerMenu.addEventListener("click", function (e) {

    e.stopPropagation();

});

document.addEventListener("click", function (e) {

    if (!document.getElementById("passengerBox").contains(e.target)) {

        passengerMenu.style.display = "none";

    }

});

function changeCount(type, change) {

    const el = document.getElementById(type);

    let count = parseInt(el.innerText);

    if (type === "adult") {

        count = Math.max(1, count + change);

    } else {

        count = Math.max(0, count + change);

    }

    el.innerText = count;

}

function updateSummary() {

    let adults = parseInt(document.getElementById("adult").innerText);

    let child = parseInt(document.getElementById("child").innerText);

    let cls = document.getElementById("travelClass").value;

    let txt = adults + (adults > 1 ? " Adults" : " Adult");

    if (child > 0) {

        txt += ", " + child + (child > 1 ? " Children" : " Child");

    }

    txt += ", " + cls;

    document.getElementById("summary").innerText = txt;

}

document.getElementById("donePassenger").onclick = function () {

    updateSummary();

    passengerMenu.style.display = "none";

    this.onclick

};
// ==============================
// DEALS CAROUSEL
// ==============================

const deals = document.getElementById("dealsCarousel");

if (deals) {

    const carousel = new bootstrap.Carousel(deals);

    document.getElementById("prevDeal")?.addEventListener("click", () => {

        carousel.prev();

    });

    document.getElementById("nextDeal")?.addEventListener("click", () => {

        carousel.next();

    });

}
// ===============================================

// ==============================
// DEALS CAROUSEL 2
// ==============================

const deals2 = document.getElementById("dealsCarousel2");

if (deals2) {

    const carousel2 = new bootstrap.Carousel(deals2);

    document.getElementById("prevDeal2")?.addEventListener("click", () => {

        carousel2.prev();

    });

    document.getElementById("nextDeal2")?.addEventListener("click", () => {

        carousel2.next();

    });

}

// =============================== guest/room section>

// ==================
// ==============================
// DATE PICKER
// ==============================

const departureDate = flatpickr("#departureDate", {

    dateFormat: "d-m-Y",

    minDate: "today",

    disableMobile: true,

    allowInput: false,

    clickOpens: true,

    position: "above",

    onChange: function (selectedDates) {

        if (selectedDates.length > 0) {

            returnDate.set("minDate", selectedDates[0]);

            returnDate.clear();

        }

    }

});

const returnDate = flatpickr("#returnDate", {

    dateFormat: "d-m-Y",

    minDate: "today",

    disableMobile: true,

    allowInput: false,

    clickOpens: true,

    position: "above"

});


// round or oneway======================.......................
// ==============================
// ONE WAY / ROUND TRIP
// ==============================

const oneWay = document.getElementById("oneway");

const roundTrip = document.getElementById("roundtrip");

const returnBox = document.getElementById("returnBox");

function toggleReturnDate() {

    if (oneWay.checked) {

        returnBox.style.display = "none";

    } else {

        returnBox.style.display = "block";

    }

}

oneWay.addEventListener("change", toggleReturnDate);

roundTrip.addEventListener("change", toggleReturnDate);

toggleReturnDate();
