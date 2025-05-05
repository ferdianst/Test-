# Advanced CPA Link Generator

This project is a comprehensive CPA (Cost Per Action) link generator tool designed to optimize affiliate marketing campaigns with advanced features to maximize conversions and protect against bot detection, especially from Facebook.

## Key Features

- **Dynamic Link Generation:**  
  Generates unique, short CPA tracking links with randomized parameters to avoid spam detection and improve tracking accuracy.

- **Facebook Bot Protection:**  
  Detects Facebook bots (both desktop and mobile) and serves them a clean, minimal white page with dynamic Open Graph (OG) metadata to prevent your CPA offer URLs from being flagged or blocked.

- **Mobile and Desktop Device Simulation:**  
  Simulates real mobile and desktop devices with realistic user-agent strings, headers, and fingerprinting to mimic genuine user traffic.

- **Image Proxy and Shortlinking:**  
  Proxies OG images through a secure server and generates dynamic shortlinks for images, ensuring privacy and preventing direct exposure of original image URLs.

- **JavaScript Challenge Verification:**  
  Implements a JavaScript-based human verification challenge for suspicious visitors to further block bots without impacting real users.

- **Comprehensive Logging:**  
  Logs all clicks and verification attempts for detailed analytics and monitoring.

- **Flexible Configuration:**  
  Supports fixed or dynamic campaign names, customizable OG metadata, and multiple redirect script options for obfuscation.

## Recent Updates

- Added dynamic random strings appended to OG titles, descriptions, and image URLs for uniqueness.
- Implemented multi-layer Facebook bot detection with separate handling for desktop and mobile bots.
- Integrated image proxy shortlinking with dynamic shortcodes.
- Added JavaScript challenge verification flow for enhanced bot filtering.
- Improved error handling and logging across all components.
- Updated .htaccess with comprehensive rewrite and security rules.
- Enhanced GitHub deployment support with detailed instructions.

## Usage

1. Open `index.html` in your browser.
2. Enter your Trafee Smartlink URL (or use the fixed URL in the code).
3. Customize your campaign's OG title, description, and image or select from the image manager.
4. Choose protection features as needed.
5. Generate your protected shortlink.
6. Share the generated link on social media platforms like Facebook.
7. Monitor clicks and conversions via the logs and your CPA platform.

## Deployment

- Ensure your web server supports `.htaccess` and mod_rewrite.
- Place all project files in your web root or appropriate directory.
- Configure your domain and SSL certificates properly.
- Add your GitHub repository as remote and push updates regularly.

## Notes

- Facebook bots receive a unique white page with dynamic OG metadata to prevent spam detection.
- Real users are redirected with full protection and tracking.
- Image uploads are moderated and proxied for security and privacy.
- The system is designed to be extensible and customizable for various CPA networks.

---

For any questions or support, please contact the project maintainer.
