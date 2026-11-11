<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Centre;
use App\Models\Language;

class SeoService
{
    /**
     * Generate Organization & EducationalOrganization JSON-LD with all active physical coaching centres
     */
    public static function getOrganizationSchema(): string
    {
        $dbCentres = Centre::activeOrdered();
        $hqCentre = Centre::headquarters();

        if ($dbCentres->isNotEmpty()) {
            $subOrganizations = $dbCentres->map(function (Centre $c) {
                $org = [
                    '@type' => 'EducationalOrganization',
                    'name' => 'Civil Service Gurukul - ' . $c->name,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $c->address,
                        'addressLocality' => $c->city,
                        'addressRegion' => $c->state,
                        'postalCode' => $c->pincode ?? '',
                        'addressCountry' => 'IN',
                    ],
                    'telephone' => $c->phone,
                    'email' => $c->email ?: 'info@civilservicegurukul.com',
                ];

                if ($c->latitude !== null && $c->longitude !== null) {
                    $org['geo'] = [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $c->latitude,
                        'longitude' => $c->longitude,
                    ];
                }

                return $org;
            })->values()->all();
        } else {
            $fallbackCentres = [
                ['name' => 'Delhi Old Rajinder Nagar (Headquarters)', 'street' => '57, 16, Block 47, Old Rajinder Nagar, Rajinder Nagar', 'city' => 'Delhi', 'state' => 'Delhi', 'postalCode' => '110060', 'lat' => 28.6387, 'lng' => 77.1852],
            ];

            $subOrganizations = array_map(function ($c) {
                return [
                    '@type' => 'EducationalOrganization',
                    'name' => 'Civil Service Gurukul - ' . $c['name'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $c['street'],
                        'addressLocality' => $c['city'],
                        'addressRegion' => $c['state'],
                        'postalCode' => $c['postalCode'],
                        'addressCountry' => 'IN',
                    ],
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $c['lat'],
                        'longitude' => $c['lng'],
                    ],
                    'telephone' => '+91-9911020626',
                    'email' => 'info@civilservicegurukul.com',
                ];
            }, $fallbackCentres);
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => 'Civil Service Gurukul',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => 'Premier civil services examination and competitive exam preparation institute in India.',
            'telephone' => $hqCentre?->phone ?? '+91-9911020626',
            'email' => $hqCentre?->email ?? 'info@civilservicegurukul.com',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $hqCentre?->address ?? '57, 16, Block 47, Old Rajinder Nagar, Rajinder Nagar',
                'addressLocality' => $hqCentre?->city ?? 'Delhi',
                'addressRegion' => $hqCentre?->state ?? 'Delhi',
                'postalCode' => $hqCentre?->pincode ?? '110060',
                'addressCountry' => 'IN',
            ],
            'subOrganization' => $subOrganizations,
            'sameAs' => [
                'https://x.com/civilservicegurukul',
                'https://www.facebook.com/civilservicegurukul1',
                'https://www.youtube.com/@civilservicegurukul',
                'https://t.me/civilservicegurukul',
            ]
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate NewsArticle & BreadcrumbList JSON-LD for an Article
     */
    public static function getArticleSchema(Article $article): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'NewsArticle',
                    'mainEntityOfPage' => [
                        '@type' => 'WebPage',
                        '@id' => $article->url,
                    ],
                    'headline' => $article->title,
                    'description' => $article->excerpt,
                    'image' => [
                        $article->feature_image_url ?: asset('images/logo.png')
                    ],
                    'datePublished' => $article->published_at ? $article->published_at->toIso8601String() : now()->toIso8601String(),
                    'dateModified' => $article->updated_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $article->author ? $article->author->name : 'Civil Service Gurukul Editorial Board',
                        'url' => url('/about-us'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => 'Civil Service Gurukul',
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => asset('images/logo.png'),
                        ]
                    ],
                    'articleSection' => $article->section ? ($article->section->translation()?->name ?? 'Current Affairs') : 'General Studies',
                    'inLanguage' => $article->language ? $article->language->code : 'en',
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => url($article->language && $article->language->code === 'hi' ? '/hindi' : '/'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $article->contentType ? $article->contentType->name : 'Daily Updates',
                            'item' => url(($article->language && $article->language->code === 'hi' ? '/hindi/' : '/') . ($article->contentType?->url_prefix ?? 'daily-updates/daily-news-analysis')),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $article->title,
                            'item' => $article->url,
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
