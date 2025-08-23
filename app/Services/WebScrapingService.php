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
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
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
            ]
        ]);
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

            // Make HTTP request
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();
            
            if (empty($html)) {
                Log::warning("Empty response from URL: {$url}");
                return null;
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
                'status' => 'success'
            ];

        } catch (RequestException $e) {
            Log::error("HTTP request failed for URL {$url}: " . $e->getMessage());
            return [
                'url' => $url,
                'error' => 'HTTP request failed: ' . $e->getMessage(),
                'status' => 'error'
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
        
        foreach ($urls as $url) {
            $url = trim($url);
            if (!empty($url)) {
                $results[] = $this->scrapeUrl($url);
            }
        }

        return array_filter($results); // Remove null results
    }

    /**
     * Check if a URL is accessible
     */
    public function isUrlAccessible(string $url): bool
    {
        try {
            $response = $this->client->head($url);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
