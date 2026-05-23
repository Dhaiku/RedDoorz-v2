-- =====================================================
-- RedDoorz Additional Hotels Seed
-- Covers: Philippines, Indonesia, Singapore, Vietnam
-- Safe to run independently (does NOT recreate tables)
-- =====================================================

USE reddoorz;

-- =====================================================
-- PHILIPPINES
-- =====================================================
INSERT INTO Hotels (Hotel_Name, Hotel_City, Hotel_Address, Hotel_Description, Hotel_Rating) VALUES

-- Metro Manila
('RedDoorz @ Ermita Manila',        'Manila',        'M.H. Del Pilar Street, Ermita, Manila',              'Steps from Manila Bay and Rizal Park. A great base for exploring Intramuros and the historic heart of Manila.',                    4.1),
('RedDoorz Plus @ Ortigas Center',  'Pasig',         'Julia Vargas Avenue, Ortigas Center, Pasig',          'Modern property in the bustling Ortigas CBD. Close to major malls, restaurants, and the financial district.',                  4.3),
('RedDoorz @ Pasay Roxas Boulevard','Pasay',         'Roxas Boulevard, Pasay City, Metro Manila',           'Seafront location along Roxas Boulevard with easy access to the airport and cultural landmarks.',                            4.0),
('RedDoorz Premium @ Alabang',      'Muntinlupa',    'Filinvest Avenue, Alabang, Muntinlupa',               'Upscale property in the Alabang business district. Ideal for corporate travelers and families visiting the south.',           4.4),

-- Luzon
('RedDoorz @ Baguio City Center',   'Baguio',        'Session Road, Baguio City, Benguet',                  'Nestled in the cool mountain air of Baguio. Walking distance to Burnham Park, Night Market, and SM Baguio.',                 4.3),
('RedDoorz @ Baguio Burnham',       'Baguio',        'Jose Abad Santos Drive, Baguio City',                 'Overlooks Burnham Park with fresh highland air. Perfect for families and weekend getaways to the summer capital.',           4.2),
('RedDoorz @ Subic Bay',            'Olongapo',      'Rizal Highway, Subic Bay Freeport Zone',              'Set inside the Subic Bay Freeport with access to beaches, duty-free shopping, and adventure activities.',                   4.1),
('RedDoorz @ Clark Pampanga',       'Angeles',       'Don Juico Avenue, Clark Freeport Zone, Pampanga',     'Located in the Clark Freeport, close to the international airport. Great for stopovers and business travelers.',            4.0),
('RedDoorz @ Ilocos Norte',         'Laoag',         'General Segundo Avenue, Laoag City, Ilocos Norte',    'Gateway to the windmills, Bangui Bay, and the heritage towns of Ilocos Norte.',                                            4.0),

-- Visayas
('RedDoorz @ Cebu IT Park',         'Cebu City',     'Salinas Drive, Lahug, Cebu City',                     'Located inside the Cebu IT Park. Modern rooms with fast WiFi, popular with remote workers and BPO professionals.',          4.4),
('RedDoorz @ Mactan Cebu',          'Lapu-Lapu',     'M.L. Quezon National Highway, Mactan, Lapu-Lapu City','Minutes from Mactan-Cebu International Airport. Great for early departures, island hopping, and beach trips.',             4.2),
('RedDoorz @ Iloilo City Center',   'Iloilo City',   'General Luna Street, Iloilo City',                    'In the heart of the City of Love. Close to the esplanade, heritage churches, and the famous Dinagyang Festival grounds.',  4.2),
('RedDoorz @ Bacolod City',         'Bacolod',       'Lacson Street, Bacolod City, Negros Occidental',      'MassKara City hotel near the central business district, popular restaurants, and the famous Bacolod plaza.',               4.1),
('RedDoorz @ Boracay Station 2',    'Boracay',       'Station 2, White Beach, Boracay Island, Aklan',       'Right on White Beach Station 2, the liveliest stretch. Surrounded by beach bars, restaurants, and watersports rentals.',   4.5),
('RedDoorz @ Palawan Puerto',       'Puerto Princesa','National Highway, Puerto Princesa, Palawan',          'Starting point for the Underground River tour, Honda Bay, and the best of pristine Palawan.',                             4.3),

