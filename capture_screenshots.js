const fs = require('fs');
const path = require('path');

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outputDir = path.join(__dirname, 'screenshots');

if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

const flowScreenshots = [
    { name: '1_login_real.jpg', url: 'http://localhost/airport/project/login.php', label: 'Step 1: User Login' },
    { name: '2_home_search_real.jpg', url: 'http://localhost/airport/project/index.php', label: 'Step 2: Flight Search Box' },
    { name: '3_flight_results_real.jpg', url: 'http://localhost/airport/project/flight.php?from_city=Delhi&to_city=Mumbai&departure_date=2026-09-15', label: 'Step 3: Flight Fleet Results' },
    { name: '4_detail_checkout_real.jpg', url: 'http://localhost/airport/project/detail.php?id=90001', label: 'Step 4: Passenger Checkout' },
    { name: '5_payment_otp_real.jpg', url: 'http://localhost/airport/project/payment.php?id=90001', label: 'Step 5: Payment OTP Verification' },
    { name: '6_confirmation_real.jpg', url: 'http://localhost/airport/project/confirmation.php?pnr=SKP982', label: 'Step 6: Booking Confirmation & E-Ticket' },
    { name: '7_webcheckin_real.jpg', url: 'http://localhost/airport/project/webcheckin.php', label: 'Step 7: Web Check-In & Gate QR Pass' },
    { name: '8_mybooking_real.jpg', url: 'http://localhost/airport/project/mybooking.php', label: 'Step 8: My Trips Dashboard' },
    { name: '9_admin_real.jpg', url: 'http://localhost/airport/project/admin/index.php', label: 'Step 9: Super-Admin Control Panel' }
];

(async () => {
    try {
        const puppeteer = await import('puppeteer-core');
        console.log('Launching Chrome for REAL HIGH-RES JPEG SCREENSHOTS...');
        const browser = await puppeteer.launch({
            executablePath: chromePath,
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 850, deviceScaleFactor: 1 });

        for (const item of flowScreenshots) {
            console.log(`Capturing ${item.name} (${item.label})...`);
            try {
                await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
                const filePath = path.join(outputDir, item.name);
                await page.screenshot({ path: filePath, type: 'jpeg', quality: 95, fullPage: false });
                console.log(`Saved ${item.name} (${fs.statSync(filePath).size} bytes)`);
            } catch (err) {
                console.error(`Failed to capture ${item.name}:`, err.message);
            }
        }

        await browser.close();
        console.log('ALL REAL JPEG SCREENSHOTS CAPTURED SUCCESSFULLY!');
    } catch (e) {
        console.error('Screenshot script error:', e);
    }
})();
