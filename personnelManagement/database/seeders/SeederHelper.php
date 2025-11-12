<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SeederHelper
{
    private static $provinces = null;
    private static $citiesByProvince = [];
    private static $barangaysByCity = [];

    /**
     * Fetch all provinces from PSGC API
     */
    public static function getProvinces(): array
    {
        if (self::$provinces === null) {
            try {
                $response = Http::timeout(30)->get('https://psgc.gitlab.io/api/provinces/');
                self::$provinces = $response->json();
            } catch (\Exception $e) {
                // Fallback to Metro Manila if API fails
                self::$provinces = [
                    ['code' => '133900000', 'name' => 'Metro Manila']
                ];
            }
        }
        return self::$provinces;
    }

    /**
     * Get cities/municipalities for a province
     */
    public static function getCities(string $provinceCode): array
    {
        if (!isset(self::$citiesByProvince[$provinceCode])) {
            try {
                $cities = Http::timeout(30)->get("https://psgc.gitlab.io/api/provinces/{$provinceCode}/cities/")->json();
                $municipalities = Http::timeout(30)->get("https://psgc.gitlab.io/api/provinces/{$provinceCode}/municipalities/")->json();
                self::$citiesByProvince[$provinceCode] = array_merge($cities ?: [], $municipalities ?: []);
            } catch (\Exception $e) {
                // Fallback cities for Metro Manila
                self::$citiesByProvince[$provinceCode] = [
                    ['code' => '137401000', 'name' => 'Quezon City'],
                    ['code' => '133900000', 'name' => 'Manila'],
                    ['code' => '137402000', 'name' => 'Makati'],
                    ['code' => '137403000', 'name' => 'Pasig'],
                    ['code' => '137404000', 'name' => 'Taguig'],
                    ['code' => '137405000', 'name' => 'Caloocan']
                ];
            }
        }
        return self::$citiesByProvince[$provinceCode];
    }

    /**
     * Get barangays for a city
     */
    public static function getBarangays(string $cityCode): array
    {
        if (!isset(self::$barangaysByCity[$cityCode])) {
            try {
                $response = Http::timeout(30)->get("https://psgc.gitlab.io/api/cities-municipalities/{$cityCode}/barangays/");
                self::$barangaysByCity[$cityCode] = $response->json();
            } catch (\Exception $e) {
                // Fallback barangays
                self::$barangaysByCity[$cityCode] = [
                    ['name' => 'Barangay 1'],
                    ['name' => 'Barangay 2'],
                    ['name' => 'Barangay 3'],
                    ['name' => 'Barangay 4'],
                    ['name' => 'Barangay 5'],
                    ['name' => 'Barangay 6']
                ];
            }
        }
        return self::$barangaysByCity[$cityCode];
    }

    /**
     * Get a random location (province, city, barangay)
     */
    public static function getRandomLocation(): array
    {
        $provinces = self::getProvinces();
        $province = $provinces[array_rand($provinces)];

        $cities = self::getCities($province['code']);
        if (empty($cities)) {
            // Fallback
            return [
                'province' => 'Metro Manila',
                'city' => 'Quezon City',
                'barangay' => 'Barangay ' . rand(1, 100)
            ];
        }

        $city = $cities[array_rand($cities)];

        $barangays = self::getBarangays($city['code']);
        if (empty($barangays)) {
            $barangay = 'Barangay ' . rand(1, 100);
        } else {
            $barangay = $barangays[array_rand($barangays)]['name'];
        }

        return [
            'province' => $province['name'],
            'city' => $city['name'],
            'barangay' => $barangay
        ];
    }

    /**
     * Generate a profile picture with initials
     */
    public static function generateProfilePicture(string $firstName, string $lastName, string $filename): string
    {
        $path = 'profile_pictures/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Create directory if it doesn't exist
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Check if GD library is available
        if (!function_exists('imagecreatetruecolor')) {
            // Generate SVG image instead
            return self::generateSVGProfilePicture($firstName, $lastName, $filename);
        }

        // Create 400x400 image
        $width = 400;
        $height = 400;
        $image = imagecreatetruecolor($width, $height);

        // Random background color (soft pastel colors)
        $colors = [
            [100, 149, 237], // Cornflower blue
            [255, 182, 193], // Light pink
            [152, 251, 152], // Pale green
            [255, 218, 185], // Peach
            [221, 160, 221], // Plum
            [173, 216, 230], // Light blue
            [240, 128, 128], // Light coral
            [144, 238, 144], // Light green
        ];
        $color = $colors[array_rand($colors)];
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        imagefill($image, 0, 0, $bgColor);

        // Text color (white)
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Get initials
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

        // Add text (initials)
        $fontSize = 120;
        $fontFile = public_path('fonts/arial.ttf');

        // Check if font exists, if not use imagestring
        if (file_exists($fontFile)) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $initials);
            $x = ($width - ($bbox[2] - $bbox[0])) / 2;
            $y = ($height - ($bbox[7] - $bbox[1])) / 2 + abs($bbox[7]);
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $initials);
        } else {
            // Fallback to built-in font (5 is the largest built-in font)
            $font = 5;
            $textWidth = imagefontwidth($font) * strlen($initials);
            $textHeight = imagefontheight($font);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            imagestring($image, $font, $x, $y, $initials, $textColor);
        }

        // Save to storage
        imagepng($image, $fullPath);
        imagedestroy($image);

        return $path;
    }

    /**
     * Generate an SVG profile picture as fallback when GD is not available
     */
    private static function generateSVGProfilePicture(string $firstName, string $lastName, string $filename): string
    {
        // Get initials
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

        // Random background color (soft pastel colors in hex)
        $colors = [
            '#6495ED', // Cornflower blue
            '#FFB6C1', // Light pink
            '#98FB98', // Pale green
            '#FFDAB9', // Peach
            '#DDA0DD', // Plum
            '#ADD8E6', // Light blue
            '#F08080', // Light coral
            '#90EE90', // Light green
        ];
        $bgColor = $colors[array_rand($colors)];

        // Create SVG content
        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
  <rect width="400" height="400" fill="{$bgColor}"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="120" fill="white" text-anchor="middle" dominant-baseline="central">
    {$initials}
  </text>
</svg>
SVG;

        // Change filename to .svg
        $filename = str_replace('.png', '.svg', $filename);
        $path = 'profile_pictures/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Create directory if it doesn't exist
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $svg);

        return $path;
    }

    /**
     * Generate a resume PDF file
     */
    public static function generateResumePDF(array $userData, string $filename): string
    {
        $path = 'resumes/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Create directory if it doesn't exist
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Create simple text-based PDF content
        $pdfContent = self::generateSimpleResumePDF($userData);

        file_put_contents($fullPath, $pdfContent);

        return $path;
    }

    /**
     * Generate a simple text-based resume (as a placeholder)
     * In production, you'd use a proper PDF library like TCPDF or DomPDF
     */
    private static function generateSimpleResumePDF(array $userData): string
    {
        // This creates a very basic PDF structure
        // For production use, consider using libraries like TCPDF, FPDF, or DomPDF

        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n";
        $pdf .= "<< /Type /Catalog /Pages 2 0 R >>\n";
        $pdf .= "endobj\n";

        $pdf .= "2 0 obj\n";
        $pdf .= "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
        $pdf .= "endobj\n";

        $content = "RESUME\n\n";
        $content .= "Name: {$userData['first_name']} {$userData['middle_name']} {$userData['last_name']}\n";
        $content .= "Email: {$userData['email']}\n";
        $content .= "Mobile: {$userData['mobile_number']}\n";
        $content .= "Address: {$userData['full_address']}\n\n";
        $content .= "Birth Date: {$userData['birth_date']}\n";
        $content .= "Gender: {$userData['gender']}\n";
        $content .= "Civil Status: {$userData['civil_status']}\n";
        $content .= "Nationality: {$userData['nationality']}\n\n";
        $content .= "OBJECTIVE\n";
        $content .= "To obtain a challenging position in a progressive organization.\n\n";
        $content .= "EDUCATION\n";
        $content .= "Bachelor's Degree - University of the Philippines\n";
        $content .= "Year Graduated: " . (date('Y') - rand(5, 15)) . "\n\n";
        $content .= "WORK EXPERIENCE\n";
        $content .= rand(1, 10) . " years of professional experience in various industries.\n";

        $contentLength = strlen($content);

        $pdf .= "3 0 obj\n";
        $pdf .= "<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /Contents 4 0 R >>\n";
        $pdf .= "endobj\n";

        $pdf .= "4 0 obj\n";
        $pdf .= "<< /Length {$contentLength} >>\n";
        $pdf .= "stream\n";
        $pdf .= "BT\n";
        $pdf .= "/F1 12 Tf\n";
        $pdf .= "50 750 Td\n";

        $lines = explode("\n", $content);
        $yPos = 750;
        foreach ($lines as $line) {
            $pdf .= "({$line}) Tj\n";
            $yPos -= 15;
            $pdf .= "0 -15 Td\n";
        }

        $pdf .= "ET\n";
        $pdf .= "endstream\n";
        $pdf .= "endobj\n";

        $pdf .= "xref\n";
        $pdf .= "0 5\n";
        $pdf .= "0000000000 65535 f\n";
        $pdf .= "0000000009 00000 n\n";
        $pdf .= "0000000056 00000 n\n";
        $pdf .= "0000000115 00000 n\n";
        $pdf .= "0000000300 00000 n\n";

        $pdf .= "trailer\n";
        $pdf .= "<< /Size 5 /Root 1 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= strlen($pdf) . "\n";
        $pdf .= "%%EOF\n";

        return $pdf;
    }

    /**
     * Get job industries list from the application
     * This must match the list in resources/views/components/shared/preference.blade.php
     */
    public static function getJobIndustries(): array
    {
        return [
            "Accounting",
            "Administration",
            "Architecture",
            "Arts and Design",
            "Automotive",
            "Banking and Finance",
            "Business Process Outsourcing (BPO)",
            "Construction",
            "Customer Service",
            "Data and Analytics",
            "Education",
            "Engineering",
            "Entertainment",
            "Environmental Services",
            "Food and Beverage",
            "Government",
            "Healthcare",
            "Hospitality",
            "Human Resources",
            "Information Technology",
            "Insurance",
            "Legal",
            "Logistics and Supply Chain",
            "Manufacturing",
            "Marketing",
            "Media and Communications",
            "Nonprofit",
            "Pharmaceuticals",
            "Public Relations",
            "Real Estate",
            "Retail",
            "Sales",
            "Science and Research",
            "Skilled Trades",
            "Sports and Recreation",
            "Telecommunications",
            "Tourism",
            "Transportation",
            "Utilities",
            "Warehouse and Distribution",
            "Writing and Publishing"
        ];
    }
}