-- Mindanao
('RedDoorz @ Davao Lanang',         'Davao',         'J.P. Laurel Avenue, Lanang, Davao City',              'Upscale area near SM Lanang Premier and the waterfront. Great access to Samal Island and Mt. Apo excursions.',              4.3),
('RedDoorz @ Cagayan de Oro City',  'Cagayan de Oro','Corrales Avenue, Cagayan de Oro City, Misamis Oriental','Gateway to white-water rafting on the Cagayan River and the scenic highlands of Bukidnon.',                              4.1),
('RedDoorz @ General Santos City',  'General Santos', 'Pioneer Avenue, General Santos City, South Cotabato', 'In the Tuna Capital of the Philippines. Ideal for business travelers and seafood lovers exploring the region.',             3.9),

-- =====================================================
-- INDONESIA
-- =====================================================

-- Jakarta
('RedDoorz @ Jakarta Sudirman',     'Jakarta',       'Jl. Jendral Sudirman, Senayan, Jakarta Pusat',        'Prime location in the Golden Triangle business district. Steps from Sudirman MRT and major shopping centers.',               4.3),
('RedDoorz Plus @ Jakarta Thamrin', 'Jakarta',       'Jl. M.H. Thamrin, Menteng, Jakarta Pusat',            'Iconic location along Thamrin with easy access to the National Monument, grand mosques, and luxury malls.',                 4.4),
('RedDoorz @ Jakarta Kemang',       'Jakarta',       'Jl. Kemang Raya, Kemang, Jakarta Selatan',            'Trendy Kemang neighborhood known for its cafes, art galleries, and expat-friendly vibe.',                                  4.2),
('RedDoorz @ Jakarta Airport',      'Tangerang',     'Jl. Husein Sastranegara, Tangerang, Banten',           'Closest RedDoorz to Soekarno-Hatta International Airport. Ideal for transit stays and early morning flights.',             4.0),

-- Bali
('RedDoorz @ Bali Kuta Beach',      'Bali',        'Jl. Pantai Kuta, Kuta, Bali',                         'Steps from the famous Kuta Beach. Perfect for surfers, shoppers, and travelers soaking in Bali\'s vibrant nightlife.',      4.5),
('RedDoorz Plus @ Bali Seminyak',   'Bali',        'Jl. Laksmana, Seminyak, Bali',                        'Seminyak\'s stylish strip of boutiques, rooftop bars, and sunset beach clubs. Bali\'s most cosmopolitan neighborhood.',    4.6),
('RedDoorz @ Bali Ubud',            'Bali',       'Jl. Monkey Forest, Ubud, Gianyar, Bali',              'In the cultural heart of Bali, surrounded by rice terraces, temples, and traditional dance performances.',                4.5),
('RedDoorz @ Bali Sanur',           'Denpasar',      'Jl. Danau Tamblingan, Sanur, Denpasar, Bali',         'Calm and family-friendly Sanur with a beautiful beachfront promenade and proximity to Nusa Lembongan ferries.',            4.3),

-- Java & Others
('RedDoorz @ Bandung Dago',         'Bandung',       'Jl. Ir. H. Juanda (Dago), Bandung, Jawa Barat',       'Dago is Bandung\'s most popular street for fashion shopping, factory outlets, and cafe culture.',                          4.3),
('RedDoorz @ Yogyakarta Malioboro', 'Yogyakarta',    'Jl. Malioboro, Gedongtengen, Yogyakarta',              'On the iconic Malioboro street, minutes from Kraton Palace, Borobudur, and Prambanan temples.',                           4.4),
('RedDoorz @ Surabaya City',        'Surabaya',      'Jl. Pemuda, Genteng, Surabaya, Jawa Timur',           'In the heart of Indonesia\'s second-largest city. Walking distance to Tunjungan Plaza and Heroes Monument.',               4.1),
('RedDoorz @ Lombok Senggigi',      'Lombok',        'Jl. Raya Senggigi, Senggigi, Lombok Barat, NTB',      'Senggigi beachfront with views of Bali\'s Mount Agung. Great base for Gili Islands trips and Rinjani trekking.',           4.2),

