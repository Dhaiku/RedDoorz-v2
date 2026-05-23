-- =====================================================
-- RedDoorz Real Hotel Properties
-- Based on actual RedDoorz locations across SE Asia
-- Philippines · Indonesia · Singapore · Vietnam
-- Run AFTER setup.sql and seed_hotels.sql
-- Safe to run independently.
-- =====================================================

USE reddoorz;

-- =====================================================
-- PHILIPPINES
-- =====================================================
INSERT INTO Hotels (Hotel_Name, Hotel_City, Hotel_Address, Hotel_Description, Hotel_Rating) VALUES

-- Manila
('RedDoorz Plus @ San Marcelino Malate',    'Manila',        '1923 San Marcelino Street, Malate, Manila',                     'Situated in the lively Malate district, steps from Robinsons Place Manila and Manila Bay. Popular with both business and leisure travelers.',        4.2),
('RedDoorz Near Robinsons Place Manila',    'Manila',        '507 Sta. Monica Street, Ermita, Manila',                        'Excellent value property near Robinsons Place Manila, walking distance to Rizal Park and the historic Intramuros walled city.',                    4.0),
('RedDoorz Plus @ Chinatown Binondo',       'Manila',        '805-807 Benavidez Street, Binondo, Manila',                     'In the heart of the world\'s oldest Chinatown. Surrounded by famous dimsum stalls, gold shops, and ancestral streets of Binondo.',             4.1),
('RedDoorz Near SM City Manila',            'Manila',        '181 Natividad Almeda Lopez Street, Ermita, Manila',             'Practical mid-city location close to SM City Manila, Manila Ocean Park, and the Department of Tourism headquarters.',                          3.9),
('RedDoorz @ The Providence Tower Malate',  'Manila',        'Leon Guinto Street, Malate, Manila',                            'Affordable Malate property in the bohemian arts and dining belt. Walking distance to restaurants, bars, and Manila Bay sunset spots.',           4.0),
('RedDoorz Plus @ Seventy Five Inn',        'Parañaque',     'Ninoy Aquino Avenue, Parañaque City, Metro Manila',             'Minutes from NAIA Terminal 1, 2, and 3. Ideal for airline crews, transit passengers, and early morning departures.',                          4.1),

-- Quezon City
('RedDoorz @ Timog Avenue Quezon City',     'Quezon City',   'Timog Avenue, South Triangle, Quezon City',                     'On the famous Timog strip known for its restaurants, bars, and nightlife. Close to ABS-CBN, GMA studios, and Scout area dining.',              4.2),
('RedDoorz Premium @ West Avenue QC',       'Quezon City',   'West Avenue, Bahay Toro, Quezon City',                          'Premium rooms in a quiet residential neighborhood of QC. Great access to SM North EDSA and Trinoma malls via a short drive.',                 4.3),
('RedDoorz Premium @ SMDC Fairview',        'Quezon City',   'Quirino Highway, Fairview, Quezon City',                        'Modern RedDoorz in the growing Fairview district. Adjacent to SM City Fairview and the La Mesa Eco Park.',                                     4.2),
('RedDoorz @ Manila Venetian Quezon City',  'Quezon City',   'Commonwealth Avenue, Batasan Hills, Quezon City',               'Accessible Commonwealth property near the Batasan Complex, Lagro, and key transport hubs linking Metro Manila\'s north end.',                 3.9),
('RedDoorz near Fernwoods Garden QC',       'Quezon City',   'Batasan Road, Holy Spirit, Quezon City',                        'Comfortable budget hotel near Fernwoods Garden events venue and major Quezon City expressways.',                                               3.8),

-- Makati
('RedDoorz Plus @ Danlig Street Makati',    'Makati',        'Danlig Street, Bel-Air, Makati City',                           'Premium property in the exclusive Bel-Air neighborhood, a five-minute drive from Bonifacio High Street and BGC restaurants.',                   4.4),
('RedDoorz near Osmena Highway Makati',     'Makati',        'Osmeña Highway, Bangkal, Makati City',                          'Strategic location along Osmeña Highway with easy access to Makati CBD, EDSA, and the MRT-3 Magallanes station.',                             4.0),
('RedDoorz @ San Antonio Village Makati',   'Makati',        'Pasay Road, San Antonio Village, Makati City',                  'Tucked in the peaceful San Antonio Village, close to Greenbelt, Glorietta, and the Ayala Triangle.',                                          4.1),
('RedDoorz Plus @ La Belle Residences',     'Makati',        'Chino Roces Avenue, Guadalupe Nuevo, Makati City',              'Upgraded RedDoorz property in Guadalupe near the MRT and multiple dining options along Chino Roces.',                                          4.2),

-- Cebu City
('RedDoorz Plus @ Jones Avenue Cebu',       'Cebu City',     'Jones Avenue, Kamagayan, Cebu City',                            'Well-rated property with a rooftop pool. Walking distance to Magellan\'s Cross, Colon Street, and the heritage downtown core of Cebu.',      4.4),
('RedDoorz @ MJ Cuenco Avenue Cebu',        'Cebu City',     'MJ Cuenco Avenue, Mabolo, Cebu City',                           'Conveniently located near Ayala Center Cebu, Waterfront Hotel, and the upscale Mabolo residential district.',                                 4.1),
('RedDoorz @ Escario Street Cebu',          'Cebu City',     'Escario Street, Capitol Site, Cebu City',                       'Near the Capitol complex and major Cebu City hospitals. Easy access to public transport and Fuente Osmeña Circle.',                          3.9),
('RedDoorz Plus @ Nivel Hills Cebu',        'Cebu City',     'Nivel Hills, Lahug, Cebu City',                                 'Elevated property in Lahug with panoramic views of Cebu City. A short drive to IT Park and Tops Lookout.',                                   4.2),
('RedDoorz @ N Bacalso Avenue Cebu',        'Cebu City',     'N. Bacalso Avenue, Punta Princesa, Cebu City',                  'Southern Cebu City location near the South Road Properties development corridor and SRP Mall.',                                               3.8),

-- Davao City
('RedDoorz Plus @ Poblacion Davao',         'Davao',         'Illustre Street, Poblacion District, Davao City',               'In the heart of Poblacion, Davao\'s historic district close to Rizal Park, the waterfront, and major commercial centers.',                   4.2),
('RedDoorz @ Traveler\'s Inn Bajada',       'Davao',         'JP Laurel Avenue, Bajada, Davao City',                          'Established Bajada property near Gaisano Mall, Victoria Plaza, and multiple Davao City landmarks.',                                           4.0),
('RedDoorz @ General Luna Davao',           'Davao',         'General Luna Street, Agdao, Davao City',                        'Budget-friendly option in Agdao with access to the Bankerohan Public Market and Davao\'s street food scene.',                                  3.9),
('RedDoorz Plus @ Malvar Street Davao',     'Davao',         'Malvar Street, Davao City',                                     'Centrally located near Ateneo de Davao University and the Davao City Overland Transport Terminal.',                                            4.1),
('RedDoorz @ Quimpo Boulevard Davao',       'Davao',         'Quimpo Boulevard, Ecoland, Davao City',                         'Ecoland area property close to Abreeza Ayala Mall, SM Davao, and the SM Ecoland shopping complex.',                                          4.0),

