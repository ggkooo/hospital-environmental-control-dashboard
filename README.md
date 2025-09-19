<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Hospital Environmental Control Dashboard

A comprehensive hospital environmental monitoring system built with Laravel, featuring temperature, humidity, and noise monitoring capabilities with a robust API for data collection.

## Features

### Dashboard
- **Temperature Page:** Complete implementation of a temperature monitoring page with dynamic, interactive charts.
- **Humidity & Noise Monitoring:** Comprehensive monitoring of humidity levels and noise data throughout the hospital.
- **Dynamic Graphs:** Visualization of multiple environmental datasets with interactive charts.
- **Internationalization:** Full translation support for all supported languages.
- **Asset Organization:** Dedicated SASS and JavaScript files for all pages, following best practices for maintainability.

### API Integration
- **Secure Data Collection:** RESTful API with API key authentication for secure sensor data collection.
- **Multi-parameter Support:** Simultaneous collection of temperature, humidity, and noise data.
- **Background Processing:** Asynchronous data processing using Laravel Jobs to handle high-volume sensor input.
- **Data Aggregation:** Automatic calculation of minute-by-minute averages for all environmental parameters.

### Data Storage
- **Multi-resolution Storage:** Two-tier storage system for both raw second-level readings and minute-level aggregated data.
- **Optimized Database Schema:** Dedicated tables for each environmental parameter and time resolution.

## API Documentation

### Authentication
The API uses key-based authentication. All requests must include the `X-API-KEY` header with a valid API key.

### Generating API Keys
Generate a new API key using the following console command:
```
php artisan generate:api-key
```

### Endpoints

#### POST /api
Submit sensor data to the system.

**Request Body Format:**
```json
[
  {
    "temperature": 23.5,
    "humidity": 45.2,
    "noise": 42.8,
    "timestamp": "2025-09-19 14:30:22"
  },
  {
    "temperature": 23.6,
    "humidity": 45.1,
    "noise": 43.1,
    "timestamp": "2025-09-19 14:30:23"
  }
]
```

**Required Fields:**
- `temperature`: Numeric value representing temperature in degrees Celsius
- `humidity`: Numeric value representing relative humidity percentage
- `noise`: Numeric value representing noise level in decibels (dB)
- `timestamp`: String in format `YYYY-MM-DD HH:MM:SS`

**Response:**
- Success: `{"success": true}` with status code 200
- Error: JSON object with an `error` field and appropriate status code

### Data Processing
When data is received via the API:
1. Raw data is temporarily stored in `storage/app/api_data.json`
2. The `ProcessSensorData` job is dispatched to handle the data asynchronously
3. Data is validated, normalized, and inserted into respective second-level tables
4. Minute-level averages are calculated and stored in the minutes tables

## Database Schema

The system uses six tables to store environmental data:

- **temperature_seconds**: Raw temperature readings at second resolution
- **temperature_minutes**: Aggregated temperature averages at minute resolution
- **humidity_seconds**: Raw humidity readings at second resolution
- **humidity_minutes**: Aggregated humidity averages at minute resolution
- **noise_seconds**: Raw noise readings at second resolution
- **noise_minutes**: Aggregated noise averages at minute resolution

## Getting Started

1. Install dependencies: `composer install` and `npm install`
2. Compile assets: `npm run dev`
3. Configure your `.env` file and run migrations: `php artisan migrate`
4. Generate an API key: `php artisan generate:api-key`
5. Start the server: `php artisan serve`
6. Configure your queue worker: `php artisan queue:work`

## Testing the API

You can test the API using curl:
```bash
curl -X POST http://localhost:8000/api \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your_api_key_here" \
  -d '[{"temperature": 23.5, "humidity": 45.2, "noise": 42.8, "timestamp": "2025-09-19 14:30:22"}]'
```

---
Developed by Giordano Berwig