-- =====================================================
-- SINGAPORE
-- =====================================================
('RedDoorz @ Singapore Orchard',    'Singapore',     '442 Orchard Road, Singapore 238879',                  'Prime Orchard Road location steps from ION Orchard, Takashimaya, and Singapore\'s best dining and shopping.',             4.6),
('RedDoorz Plus @ Singapore Clarke Quay','Singapore','3 River Valley Road, Clarke Quay, Singapore 179024',   'Vibrant Clarke Quay riverside known for its nightlife, restaurants, and easy access to Chinatown and Marina Bay.',        4.5),
('RedDoorz @ Singapore Bugis',      'Singapore',     '200 Victoria Street, Bugis, Singapore 188024',         'Central Bugis location near Bugis Junction, Arab Street, and a short MRT ride to Marina Bay Sands.',                     4.4),
('RedDoorz @ Singapore Jurong East','Singapore',     '1 Jurong East Street 11, Jurong East, Singapore 609774','Convenient Jurong East hub for business and leisure. Close to Westgate, JEM, and cross-border bus terminals to Malaysia.',4.2),

-- =====================================================
-- VIETNAM
-- =====================================================
('RedDoorz @ Ho Chi Minh District 1','Ho Chi Minh City','29 Le Duan Boulevard, District 1, Ho Chi Minh City', 'In the dynamic center of Ho Chi Minh City near Ben Thanh Market, Notre-Dame Cathedral, and Bui Vien walking street.',    4.4),
('RedDoorz Plus @ HCMC Bui Vien',   'Ho Chi Minh City','Bui Vien Street, District 1, Ho Chi Minh City',      'Right on the famous backpacker street. Ideal for budget travelers exploring the city\'s iconic street food and nightlife.', 4.2),
('RedDoorz @ Hanoi Old Quarter',    'Hanoi',         '38 Hang Bac Street, Hoan Kiem District, Hanoi',        'Nestled in the 1,000-year-old Old Quarter. Minutes from Hoan Kiem Lake, Ngoc Son Temple, and authentic pho stalls.',       4.3),
('RedDoorz @ Hanoi Ba Dinh',        'Hanoi',         '1 Ba Dinh Square, Ba Dinh District, Hanoi',            'Near Ho Chi Minh Mausoleum and the Temple of Literature. Perfect for cultural exploration of the Vietnamese capital.',    4.2),
('RedDoorz @ Da Nang Beachfront',   'Da Nang',       'Vo Nguyen Giap Street, My Khe Beach, Da Nang',         'Directly on My Khe Beach, rated among the world\'s best beaches. Close to the Golden Bridge and Ba Na Hills.',           4.5),
('RedDoorz Plus @ Da Nang City',    'Da Nang',       'Tran Phu Street, Hai Chau District, Da Nang',          'Central Da Nang with stunning Dragon Bridge views and easy day trips to Hoi An Ancient Town and Hue Citadel.',           4.3),
('RedDoorz @ Hoi An Ancient Town',  'Hoi An',        'Le Loi Street, Hoi An Ancient Town, Quang Nam',        'Walking distance to the UNESCO-listed Ancient Town lantern streets, Thu Bon River, and silk tailor shops.',              4.6),
('RedDoorz @ Nha Trang Beachfront', 'Nha Trang',     'Tran Phu Boulevard, Loc Tho Ward, Nha Trang, Khanh Hoa','On the stunning 6km Nha Trang beach promenade. Popular for scuba diving, island hopping, and seafood by the shore.',   4.4),
('RedDoorz @ Phu Quoc Island',      'Phu Quoc',      'Tran Hung Dao Street, Duong Dong, Phu Quoc Island',    'On Vietnam\'s largest island with pristine white beaches, clear waters, and the famous Phu Quoc night market nearby.',   4.5);


-- =====================================================
-- ROOMS for new hotels
-- Assign rooms by fetching Hotel_Id of the newly added hotels
-- =====================================================

-- Helper: we INSERT rooms by matching Hotel_Name exactly
-- Philippines — Metro Manila
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Clean room with double bed, free WiFi, and cable TV.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ermita Manila';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Spacious room with queen bed and bay-view window.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ermita Manila';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Modern room with smart TV and high-speed WiFi.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Ortigas Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded room with city-skyline view and work desk.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Ortigas Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          2299.00, 2, 'Executive suite with separate lounge area.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Ortigas Center';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   849.00, 2, 'Comfortable bay-view room with free WiFi.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Pasay Roxas Boulevard';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1149.00, 2, 'Upgraded room with boulevard panoramic views.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Pasay Roxas Boulevard';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Premium standard room in the Alabang business district.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Alabang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Spacious deluxe with lounge access and work desk.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Alabang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    1999.00, 4, 'Large family room with two double beds.' FROM Hotels WHERE Hotel_Name='RedDoorz Premium @ Alabang';