-- Baguio City
('RedDoorz Premium @ Mines View Park',      'Baguio',        'Mines View Park Road, Baguio City, Benguet',                    'Closest RedDoorz to Mines View Park. Wake up to cool mountain mist and panoramic views of the Cordillera highlands.',                         4.3),
('RedDoorz Plus @ Aurora Hill Baguio',      'Baguio',        'Aurora Hill, Baguio City, Benguet',                             'Quiet Aurora Hill retreat with easy access to the Baguio Night Market, Panagbenga Festival grounds, and Session Road.',                      4.2),
('RedDoorz Near Baguio Night Market',       'Baguio',        'Harrison Road, Baguio City, Benguet',                           'Prime downtown Baguio location on Harrison Road, steps from the famous Night Market and Baguio Central Market.',                              4.1),
('RedDoorz near Burnham Park Baguio',       'Baguio',        'Jose Abad Santos Drive, Baguio City, Benguet',                  'Overlooking Burnham Park\'s famous rose garden and boating lake. Ideal base for highland day trips to Sagada and Benguet.',                    4.2),
('RedDoorz @ Easter Road Baguio',           'Baguio',        'Easter Road, Baguio City, Benguet',                             'Peaceful Easter Road property between Camp John Hay and the botanical gardens. Perfect for families.',                                         4.0),

-- Puerto Princesa, Palawan
('RedDoorz Plus @ Manalo Extension Palawan','Puerto Princesa','Manalo Extension, corner Diaz Street, Puerto Princesa, Palawan', 'Ideal launching point for the Puerto Princesa Subterranean River UNESCO World Heritage site and Honda Bay island hopping.',                    4.3),
('RedDoorz Plus @ Rizal Avenue Palawan',    'Puerto Princesa','Rizal Avenue, Puerto Princesa, Palawan',                        'Along the main commercial strip of Puerto Princesa. Walking distance to the Palawan Heritage Center and local restaurants.',                   4.1),
('RedDoorz near Puerto Airport Palawan',    'Puerto Princesa','San Miguel Road, Puerto Princesa, Palawan',                     'Closest RedDoorz to Puerto Princesa International Airport. Convenient for early morning departures to island destinations.',                   4.0),
('RedDoorz @ Recaido Road Puerto Princesa', 'Puerto Princesa','Recaido Road, Sta. Monica, Puerto Princesa, Palawan',           'Peaceful residential street property near the city center. Great value base for Palawan nature adventures.',                                  3.9),
('RedDoorz @ Malvar Street Puerto Princesa','Puerto Princesa','Malvar Street, Puerto Princesa, Palawan',                       'Budget-friendly city-center option with free WiFi and modern amenities in the heart of Puerto Princesa.',                                      3.8),

-- Iloilo City
('RedDoorz Near Iloilo Airport',            'Iloilo City',   'Airport Access Road, Brgy. Duyan-Duyan, Iloilo City',           'Minutes from Iloilo International Airport. Convenient for early check-ins and travelers connecting to Guimaras Island.',                     4.0),
('RedDoorz @ Iznart Street Iloilo',         'Iloilo City',   'Iznart Street, Iloilo City',                                    'Downtown Iloilo steps from SM City Iloilo and the famous Molo Church. Central location for exploring the City of Love.',                      4.1),
('RedDoorz @ Diversion Road Iloilo',        'Iloilo City',   'Diversion Road, Mandurriao, Iloilo City',                       'Near Gaisano Capital Mall and the vibrant Smallville complex, Iloilo\'s hub for dining and entertainment.',                                    4.0),
('RedDoorz @ Fuentes Drive Iloilo',         'Iloilo City',   'Fuentes Drive, Iloilo City',                                    'Quiet property near the Iloilo Convention Center and the scenic Iloilo River Esplanade.',                                                      3.9),
('RedDoorz Plus @ Fort San Pedro Iloilo',   'Iloilo City',   'Fort San Pedro Drive, La Paz, Iloilo City',                     'Heritage district stay near Fort San Pedro. Ideal for cultural tourism and exploring Iloilo\'s Spanish-era churches and plazas.',              4.2),

-- Boracay
('RedDoorz @ Sir G Hotel D\'Mall Boracay',  'Boracay',         'Station 2, D\'Mall area, Boracay Island, Aklan',                'Right at D\'Mall, the shopping and dining heart of Boracay. Steps from White Beach and the island\'s most popular restaurants and shops.',     4.4),
('RedDoorz @ Station 3 Boracay',            'Boracay',         'Station 3, Balabag, Boracay Island, Aklan',                     'The quieter southern end of White Beach. Great for families and travelers looking for a relaxed Boracay experience.',                           4.2),
('RedDoorz near Willy\'s Rock Boracay',     'Boracay',         'Station 1, Yapak, Boracay Island, Aklan',                       'Near the iconic Willy\'s Rock at Station 1. The most pristine and picturesque stretch of White Beach.',                                       4.5),
('RedDoorz @ Tulubhan Road Boracay',        'Boracay',         'Tulubhan Road, Caticlan, Malay, Aklan',                          'At the Caticlan jetty port. Perfect as a one-night stopover before the boat crossing to Boracay Island.',                                      3.9),
('RedDoorz Plus @ Beachfront Boracay',      'Boracay',         'Station 2, White Beach Path, Boracay Island, Aklan',            'Premium beachfront property directly on the White Beach path with stunning sunset views over the Sibuyan Sea.',                               4.6),

-- Bacolod
('RedDoorz @ Lacson-Rizal Bacolod',         'Bacolod',       'Rizal-Lacson Street, Brgy. 22, Bacolod City, Negros Occidental','Downtown Bacolod property near the City Plaza and the famous MassKara Festival grounds. Steps from local bakeries and kakanin stalls.',        4.0),
('RedDoorz Plus @ Goldenfield Bacolod',     'Bacolod',       'Goldenfield Commercial Complex, Bacolod City',                  'In the bustling Goldenfield area known for its weekend night life, car shows, and proximity to Robinsons Place Bacolod.',                    4.2),
('RedDoorz @ Burgos Street Bacolod',        'Bacolod',       'Burgos Street, Bacolod City, Negros Occidental',                'Quiet Burgos Street option near the Bacolod Government Center and the SM City Bacolod mall.',                                                  3.9),
('RedDoorz near SM City Bacolod',           'Bacolod',       'Circumferential Road, Bacolod City, Negros Occidental',         'Walking distance from SM City Bacolod and the Calea bakery, famous for the best cakes in the Visayas.',                                       4.1),
('RedDoorz @ Lopue\'s East Bacolod',        'Bacolod',       'Lacson Street Extension, Mandalagan, Bacolod City',             'Northern Bacolod property near Lopue\'s East commercial area and the scenic Bacolod-Silay countryside.',                                      3.9),

-- =====================================================
-- INDONESIA
-- =====================================================

-- Bali — Kuta / Legian
('RedDoorz @ Benesari Legian Bali',         'Bali',        'Jl. Benesari, Lebak Bene, Legian, Kuta, Bali 80361',            'Walking distance from Legian Beach and Double Six Beach. Situated in the heart of Bali\'s most popular resort strip.',                           4.3),
('RedDoorz @ Raya Kuta Bali',               'Bali',        'Jl. Raya Kuta No. 20X, Kuta, Bali 80361',                       'Prime Kuta location on the main road. Easy access to Kuta Beach, Beachwalk Shopping Center, and Waterbom Bali.',                              4.2),
('RedDoorz @ Raya Pantai Kuta',             'Bali',        'Jl. Pantai Kuta, Kuta, Bali 80361',                             'Right on the Kuta Beach road. Ideal for surfers and sunset chasers steps from the most famous beach in Bali.',                                4.4),
('RedDoorz @ Raya Kubu Anyar Kuta',         'Bali',        'Jl. Raya Kubu Anyar, Kuta, Bali 80361',                         'Tucked in a quiet gang off the main Kuta road. A peaceful retreat 18 minutes\' walk from Segara Beach.',                                      4.0),
('RedDoorz Plus @ Sunset Road Kerobokan',   'Bali',        'Jl. Sunset Road, Gang Meduri No. 6, Kerobokan Kelod, Bali',     'Located off Sunset Road between Seminyak and Kuta. Great access to both resort strips and Echo Beach in Canggu.',                             4.2),

