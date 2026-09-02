# SkyPort Airline Booking System - Vercel Deployment & Docker Guide

This guide explains how to run locally using Docker and deploy the SkyPort PHP application to **Vercel**.

---

## 📁 Final Project Structure

```
airport/project/
├── .env.example                # Environment variables template
├── .gitignore                  # Git ignore rules (node_modules, etc.)
├── Dockerfile.vercel           # PHP 8.2 + Apache Docker image configuration
├── docker-compose.yml          # Local Docker + MySQL multi-container setup
├── vercel.json                 # Vercel deployment & routing configuration
├── README_VERCEL.md            # Deployment & execution documentation
├── config.php                  # Database, SMTP & SMS configuration (env bound)
├── auth.php                    # User authentication & session management
├── index.php                   # Homepage & search portal
├── flight.php                  # Flight search & results page
├── detail.php                  # Passenger details & seat selection
├── payment.php                 # Payment gateway & OTP verification
├── confirmation.php            # E-Ticket & confirmation page
├── boardingpass.php            # Boarding pass generator & download
├── mybooking.php               # Passenger bookings management
├── webcheckin.php              # Web Check-In portal
├── news.php                    # Aviation news portal
├── admin/                      # Admin panel (flights, bookings, settings, users)
├── include/
│   ├── db_json.php             # JSON database helper with /tmp fallback
│   ├── header.php              # Global navigation header
│   ├── footer.php              # Global page footer
│   └── pdf_generator.php       # PDF E-Ticket & Pass generator
├── data/                       # Initial JSON dataset (flights, bookings, users)
└── screenshots/                # Application preview screenshots
```

---

## 🛠️ Required Vercel Environment Variables

In your **Vercel Dashboard** -> **Project Settings** -> **Environment Variables**, add the following:

| Variable Name | Example Value | Description |
|---|---|---|
| `DB_HOST` | `aws.connect.psdb.cloud` | Remote MySQL Host (PlanetScale/Railway/Supabase) |
| `DB_USER` | `root` | Database Username |
| `DB_PASS` | `your_db_password` | Database Password |
| `DB_NAME` | `airport` | Database Name |
| `SMTP_HOST` | `smtp.gmail.com` | SMTP Server Host |
| `SMTP_PORT` | `587` | SMTP Server Port (587 TLS / 465 SSL) |
| `SMTP_USER` | `your_email@gmail.com` | Sender Gmail address |
| `SMTP_PASS` | `abcd efgh ijkl mnop` | Google 16-digit App Password |
| `SMTP_FROM_EMAIL` | `no-reply@skyport.com` | From email header |
| `SMTP_FROM_NAME` | `SkyPort Airlines` | From display name |
| `SMS_API_KEY` | `your_fast2sms_key` | Fast2SMS / 2Factor SMS API Key |

---

## 🐳 Local Docker Testing Commands

To test the application locally using Docker before deploying:

### Option 1: Run with Docker Compose (Recommended)
```bash
docker-compose up --build
```
Open your browser at: **`http://localhost:8080`**

### Option 2: Build & Run Single Container
```bash
# Build the Docker image
docker build -f Dockerfile.vercel -t skyport-app .

# Run the container on port 8080
docker run -d -p 8080:80 --name skyport skyport-app
```
Open your browser at: **`http://localhost:8080`**

---

## 🚀 Vercel Deployment Steps

### Option A: Deploy via GitHub (Recommended)
1. Commit and push changes to your GitHub repository:
   ```bash
   git add .
   git commit -m "Configure project for Vercel deployment"
   git push origin new_develop
   ```
2. Go to [Vercel Dashboard](https://vercel.com/dashboard).
3. Click **Add New** -> **Project**.
4. Import `manthan123-coder/skyport-airline-ticket-booking-system`.
5. Under **Environment Variables**, add your keys (see table above).
6. Click **Deploy**.

### Option B: Deploy via Vercel CLI
1. Install Vercel CLI:
   ```bash
   npm i -g vercel
   ```
2. Run deployment command in the project directory:
   ```bash
   vercel
   ```
3. For production deployment:
   ```bash
   vercel --prod
   ```

---

## ✅ Deployment Checklist

- [x] **Filesystem Fallback**: `include/db_json.php` and `auth.php` automatically handle Vercel's read-only serverless filesystem by using `/tmp/skyport_data/` and `$_SESSION`.
- [x] **Environment Variables**: `config.php` dynamically loads database credentials, SMTP settings, and SMS keys via `getenv()`.
- [x] **Vercel Routing**: `vercel.json` maps static assets (CSS, JS, images, PDFs) and PHP routes cleanly.
- [x] **Local Docker Setup**: `Dockerfile.vercel` and `docker-compose.yml` configured for container testing.
- [x] **Zero UI/UX Alteration**: 100% of website pages, Bootstrap styling, JS features, and admin workflows remain identical.