-- Philippines — Luzon
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Mountain-cool room with TV and free WiFi.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Baguio City Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1399.00, 2, 'Cozy highland deluxe with extra blankets and heater.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Baguio City Center';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   949.00, 2, 'Bright room with park views and fresh mountain air.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Baguio Burnham';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    1499.00, 4, 'Spacious family room with Burnham Park overlook.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Baguio Burnham';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Clean comfortable room in the Subic Freeport Zone.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Subic Bay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Upgraded room with forest views and air conditioning.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Subic Bay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Modern room minutes from Clark International Airport.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Clark Pampanga';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Spacious deluxe with airport shuttle convenience.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Clark Pampanga';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   699.00, 2, 'Comfortable budget room with Ilocos heritage charm.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ilocos Norte';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',     999.00, 2, 'Upgraded room with local wooden furnishings.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ilocos Norte';

-- Philippines — Visayas
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Modern room with fiber-speed WiFi inside Cebu IT Park.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cebu IT Park';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Spacious deluxe with dual monitors and ergonomic chair.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cebu IT Park';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Island-hop base with easy access to Mactan beaches.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Mactan Cebu';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Upgraded room with garden terrace near the airport.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Mactan Cebu';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Budget-friendly room in the City of Love, Iloilo.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Iloilo City Center';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Deluxe room with river esplanade view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Iloilo City Center';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   749.00, 2, 'Comfortable room in the heart of Bacolod.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bacolod City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1049.00, 2, 'Upgraded room steps from the Bacolod City Plaza.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bacolod City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1699.00, 2, 'Beachfront standard on Boracay White Beach Station 2.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Boracay Station 2';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2399.00, 2, 'Beachfront deluxe with private terrace and sea view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Boracay Station 2';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Clean comfortable room in Puerto Princesa, Palawan.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Palawan Puerto';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Spacious deluxe perfect after Underground River tours.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Palawan Puerto';

-- Philippines — Mindanao
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Modern room near SM Lanang and Davao waterfront.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Davao Lanang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Deluxe with city view and proximity to Samal Island.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Davao Lanang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   799.00, 2, 'Comfortable stay near the Cagayan de Oro river.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cagayan de Oro City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1099.00, 2, 'Upgraded room ideal for white-water rafting trips.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Cagayan de Oro City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   699.00, 2, 'Clean budget room in General Santos city center.' FROM Hotels WHERE Hotel_Name='RedDoorz @ General Santos City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',     999.00, 2, 'Deluxe room close to fish port and tuna market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ General Santos City';

-- Indonesia — Jakarta
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Business-ready room in the Sudirman Golden Triangle.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Sudirman';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Spacious deluxe with skyline view and premium bedding.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Sudirman';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          2999.00, 2, 'Executive suite with separate meeting nook and espresso bar.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Sudirman';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Central Thamrin room near the National Monument.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Jakarta Thamrin';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Upgraded deluxe with Grand Indonesia mall access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Jakarta Thamrin';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Trendy Kemang room in a cafe-lined neighborhood.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Kemang';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Garden-view deluxe in the hip Kemang district.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Kemang';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Transit room 10 minutes from Soekarno-Hatta Airport.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Airport';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded room with complimentary airport shuttle.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Jakarta Airport';

-- Indonesia — Bali
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Steps from Kuta Beach. Vibrant surf-town atmosphere.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Kuta Beach';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Ocean-view deluxe with private balcony in Kuta.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Kuta Beach';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Stylish Seminyak room with tropical garden courtyard.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Bali Seminyak';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2199.00, 2, 'Deluxe poolside room with Seminyak sunset views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Bali Seminyak';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Villa',          3999.00, 2, 'Private villa with plunge pool and butler service.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Bali Seminyak';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Rice terrace-view room in the cultural heart of Bali.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Ubud';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Jungle-view deluxe with traditional Balinese decor.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Ubud';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Calm beachfront room in family-friendly Sanur.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Sanur';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1599.00, 2, 'Deluxe room with sunrise sea view and promenade access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bali Sanur';