-- Bali — Seminyak / Canggu
('RedDoorz Plus @ Petitenget Seminyak',     'Bali',        'Jl. Petitenget, Seminyak, Kuta Utara, Bali',                    'Seminyak\'s premium beach club strip. Steps from Potato Head Beach Club, Ku De Ta, and Alila Seminyak.',                                      4.5),
('RedDoorz @ Oberoi Seminyak',              'Bali',        'Jl. Kayu Aya (Oberoi), Seminyak, Kuta, Bali 80361',             'On the fashionable Kayu Aya street lined with designer boutiques, luxury villas, and acclaimed restaurants.',                                  4.4),
('RedDoorz @ Canggu Berawa',                'Bali',        'Jl. Pantai Berawa, Tibubeneng, Canggu, Bali',                   'In the hip Canggu surf town. Close to Berawa Beach, Finn\'s Beach Club, and Canggu\'s famous vegan cafes and coworking spaces.',              4.4),
('RedDoorz Plus @ Echo Beach Canggu',       'Bali',        'Jl. Raya Semat, Tibubeneng, Canggu, Bali',                      'Near Echo Beach, Canggu\'s best surf break. Surrounded by rice paddies, beach clubs, and Bali\'s coolest digital-nomad scene.',              4.5),
('RedDoorz @ Nakula Seminyak',              'Bali',        'Jl. Nakula, Seminyak, Kuta, Bali 80361',                        'Quiet Seminyak side street off Jl. Raya Seminyak. Walking distance to Seminyak Square and the beach pura.',                                   4.1),

-- Bali — Ubud / Gianyar
('RedDoorz Plus @ Monkey Forest Ubud',      'Bali',       'Jl. Monkey Forest, Ubud, Gianyar, Bali 80571',                  'Directly on Monkey Forest Road, Ubud\'s most famous street. Between the Sacred Monkey Forest Sanctuary and Ubud Palace.',                    4.5),
('RedDoorz @ Bisma Street Ubud',            'Bali',       'Jl. Bisma, Ubud, Gianyar, Bali',                                'Perched on the scenic Bisma ridge with jungle-valley views and a short walk to Ubud\'s art galleries and organic restaurants.',               4.4),
('RedDoorz @ Penestanan Ubud',              'Bali',       'Jl. Penestanan, Sayan, Ubud, Gianyar, Bali',                    'Artists\' colony neighborhood west of Ubud center. Surrounded by rice fields and home to Bali\'s most authentic traditional painter workshops.',4.3),
('RedDoorz near Tegallalang Rice Terrace',  'Bali',       'Jl. Raya Tegallalang, Tegallalang, Gianyar, Bali',              'Adjacent to the iconic UNESCO Tegallalang rice terraces. Perfect base for sunrise shots and Balinese highland village walks.',                4.3),
('RedDoorz @ Hanoman Street Ubud',          'Bali',       'Jl. Hanoman, Padang Tegal, Ubud, Gianyar, Bali',                'On the vibrant Hanoman street parallel to Monkey Forest Road, surrounded by cafes, yoga studios, and artisan shops.',                         4.2),

-- Jakarta
('RedDoorz Plus @ Kebayoran Baru Jakarta',  'Jakarta',       'Jl. Raya Kebayoran Lama, Kebayoran Baru, Jakarta Selatan',      'Popular South Jakarta location close to Blok M plaza, Kebayoran fresh market, and the upscale SCBD district.',                               4.2),
('RedDoorz @ Gajah Mada Jakarta',           'Jakarta',       'Jl. Gajah Mada, Petojo Utara, Gambir, Jakarta Pusat',           'On the historic Gajah Mada corridor. Easy access to Glodok (Chinatown), Kota Tua old town, and the National Museum.',                         4.0),
('RedDoorz Plus @ Tebet Jakarta',           'Jakarta',       'Jl. Dr. Saharjo, Tebet, Jakarta Selatan',                       'Trendy Tebet district with excellent cafe culture, vintage shops, and proximity to Kampung Inggris language schools.',                         4.2),
('RedDoorz @ Kelapa Gading Jakarta',        'Jakarta',       'Jl. Boulevard Raya, Kelapa Gading, Jakarta Utara',              'In the prosperous Kelapa Gading district, close to La Piazza mall, AEON Mall, and the Garden Bulevar complex.',                               4.1),
('RedDoorz @ Mangga Dua Jakarta',           'Jakarta',       'Jl. Mangga Dua Raya, Mangga Dua, Jakarta Utara',                'Near the famous Mangga Dua wholesale electronics market and Carrefour. Steps from the Mangga Dua Square shopping center.',                   3.9),
('RedDoorz Premium @ Cipete Jakarta',       'Jakarta',       'Jl. Cipete Raya, Cipete Selatan, Jakarta Selatan',              'In the leafy, expat-friendly Cipete neighborhood known for its excellent restaurants, cafes, and boutique grocery stores.',                   4.3),

-- Bandung
('RedDoorz Premium @ Bandung Station',      'Bandung',       'Jl. Kebon Kawung, Pasir Kaliki, Cicendo, Bandung, Jawa Barat',  'Steps from Bandung Railway Station. Excellent for train travelers exploring Bandung\'s factory outlet scene and culinary trail.',               4.2),
('RedDoorz @ Braga Street Bandung',         'Bandung',       'Jl. Braga, Braga, Sumur Bandung, Bandung, Jawa Barat',          'On the iconic Braga Street, Bandung\'s most elegant colonial boulevard lined with art deco cafes, galleries, and heritage buildings.',         4.3),
('RedDoorz Plus @ Setiabudhi Bandung',      'Bandung',       'Jl. Setiabudhi, Isola, Sukasari, Bandung, Jawa Barat',          'Upper Setiabudhi near Lembang Road factory outlets and the popular De Ranch Lembang highland park.',                                           4.1),
('RedDoorz @ Cihampelas Bandung',           'Bandung',       'Jl. Cihampelas, Cipaganti, Coblong, Bandung, Jawa Barat',       'On the famous Cihampelas jeans street. Walking distance to Cihampelas Walk (CiWalk) mall and Bandung Museum of Geology.',                     4.0),
('RedDoorz @ Pasteur Bandung',              'Bandung',       'Jl. Dr. Djundjunan (Pasteur), Husein Sastranegara, Bandung',    'Close to Husein Sastranegara Airport and the Pasteur factory outlet strip — a must-stop for fashion shopping in Bandung.',                    4.0),

