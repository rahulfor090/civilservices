<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleBlock;
use App\Models\ArticleSection;
use App\Models\Author;
use App\Models\Banner;
use App\Models\ContentType;
use App\Models\HomepageSlot;
use App\Models\HomepageSlotItem;
use App\Models\Language;
use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use App\Models\Role;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BilingualContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Languages
        $en = Language::updateOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'url_prefix' => '',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        $hi = Language::updateOrCreate(
            ['code' => 'hi'],
            [
                'name' => 'Hindi',
                'native_name' => 'हिन्दी',
                'direction' => 'ltr',
                'url_prefix' => 'hindi',
                'is_default' => false,
                'is_active' => true,
            ]
        );

        // 2. Demo Users
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@civilservicegurukul.com'],
            [
                'name' => 'Chief Platform Architect',
                'password' => Hash::make('Password123!'),
                'mobile' => '9876543210',
                'country_code' => '+91',
                'locale' => 'en',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('super_admin');

        $editorUser = User::updateOrCreate(
            ['email' => 'editor@civilservicegurukul.com'],
            [
                'name' => 'Senior Editorial Lead',
                'password' => Hash::make('Password123!'),
                'mobile' => '9876543211',
                'country_code' => '+91',
                'locale' => 'en',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $editorUser->assignRole('senior_editor');

        $studentUser = User::updateOrCreate(
            ['email' => 'student@civilservicegurukul.com'],
            [
                'name' => 'Aditya Sharma',
                'password' => Hash::make('Password123!'),
                'mobile' => '9876543212',
                'country_code' => '+91',
                'locale' => 'en',
                'status' => 'active',
                'target_exam' => 'UPSC Civil Services 2027',
                'email_verified_at' => now(),
            ]
        );
        $studentUser->assignRole('registered_student');

        // 3. Authors
        $authorEn = Author::updateOrCreate(
            ['slug' => 'editorial-faculty-board'],
            [
                'user_id' => $editorUser->id,
                'name' => 'Faculty Editorial Board',
                'designation' => 'Senior Academic Panel, Civil Service Gurukul',
                'bio_html' => '<p>Distinguished civil service mentors specializing in General Studies, Ethics, and Current Affairs analysis.</p>',
                'twitter_url' => 'https://x.com/civilservicegurukul',
                'linkedin_url' => 'https://linkedin.com/company/civilservicegurukul',
                'is_active' => true,
            ]
        );

        // 4. Ten Core Content Types
        $contentTypesData = [
            [
                'code' => 'daily-news-analysis',
                'name' => 'Daily News Analysis',
                'url_prefix' => 'daily-updates/daily-news-analysis',
                'template' => 'standard',
                'icon' => 'newspaper',
            ],
            [
                'code' => 'daily-news-editorials',
                'name' => 'Daily Editorial Analysis',
                'url_prefix' => 'daily-news-editorials',
                'template' => 'editorial',
                'icon' => 'pen-tool',
            ],
            [
                'code' => 'state-pcs-current-affairs',
                'name' => 'State PCS Current Affairs',
                'url_prefix' => 'state-pcs-current-affairs',
                'template' => 'standard',
                'icon' => 'map-pin',
            ],
            [
                'code' => 'loksabha-rajyasabha-discussions',
                'name' => 'Sansad TV Discussions',
                'url_prefix' => 'loksabha-rajyasabha-discussions',
                'template' => 'standard',
                'icon' => 'tv',
            ],
            [
                'code' => 'pib-current-affairs',
                'name' => 'PIB Current Affairs',
                'url_prefix' => 'pib-current-affairs',
                'template' => 'standard',
                'icon' => 'file-text',
            ],
            [
                'code' => 'monthly-ca-consolidation',
                'name' => 'Monthly CA Consolidation',
                'url_prefix' => 'daily-updates/monthly-ca-consolidation',
                'template' => 'standard',
                'icon' => 'archive',
            ],
            [
                'code' => 'summary-of-important-reports',
                'name' => 'Summary of Important Reports',
                'url_prefix' => 'summary-of-important-reports',
                'template' => 'standard',
                'icon' => 'bar-chart-2',
            ],
            [
                'code' => 'mind-map',
                'name' => 'Mind Maps',
                'url_prefix' => 'mind-map',
                'template' => 'map',
                'icon' => 'git-merge',
            ],
            [
                'code' => 'to-the-points',
                'name' => 'To The Point',
                'url_prefix' => 'to-the-points',
                'template' => 'standard',
                'icon' => 'check-circle',
            ],
            [
                'code' => 'ethics-case-studies',
                'name' => 'Ethics Case Studies',
                'url_prefix' => 'ethics/case-studies',
                'template' => 'standard',
                'icon' => 'shield',
            ],
        ];

        $contentTypes = [];
        foreach ($contentTypesData as $ct) {
            $contentTypes[$ct['code']] = ContentType::updateOrCreate(['code' => $ct['code']], $ct);
        }

        // 5. Sections & Translations
        $sectionsData = [
            'gs-1' => ['en' => 'General Studies I', 'hi' => 'सामान्य अध्ययन प्रश्नपत्र I', 'slug' => 'general-studies-1'],
            'gs-2' => ['en' => 'General Studies II', 'hi' => 'सामान्य अध्ययन प्रश्नपत्र II', 'slug' => 'general-studies-2'],
            'gs-3' => ['en' => 'General Studies III', 'hi' => 'सामान्य अध्ययन प्रश्नपत्र III', 'slug' => 'general-studies-3'],
            'gs-4' => ['en' => 'General Studies IV', 'hi' => 'सामान्य अध्ययन प्रश्नपत्र IV (नीतिशास्त्र)', 'slug' => 'general-studies-4'],
        ];

        $sections = [];
        foreach ($sectionsData as $key => $sData) {
            $existingTranslation = SectionTranslation::where('slug', $sData['slug'])->where('language_id', $en->id)->first();
            if ($existingTranslation) {
                $section = $existingTranslation->section;
            } else {
                $section = Section::create([
                    'content_type_id' => $contentTypes['daily-news-analysis']->id,
                    'sort_order' => count($sections) + 1,
                    'is_active' => true,
                ]);
            }

            SectionTranslation::updateOrCreate(
                ['section_id' => $section->id, 'language_id' => $en->id],
                [
                    'name' => $sData['en'],
                    'slug' => $sData['slug'],
                    'description' => "Curated curriculum content for {$sData['en']}",
                ]
            );

            SectionTranslation::updateOrCreate(
                ['section_id' => $section->id, 'language_id' => $hi->id],
                [
                    'name' => $sData['hi'],
                    'slug' => $sData['slug'],
                    'description' => "{$sData['hi']} के लिए परीक्षा-उपयोगी सामग्री",
                ]
            );

            $sections[$key] = $section;
        }

        // 6. Tags
        $tagsDataEn = ['Art & Culture', 'Polity & Governance', 'Economy', 'Environment & Ecology', 'Science & Tech', 'Ethics & Integrity', 'Current Affairs'];
        $tagsDataHi = ['कला एवं संस्कृति', 'राजव्यवस्था एवं शासन', 'अर्थव्यवस्था', 'पर्यावरण एवं पारिस्थितिकी', 'विज्ञान एवं प्रौद्योगिकी', 'नीतिशास्त्र', 'समसामयिकी'];

        $tagsEn = [];
        foreach ($tagsDataEn as $t) {
            $tagsEn[] = Tag::updateOrCreate(
                ['language_id' => $en->id, 'slug' => Str::slug($t)],
                ['name' => $t, 'use_count' => 1]
            );
        }

        $tagsHi = [];
        foreach ($tagsDataHi as $t) {
            $tagsHi[] = Tag::updateOrCreate(
                ['language_id' => $hi->id, 'slug' => Str::slug($t)],
                ['name' => $t, 'use_count' => 1]
            );
        }

        // 7. Twenty Bilingual Sample Articles (10 Pairs)
        $articlePairs = [
            [
                'content_type' => 'daily-news-analysis',
                'section' => 'gs-1',
                'en' => [
                    'title' => 'ASI to Conserve Ancient Buddhist Site Near Kanaganahalli',
                    'slug' => 'conservation-of-ancient-buddhist-site',
                    'excerpt' => 'The Archaeological Survey of India (ASI) has initiated an extensive conservation project for the ancient Buddhist settlement at Sannati-Kanaganahalli in Kalaburagi, Karnataka.',
                    'source_name' => 'The Hindu',
                    'source_url' => 'https://www.thehindu.com',
                    'sections' => [
                        [
                            'heading' => 'Why in News?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>The Archaeological Survey of India (ASI) has approved a comprehensive architectural restoration blueprint for the stupa site at Kanaganahalli, famous for the unique inscribed stone relief sculpture of Emperor Ashoka surrounded by his queens and attendants.</p>'
                        ],
                        [
                            'heading' => 'What are the Key Archaeological Findings?',
                            'anchor' => 'key-findings',
                            'body' => '<p>Excavations revealed an Adholoka Maha Stupa mentioned in Prakrit inscriptions in Brahmi script dating between the 1st century BCE and the 3rd century CE. The most monumental find is the stone slab bearing the label <em>"Ranyo Asoko"</em> (King Ashoka), which is the only surviving sculpted likeness of the Mauryan Emperor anywhere in the world.</p>'
                        ],
                        [
                            'heading' => 'Significance for Prelims & Mains',
                            'anchor' => 'exam-significance',
                            'body' => '<p>The site provides crucial corroboration for the spread of Buddhism into the Deccan plateau during the Mauryan and Satavahana epochs. For GS Paper 1, questions frequently probe Mauryan art, epigraphy, and the geographical reach of Ashokan edicts across peninsular India.</p>'
                        ]
                    ],
                    'pyq' => [
                        'question' => 'With reference to the cultural history of India, consider the following statements regarding the Kanaganahalli site: 1. It contains a portrait sculpture of Emperor Ashoka inscribed with the label "Ranyo Asoko". 2. It was primarily developed during the Gupta period. Which of the statements given above is/are correct?',
                        'options' => ['1 only', '2 only', 'Both 1 and 2', 'Neither 1 nor 2'],
                        'answer' => '1 only',
                        'explanation' => 'The inscribed panel depicting King Ashoka was excavated at Kanaganahalli on the Bhima river in Karnataka, dating to the Satavahana era, not the Gupta period.'
                    ]
                ],
                'hi' => [
                    'title' => 'कणगनहल्ली के समीप प्राचीन बौद्ध स्थल का संरक्षण: भारतीय पुरातत्व सर्वेक्षण',
                    'slug' => 'conservation-of-ancient-buddhist-site',
                    'excerpt' => 'भारतीय पुरातत्व सर्वेक्षण (ASI) ने कर्नाटक के कालबुर्गी स्थित सन्नति-कणगनहल्ली में प्राचीन बौद्ध स्थल के व्यापक संरक्षण का कार्य आरंभ किया है।',
                    'source_name' => 'द हिन्दू',
                    'source_url' => 'https://www.thehindu.com',
                    'sections' => [
                        [
                            'heading' => 'चर्चा में क्यों?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>भारतीय पुरातत्व सर्वेक्षण (ASI) ने कणगनहल्ली स्थित प्राचीन महास्तूप के वैज्ञानिक संरक्षण और जीर्णोद्धार के लिए एक वृहद योजना को स्वीकृति दी है। यह स्थल सम्राट अशोक की एकमात्र सचित्र पाषाण प्रतिमा के लिए विख्यात है।</p>'
                        ],
                        [
                            'heading' => 'उत्खनन के प्रमुख पुरातात्विक साक्ष्य क्या हैं?',
                            'anchor' => 'key-findings',
                            'body' => '<p>उत्खनन से ब्राह्मी लिपि और प्राकृत भाषा में उत्कीर्ण "अधोलोक महास्तूप" के अवशेष प्रकाश में आए हैं। सबसे महत्वपूर्ण खोज वह पाषाण पट्टिका है जिस पर <em>"रायो असोको"</em> (राजा अशोक) उत्कीर्ण है, जो विश्व में मौर्य सम्राट की उपलब्ध एकमात्र प्रामाणिक प्रतिमा है।</p>'
                        ],
                        [
                            'heading' => 'प्रारंभिक एवं मुख्य परीक्षा हेतु महत्व',
                            'anchor' => 'exam-significance',
                            'body' => '<p>यह स्थल दक्कन में बौद्ध धर्म के विस्तार, मौर्यकालीन स्थापत्य कला और सातवाहन युगीन व्यापारिक मार्गों के अध्ययन की दृष्टि से अत्यंत महत्वपूर्ण है। मुख्य परीक्षा के कला एवं संस्कृति खंड में यह निरंतर प्रासंगिक है।</p>'
                        ]
                    ],
                    'pyq' => [
                        'question' => 'भारत के सांस्कृतिक इतिहास के संदर्भ में, कणगनहल्ली स्थल के संबंध में निम्नलिखित कथनों पर विचार कीजिए: 1. यहाँ "रायो असोको" उत्कीर्ण सम्राट अशोक की पाषाण प्रतिमा पाई गई है। 2. यह मुख्य रूप से गुप्त काल के दौरान विकसित हुआ था। उपर्युक्त में से कौन-सा/से कथन सही है/हैं?',
                        'options' => ['केवल 1', 'केवल 2', '1 और 2 दोनों', 'न तो 1, न ही 2'],
                        'answer' => 'केवल 1',
                        'explanation' => 'कणगनहल्ली कर्नाटक में भीमा नदी के तट पर स्थित सातवाहन कालीन स्थल है, जहाँ मौर्य सम्राट अशोक की नामांकित प्रतिमा प्राप्त हुई है।'
                    ]
                ]
            ],
            [
                'content_type' => 'daily-news-editorials',
                'section' => 'gs-3',
                'en' => [
                    'title' => 'Energy Poverty and India’s Renewable Energy Transition Paradigm',
                    'slug' => 'energy-poverty-and-clean-energy-transition',
                    'excerpt' => 'Balancing aggressive decarbonisation goals with equitable energy access remains a pivotal socioeconomic imperative for Indian policymakers.',
                    'source_name' => 'Indian Express',
                    'source_url' => 'https://indianexpress.com',
                    'sections' => [
                        [
                            'heading' => 'What is Energy Poverty?',
                            'anchor' => 'what-is-energy-poverty',
                            'body' => '<p>Energy poverty transcends mere lack of electricity grid connectivity; it encapsulates inadequate, unreliable, and unaffordable modern energy services essential for human development, healthcare, and economic mobility.</p>'
                        ],
                        [
                            'heading' => 'What are the Key Drivers of Clean Energy Disparity?',
                            'anchor' => 'drivers-of-disparity',
                            'body' => '<p>High upfront capital expenditure for rooftop solar, legacy DISCOM financial stress, regional grid congestion, and dependency on biomass for rural cooking represent persistent systemic barriers.</p>'
                        ],
                        [
                            'heading' => 'What Measures are Required for Just Transition?',
                            'anchor' => 'measures-required',
                            'body' => '<p>Targeted subsidies via PM Surya Ghar Muft Bijli Yojana, community micro-grids, domestic battery storage manufacturing under PLI schemes, and progressive cross-subsidies are essential to insulate low-income households.</p>'
                        ]
                    ],
                    'pyq' => [
                        'question' => 'Critically analyze how the "Just Transition" framework can safeguard vulnerable communities during India\'s transition from fossil fuels to renewable energy sources. (250 Words, 15 Marks)',
                        'options' => [],
                        'answer' => 'Model answer available in Mains Answer Writing module.',
                        'explanation' => 'Evaluate ecological commitments (Panchamrit) against socio-economic realities such as coal sector employment and rural energy access.'
                    ]
                ],
                'hi' => [
                    'title' => 'ऊर्जा निर्धनता और भारत का स्वच्छ ऊर्जा संक्रमण प्रतिमान',
                    'slug' => 'energy-poverty-and-clean-energy-transition',
                    'excerpt' => 'डीकार्बोनाइजेशन के महत्वाकांक्षी लक्ष्यों को न्यायसंगत ऊर्जा उपलब्धता के साथ संतुलित करना भारतीय नीति निर्माताओं के लिए एक प्रमुख सामाजिक-आर्थिक अनिवार्यता है।',
                    'source_name' => 'इंडियन एक्सप्रेस',
                    'source_url' => 'https://indianexpress.com',
                    'sections' => [
                        [
                            'heading' => 'ऊर्जा निर्धनता क्या है?',
                            'anchor' => 'what-is-energy-poverty',
                            'body' => '<p>ऊर्जा निर्धनता केवल विद्युत ग्रिड की अनुपलब्धता तक सीमित नहीं है; यह मानव विकास, स्वास्थ्य और आर्थिक सशक्तिकरण के लिए आवश्यक आधुनिक ऊर्जा सेवाओं की अपर्याप्तता और वहनीयता के अभाव को व्यक्त करती है।</p>'
                        ],
                        [
                            'heading' => 'स्वच्छ ऊर्जा विषमता के प्रमुख कारक क्या हैं?',
                            'anchor' => 'drivers-of-disparity',
                            'body' => '<p>रूफटॉप सोलर हेतु उच्च प्रारंभिक पूंजीगत व्यय, विद्युत वितरण कंपनियों (DISCOMs) का वित्तीय तनाव और ग्रामीण क्षेत्रों में खाना पकाने के लिए बायोमास पर निर्भरता प्रमुख संरचनात्मक बाधाएँ हैं।</p>'
                        ],
                        [
                            'heading' => 'न्यायसंगत ऊर्जा संक्रमण के लिए क्या उपाय आवश्यक हैं?',
                            'anchor' => 'measures-required',
                            'body' => '<p>पीएम सूर्य घर मुफ्त बिजली योजना, विकेंद्रीकृत मिनी-ग्रिड, पीएलआई योजना के तहत बैटरी स्टोरेज विनिर्माण और लक्षित सब्सिडी निम्न आय वर्ग को ऊर्जा सुरक्षा प्रदान करने के लिए अपरिहार्य हैं।</p>'
                        ]
                    ],
                    'pyq' => [
                        'question' => 'जीवाश्म ईंधन से नवीकरणीय ऊर्जा की ओर भारत के संक्रमण के दौरान "न्यायसंगत संक्रमण" (Just Transition) की रूपरेखा किस प्रकार संवेदनशील समुदायों के हितों की रक्षा कर सकती है? समालोचनात्मक विश्लेषण कीजिए। (250 शब्द, 15 अंक)',
                        'options' => [],
                        'answer' => 'मुख्य परीक्षा उत्तर लेखन मॉड्यूल में विस्तृत मॉडल उत्तर उपलब्ध है।',
                        'explanation' => 'पंचामृत लक्ष्यों एवं कोयला क्षेत्र पर निर्भर श्रमबल के पुनर्वास को संतुलित करते हुए उत्तर प्रस्तुत करें।'
                    ]
                ]
            ],
            [
                'content_type' => 'pib-current-affairs',
                'section' => 'gs-3',
                'en' => [
                    'title' => 'National Green Hydrogen Mission: Strategic Interventions for Decarbonisation',
                    'slug' => 'national-green-hydrogen-mission-interventions',
                    'excerpt' => 'Ministry of New & Renewable Energy outlines key PLI incentives and electrolyser manufacturing allocations to position India as a global green hydrogen export hub.',
                    'source_name' => 'PIB New Delhi',
                    'source_url' => 'https://pib.gov.in',
                    'sections' => [
                        [
                            'heading' => 'Why in News?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>The Ministry has awarded tenders under the Strategic Interventions for Green Hydrogen Transition (SIGHT) programme, allocating domestic manufacturing capacities for advanced electrolysers.</p>'
                        ],
                        [
                            'heading' => 'Key Targets of the Mission',
                            'anchor' => 'key-targets',
                            'body' => '<p>Development of at least 5 MMT (Million Metric Tonnes) of Green Hydrogen production capacity per annum by 2030, alongside an addition of ~125 GW of associated renewable energy capacity.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'राष्ट्रीय हरित हाइड्रोजन मिशन: डीकार्बोनाइजेशन हेतु रणनीतिक हस्तक्षेप',
                    'slug' => 'national-green-hydrogen-mission-interventions',
                    'excerpt' => 'नवीन एवं नवीकरणीय ऊर्जा मंत्रालय ने भारत को हरित हाइड्रोजन का वैश्विक निर्यातक केंद्र बनाने हेतु इलेक्ट्रोलाइज़र विनिर्माण प्रोत्साहन जारी किए।',
                    'source_name' => 'पीआईबी नई दिल्ली',
                    'source_url' => 'https://pib.gov.in',
                    'sections' => [
                        [
                            'heading' => 'चर्चा में क्यों?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>मंत्रालय ने हरित हाइड्रोजन संक्रमण हेतु रणनीतिक हस्तक्षेप (SIGHT) कार्यक्रम के अंतर्गत उन्नत इलेक्ट्रोलाइज़र विनिर्माण क्षमता आवंटन की प्रक्रिया पूर्ण की है।</p>'
                        ],
                        [
                            'heading' => 'मिशन के प्रमुख लक्ष्य',
                            'anchor' => 'key-targets',
                            'body' => '<p>वर्ष 2030 तक प्रतिवर्ष कम से कम 5 मिलियन मीट्रिक टन हरित हाइड्रोजन उत्पादन क्षमता का विकास और लगभग 125 गीगावाट अतिरिक्त नवीकरणीय ऊर्जा क्षमता का संवर्धन।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'state-pcs-current-affairs',
                'section' => 'gs-2',
                'en' => [
                    'title' => 'Roka-Cheka Abhiyan: Traditional Livestock Management in Chhattisgarh',
                    'slug' => 'roka-cheka-abhiyan-chhattisgarh',
                    'excerpt' => 'State government intensifies traditional community-driven livestock confinement to prevent open grazing and protect Kharif seasonal crops.',
                    'source_name' => 'State PR Dept',
                    'source_url' => 'https://dprcg.gov.in',
                    'sections' => [
                        [
                            'heading' => 'Why in News?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>The annual "Roka-Cheka" campaign has commenced across rural panchayats in Chhattisgarh to channel livestock into community Gauthans, preventing crop depredation during monsoon sowing.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'रोका-छेका अभियान: छत्तीसगढ़ में पारंपरिक पशुधन एवं फसल प्रबंधन',
                    'slug' => 'roka-cheka-abhiyan-chhattisgarh',
                    'excerpt' => 'राज्य सरकार ने खरीफ फसलों की सुरक्षा और खुले में चराई रोकने हेतु पारंपरिक सामुदायिक रोका-छेका अभियान को गति प्रदान की।',
                    'source_name' => 'राज्य जनसंपर्क विभाग',
                    'source_url' => 'https://dprcg.gov.in',
                    'sections' => [
                        [
                            'heading' => 'चर्चा में क्यों?',
                            'anchor' => 'why-in-news',
                            'body' => '<p>छत्तीसगढ़ की सभी ग्राम पंचायतों में मानसून बुवाई के दौरान फसलों को सुरक्षित रखने और पशुओं को गौठानों में संकेंद्रित करने हेतु रोका-छेका अभियान आरंभ किया गया है।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'loksabha-rajyasabha-discussions',
                'section' => 'gs-2',
                'en' => [
                    'title' => 'Sansad TV Debate: Ethics and Regulatory Architecture for Generative AI',
                    'slug' => 'ethics-and-regulatory-architecture-for-generative-ai',
                    'excerpt' => 'Parliamentary debate examines balancing technological sovereignty, copyright protection, deepfake mitigation, and startup innovation in India.',
                    'source_name' => 'Sansad TV',
                    'source_url' => 'https://sansadtv.nic.in',
                    'sections' => [
                        [
                            'heading' => 'Key Discussion Points',
                            'anchor' => 'key-points',
                            'body' => '<p>Lawmakers and experts debated the Digital Personal Data Protection (DPDP) Act enforcement, watermarking requirements for synthetic media, and establishing an independent AI Safety Institute.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'संसद टीवी चर्चा: जेनेरेटिव एआई हेतु नैतिक एवं नियामक रूपरेखा',
                    'slug' => 'ethics-and-regulatory-architecture-for-generative-ai',
                    'excerpt' => 'संसदीय परिचर्चा में तकनीकी संप्रभुता, कॉपीराइट संरक्षण, डीपफेक रोकथाम और नवाचार के संतुलन पर विचार-विमर्श किया गया।',
                    'source_name' => 'संसद टीवी',
                    'source_url' => 'https://sansadtv.nic.in',
                    'sections' => [
                        [
                            'heading' => 'प्रमुख चर्चा बिंदु',
                            'anchor' => 'key-points',
                            'body' => '<p>डिजिटल व्यक्तिगत डेटा संरक्षण (DPDP) अधिनियम के क्रियान्वयन, कृत्रिम मीडिया पर अनिवार्य वॉटरमार्किंग तथा भारतीय संदर्भ में एआई सुरक्षा संस्थान की स्थापना पर बल दिया गया।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'summary-of-important-reports',
                'section' => 'gs-2',
                'en' => [
                    'title' => 'NITI Aayog National Multidimensional Poverty Index: Progress Report Analysis',
                    'slug' => 'niti-aayog-multidimensional-poverty-index-analysis',
                    'excerpt' => 'Over 13.5 crore Indians exited multidimensional poverty between 2015-16 and 2019-21, driven by improvements in nutrition, sanitation, and clean cooking fuel.',
                    'source_name' => 'NITI Aayog',
                    'source_url' => 'https://niti.gov.in',
                    'sections' => [
                        [
                            'heading' => 'Methodology and Alkire-Foster Method',
                            'anchor' => 'methodology',
                            'body' => '<p>The National MPI captures overlapping deprivations across 12 indicators spanning Health, Education, and Standard of Living, aligning with Global MPI standards while adding maternal health and bank accounts.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'नीति आयोग राष्ट्रीय बहुआयामी निर्धनता सूचकांक: प्रगति रिपोर्ट का सार',
                    'slug' => 'niti-aayog-multidimensional-poverty-index-analysis',
                    'excerpt' => 'वर्ष 2015-16 से 2019-21 के मध्य 13.5 करोड़ से अधिक भारतीय बहुआयामी निर्धनता से बाहर निकले; पोषण, स्वच्छता एवं स्वच्छ ईंधन में उल्लेखनीय सुधार।',
                    'source_name' => 'नीति आयोग',
                    'source_url' => 'https://niti.gov.in',
                    'sections' => [
                        [
                            'heading' => 'कार्यप्रणाली एवं अल्कियारे-फॉस्टर पद्धति',
                            'anchor' => 'methodology',
                            'body' => '<p>राष्ट्रीय एमपीआई स्वास्थ्य, शिक्षा और जीवन स्तर के 12 संकेतकों के आधार पर वंचनाओं का मापन करता है, जिसमें मातृ स्वास्थ्य और बैंक खाते भी सम्मिलित हैं।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'mind-map',
                'section' => 'gs-2',
                'en' => [
                    'title' => 'Mind Map: Fundamental Rights vs Directive Principles of State Policy',
                    'slug' => 'mind-map-fundamental-rights-vs-dpsp',
                    'excerpt' => 'Visual schematic outlining judicial evolution from Champakam Dorairajan to Kesavananda Bharati and Minerva Mills harmonization doctrine.',
                    'source_name' => 'Civil Service Gurukul Faculty',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'Harmonious Construction Doctrine',
                            'anchor' => 'harmonious-construction',
                            'body' => '<p>The Supreme Court in Minerva Mills held that the Constitution rests on the bedrock of balance between Part III (Fundamental Rights) and Part IV (Directive Principles).</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'माइंड मैप: मूल अधिकार बनाम राज्य के नीति निर्देशक तत्व',
                    'slug' => 'mind-map-fundamental-rights-vs-dpsp',
                    'excerpt' => 'चंपाकम दोरैराजन से लेकर केशवानंद भारती एवं मिनर्वा मिल्स तक न्यायिक व्याख्याओं का सुगम दृश्यमान रेखाचित्र।',
                    'source_name' => 'एग्जामप्रेप आईएएस फैकल्टी',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'समन्वयात्मक व्याख्या का सिद्धांत',
                            'anchor' => 'harmonious-construction',
                            'body' => '<p>सर्वोच्च न्यायालय ने मिनर्वा मिल्स वाद में व्यवस्था दी कि भारतीय संविधान भाग III और भाग IV के मध्य संतुलन के आधार स्तंभ पर टिका है।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'to-the-points',
                'section' => 'gs-3',
                'en' => [
                    'title' => 'To The Point: Monetary Policy Committee and Flexible Inflation Targeting',
                    'slug' => 'to-the-point-monetary-policy-committee-fit',
                    'excerpt' => 'Concise revision notes on MPC composition, CPI target band (4% +/- 2%), repo rate transmission, and liquidity management tools.',
                    'source_name' => 'RBI Bulletin',
                    'source_url' => 'https://rbi.org.in',
                    'sections' => [
                        [
                            'heading' => 'Statutory Basis & Composition',
                            'anchor' => 'statutory-basis',
                            'body' => '<p>Formed under Section 45ZB of the amended RBI Act 1934; 6 members (3 from RBI including Governor with casting vote, and 3 external experts appointed by the Central Government).</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'टू द पॉइंट: मौद्रिक नीति समिति एवं लचीला मुद्रास्फीति लक्ष्यीकरण',
                    'slug' => 'to-the-point-monetary-policy-committee-fit',
                    'excerpt' => 'एमपीसी संरचना, सीपीआई मुद्रास्फीति लक्ष्य (4% +/- 2%), रेपो दर संचरण और तरलता प्रबंधन उपकरणों पर त्वरित पुनरावलोकन नोट्स।',
                    'source_name' => 'आरबीआई बुलेटिन',
                    'source_url' => 'https://rbi.org.in',
                    'sections' => [
                        [
                            'heading' => 'वैधानिक आधार एवं संरचना',
                            'anchor' => 'statutory-basis',
                            'body' => '<p>आरबीआई अधिनियम 1934 की धारा 45ZB के तहत गठित; 6 सदस्य (गवर्नर सहित 3 आरबीआई से तथा 3 केंद्र सरकार द्वारा नियुक्त स्वतंत्र विशेषज्ञ)।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'ethics-case-studies',
                'section' => 'gs-4',
                'en' => [
                    'title' => 'Ethics Case Study 01: Mining Lease Clearance vs Indigenous Tribal Rights',
                    'slug' => 'ethics-case-study-mining-lease-tribal-rights',
                    'excerpt' => 'A District Collector confronts political pressure to bypass Gram Sabha consent under PESA for a critical mineral extraction project.',
                    'source_name' => 'GS-4 Ethics Cell',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'Case Scenario and Dilemma',
                            'anchor' => 'scenario',
                            'body' => '<p>You are posted as District Magistrate in a Fifth Schedule district. A multinational consortium proposes a rare-earth mining facility promising 5,000 industrial jobs, but local Dongria tribal groups invoke PESA Act protections against displacement.</p>'
                        ],
                        [
                            'heading' => 'Ethical Framework for Resolution',
                            'anchor' => 'resolution',
                            'body' => '<p>Apply constitutional morality, statutory compliance under FRA 2006, transparent stakeholder consultation, and sustainable rehabilitation before sanctioning approvals.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'नीतिशास्त्र केस स्टडी 01: खनन पट्टा स्वीकृति बनाम जनजातीय अधिकार',
                    'slug' => 'ethics-case-study-mining-lease-tribal-rights',
                    'excerpt' => 'एक जिला कलेक्टर के समक्ष महत्वपूर्ण खनिज परियोजना हेतु पेसा (PESA) के अंतर्गत ग्राम सभा सहमति को दरकिनार करने का राजनीतिक दबाव।',
                    'source_name' => 'जीएस-4 नीतिशास्त्र सेल',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'केस परिदृश्य एवं नैतिक दुविधा',
                            'anchor' => 'scenario',
                            'body' => '<p>आप पांचवीं अनुसूची के एक जिले में जिला मजिस्ट्रेट हैं। 5,000 रोजगार देने वाली महत्वपूर्ण दुर्लभ खनिज परियोजना प्रस्तावित है, किंतु स्थानीय जनजातीय समुदाय विस्थापन के विरुद्ध विरोध कर रहे हैं।</p>'
                        ],
                        [
                            'heading' => 'समाधान हेतु नैतिक रूपरेखा',
                            'anchor' => 'resolution',
                            'body' => '<p>संवैधानिक नैतिकता, वन अधिकार अधिनियम 2006 का अनुपालन, पारदर्शी परामर्श और पर्यावरण संतुलन को प्राथमिकता देते हुए निर्णय प्रक्रिया सुनिश्चित करें।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
            [
                'content_type' => 'monthly-ca-consolidation',
                'section' => 'gs-3',
                'en' => [
                    'title' => 'Monthly Current Affairs Compendium: Complete Prelims & Mains Digest',
                    'slug' => 'monthly-current-affairs-compendium-digest',
                    'excerpt' => 'Comprehensive subject-wise consolidation of daily updates, editorial takeaways, government schemes, and PIB releases with downloadable PDF.',
                    'source_name' => 'Civil Service Gurukul Editorial',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'Monthly Thematic Overview',
                            'anchor' => 'thematic-overview',
                            'body' => '<p>This consolidation synthesizes all high-yield developments across Polity, Economy, International Relations, Ecology, and Science & Technology, mapped directly to UPSC CSE syllabus papers.</p>'
                        ]
                    ],
                    'pyq' => null
                ],
                'hi' => [
                    'title' => 'मासिक समसामयिकी संकलन: प्रारंभिक एवं मुख्य परीक्षा विशेषांक',
                    'slug' => 'monthly-current-affairs-compendium-digest',
                    'excerpt' => 'दैनिक घटनाक्रम, संपादकीय विश्लेषण, सरकारी योजनाओं और पीआईबी विज्ञप्तियों का विषयवार संपूर्ण संकलन, निशुल्क पीडीएफ सहित।',
                    'source_name' => 'एग्जामप्रेप आईएएस संपादकीय',
                    'source_url' => null,
                    'sections' => [
                        [
                            'heading' => 'मासिक विषयवार अवलोकन',
                            'anchor' => 'thematic-overview',
                            'body' => '<p>यह संकलन राजव्यवस्था, अर्थव्यवस्था, अंतरराष्ट्रीय संबंध, पर्यावरण तथा विज्ञान एवं प्रौद्योगिकी के समस्त परीक्षा-उपयोगी तथ्यों का विश्लेषणात्मक संकलन प्रस्तुत करता है।</p>'
                        ]
                    ],
                    'pyq' => null
                ]
            ],
        ];

        foreach ($articlePairs as $index => $pair) {
            $groupId = (string) Str::uuid();
            $ct = $contentTypes[$pair['content_type']];
            $section = $sections[$pair['section']];

            // 1. English Article
            $artEn = Article::updateOrCreate(
                ['language_id' => $en->id, 'slug' => $pair['en']['slug']],
                [
                    'content_type_id' => $ct->id,
                    'section_id' => $section->id,
                    'author_id' => $authorEn->id,
                    'translation_group_id' => $groupId,
                    'title' => $pair['en']['title'],
                    'excerpt' => $pair['en']['excerpt'],
                    'body_html' => $pair['en']['sections'][0]['body'] ?? '<p>Comprehensive study material.</p>',
                    'reading_minutes' => 6,
                    'status' => 'published',
                    'published_at' => Carbon::now()->subHours($index * 4),
                    'is_featured' => $index < 3,
                    'allow_comments' => true,
                    'view_count' => rand(150, 4200),
                    'sort_order' => $index + 1,
                    'meta_title' => $pair['en']['title'] . ' | Civil Service Gurukul',
                    'meta_description' => $pair['en']['excerpt'],
                    'source_name' => $pair['en']['source_name'],
                    'source_url' => $pair['en']['source_url'],
                    'created_by' => $adminUser->id,
                ]
            );

            ArticleSection::where('article_id', $artEn->id)->delete();
            foreach ($pair['en']['sections'] as $sIndex => $sec) {
                ArticleSection::create([
                    'article_id' => $artEn->id,
                    'heading' => $sec['heading'],
                    'anchor' => $sec['anchor'],
                    'sort_order' => $sIndex + 1,
                    'body_html' => $sec['body'],
                ]);
            }

            ArticleBlock::where('article_id', $artEn->id)->delete();
            if (!empty($pair['en']['pyq'])) {
                ArticleBlock::create([
                    'article_id' => $artEn->id,
                    'type' => 'pyq',
                    'sort_order' => 10,
                    'payload_json' => $pair['en']['pyq'],
                ]);
            }

            $artEn->tags()->sync([$tagsEn[0]->id, $tagsEn[1]->id]);

            // 2. Hindi Article
            $artHi = Article::updateOrCreate(
                ['language_id' => $hi->id, 'slug' => $pair['hi']['slug']],
                [
                    'content_type_id' => $ct->id,
                    'section_id' => $section->id,
                    'author_id' => $authorEn->id,
                    'translation_group_id' => $groupId,
                    'title' => $pair['hi']['title'],
                    'excerpt' => $pair['hi']['excerpt'],
                    'body_html' => $pair['hi']['sections'][0]['body'] ?? '<p>विस्तृत अध्ययन सामग्री।</p>',
                    'reading_minutes' => 6,
                    'status' => 'published',
                    'published_at' => Carbon::now()->subHours($index * 4),
                    'is_featured' => $index < 3,
                    'allow_comments' => true,
                    'view_count' => rand(150, 4200),
                    'sort_order' => $index + 1,
                    'meta_title' => $pair['hi']['title'] . ' | एग्जामप्रेप आईएएस',
                    'meta_description' => $pair['hi']['excerpt'],
                    'source_name' => $pair['hi']['source_name'],
                    'source_url' => $pair['hi']['source_url'],
                    'created_by' => $adminUser->id,
                ]
            );

            ArticleSection::where('article_id', $artHi->id)->delete();
            foreach ($pair['hi']['sections'] as $sIndex => $sec) {
                ArticleSection::create([
                    'article_id' => $artHi->id,
                    'heading' => $sec['heading'],
                    'anchor' => $sec['anchor'],
                    'sort_order' => $sIndex + 1,
                    'body_html' => $sec['body'],
                ]);
            }

            ArticleBlock::where('article_id', $artHi->id)->delete();
            if (!empty($pair['hi']['pyq'])) {
                ArticleBlock::create([
                    'article_id' => $artHi->id,
                    'type' => 'pyq',
                    'sort_order' => 10,
                    'payload_json' => $pair['hi']['pyq'],
                ]);
            }

            $artHi->tags()->sync([$tagsHi[0]->id, $tagsHi[1]->id]);
        }

        // 8. Navigation Menus for Crawlable Mega-Menu (English & Hindi)
        $menuEn = NavigationMenu::updateOrCreate(
            ['code' => 'mega_menu', 'language_id' => $en->id],
            ['name' => 'English Primary Mega Menu', 'is_active' => true]
        );

        $enCols = [
            'Prelims' => [
                ['label' => 'GS Prelims Strategy', 'url' => '/daily-updates/daily-news-analysis'],
                ['label' => 'Prelims Previous Year Papers', 'url' => '/free-downloads'],
                ['label' => 'Mind Maps', 'url' => '/mind-map'],
                ['label' => 'To The Point Notes', 'url' => '/to-the-points'],
            ],
            'Mains' => [
                ['label' => 'Mains GS Strategy', 'url' => '/daily-news-editorials'],
                ['label' => 'Ethics Case Studies', 'url' => '/ethics/case-studies'],
                ['label' => 'UPSC Mains Solved Papers', 'url' => '/free-downloads'],
                ['label' => 'Model Essays & Fodder', 'url' => '/daily-news-editorials'],
            ],
            'Current Affairs' => [
                ['label' => 'Daily News Analysis', 'url' => '/daily-updates/daily-news-analysis'],
                ['label' => 'Daily Editorial Analysis', 'url' => '/daily-news-editorials'],
                ['label' => 'PIB Current Affairs', 'url' => '/pib-current-affairs'],
                ['label' => 'Sansad TV Discussions', 'url' => '/loksabha-rajyasabha-discussions'],
                ['label' => 'Monthly CA Compendium', 'url' => '/daily-updates/monthly-ca-consolidation'],
            ],
            'Gurukul Specials' => [
                ['label' => 'Important Reports Summary', 'url' => '/summary-of-important-reports'],
                ['label' => 'State PCS Current Affairs', 'url' => '/state-pcs-current-affairs'],
                ['label' => 'Free Downloads Hub', 'url' => '/free-downloads'],
            ],
        ];

        NavigationItem::where('menu_id', $menuEn->id)->delete();

        foreach ($enCols as $colName => $items) {
            $parent = NavigationItem::create([
                'menu_id' => $menuEn->id,
                'label' => $colName,
                'url' => '#',
                'link_type' => 'category',
                'sort_order' => 1,
            ]);

            foreach ($items as $idx => $item) {
                NavigationItem::create([
                    'menu_id' => $menuEn->id,
                    'parent_id' => $parent->id,
                    'label' => $item['label'],
                    'url' => $item['url'],
                    'link_type' => 'internal',
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        // 9. Announcement Ticker
        $slotTickerEn = HomepageSlot::updateOrCreate(
            ['language_id' => $en->id, 'key' => 'announcement_ticker'],
            ['title' => 'This Just In Ticker', 'slot_type' => 'ticker', 'sort_order' => 1, 'is_active' => true]
        );

        $tickerItems = [
            ['headline' => 'UPSC Civil Services Prelims 2026 Examination Notification Released', 'cta_url' => '/daily-updates/daily-news-analysis'],
            ['headline' => 'Download Monthly Current Affairs Consolidation PDF (Latest Edition)', 'cta_url' => '/daily-updates/monthly-ca-consolidation'],
            ['headline' => 'State PCS Mains Solved Papers & Toppers Copies Available', 'cta_url' => '/free-downloads'],
            ['headline' => 'Special Classroom Foundation Batches Commencing in Delhi & Prayagraj', 'cta_url' => '/daily-news-editorials'],
        ];

        foreach ($tickerItems as $idx => $ti) {
            HomepageSlotItem::create([
                'slot_id' => $slotTickerEn->id,
                'item_type' => 'custom',
                'headline' => $ti['headline'],
                'cta_url' => $ti['cta_url'],
                'sort_order' => $idx + 1,
                'is_active' => true,
            ]);
        }

        // 10. Top Promo Banner
        Banner::updateOrCreate(
            ['placement' => 'top_strip', 'language_id' => $en->id],
            [
                'headline' => 'Master UPSC with Civil Service Gurukul Complete Foundation Mentorship Programme',
                'subheadline' => 'Scholarship test open for 2026-27 aspirants. Register today for expert counselling.',
                'cta_label' => 'Book Free Counselling',
                'url' => '#leadsform',
                'is_active' => true,
            ]
        );
    }
}