-- Indonesia — Java & Others
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Cool highland room in Bandung Dago fashion district.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bandung Dago';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1299.00, 2, 'Upgraded room with Bandung mountain panorama.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Bandung Dago';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Malioboro-adjacent room steps from batik stalls.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Yogyakarta Malioboro';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1399.00, 2, 'Upgraded room with Kraton Palace walking access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Yogyakarta Malioboro';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Business-ready room in the heart of Surabaya.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Surabaya City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Upgraded deluxe near Tunjungan Plaza mall.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Surabaya City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'Senggigi beach-view room with volcano backdrop.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Lombok Senggigi';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1699.00, 2, 'Deluxe beachfront room near Gili Island ferry piers.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Lombok Senggigi';

-- Singapore
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  3999.00, 2, 'Premium room on Orchard Road with ION mall at doorstep.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Orchard';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    5499.00, 2, 'Orchard deluxe with city panorama and premium toiletries.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Orchard';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          8999.00, 2, 'Luxury Orchard suite with butler service and lounge access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Orchard';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  3499.00, 2, 'Riverside Clarke Quay room with nightlife at your feet.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Singapore Clarke Quay';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    4999.00, 2, 'Upgraded river-view deluxe in the Clarke Quay precinct.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Singapore Clarke Quay';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2999.00, 2, 'Central Bugis room near the MRT and Arab Street.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Bugis';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    3999.00, 2, 'Spacious Bugis deluxe with Heritage Quarter surroundings.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Bugis';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  2199.00, 2, 'Value Jurong East room near westside malls and transport.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Jurong East';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2999.00, 2, 'Upgraded Jurong room with easy JB cross-border access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Singapore Jurong East';

-- Vietnam
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Central District 1 room near Ben Thanh Market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ho Chi Minh District 1';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Upgraded room with Saigon River partial view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ho Chi Minh District 1';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Suite',          2999.00, 2, 'Luxury suite with Notre-Dame Cathedral view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Ho Chi Minh District 1';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   899.00, 2, 'Budget-friendly Bui Vien room with street-food access.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ HCMC Bui Vien';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1199.00, 2, 'Upgraded backpacker-friendly deluxe on walking street.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ HCMC Bui Vien';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1099.00, 2, 'Old Quarter room with lantern-lit alley views.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hanoi Old Quarter';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1499.00, 2, 'Heritage deluxe room steps from Hoan Kiem Lake.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hanoi Old Quarter';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',   999.00, 2, 'Cultural Ba Dinh room near the Mausoleum.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hanoi Ba Dinh';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1399.00, 2, 'Upgraded room for Temple of Literature explorations.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hanoi Ba Dinh';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1399.00, 2, 'Beachfront My Khe room with ocean sunrise views.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Da Nang Beachfront';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1899.00, 2, 'Deluxe sea-view room with Golden Bridge day-trip access.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Da Nang Beachfront';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    2499.00, 4, 'Beachside family room with two double beds and balcony.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Da Nang Beachfront';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1199.00, 2, 'City-center room with Dragon Bridge night light views.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Da Nang City';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1599.00, 2, 'Upgraded room for easy Hoi An and Hue day trips.' FROM Hotels WHERE Hotel_Name='RedDoorz Plus @ Da Nang City';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Hoi An lantern-lit ancient town walking-distance room.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hoi An Ancient Town';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1999.00, 2, 'Heritage-style deluxe with Thu Bon River terrace view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Hoi An Ancient Town';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1299.00, 2, 'Beachfront Nha Trang room with ocean breeze.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Nha Trang Beachfront';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    1799.00, 2, 'Sea-view deluxe with private balcony on the bay.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Nha Trang Beachfront';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Family Room',    2299.00, 4, 'Spacious family beach room for island-hopping groups.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Nha Trang Beachfront';

INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Standard Room',  1499.00, 2, 'Island resort room near Phu Quoc night market.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Phu Quoc Island';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Deluxe Room',    2199.00, 2, 'Beachfront deluxe with hammock terrace and sea view.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Phu Quoc Island';
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description)
SELECT Hotel_Id, 'Villa',          3999.00, 2, 'Private garden villa with plunge pool, Phu Quoc style.' FROM Hotels WHERE Hotel_Name='RedDoorz @ Phu Quoc Island';