-- Yogyakarta
('RedDoorz Plus @ Sosrowijayan Yogyakarta', 'Yogyakarta',    'Jl. Sosrowijayan, Sosromenduran, Gedong Tengen, Yogyakarta',    'Steps from Malioboro and the bustling Sosrowijayan backpacker street. Perfect base for Borobudur and Prambanan day trips.',                     4.3),
('RedDoorz @ Prawirotaman Yogyakarta',      'Yogyakarta',    'Jl. Prawirotaman, Brontokusuman, Mergangsan, Yogyakarta',       'In the artistic Prawirotaman neighborhood, known for galleries, batik workshops, and international restaurants.',                              4.3),
('RedDoorz Plus @ Kaliurang Yogyakarta',    'Yogyakarta',    'Jl. Kaliurang KM 5, Caturtunggal, Depok, Sleman, Yogyakarta',  'University strip near UGM and UNY. Popular with students, academics, and travelers heading up to Kaliurang nature reserve.',                   4.1),
('RedDoorz @ Dagen Street Yogyakarta',      'Yogyakarta',    'Jl. Dagen, Sosromenduran, Gedong Tengen, Yogyakarta',           'In the heart of the Malioboro-Dagen tourist district. Easy access to Kraton, Taman Sari Water Castle, and Beringharjo Market.',             4.2),
('RedDoorz @ Parangtritis Road Yogyakarta', 'Yogyakarta',    'Jl. Parangtritis, Brontokusuman, Mergangsan, Yogyakarta',       'Along Parangtritis Road heading south toward the famous black-sand beaches of the Indian Ocean coast.',                                       4.0),

-- Surabaya
('RedDoorz @ Darmo Surabaya',               'Surabaya',      'Taman Ketampon No. 25, DR. Soetomo, Tegalsari, Surabaya',       'In the upscale Darmo residential area. Close to Ciputra World mall, Pakuwon Mall, and Surabaya\'s elite golf courses.',                       4.2),
('RedDoorz @ Raya Gubeng Surabaya',         'Surabaya',      'Jl. Sumatra No. 92A, Gubeng, Surabaya, Jawa Timur 60281',       'Near Surabaya Gubeng Railway Station. Ideal for travelers arriving by train and exploring the city center.',                                   4.1),
('RedDoorz Plus @ Surabaya City Center',    'Surabaya',      'Jl. Pemuda, Embong Kaliasin, Genteng, Surabaya',                'Central Pemuda street location close to Tunjungan Plaza, the Surya Hotel, and the Heroes Monument.',                                           4.2),
('RedDoorz @ Mayjen Sungkono Surabaya',     'Surabaya',      'Jl. Dukuh Kupang X No. 20, Dukuh Kupang, Surabaya',            'West Surabaya property near CitraLand Mall and the developing west corridor of the city.',                                                     4.0),
('RedDoorz near Surabaya Town Square',      'Surabaya',      'Jl. Adityawarman, Wonokromo, Surabaya, Jawa Timur',             'Adjacent to Surabaya Town Square (SUTOS) and Royal Plaza. A short trip to the famous Wonokromo flea market.',                                4.0),

-- =====================================================
-- SINGAPORE
-- =====================================================
('RedDoorz @ Aljunied Singapore',           'Singapore',     '34 Lorong 22, Geylang (off Guillemard Road), Singapore 398691','Walking distance from Aljunied MRT on the East-West Line. Access to Geylang food street, Paya Lebar Square, and Kallang sports hub.',           4.1),
('RedDoorz @ Geylang Road Singapore',       'Singapore',     '424 Geylang Road, Geylang, Singapore 389390',                   'On Geylang Road, Singapore\'s famous 24-hour food street. Known for durian stalls, frog porridge, and authentic zi char restaurants.',          4.0),
('RedDoorz Plus @ Little India Singapore',  'Singapore',     'Serangoon Road, Little India, Singapore 218227',                'In vibrant Little India near Mustafa Centre (24-hr shopping), Sri Veeramakaliamman Temple, and Little India MRT.',                            4.2),
('RedDoorz near Marine Parade Singapore',   'Singapore',     'Marine Parade Central, Marine Parade, Singapore 449277',        'East Coast residential area near East Coast Park, Katong\'s Peranakan shophouses, and the Marine Parade hawker center.',                      4.1),
('RedDoorz @ Farrer Park Singapore',        'Singapore',     'Rangoon Road, Farrer Park, Singapore 218451',                   'Between Little India and Novena. A short walk to Farrer Park MRT, City Square Mall, and the Farrer Park sports complex.',                     4.2),
('RedDoorz Plus @ Lavender Singapore',      'Singapore',     'Lavender Street, Kallang, Singapore 338713',                    'Near Lavender MRT and the historic Jalan Besar neighborhood. Walking access to Haji Lane, Arab Street, and Bugis.',                           4.3),
('RedDoorz @ Toa Payoh Singapore',          'Singapore',     'Toa Payoh Lorong 2, Toa Payoh, Singapore 319641',               'In the beloved Toa Payoh HDB estate known for its hawker centers, retro architecture, and central island location.',                          4.0),

-- =====================================================
-- VIETNAM
-- =====================================================

-- Ho Chi Minh City
('RedDoorz @ Bui Vien Walking Street 3',    'Ho Chi Minh City','74 Bui Vien Street, Pham Ngu Lao Ward, District 1, HCMC',    'On the famous Bui Vien walking street, the epicenter of HCMC backpacker life with street food, live music, and budget bars.',                 4.2),
('RedDoorz Near Bui Vien Walking St 2',     'Ho Chi Minh City','168/8 Nguyen Cu Trinh Street, District 1, Ho Chi Minh City', 'One block from the Bui Vien pedestrian zone. Quieter alternative while still in the heart of the Pham Ngu Lao backpacker district.',          4.1),
('RedDoorz @ Cong Hoa Street HCMC',         'Ho Chi Minh City','Cong Hoa Street, Tan Binh District, Ho Chi Minh City',       'Airport-adjacent property 7 minutes from Tan Son Nhat International Airport. Perfect for transit stays and early morning flights.',            4.1),
('RedDoorz Plus @ Nguyen Trai HCMC',        'Ho Chi Minh City','Nguyen Trai Street, District 5, Ho Chi Minh City',           'On Nguyen Trai in Cholon, Ho Chi Minh City\'s Chinatown. Surrounded by dim sum restaurants, temple incense, and textile markets.',             4.2),
('RedDoorz @ Ly Chinh Thang HCMC',          'Ho Chi Minh City','Ly Chinh Thang Street, District 3, Ho Chi Minh City',        'Upscale District 3 property near War Remnants Museum, Reunification Palace, and the Notre-Dame Cathedral of Saigon.',                       4.3),
('RedDoorz @ Pham Ngu Lao HCMC',            'Ho Chi Minh City','Pham Ngu Lao Street, Pham Ngu Lao Ward, District 1, HCMC',  'Heart of the District 1 backpacker triangle, steps from Ben Thanh Market and the Ho Chi Minh City Museum.',                                  4.0),

