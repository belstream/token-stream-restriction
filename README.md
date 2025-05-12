# Token Stream Restriction

This project demonstrates how to use **token-based protection** for live audio streams such as **Stream** or **HLS**, using the [Belstream](https://console.belstream.com) API.

It includes a basic script that:
- Requests a secure access token from the API
- Appends the token to the stream URL
- Embeds the protected stream into a simple HTML audio player

## ✅ Requirements

- PHP 7.0 or higher
- `curl` extension enabled in PHP
- A valid **API key** from Belstream

## 🔧 Configuration

Edit the top of the `index.php` file:

```php
define('API_KEY', 'your-api-key-here');
define('MODE', 'stream'); // or 'hls'
define('MOUNTNAME', 'radio-demo-128.mp3'); // or .m3u8 for HLS
define('VALIDITY', 3600); // Token validity in seconds (e.g. 3600 = 1 hour)
