<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class WebScrapingService
{
    protected $client;
    protected $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'verify' => false, // For development - remove in production
            'headers' => [
                'User-Agent' => $this->userAgents[array_rand($this->userAgents)],
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'none',
                'Sec-Fetch-User' => '?1'
            ]
        ]);
    }

    /**
     * Validate and check if URLs are accessible before scraping
     */
    public function validateAndCheckUrls(array $urls): array
    {
        $validatedUrls = [];
        $invalidUrls = [];
        
        foreach ($urls as $url) {
            $url = trim($url);
            if (empty($url)) {
                continue;
            }
            
            // Basic URL format validation
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $invalidUrls[] = [
                    'url' => $url,
                    'error' => 'Invalid URL format',
                    'status' => 'invalid_format'
                ];
                continue;
            }
            
            // Check if URL is accessible
            $accessibilityCheck = $this->checkUrlAccessibility($url);
            
            if ($accessibilityCheck['accessible']) {
                $validatedUrls[] = [
                    'url' => $url,
                    'status' => 'accessible',
                    'response_time' => $accessibilityCheck['response_time'],
                    'status_code' => $accessibilityCheck['status_code']
                ];
            } else {
                $invalidUrls[] = [
                    'url' => $url,
                    'error' => $accessibilityCheck['error'],
                    'status' => 'inaccessible',
                    'status_code' => $accessibilityCheck['status_code']
                ];
            }
        }
        
        return [
            'valid_urls' => $validatedUrls,
            'invalid_urls' => $invalidUrls,
            'total_checked' => count($urls),
            'accessible_count' => count($validatedUrls),
            'inaccessible_count' => count($invalidUrls)
        ];
    }

    /**
     * Check if a specific URL is accessible
     */
    public function checkUrlAccessibility(string $url): array
    {
        try {
            $startTime = microtime(true);
            
            // First try a HEAD request to check accessibility
            $response = $this->client->head($url, [
                'timeout' => 15,
                'connect_timeout' => 10
            ]);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2); // in milliseconds
            $statusCode = $response->getStatusCode();
            
            // Check if status code indicates success
            if ($statusCode >= 200 && $statusCode < 400) {
                return [
                    'accessible' => true,
                    'status_code' => $statusCode,
                    'response_time' => $responseTime,
                    'error' => null
                ];
            } else {
                return [
                    'accessible' => false,
                    'status_code' => $statusCode,
                    'response_time' => $responseTime,
                    'error' => "HTTP status code: {$statusCode}"
                ];
            }
            
        } catch (RequestException $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            
            $errorMessage = $this->getReadableErrorMessage($e, $statusCode);
            
            return [
                'accessible' => false,
                'status_code' => $statusCode,
                'response_time' => $responseTime,
                'error' => $errorMessage
            ];
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'accessible' => false,
                'status_code' => null,
                'response_time' => $responseTime,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get readable error message for different HTTP status codes
     */
    protected function getReadableErrorMessage(RequestException $e, ?int $statusCode): string
    {
        if ($statusCode === null) {
            return 'Connection failed: ' . $e->getMessage();
        }
        
        switch ($statusCode) {
            case 403:
                return 'Access forbidden - This website blocks automated access (bot protection)';
            case 401:
                return 'Unauthorized access - Authentication required';
            case 404:
                return 'Page not found - URL may be invalid or content removed';
            case 429:
                return 'Too many requests - Rate limiting in effect';
            case 500:
                return 'Server error - Website is experiencing technical issues';
            case 502:
            case 503:
            case 504:
                return 'Service unavailable - Website is down or overloaded';
            default:
                return "HTTP error {$statusCode}: " . $e->getMessage();
        }
    }

    /**
     * Fetch and extract content from a URL
     */
    public function scrapeUrl(string $url): ?array
    {
        try {
            Log::info("Starting to scrape URL: {$url}");
            
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::warning("Invalid URL format: {$url}");
                return null;
            }

            // Check accessibility first
            $accessibilityCheck = $this->checkUrlAccessibility($url);
            if (!$accessibilityCheck['accessible']) {
                Log::warning("URL not accessible: {$url} - {$accessibilityCheck['error']}");
                return [
                    'url' => $url,
                    'error' => $accessibilityCheck['error'],
                    'status' => 'error',
                    'status_code' => $accessibilityCheck['status_code']
                ];
            }

            // Make HTTP request with enhanced headers
            $response = $this->client->get($url, [
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => $this->userAgents[array_rand($this->userAgents)],
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache',
                    'Sec-Fetch-Dest' => 'document',
                    'Sec-Fetch-Mode' => 'navigate',
                    'Sec-Fetch-Site' => 'none',
                    'Sec-Fetch-User' => '?1'
                ]
            ]);
            
            $html = $response->getBody()->getContents();
            
            if (empty($html)) {
                Log::warning("Empty response from URL: {$url}");
                return null;
            }

            // Check if response contains anti-bot protection
            if ($this->isAntiBotProtected($html)) {
                Log::warning("Anti-bot protection detected for URL: {$url}");
                return [
                    'url' => $url,
                    'error' => 'This website has anti-bot protection that prevents automated access',
                    'status' => 'error',
                    'status_code' => 403
                ];
            }

            // Extract content
            $content = $this->extractContent($html, $url);
            
            if (empty($content)) {
                Log::warning("No meaningful content extracted from URL: {$url}");
                return [
                    'url' => $url,
                    'error' => 'No meaningful content could be extracted from this URL',
                    'status' => 'error'
                ];
            }

            // Extract title
            $title = $this->extractTitle($html);
            
            Log::info("Successfully scraped URL: {$url}, extracted " . strlen($content) . " characters, title: " . ($title ?: 'N/A'));
            
            return [
                'url' => $url,
                'title' => $title,
                'content' => $content,
                'word_count' => str_word_count($content),
                'scraped_at' => now()->toISOString(),
                'status' => 'success',
                'status_code' => $response->getStatusCode()
            ];

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $errorMessage = $this->getReadableErrorMessage($e, $statusCode);
            
            Log::error("HTTP request failed for URL {$url}: " . $e->getMessage());
            return [
                'url' => $url,
                'error' => $errorMessage,
                'status' => 'error',
                'status_code' => $statusCode
            ];
        } catch (\Exception $e) {
            Log::error("Error scraping URL {$url}: " . $e->getMessage());
            return [
                'url' => $url,
                'error' => 'Scraping failed: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    /**
     * Check if HTML content contains anti-bot protection indicators
     */
    protected function isAntiBotProtected(string $html): bool
    {
        $antiBotIndicators = [
            'just a moment',
            'checking your browser',
            'ddos protection',
            'cloudflare',
            'captcha',
            'security check',
            'bot protection',
            'access denied',
            'blocked by',
            'suspicious activity',
            'verify you are human',
            'please wait while we verify',
            'challenge page',
            'security verification',
            'please complete the security check',
            'blocked by security',
            'rate limited',
            'too many requests'
        ];
        
        $htmlLower = strtolower($html);
        
        foreach ($antiBotIndicators as $indicator) {
            if (strpos($htmlLower, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract main content from HTML
     */
    protected function extractContent(string $html, string $url): string
    {
        try {
            $crawler = new Crawler($html);
            
            // Remove script and style elements
            $crawler->filter('script, style, noscript, iframe, img, video, audio')->each(function (Crawler $node) {
                try {
                    if ($node->getNode(0) && $node->getNode(0)->parentNode) {
                        $node->getNode(0)->parentNode->removeChild($node->getNode(0));
                    }
                } catch (\Exception $e) {
                    // Continue if removal fails
                }
            });

            // Try different content extraction strategies
            $content = $this->extractMainContent($crawler);
            
            if (empty($content) || strlen($content) < 100) {
                $content = $this->extractArticleContent($crawler);
            }
            
            if (empty($content) || strlen($content) < 100) {
                $content = $this->extractBodyContent($crawler);
            }

            // If still no content, try to extract from paragraphs
            if (empty($content) || strlen($content) < 100) {
                $content = $this->extractParagraphContent($crawler);
            }

            // Clean and format content
            return $this->cleanContent($content);

        } catch (\Exception $e) {
            Log::error("Error extracting content from HTML: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Try to extract main content area
     */
    protected function extractMainContent(Crawler $crawler): string
    {
        $selectors = [
            'main',
            '[role="main"]',
            '.main-content',
            '.content',
            '.post-content',
            '.article-content',
            '.entry-content',
            '#content',
            '#main'
        ];

        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count() > 0) {
                    $text = $node->text();
                    if (strlen($text) > 100) {
                        return $text;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return '';
    }

    /**
     * Try to extract article content
     */
    protected function extractArticleContent(Crawler $crawler): string
    {
        try {
            $node = $crawler->filter('article')->first();
            if ($node->count() > 0) {
                return $node->text();
            }
        } catch (\Exception $e) {
            // Continue to next method
        }

        return '';
    }

    /**
     * Extract content from body as fallback
     */
    protected function extractBodyContent(Crawler $crawler): string
    {
        try {
            $body = $crawler->filter('body')->first();
            if ($body->count() > 0) {
                return $body->text();
            }
        } catch (\Exception $e) {
            Log::error("Error extracting body content: " . $e->getMessage());
        }

        return '';
    }

    /**
     * Extract content from paragraphs
     */
    protected function extractParagraphContent(Crawler $crawler): string
    {
        try {
            $paragraphs = $crawler->filter('p');
            $content = '';
            foreach ($paragraphs as $paragraph) {
                $content .= $paragraph->textContent . ' ';
            }
            return trim($content);
        } catch (\Exception $e) {
            Log::error("Error extracting paragraph content: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Extract title from HTML
     */
    protected function extractTitle(string $html): string
    {
        try {
            $crawler = new Crawler($html);
            $title = $crawler->filter('title')->first();
            if ($title->count() > 0) {
                return trim($title->text());
            }
        } catch (\Exception $e) {
            // Continue without title
        }

        return '';
    }

    /**
     * Clean and format extracted content
     */
    protected function cleanContent(string $content): string
    {
        // Remove extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Remove special characters and normalize
        $content = preg_replace('/[^\p{L}\p{N}\s\-\.\,\!\?\(\)\:\;]/u', ' ', $content);
        
        // Trim and limit length
        $content = trim($content);
        
        // Check if content is meaningful
        if (!$this->isContentMeaningful($content)) {
            return '';
        }
        
        // Limit to reasonable length (avoid extremely long content)
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . '... [Content truncated for analysis]';
        }

        return $content;
    }

    /**
     * Check if extracted content is meaningful
     */
    protected function isContentMeaningful(string $content): bool
    {
        // Content should be at least 100 characters
        if (strlen($content) < 100) {
            return false;
        }
        
        // Content should have some variety (not just repeated characters)
        $uniqueChars = count(array_unique(str_split($content)));
        if ($uniqueChars < 20) {
            return false;
        }
        
        // Content should have some structure (not just random characters)
        $wordCount = str_word_count($content);
        if ($wordCount < 20) {
            return false;
        }
        
        return true;
    }

    /**
     * Scrape multiple URLs and return results
     */
    public function scrapeMultipleUrls(array $urls): array
    {
        $results = [];
        
        // First validate and check all URLs for accessibility
        $urlValidation = $this->validateAndCheckUrls($urls);
        
        Log::info("URL validation completed", [
            'total_urls' => $urlValidation['total_checked'],
            'accessible_count' => $urlValidation['accessible_count'],
            'inaccessible_count' => $urlValidation['inaccessible_count']
        ]);
        
        // Add validation results for inaccessible URLs
        foreach ($urlValidation['invalid_urls'] as $invalidUrl) {
            $results[] = [
                'url' => $invalidUrl['url'],
                'error' => $invalidUrl['error'],
                'status' => 'error',
                'status_code' => $invalidUrl['status_code'] ?? null,
                'validation_status' => 'failed_accessibility_check'
            ];
        }
        
        // Only attempt to scrape accessible URLs
        foreach ($urlValidation['valid_urls'] as $validUrl) {
            $scrapeResult = $this->scrapeUrl($validUrl['url']);
            if ($scrapeResult) {
                $results[] = $scrapeResult;
            }
        }

        return array_filter($results); // Remove null results
    }

    /**
     * Check if a URL is accessible (legacy method - use checkUrlAccessibility for detailed info)
     */
    public function isUrlAccessible(string $url): bool
    {
        $accessibilityCheck = $this->checkUrlAccessibility($url);
        return $accessibilityCheck['accessible'];
    }

    /**
     * Validate URLs and provide detailed feedback for user interface
     */
    public function validateUrlsForUser(array $urls): array
    {
        $validation = $this->validateAndCheckUrls($urls);
        
        $results = [
            'summary' => [
                'total_urls' => $validation['total_checked'],
                'accessible_count' => $validation['accessible_count'],
                'inaccessible_count' => $validation['inaccessible_count'],
                'success_rate' => $validation['total_checked'] > 0 ? 
                    round(($validation['accessible_count'] / $validation['total_checked']) * 100, 1) : 0
            ],
            'accessible_urls' => $validation['valid_urls'],
            'inaccessible_urls' => $validation['invalid_urls'],
            'recommendations' => []
        ];
        
        // Generate recommendations based on validation results
        if ($validation['inaccessible_count'] > 0) {
            $results['recommendations'][] = 'Some URLs are not accessible. Consider removing them or finding alternative sources.';
            
            // Add specific recommendations for common issues
            $has403Errors = false;
            $has404Errors = false;
            $hasConnectionErrors = false;
            
            foreach ($validation['invalid_urls'] as $invalidUrl) {
                if (isset($invalidUrl['status_code'])) {
                    if ($invalidUrl['status_code'] === 403) {
                        $has403Errors = true;
                    } elseif ($invalidUrl['status_code'] === 404) {
                        $has404Errors = true;
                    }
                } else {
                    $hasConnectionErrors = true;
                }
            }
            
            if ($has403Errors) {
                $results['recommendations'][] = 'Some websites block automated access (403 errors). Try finding alternative sources or manually copy relevant content.';
            }
            
            if ($has404Errors) {
                $results['recommendations'][] = 'Some URLs return "not found" errors (404). Check if the links are still valid or have been moved.';
            }
            
            if ($hasConnectionErrors) {
                $results['recommendations'][] = 'Some URLs cannot be reached due to network issues. Check your internet connection and try again.';
            }
        }
        
        if ($validation['accessible_count'] === 0) {
            $results['recommendations'][] = 'No URLs are accessible. Please check your URLs or try again later.';
            $results['recommendations'][] = 'Consider using different sources or manually providing key information in your input text.';
        }
        
        if ($validation['accessible_count'] > 0 && $validation['inaccessible_count'] > 0) {
            $results['recommendations'][] = 'Some URLs are accessible and will be used for analysis.';
            $results['recommendations'][] = 'The AI will work with available sources and use general knowledge for blocked content.';
        }
        
        return $results;
    }
}