-- Hanoi
('RedDoorz @ Dinh Liet Old Quarter Hanoi',  'Hanoi',         '36 Dinh Liet Street, Hang Bac Ward, Hoan Kiem District, Hanoi','On the heritage Dinh Liet street of the 1,000-year-old Old Quarter. Near Ngoc Son Temple on Hoan Kiem Lake and the Dong Xuan Market.',        4.3),
('RedDoorz near Vietnam-France Hospital',   'Hanoi',         'Tran Khanh Du Street, Hai Ba Trung District, Hanoi',           'Close to the French-Vietnamese Hospital and the Thien Quang Lake park. Access to the Hanoi Old Quarter within 2km.',                          4.0),
('RedDoorz @ Ta Hien Street Hanoi',         'Hanoi',         'Ta Hien Street, Hoan Kiem District, Hanoi',                    'On the famous Ta Hien beer street, the heart of Hanoi\'s vibrant evening social scene and bia hoi corner culture.',                          4.2),
('RedDoorz Plus @ Hoan Kiem Hanoi',         'Hanoi',         'Dinh Tien Hoang Street, Hoan Kiem District, Hanoi',            'Facing Hoan Kiem Lake, Hanoi\'s most iconic landmark. Walking distance to Ngoc Son Temple, Hanoi Opera House, and the French Quarter.',       4.4),
('RedDoorz @ Hang Bong Hanoi',              'Hanoi',         'Hang Bong Street, Hang Trong Ward, Hoan Kiem District, Hanoi', 'On Hang Bong in the Old Quarter\'s silk and lacquerware street. Near St. Joseph\'s Cathedral and Hoan Kiem Lake.',                           4.2),

-- Da Nang
('RedDoorz Plus @ An Thuong Da Nang',       'Da Nang',       'An Thuong 3 Street, My An Ward, Ngu Hanh Son District, Da Nang','In the expat-popular An Thuong beach enclave. Close to My Khe Beach, Non Nuoc marble mountains, and Hoi An Ancient Town day trips.',        4.3),
('RedDoorz near Thuan Phuoc Bridge',        'Da Nang',       'Tran Hung Dao Street, Thanh Khe District, Da Nang',            'Near the iconic Thuan Phuoc suspension bridge. Great views of the Han River and easy access to Dragon Bridge light shows.',                    4.1),
('RedDoorz Near Han River Bridge Da Nang',  'Da Nang',       'Bach Dang Street, Hai Chau District, Da Nang',                 'On the Bach Dang riverside promenade next to the Han River Swing Bridge. The best vantage point for the Da Nang fireworks festival.',          4.3),
('RedDoorz near My Khe Beach Da Nang',      'Da Nang',       'Vo Nguyen Giap Street, Phuoc My Ward, Son Tra District, Da Nang','Directly on the My Khe beach road, rated by Forbes as one of Asia\'s most beautiful beaches. Walk to the sea in under a minute.',           4.4),
('RedDoorz @ Tran Phu Da Nang',             'Da Nang',       'Tran Phu Street, Hai Chau District, Da Nang',                  'Downtown Da Nang on Tran Phu near the Da Nang Museum, Cham Sculpture Museum, and the city\'s best pho and banh mi stalls.',                   4.1),

-- Nha Trang
('RedDoorz near Nha Trang Beach',           'Nha Trang',     '98A Tran Phu Street, Loc Tho Ward, Nha Trang, Khanh Hoa',     'Beachfront Tran Phu boulevard location steps from Nha Trang\'s 6km main beach strip, scuba diving operators, and vinpearl cable car.',       4.3),
('RedDoorz near Tran Phu Street Nha Trang', 'Nha Trang',     'Tran Phu Street, Nha Trang City, Khanh Hoa Province',         'On the Nha Trang beachfront road. Walking distance to Nha Trang Night Market, Sailing Club beach bar, and snorkeling boat tours.',           4.2),
('RedDoorz near Hung Vuong Nha Trang',      'Nha Trang',     'Hung Vuong Street, Nha Trang City, Khanh Hoa Province',        'Two blocks from the beach on the busy Hung Vuong commercial street. Near Po Nagar Cham Towers and the Long Son Pagoda.',                       4.0),
('RedDoorz Plus @ Biet Thu Nha Trang',      'Nha Trang',     'Biet Thu Street, Nha Trang City, Khanh Hoa Province',          'On the famous Biet Thu restaurant and seafood street. Perfect for evenings of grilled seafood, fresh lobster, and bia hoi.',                  4.2),
('RedDoorz @ Nguyen Thien Thuat Nha Trang', 'Nha Trang',     'Nguyen Thien Thuat Street, Nha Trang, Khanh Hoa',              'In the backpacker heart of Nha Trang close to the central bus station, local diving schools, and boat-trip operators.',                       3.9),

-- Hoi An
('RedDoorz @ Cua Dai Road Hoi An',          'Hoi An',        'Cua Dai Road, Hoi An City, Quang Nam Province',                'Along the road connecting Hoi An Ancient Town to Cua Dai Beach. Bike to the beach or walk to lantern-lit streets in minutes.',               4.4),
('RedDoorz Plus @ Phan Chu Trinh Hoi An',   'Hoi An',        'Phan Chu Trinh Street, Hoi An City, Quang Nam Province',       'Steps from the UNESCO Ancient Town entrance. Surrounded by tailor shops, silk lanterns, and the Thu Bon riverside market.',                   4.5),
('RedDoorz @ Bach Dang Riverside Hoi An',   'Hoi An',        'Bach Dang Street, Hoi An City, Quang Nam Province',            'Along the scenic Thu Bon River promenade. Watch lantern boats float by from the balcony of this riverside Hoi An property.',                  4.4),
('RedDoorz near An Bang Beach Hoi An',      'Hoi An',        'An Bang Village, Cam An Ward, Hoi An City, Quang Nam',         'Minutes from the bohemian An Bang Beach, a quieter alternative to Cua Dai. Surrounded by thatch-roof beach clubs.',                           4.3),
('RedDoorz @ Tran Hung Dao Hoi An',         'Hoi An',        'Tran Hung Dao Street, Hoi An City, Quang Nam Province',        'Hoi An city center property a short walk from the covered Japanese Bridge and the Ancient Town\'s UNESCO heritage streets.',                   4.2),

-- Phu Quoc
('RedDoorz @ Tran Hung Dao Phu Quoc',       'Phu Quoc',      'Tran Hung Dao Street, Duong Dong Town, Phu Quoc, Kien Giang',  'On the main commercial street of Duong Dong. Walking distance to Phu Quoc Night Market, Dinh Cau Night Market, and Long Beach.',            4.3),
('RedDoorz Plus @ Long Beach Phu Quoc',     'Phu Quoc',      'Tran Hung Dao Street, Long Beach, Phu Quoc Island',            'Directly facing Long Beach (Bai Truong), the longest and most popular beach on Phu Quoc Island. Stunning sunsets guaranteed.',                 4.5),
('RedDoorz near Duong Dong Market',         'Phu Quoc',      'Bach Dang Street, Duong Dong, Phu Quoc, Kien Giang',           'Next to the famous Duong Dong market where locals sell fresh seafood, Phu Quoc fish sauce, and black pepper by the kilo.',                   4.1),
('RedDoorz @ Ong Lang Beach Phu Quoc',      'Phu Quoc',      'Ong Lang Beach Road, Cua Can, Phu Quoc, Kien Giang',           'On the secluded Ong Lang Beach in northern Phu Quoc, away from crowds. A perfect eco-resort base for nature-lovers.',                         4.3),
('RedDoorz Plus @ Phu Quoc United Center',  'Phu Quoc',      'Bai Dai Beach Road, Ganh Dau, Phu Quoc, Kien Giang',           'Near the massive Phu Quoc United Center entertainment complex, VinWonders, and the stunning Bai Dai white sand beach.',                       4.4);


-- =====================================================
-- ROOMS for all new real hotels
-- =====================================================

