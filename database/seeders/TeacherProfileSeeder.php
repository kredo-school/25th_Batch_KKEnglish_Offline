<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherProfileSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'first_name' => 'Mary Jane',
                'last_name' => 'Santos',
                'email' => 'mary.santos@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400',
                'biography' => 'Majored in English Literature and hold a TESOL certification. I specialize in making lessons engaging and easy to understand for beginners.',
                'point_consumed' => 100,
                'career' => 'Online ESL Instructor (4 years)',
                'graduation_school' => 'University of the Philippines',
                'certification' => 'TESOL, TEYL',
                'about_me' => 'I love traveling and watching movies. Let us enjoy learning English together in a fun, relaxed atmosphere!',
                'specialty' => 'Daily English, Callan Method, Kids English',
                'rating_average' => 4.95,
            ],
            [
                'first_name' => 'John Patrick',
                'last_name' => 'Reyes',
                'email' => 'john.reyes@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400',
                'biography' => 'Focusing on Business English and practical expressions for professional settings. Highly experienced in accent and pronunciation coaching.',
                'point_consumed' => 120,
                'career' => 'Global BPO Corporate Trainer (3 years), ESL Instructor (2 years)',
                'graduation_school' => 'Ateneo de Manila University',
                'certification' => 'TESOL, TOEIC 990',
                'about_me' => 'I enjoy playing basketball on weekends. I can help you master the key phrases needed for global business meetings.',
                'specialty' => 'Business English, Pronunciation Training, TOEIC Preparation',
                'rating_average' => 4.88,
            ],
            [
                'first_name' => 'Grace',
                'last_name' => 'Villanueva',
                'email' => 'grace.v@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1580894732444-8ecded7900cd?w=400',
                'biography' => 'Patient, enthusiastic, and dedicated to helping beginners build confidence from day one.',
                'point_consumed' => 80,
                'career' => 'ESL Instructor (1 year)',
                'graduation_school' => 'University of San Carlos',
                'certification' => 'TESOL',
                'about_me' => 'I am a big fan of Japanese culture, music, and anime. Let us talk about our favorite topics!',
                'specialty' => 'Beginner English, Free Talk',
                'rating_average' => 4.75,
            ],
            [
                'first_name' => 'Mark Anthony',
                'last_name' => 'Garcia',
                'email' => 'mark.garcia@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
                'biography' => 'Certified Callan Method trainer focused on building quick speaking reflexes, sentence structure, and fluency.',
                'point_consumed' => 100,
                'career' => 'Language School Instructor (5 years)',
                'graduation_school' => 'De La Salle University',
                'certification' => 'TESOL, CELTA',
                'about_me' => 'I enjoy playing acoustic guitar and reading historical fiction. My goal is to make every lesson structured and productive.',
                'specialty' => 'Callan Method, Grammar Building, IELTS Preparation',
                'rating_average' => 4.92,
            ],
            [
                'first_name' => 'Kristine',
                'last_name' => 'Cruz',
                'email' => 'kristine.cruz@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=400',
                'biography' => 'Extensive experience in teaching young learners using interactive games, phonics, and storytelling.',
                'point_consumed' => 100,
                'career' => 'Early Childhood Educator (2 years), Online ESL Teacher (3 years)',
                'graduation_school' => 'Cebu Normal University (College of Education)',
                'certification' => 'TEYL, Licensed Professional Teacher',
                'about_me' => 'Baking treats and drawing are my passions. I look forward to bringing smiles and energy into every class!',
                'specialty' => 'Kids English, Phonics, Elementary Conversation',
                'rating_average' => 4.90,
            ],
            [
                'first_name' => 'Angelo',
                'last_name' => 'Bautista',
                'email' => 'angelo.b@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                'biography' => 'Specializes in news article discussions and topical debate, helping students expand their vocabulary and express nuanced opinions.',
                'point_consumed' => 100,
                'career' => 'English Instructor (3 years)',
                'graduation_school' => 'University of Santo Tomas',
                'certification' => 'TESOL',
                'about_me' => 'I love cafe hopping and street photography. Let us have insightful conversations on current trends and culture.',
                'specialty' => 'News Discussion, Intermediate & Advanced Free Talk',
                'rating_average' => 4.82,
            ],
            [
                'first_name' => 'Bea Joy',
                'last_name' => 'Mendoza',
                'email' => 'bea.mendoza@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=400',
                'biography' => 'Known for clear pronunciation and constructive feedback. I ensure a comfortable, mistake-friendly environment for all learners.',
                'point_consumed' => 100,
                'career' => 'Online ESL Instructor (2 years)',
                'graduation_school' => 'Silliman University',
                'certification' => 'TESOL',
                'about_me' => 'Yoga and mountain hiking keep me grounded. I am eager to help you achieve your learning milestones.',
                'specialty' => 'Travel English, Daily Conversation, Listening Skills',
                'rating_average' => 4.86,
            ],
            [
                'first_name' => 'Rafael',
                'last_name' => 'Navarro',
                'email' => 'rafael.n@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400',
                'biography' => 'Pronunciation specialist focusing on intonation, mouth placement, and natural sentence rhythm.',
                'point_consumed' => 120,
                'career' => 'Speech & Accent Trainer (4 years)',
                'graduation_school' => 'Far Eastern University',
                'certification' => 'TESOL, English Phonetics Diploma',
                'about_me' => 'I enjoy voice acting and watching classic cinema. Let us work together on sounding natural and clear.',
                'specialty' => 'Accent Reduction, Speaking Fluency, Intonation',
                'rating_average' => 4.96,
            ],
            [
                'first_name' => 'Princess',
                'last_name' => 'Dela Cruz',
                'email' => 'princess.dc@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400',
                'biography' => 'Energetic and friendly tutor focused on boosting speaking confidence and making daily conversations effortless.',
                'point_consumed' => 80,
                'career' => 'Online ESL Teacher (1 year)',
                'graduation_school' => 'Polytechnic University of the Philippines',
                'certification' => 'TESOL',
                'about_me' => 'I love cooking local delicacies and modern dance. I make sure every session is engaging and fun!',
                'specialty' => 'Casual Conversation, Kids English, Vocabulary Building',
                'rating_average' => 4.78,
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Alcantara',
                'email' => 'christian.a@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=400',
                'biography' => 'Specializes in executive presentation training, business correspondence, and intensive TOEIC prep.',
                'point_consumed' => 150,
                'career' => 'Corporate English Training Specialist (6 years)',
                'graduation_school' => 'University of the Philippines Diliman',
                'certification' => 'TESOL, TOEIC 990',
                'about_me' => 'Passionate about chess and software programming. I take a structured, goal-oriented approach to language learning.',
                'specialty' => 'TOEIC Preparation, Presentation Skills, Executive Business',
                'rating_average' => 4.98,
            ],
            [
                'first_name' => 'Neil',
                'last_name' => 'Kredo',
                'email' => 'neil.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099014-SCQmaFc4LeKBgb7lxX2U9huH.png?width=1200',
                'biography' => 'An IT instructor from Naga, Cebu who enjoys programming personal projects and sharing practical skills and experience with students.',
                'point_consumed' => 120,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy programming personal projects and continuously learning new technologies. Keep learning for a brighter future!',
                'specialty' => 'Programming Fundamentals, Web Development, Project-Based Learning',
                'rating_average' => 4.90,
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Kredo',
                'email' => 'john.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099045-Y3I5RK9eMWtEkiyBpGbPlrJd.png?width=1200',
                'biography' => 'An IT instructor from Bilar, Bohol who enjoys building websites, learning new web technologies, mobile games, and basketball.',
                'point_consumed' => 120,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy creating websites and learning new languages for web development. I am happy to share my web development experience with students.',
                'specialty' => 'Web Development, Programming Fundamentals, Front-End Development',
                'rating_average' => 4.88,
            ],
            [
                'first_name' => 'Quisie',
                'last_name' => 'Kredo',
                'email' => 'quisie.kredo@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099092-SaZv1Vfbsuwydirx5KonCB4O.png?width=1200',
                'biography' => 'An IT instructor from Mactan, Cebu who enjoys movies, reading, spending time with family, and helping students overcome difficult programming challenges.',
                'point_consumed' => 100,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy reading, watching movies, and spending time with my family and cat. My advice to learners is simple: love your errors and keep learning from them.',
                'specialty' => 'Programming Fundamentals, Debugging, Beginner Web Development',
                'rating_average' => 4.91,
            ],
            [
                'first_name' => 'Shem',
                'last_name' => 'Kredo',
                'email' => 'shem.kredo@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099128-nWjl7M8R2PBZ4ixqNgATauUf.png?width=1200',
                'biography' => 'An IT instructor from Compostela, Cebu who enjoys badminton, fashion, movies, and helping students understand IT and programming in English.',
                'point_consumed' => 110,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy meeting new students every month and helping them build the skills they need for global companies, remote work, and better career opportunities.',
                'specialty' => 'Programming Fundamentals, Career-Oriented IT Skills, Web Development',
                'rating_average' => 4.93,
            ],
            [
                'first_name' => 'Garry',
                'last_name' => 'Kredo',
                'email' => 'garry.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099178-2XZyhHiYosMfOBD5Gt7Wzjvk.png?width=1200',
                'biography' => 'An IT instructor from Bayawan City, Negros Oriental who is passionate about software development, computers, chess, and teaching practical programming skills.',
                'point_consumed' => 150,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy software development, computer-related activities, and online chess. I like helping motivated students turn ideas into working applications.',
                'specialty' => 'Software Development, Application Development, Advanced Programming',
                'rating_average' => 4.97,
            ],
            [
                'first_name' => 'Edo',
                'last_name' => 'Kredo',
                'email' => 'edo.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099208-O3DBvHQwIP4UogCNaej2tEGf.png?width=1200',
                'biography' => 'An IT instructor from Apas, Cebu who enjoys mobile and computer games and likes helping students learn programming step by step.',
                'point_consumed' => 100,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy gaming and spending time with my family. I am happy when students learn programming and become able to build things by themselves.',
                'specialty' => 'Programming Fundamentals, Beginner Coding, Web Development',
                'rating_average' => 4.89,
            ],
            [
                'first_name' => 'Kurt',
                'last_name' => 'Kredo',
                'email' => 'kurt.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://assets.st-note.com/img/1764099266-WFImA2P9c3tbKfHuXerOaNQd.png?width=4000&height=4000&fit=bounds&format=jpg&quality=90',
                'biography' => 'An IT instructor from Lahug, Cebu who enjoys gaming and communicating with students while supporting them through the challenges of learning IT and English.',
                'point_consumed' => 100,
                'career' => 'IT Instructor at Kredo',
                'graduation_school' => 'Not publicly specified',
                'certification' => 'Not publicly specified',
                'about_me' => 'I enjoy games and talking with students. Learning IT and English can be difficult at first, but consistent effort will lead to improvement.',
                'specialty' => 'Programming Fundamentals, IT English, Beginner Web Development',
                'rating_average' => 4.87,
            ],
            [
                'first_name' => 'Kirby',
                'last_name' => 'Kredo',
                'email' => 'kirby.kredo@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'images/IMG_4426.jpeg',
                'biography' => 'The final boss of Kredo IT. Bugs fear him. Students respect him. Laravel sometimes listens to him.',
                'point_consumed' => 150,
                'career' => 'Professional developer, instructor, team leader, and part-time therapist for stressed programmers.',
                'graduation_school' =>'Kredo University',
                'certification' => 'Certified Bug Destroyer & Stack Overflow Specialist',
                'about_me' => 'I like sushi.',
                'specialty' => 'Laravel, IT, English, debugging, and finding the semicolon you forgot 3 hours ago.',
                'rating_average' => 5.0,
            ],
                    /*
            |--------------------------------------------------------------------------
            | Teacher 19 - General English
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Michelle',
                'last_name' => 'Lopez',
                'email' => 'michelle.lopez@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',

                'profile_image' =>
                    'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=400',

                'biography' =>
                    'A friendly English instructor who helps students improve their speaking confidence, grammar, and everyday communication skills.',

                'point_consumed' => 100,

                'career' =>
                    'Online ESL Instructor (3 years)',

                'graduation_school' =>
                    'University of Cebu',

                'certification' =>
                    'TESOL',

                'about_me' =>
                    'I enjoy traveling, reading, and meeting people from different cultures. I like creating relaxed and practical English lessons.',

                'specialty' =>
                    'General English, Daily Conversation, Grammar',

                'rating_average' => 4.84,
            ],
            /*
            |--------------------------------------------------------------------------
            | Teacher 20 - Beginner / Daily English
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Angela',
                'last_name' => 'Flores',
                'email' => 'angela.flores@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
                'biography' => 'A friendly and patient English teacher who enjoys helping beginners build confidence in everyday conversation.',
                'point_consumed' => 80,
                'career' => 'Online ESL Instructor (2 years)',
                'graduation_school' => 'Cebu Normal University',
                'certification' => 'TESOL',
                'about_me' => 'I enjoy traveling, watching movies, and meeting people from different cultures. I love making English lessons relaxed and enjoyable.',
                'specialty' => 'Beginner English, Daily Conversation, Free Talk',
                'rating_average' => 4.72,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 21 - Business English
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Joshua',
                'last_name' => 'Ramos',
                'email' => 'joshua.ramos@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400',
                'biography' => 'An experienced English instructor focused on professional communication, presentations, and practical business English.',
                'point_consumed' => 120,
                'career' => 'Corporate English Trainer (4 years)',
                'graduation_school' => 'University of San Carlos',
                'certification' => 'TESOL, TOEIC',
                'about_me' => 'I enjoy basketball, technology, and discussing business topics. I like helping students communicate confidently at work.',
                'specialty' => 'Business English, Presentation Skills, TOEIC Preparation',
                'rating_average' => 4.89,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 22 - Pronunciation / Travel
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Camille',
                'last_name' => 'Torres',
                'email' => 'camille.torres@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=400',
                'biography' => 'An energetic ESL teacher specializing in pronunciation, speaking fluency, and useful English for international travel.',
                'point_consumed' => 100,
                'career' => 'ESL Teacher (3 years)',
                'graduation_school' => 'University of Cebu',
                'certification' => 'TEFL',
                'about_me' => 'I love music, cafe hopping, and traveling. I enjoy hearing stories about different countries and cultures.',
                'specialty' => 'Pronunciation, Travel English, Free Talk',
                'rating_average' => 4.81,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 23 - Kids English
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Nicole',
                'last_name' => 'Castillo',
                'email' => 'nicole.castillo@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1534751516642-a1af1ef26a56?w=400',
                'biography' => 'A cheerful teacher who enjoys working with young learners through games, stories, vocabulary activities, and phonics.',
                'point_consumed' => 80,
                'career' => 'Kids ESL Teacher (3 years)',
                'graduation_school' => 'Cebu Normal University',
                'certification' => 'TEYL, TESOL',
                'about_me' => 'I enjoy drawing, singing, and creating fun activities for children. I believe learning English should be exciting.',
                'specialty' => 'Kids English, Phonics, Vocabulary',
                'rating_average' => 4.85,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 24 - TOEIC
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Daniel',
                'last_name' => 'Mendoza',
                'email' => 'daniel.mendoza@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=400',
                'biography' => 'A goal-oriented English instructor who helps students improve their TOEIC scores, grammar, vocabulary, and reading skills.',
                'point_consumed' => 150,
                'career' => 'TOEIC and ESL Instructor (6 years)',
                'graduation_school' => 'Ateneo de Manila University',
                'certification' => 'TESOL, TOEIC 985',
                'about_me' => 'I enjoy reading, running, and learning about technology. I like creating structured study plans for students.',
                'specialty' => 'TOEIC Preparation, Grammar, Business English',
                'rating_average' => 4.94,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 25 - Free Talk
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Samantha',
                'last_name' => 'Aquino',
                'email' => 'samantha.aquino@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400',
                'biography' => 'A conversational English teacher who creates a relaxed environment where students can speak naturally and confidently.',
                'point_consumed' => 80,
                'career' => 'Online English Tutor (2 years)',
                'graduation_school' => 'Silliman University',
                'certification' => 'TESOL',
                'about_me' => 'I love beaches, photography, food, and meeting new people. We can talk about almost anything in my lessons.',
                'specialty' => 'Free Talk, Daily English, Travel English',
                'rating_average' => 4.68,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 26 - Callan Method
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Nathan',
                'last_name' => 'Garcia',
                'email' => 'nathan.garcia@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1531384441138-2736e62e0919?w=400',
                'biography' => 'A fast-paced English instructor who focuses on quick responses, accurate grammar, and speaking fluency.',
                'point_consumed' => 120,
                'career' => 'Callan Method Instructor (4 years)',
                'graduation_school' => 'University of the Philippines Cebu',
                'certification' => 'TESOL, Callan Method Training',
                'about_me' => 'I enjoy cycling, movies, and learning languages. I like energetic lessons where students speak as much as possible.',
                'specialty' => 'Callan Method, Speaking Fluency, Grammar',
                'rating_average' => 4.91,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 27 - Advanced Conversation
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Patricia',
                'last_name' => 'Navarro',
                'email' => 'patricia.navarro@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1548142813-c348350df52b?w=400',
                'biography' => 'An advanced conversation instructor who enjoys discussing social issues, culture, news, and challenging topics.',
                'point_consumed' => 120,
                'career' => 'English Instructor (5 years)',
                'graduation_school' => 'University of Santo Tomas',
                'certification' => 'TESOL, IELTS Training',
                'about_me' => 'I enjoy books, documentaries, and discussing different perspectives. I encourage students to express their ideas clearly.',
                'specialty' => 'Advanced Free Talk, News Discussion, IELTS Speaking',
                'rating_average' => 4.87,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 28 - Beginner / Grammar
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Kevin',
                'last_name' => 'Santos',
                'email' => 'kevin.santos@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=400',
                'biography' => 'A patient teacher who explains grammar in simple language and helps beginner students develop strong English foundations.',
                'point_consumed' => 80,
                'career' => 'ESL Instructor (2 years)',
                'graduation_school' => 'University of San Jose-Recoletos',
                'certification' => 'TEFL',
                'about_me' => 'I enjoy video games, basketball, and movies. I believe making mistakes is an important part of learning.',
                'specialty' => 'Beginner English, Grammar, Daily Conversation',
                'rating_average' => 4.63,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 29 - Interview / Career English
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Andrea',
                'last_name' => 'Reyes',
                'email' => 'andrea.reyes@example.com',
                'gender' => 'Female',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1542206395-9feb3edaa68d?w=400',
                'biography' => 'A career-focused English teacher specializing in job interviews, workplace communication, and professional self-introductions.',
                'point_consumed' => 100,
                'career' => 'Business English Instructor (4 years)',
                'graduation_school' => 'De La Salle University',
                'certification' => 'TESOL',
                'about_me' => 'I enjoy traveling, reading career books, and helping people prepare for new professional opportunities.',
                'specialty' => 'Job Interview English, Business English, Presentation Skills',
                'rating_average' => 4.88,
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher 30 - Premium / IELTS
            |--------------------------------------------------------------------------
            */
            [
                'first_name' => 'Gabriel',
                'last_name' => 'Dela Cruz',
                'email' => 'gabriel.delacruz@example.com',
                'gender' => 'Male',
                'nationality' => 'Philippines',
                'profile_image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400',
                'biography' => 'An experienced English instructor specializing in IELTS speaking, academic English, pronunciation, and advanced communication.',
                'point_consumed' => 150,
                'career' => 'Senior ESL and IELTS Instructor (7 years)',
                'graduation_school' => 'University of the Philippines',
                'certification' => 'CELTA, TESOL, IELTS Training',
                'about_me' => 'I enjoy hiking, reading, and learning about different cultures. My lessons are structured around clear learning goals.',
                'specialty' => 'IELTS Speaking, Academic English, Pronunciation',
                'rating_average' => 4.97,
            ],


        ];

        foreach ($teachers as $teacher) {

            /*
            |--------------------------------------------------------------------------
            | 1. Create or Update User
            |--------------------------------------------------------------------------
            */

            $existingUser = DB::table('users')
                ->where('email', $teacher['email'])
                ->first();

            if ($existingUser) {

                // 既存Userを更新
                $userId = $existingUser->id;

                DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'first_name' => $teacher['first_name'],
                        'last_name' => $teacher['last_name'],
                        'role_id' => 2,
                        'profile_image' => $teacher['profile_image'],
                        'nationality' => $teacher['nationality'],
                        'gender' => $teacher['gender'],
                        'updated_at' => now(),
                    ]);

            } else {

                // 新規Userを作成
                $userId = DB::table('users')->insertGetId([
                    'first_name' => $teacher['first_name'],
                    'last_name' => $teacher['last_name'],
                    'role_id' => 2,
                    'email' => $teacher['email'],
                    'phone_number' => '09' . rand(100000000, 999999999),
                    'profile_image' => $teacher['profile_image'],
                    'nationality' => $teacher['nationality'],
                    'gender' => $teacher['gender'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Create or Update Teacher
            |--------------------------------------------------------------------------
            */

            DB::table('teachers')->updateOrInsert(
                [
                    'user_id' => $userId,
                ],
                [
                    'biography' => $teacher['biography'],
                    'point_consumed' => $teacher['point_consumed'],
                    'career' => $teacher['career'],
                    'graduation_school' => $teacher['graduation_school'],
                    'certification' => $teacher['certification'],
                    'about_me' => $teacher['about_me'],
                    'specialty' => $teacher['specialty'],
                    'rating_average' => $teacher['rating_average'],
                    'updated_at' => now(),
                ]
            );

            $this->command?->info(
                "Teacher updated: {$teacher['first_name']} {$teacher['last_name']}"
            );
        }
    }
}