-- Helper procedure: insert standard 2-3 room types per hotel
-- Philippines — Manila cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Comfortable room with queen bed, AC, free WiFi, and cable TV.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ San Marcelino Malate';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Spacious deluxe with upgraded amenities in Malate.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ San Marcelino Malate';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Budget-friendly room steps from Robinsons Place Manila.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Robinsons Place Manila';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Upgraded room with double bed and city views.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Robinsons Place Manila';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Clean Binondo room immersed in Chinatown heritage.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Chinatown Binondo';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded deluxe in the heart of the world\'s oldest Chinatown.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Chinatown Binondo';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'No-frills budget room near SM Manila and Manila Ocean Park.' FROM Hotels WHERE Hotel_Name='RedDoorz Near SM City Manila';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Twin Room',       849.00, 2, 'Twin-bed room ideal for friends or family traveling together.' FROM Hotels WHERE Hotel_Name='RedDoorz Near SM City Manila';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Affordable Malate room close to Manila Bay sunset spots.' FROM Hotels WHERE Hotel_Name='RedDoorz @ The Providence Tower Malate';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Bright deluxe room in the Manila arts and dining belt.' FROM Hotels WHERE Hotel_Name='RedDoorz @ The Providence Tower Malate';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Airport-transit room with free early check-in options.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Seventy Five Inn';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded NAIA-adjacent room with complimentary shuttle.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Seventy Five Inn';

-- QC cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Timog Avenue room in Quezon City\'s dining and entertainment hub.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Timog Avenue Quezon City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded Timog room near TV studios and Scout area restaurants.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Timog Avenue Quezon City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Premium standard in the quiet West Avenue district.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ West Avenue QC';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Spacious deluxe with lounge access in West QC.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ West Avenue QC';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          2199.00, 2, 'Executive suite with separate work and sleeping areas.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ West Avenue QC';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Modern room near SM Fairview and La Mesa Eco Park.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ SMDC Fairview';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded deluxe in the growing Fairview district.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ SMDC Fairview';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Affordable Commonwealth room near the Batasan Complex.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Manila Venetian Quezon City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Twin Room',       899.00, 2, 'Twin room ideal for students visiting QC universities.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Manila Venetian Quezon City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Budget room near Fernwoods Garden events venue.' FROM Hotels WHERE Hotel_Name='RedDoorz near Fernwoods Garden QC';

-- Makati cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Bel-Air standard, 5 minutes to Bonifacio High Street.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Danlig Street Makati';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Upgraded Bel-Air deluxe with premium bedding.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Danlig Street Makati';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Value room on Osmeña Highway near Magallanes MRT.' FROM Hotels WHERE Hotel_Name='RedDoorz near Osmena Highway Makati';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded room with Makati CBD walkability.' FROM Hotels WHERE Hotel_Name='RedDoorz near Osmena Highway Makati';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'San Antonio Village room near Greenbelt and Glorietta.' FROM Hotels WHERE Hotel_Name='RedDoorz @ San Antonio Village Makati';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Deluxe walking distance to Ayala Triangle gardens.' FROM Hotels WHERE Hotel_Name='RedDoorz @ San Antonio Village Makati';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Guadalupe standard near MRT Magallanes and Chino Roces.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ La Belle Residences';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Bright deluxe in the La Belle Residences complex.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ La Belle Residences';

-- Cebu cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Jones Avenue standard with access to rooftop pool.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Jones Avenue Cebu';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded deluxe near Magellan\'s Cross and Colon Street.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Jones Avenue Cebu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Mabolo standard near Ayala Center Cebu.' FROM Hotels WHERE Hotel_Name='RedDoorz @ MJ Cuenco Avenue Cebu';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded Mabolo room with Ayala mall walking access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ MJ Cuenco Avenue Cebu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Capitol Site standard near Cebu hospitals and Fuente.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Escario Street Cebu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Lahug hillside room with panoramic Cebu City views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Nivel Hills Cebu';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Elevated deluxe near Tops Lookout and IT Park.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Nivel Hills Cebu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Budget room near the South Road Properties corridor.' FROM Hotels WHERE Hotel_Name='RedDoorz @ N Bacalso Avenue Cebu';

-- Davao cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Poblacion standard in Davao\'s historic core.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Poblacion Davao';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Upgraded room near Davao waterfront and Rizal Park.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Poblacion Davao';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Bajada standard near Gaisano Mall and Victoria Plaza.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Traveler\'s Inn Bajada';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded Bajada room with easy durian market access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Traveler\'s Inn Bajada';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Budget Agdao room near Bankerohan Public Market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ General Luna Davao';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Central room near Ateneo de Davao University.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Malvar Street Davao';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded Malvar deluxe with Davao transport hub access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Malvar Street Davao';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Ecoland standard near Abreeza and SM Davao malls.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Quimpo Boulevard Davao';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded Quimpo room in the Davao Ecoland district.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Quimpo Boulevard Davao';

-- Baguio cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Mines View standard with panoramic Cordillera views.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Mines View Park';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Highland deluxe with extra blankets and mountain fog ambiance.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Mines View Park';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Aurora Hill standard with Night Market and Session Road access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Aurora Hill Baguio';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Upgraded Aurora Hill deluxe with city view terrace.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Aurora Hill Baguio';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Harrison Road standard steps from Baguio Night Market.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Baguio Night Market';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Park-view room overlooking Burnham Lake and rose gardens.' FROM Hotels WHERE Hotel_Name='RedDoorz near Burnham Park Baguio';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    1499.00, 4, 'Family room with highland views and two double beds.' FROM Hotels WHERE Hotel_Name='RedDoorz near Burnham Park Baguio';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Easter Road standard between Camp John Hay and the gardens.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Easter Road Baguio';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Twin Room',      1099.00, 2, 'Twin-bed room ideal for friends exploring Baguio highlands.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Easter Road Baguio';

-- Puerto Princesa cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Manalo Ext. standard, launchpad for Underground River tours.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Manalo Extension Palawan';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded Palawan deluxe with Honda Bay island access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Manalo Extension Palawan';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Rizal Ave standard near the Palawan Heritage Center.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Rizal Avenue Palawan';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Upgraded room on Palawan\'s main commercial strip.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Rizal Avenue Palawan';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Airport-proximity standard for easy Palawan departures.' FROM Hotels WHERE Hotel_Name='RedDoorz near Puerto Airport Palawan';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Recaido Road room in a peaceful Puerto Princesa street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Recaido Road Puerto Princesa';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Budget city-center room with free WiFi in Puerto Princesa.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Malvar Street Puerto Princesa';

-- Iloilo cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Airport-adjacent room for Guimaras Island trips.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Iloilo Airport';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Iznart standard near SM City Iloilo and Molo Church.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Iznart Street Iloilo';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded Iznart deluxe in the heart of Iloilo City.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Iznart Street Iloilo';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Diversion Road standard near Gaisano Capital Mall.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Diversion Road Iloilo';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded room near the Smallville entertainment complex.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Diversion Road Iloilo';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Fuentes Drive room near the Iloilo Convention Center.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Fuentes Drive Iloilo';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Heritage district stay near Fort San Pedro Iloilo.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Fort San Pedro Iloilo';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded heritage-area deluxe with cultural surroundings.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Fort San Pedro Iloilo';

-- Boracay cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1699.00, 2, 'D\'Mall standard in the busiest shopping strip of Boracay.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Sir G Hotel D\'Mall Boracay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2299.00, 2, 'Upgraded D\'Mall deluxe with White Beach access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Sir G Hotel D\'Mall Boracay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Quiet Station 3 room for a relaxed Boracay experience.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Station 3 Boracay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    2199.00, 4, 'Station 3 family room perfect for beach family getaways.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Station 3 Boracay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1799.00, 2, 'Station 1 premium room steps from Willy\'s Rock landmark.' FROM Hotels WHERE Hotel_Name='RedDoorz near Willy\'s Rock Boracay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2499.00, 2, 'Deluxe room on the most pristine section of White Beach.' FROM Hotels WHERE Hotel_Name='RedDoorz near Willy\'s Rock Boracay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Caticlan jetty stopover room for Boracay crossings.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Tulubhan Road Boracay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2699.00, 2, 'Premium beachfront deluxe directly on the White Beach path.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Beachfront Boracay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          3999.00, 2, 'Beachfront suite with sunset terrace and priority loungers.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Beachfront Boracay';

-- Bacolod cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Downtown Bacolod room near City Plaza and MassKara grounds.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Lacson-Rizal Bacolod';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Goldenfield standard near Robinsons Place Bacolod.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Goldenfield Bacolod';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded Goldenfield deluxe with Bacolod nightlife access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Goldenfield Bacolod';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Burgos Street budget option near SM City Bacolod.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Burgos Street Bacolod';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'SM-adjacent room near Calea, Bacolod\'s famous bakery.' FROM Hotels WHERE Hotel_Name='RedDoorz near SM City Bacolod';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Upgraded room walking distance from SM City Bacolod.' FROM Hotels WHERE Hotel_Name='RedDoorz near SM City Bacolod';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Northern Bacolod budget room near Lopue\'s East complex.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Lopue\'s East Bacolod';

-- Indonesia — Bali Kuta/Legian cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Legian standard steps from Legian Beach and surf schools.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Benesari Legian Bali';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded Legian room near Double Six Beach sunset bars.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Benesari Legian Bali';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1149.00, 2, 'Main Kuta road standard near Beachwalk and Waterbom.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Kuta Bali';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1549.00, 2, 'Upgraded Kuta room with pool access and surf-shop strip.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Kuta Bali';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1249.00, 2, 'Beach-road standard for surfers on Kuta\'s famous break.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Pantai Kuta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1749.00, 2, 'Deluxe ocean-facing room with Kuta sunset views.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Pantai Kuta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Quiet gang room 18 minutes\' walk from Segara Beach.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Kubu Anyar Kuta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Sunset Road standard between Seminyak and Canggu.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Sunset Road Kerobokan';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Upgraded Kerobokan room near Echo Beach and rice fields.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Sunset Road Kerobokan';

-- Bali Seminyak/Canggu cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1599.00, 2, 'Petitenget standard steps from Bali\'s top beach clubs.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Petitenget Seminyak';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2199.00, 2, 'Upgraded Petitenget deluxe with poolside access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Petitenget Seminyak';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Kayu Aya standard on Seminyak\'s designer boutique street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Oberoi Seminyak';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Upgraded Oberoi deluxe among acclaimed Seminyak restaurants.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Oberoi Seminyak';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Canggu Berawa standard near beach clubs and surf breaks.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Canggu Berawa';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Upgraded Berawa deluxe with Finn\'s Beach Club access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Canggu Berawa';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Echo Beach standard for Canggu\'s best surf and cafes.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Echo Beach Canggu';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2099.00, 2, 'Deluxe rice-paddy view room in the Canggu nomad scene.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Echo Beach Canggu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Quiet Nakula side-street standard near Seminyak Square.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Nakula Seminyak';

-- Bali Ubud cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Monkey Forest Road standard between sanctuary and Ubud Palace.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Monkey Forest Ubud';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1999.00, 2, 'Jungle-view deluxe with bamboo furnishings in Ubud center.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Monkey Forest Ubud';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Bisma ridge standard overlooking Ubud\'s jungle valley.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bisma Street Ubud';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded valley-view deluxe near Ubud organic restaurants.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bisma Street Ubud';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Penestanan artists\' colony room among painter workshops.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Penestanan Ubud';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Tegallalang standard for sunrise rice-terrace photography.' FROM Hotels WHERE Hotel_Name='RedDoorz near Tegallalang Rice Terrace';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded terrace-view deluxe with Bali highland scenery.' FROM Hotels WHERE Hotel_Name='RedDoorz near Tegallalang Rice Terrace';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1149.00, 2, 'Hanoman Street standard surrounded by Ubud cafes and yoga.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hanoman Street Ubud';

-- Jakarta cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'South Jakarta standard in the upscale Kebayoran area.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Kebayoran Baru Jakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded Kebayoran deluxe near Blok M and SCBD.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Kebayoran Baru Jakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Historic Gajah Mada standard near Kota Tua old town.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Gajah Mada Jakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Tebet standard in Jakarta\'s trendiest cafe district.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Tebet Jakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1599.00, 2, 'Upgraded Tebet deluxe near vintage shops and food parks.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Tebet Jakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Kelapa Gading standard near AEON Mall and La Piazza.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Kelapa Gading Jakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Mangga Dua standard near wholesale electronics markets.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Mangga Dua Jakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Leafy Cipete standard in Jakarta\'s expat-friendly south.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Cipete Jakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Premium Cipete deluxe near boutique cafes and restaurants.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Cipete Jakarta';

-- Bandung cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Station-adjacent standard for train travelers to Bandung.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Bandung Station';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Upgraded station room near Bandung factory outlets.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Bandung Station';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Colonial Braga standard in Bandung\'s most elegant street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Braga Street Bandung';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1399.00, 2, 'Art deco-adjacent deluxe on iconic Braga Boulevard.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Braga Street Bandung';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Setiabudhi standard near Lembang highland outlets.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Setiabudhi Bandung';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Cihampelas jeans-street standard near CiWalk mall.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cihampelas Bandung';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Twin Room',       999.00, 2, 'Twin room for shoppers on Bandung\'s factory outlet trail.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cihampelas Bandung';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Pasteur airport-adjacent standard near outlet shopping.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Pasteur Bandung';

-- Yogyakarta cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Sosrowijayan standard on Yogya\'s famous backpacker street.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Sosrowijayan Yogyakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Upgraded deluxe near Malioboro and Kraton Palace.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Sosrowijayan Yogyakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Prawirotaman standard in Yogya\'s arts and gallery district.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Prawirotaman Yogyakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1399.00, 2, 'Upgraded room surrounded by batik workshops and galleries.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Prawirotaman Yogyakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'University strip standard near UGM and Kaliurang park.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Kaliurang Yogyakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Dagen standard steps from Malioboro\'s souvenir strip.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Dagen Street Yogyakarta';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Upgraded Dagen deluxe near Taman Sari Water Castle.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Dagen Street Yogyakarta';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Parangtritis road standard toward Yogya\'s black-sand beaches.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Parangtritis Road Yogyakarta';

-- Surabaya cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Darmo standard in upscale South Surabaya.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Darmo Surabaya';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Upgraded Darmo deluxe near Ciputra World mall.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Darmo Surabaya';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Gubeng train-station standard for inter-city travelers.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Raya Gubeng Surabaya';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'City center standard near Tunjungan Plaza and monuments.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Surabaya City Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Upgraded Pemuda deluxe in the heart of Surabaya CBD.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Surabaya City Center';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1049.00, 2, 'West Surabaya standard near CitraLand Mall.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Mayjen Sungkono Surabaya';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1049.00, 2, 'SUTOS-adjacent standard near Wonokromo flea market.' FROM Hotels WHERE Hotel_Name='RedDoorz near Surabaya Town Square';

-- Singapore cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2799.00, 2, 'Geylang standard near Aljunied MRT and hawker centers.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Aljunied Singapore';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    3499.00, 2, 'Upgraded Geylang deluxe with 24-hour food street access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Aljunied Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2599.00, 2, 'Geylang Road standard on Singapore\'s iconic food street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Geylang Road Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2999.00, 2, 'Little India standard near Mustafa Centre 24hr shopping.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Little India Singapore';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    3899.00, 2, 'Upgraded Little India deluxe with Sri Veeramakaliamman views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Little India Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2799.00, 2, 'Marine Parade standard near East Coast Park cycling trail.' FROM Hotels WHERE Hotel_Name='RedDoorz near Marine Parade Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2899.00, 2, 'Farrer Park standard near City Square Mall and MRT.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Farrer Park Singapore';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    3699.00, 2, 'Upgraded Farrer Park deluxe near Novena medical strip.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Farrer Park Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  3199.00, 2, 'Lavender Street standard near Haji Lane and Arab Street.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Lavender Singapore';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    4199.00, 2, 'Upgraded Lavender deluxe with Bugis and Bras Basah access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Lavender Singapore';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2499.00, 2, 'Toa Payoh standard in a beloved heritage HDB neighborhood.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Toa Payoh Singapore';

-- Vietnam — HCMC cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Bui Vien standard on the famous walking street of Saigon.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bui Vien Walking Street 3';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded Bui Vien deluxe with live music strip access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bui Vien Walking Street 3';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Quiet backpacker-district standard one block from Bui Vien.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Bui Vien Walking St 2';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Airport-adjacent standard 7 min from Tan Son Nhat.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cong Hoa Street HCMC';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded Tan Binh deluxe with complimentary airport transfer.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cong Hoa Street HCMC';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Cholon Chinatown standard surrounded by dim sum culture.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Nguyen Trai HCMC';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1349.00, 2, 'Upgraded District 5 deluxe near Binh Tay Market.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Nguyen Trai HCMC';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Upscale District 3 standard near War Remnants Museum.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ly Chinh Thang HCMC';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded District 3 deluxe near Reunification Palace.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ly Chinh Thang HCMC';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Pham Ngu Lao standard steps from Ben Thanh Market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Pham Ngu Lao HCMC';

-- Hanoi cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Old Quarter standard steps from Hoan Kiem Lake.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Dinh Liet Old Quarter Hanoi';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Heritage deluxe on the 1,000-year-old Dinh Liet street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Dinh Liet Old Quarter Hanoi';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Hai Ba Trung standard near the French-Vietnamese Hospital.' FROM Hotels WHERE Hotel_Name='RedDoorz near Vietnam-France Hospital';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Ta Hien beer street standard, the heart of Hanoi evenings.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ta Hien Street Hanoi';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Hoan Kiem lakefront standard with iconic temple views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Hoan Kiem Hanoi';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded lake-view deluxe near Hanoi Opera House.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Hoan Kiem Hanoi';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Hang Bong silk-street standard near St. Joseph\'s Cathedral.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hang Bong Hanoi';

-- Da Nang cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'An Thuong beach-enclave standard near My Khe Beach.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ An Thuong Da Nang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded An Thuong deluxe near Marble Mountains.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ An Thuong Da Nang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Thanh Khe standard near the Thuan Phuoc Bridge.' FROM Hotels WHERE Hotel_Name='RedDoorz near Thuan Phuoc Bridge';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Bach Dang riverside standard with Han River bridge views.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Han River Bridge Da Nang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1599.00, 2, 'Upgraded riverside deluxe ideal for Da Nang fireworks season.' FROM Hotels WHERE Hotel_Name='RedDoorz Near Han River Bridge Da Nang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'My Khe beachfront standard on Forbes\' top Asia beach.' FROM Hotels WHERE Hotel_Name='RedDoorz near My Khe Beach Da Nang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Sea-facing deluxe steps from My Khe Beach boardwalk.' FROM Hotels WHERE Hotel_Name='RedDoorz near My Khe Beach Da Nang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Downtown Da Nang standard near the Cham Sculpture Museum.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Tran Phu Da Nang';

-- Nha Trang cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Tran Phu beachfront standard on Nha Trang\'s 6km strip.' FROM Hotels WHERE Hotel_Name='RedDoorz near Nha Trang Beach';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Sea-view deluxe with vinpearl cable-car visibility.' FROM Hotels WHERE Hotel_Name='RedDoorz near Nha Trang Beach';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1149.00, 2, 'Boulevard standard near Nha Trang Night Market.' FROM Hotels WHERE Hotel_Name='RedDoorz near Tran Phu Street Nha Trang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Hung Vuong standard near Po Nagar Cham Towers.' FROM Hotels WHERE Hotel_Name='RedDoorz near Hung Vuong Nha Trang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Biet Thu seafood-street standard for lobster evenings.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Biet Thu Nha Trang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1599.00, 2, 'Upgraded Biet Thu deluxe near the Sailing Club beach bar.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Biet Thu Nha Trang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Backpacker central standard near Nha Trang diving schools.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Nguyen Thien Thuat Nha Trang';

-- Hoi An cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Cua Dai road standard between Ancient Town and the beach.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cua Dai Road Hoi An';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Upgraded Cua Dai deluxe for cycling between beach and town.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cua Dai Road Hoi An';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Ancient Town entrance standard near silk and lantern shops.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Phan Chu Trinh Hoi An';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2099.00, 2, 'Heritage-style deluxe with Thu Bon river market access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Phan Chu Trinh Hoi An';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1449.00, 2, 'Bach Dang riverside standard with lantern boat views.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bach Dang Riverside Hoi An';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1999.00, 2, 'Upgraded promenade deluxe watching lanterns float by.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bach Dang Riverside Hoi An';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'An Bang beach standard in a bohemian thatch-roof village.' FROM Hotels WHERE Hotel_Name='RedDoorz near An Bang Beach Hoi An';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1349.00, 2, 'Tran Hung Dao standard near the Japanese Covered Bridge.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Tran Hung Dao Hoi An';

-- Phu Quoc cluster
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Duong Dong standard near Phu Quoc Night Market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Tran Hung Dao Phu Quoc';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Upgraded Duong Dong deluxe with night market street access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Tran Hung Dao Phu Quoc';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1599.00, 2, 'Long Beach standard on Phu Quoc\'s longest sand strip.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Long Beach Phu Quoc';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2299.00, 2, 'Beachfront deluxe with guaranteed Phu Quoc sunset views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Long Beach Phu Quoc';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          3499.00, 2, 'Beachfront suite with hammock garden and private loungers.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Long Beach Phu Quoc';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Market-side standard for fresh seafood and fish-sauce shopping.' FROM Hotels WHERE Hotel_Name='RedDoorz near Duong Dong Market';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Ong Lang eco-beach standard for nature-lovers in north PQ.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ong Lang Beach Phu Quoc';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2099.00, 2, 'Secluded Ong Lang deluxe away from resort-area crowds.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ong Lang Beach Phu Quoc';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1699.00, 2, 'Bai Dai standard near VinWonders and United Center.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Phu Quoc United Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2399.00, 2, 'Upgraded deluxe on the white sand of Bai Dai Beach.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Phu Quoc United Center';
