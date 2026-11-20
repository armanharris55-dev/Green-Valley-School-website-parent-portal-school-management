<?php
/* Default content + demo data. seed_base() = editable starter content for every install.
   seed_demo() = students, results, fees, attendance, admissions … so the client can see every feature working. */
if (!defined('APP')) { http_response_code(403); exit; }

function seed_dt($n) { return date('Y-m-d', strtotime(($n >= 0 ? '+' : '') . $n . ' days')); }
function seed_subjects() { return ['1-5' => ['Bangla', 'English', 'Mathematics', 'Science', 'Bangladesh & Global Studies', 'Religion'], '6-10' => ['Bangla', 'English', 'Mathematics', 'General Science', 'Bangladesh & Global Studies', 'ICT', 'Religion']]; }
function subj_for($cls) { $s = seed_subjects(); $n = $cls === 'KG' ? 0 : (int)$cls; return $n >= 6 ? $s['6-10'] : ($n >= 1 ? $s['1-5'] : ['Bangla', 'English', 'Mathematics']); }

function seed_base() {
    $Y = (int)date('Y');
    $classes = ['KG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
    $fees = []; foreach ($classes as $i => $c) $fees[] = ['cls' => $c === 'KG' ? 'KG' : 'Class ' . $c, 'admission' => 3000 + $i * 500, 'tuition' => 700 + $i * 100, 'exam' => 400 + $i * 50, 'session' => 1500 + $i * 100];
    $settings = [
        'school' => ['name' => 'Green Valley Model School & College', 'bn' => 'গ্রিন ভ্যালি মডেল স্কুল অ্যান্ড কলেজ', 'tag' => 'Knowledge · Discipline · Character', 'tag_bn' => 'জ্ঞান · শৃঙ্খলা · চরিত্র', 'eiin' => '123456', 'est' => '1998', 'address' => 'House 12, Road 5, Sector 7, Uttara, Dhaka-1230', 'phone' => '+880 1700-000000', 'phone2' => '+880 2-5890000', 'email' => 'info@greenvalley.example', 'hours' => 'Sun – Thu, 8:00 AM – 3:00 PM', 'fb' => '', 'yt' => '', 'map' => 'https://www.openstreetmap.org/export/embed.html?bbox=90.38%2C23.86%2C90.42%2C23.89&layer=mapnik', 'board' => 'Dhaka Board'],
        'announce' => 'Admission for the new session is open — apply online today | Half Yearly results are published — check the Result page | Annual Sports Day is coming soon',
        'principal' => ['name' => 'Md. Abdur Rahman', 'title' => 'Principal', 'msg' => "Dear parents and students,\n\nWelcome to Green Valley. For over two decades we have believed that a school is more than classrooms and textbooks — it is the place where a child learns to think, to question and to care for others.\n\nOur teachers combine strong academics with moral values, sports and creativity, so that every child can find and grow their own strength. We work hand in hand with parents, because a child's success is a shared journey.\n\nI invite you to visit our campus and see how we learn."],
        'about' => ['history' => "Green Valley Model School & College was founded in 1998 by a group of educators and local guardians who wanted a school that combined quality education with strong values. It began with 90 students in a rented building and today serves more than a thousand students from Kindergarten to Class 10 on its own campus.\n\nOver the years the school has built science and computer laboratories, a library of more than 8,000 books, a playground and an auditorium, and has consistently achieved some of the best results in the area.", 'mission' => 'To give every child a modern, joyful and disciplined education that builds knowledge, confidence and good character.', 'vision' => 'To be the most trusted school in the community — producing responsible citizens who serve Bangladesh and the world.', 'values' => "Honesty and respect\nCuriosity and creativity\nDiscipline and teamwork\nCare for the environment\nLove for the country"],
        'committee' => [['name' => 'Alhaj Mostafa Kamal', 'role' => 'Chairman'], ['name' => 'Md. Abdur Rahman', 'role' => 'Member Secretary (Principal)'], ['name' => 'Dr. Nasrin Sultana', 'role' => 'Guardian Representative'], ['name' => 'Engr. Tanvir Hossain', 'role' => 'Donor Member'], ['name' => 'Mrs. Farida Yasmin', 'role' => 'Teacher Representative'], ['name' => 'Mr. Shafiqul Islam', 'role' => 'Education Expert']],
        'stats' => ['years' => $Y - 1998, 'pass' => 99.4, 'gpa5' => 112, 'awards' => 48],
        'facilities' => [['ico' => 'book', 'title' => 'Library', 'text' => '8,000+ books, a quiet reading hall and a weekly reading period for every class.'], ['ico' => 'flask', 'title' => 'Science Laboratory', 'text' => 'Fully equipped physics, chemistry and biology labs for hands-on practicals.'], ['ico' => 'laptop', 'title' => 'Computer Lab', 'text' => '40 modern computers with internet — ICT from Class 3.'], ['ico' => 'ball', 'title' => 'Playground & Sports', 'text' => 'Football, cricket, badminton and athletics with trained coaches.'], ['ico' => 'mic', 'title' => 'Auditorium', 'text' => '400-seat auditorium for assemblies, cultural programmes and competitions.'], ['ico' => 'bus', 'title' => 'School Transport', 'text' => 'Safe, GPS-tracked buses covering all major areas of Uttara and nearby.'], ['ico' => 'heart', 'title' => 'Medical Room', 'text' => 'A visiting doctor, a nurse and first-aid on campus every day.'], ['ico' => 'cup', 'title' => 'Canteen', 'text' => 'Clean, affordable and healthy meals prepared under supervision.']],
        'achievers' => [['name' => 'Tasnim Akter', 'result' => 'GPA 5.00 — Golden A+', 'exam' => 'SSC ' . ($Y - 1)], ['name' => 'Rafiul Islam', 'result' => 'GPA 5.00 — Golden A+', 'exam' => 'SSC ' . ($Y - 1)], ['name' => 'Nabila Rahman', 'result' => 'Divisional Science Olympiad — 1st', 'exam' => $Y], ['name' => 'Sakib Hasan', 'result' => 'National Math Olympiad — Top 10', 'exam' => $Y], ['name' => 'Mehjabin Haque', 'result' => 'Inter-school Debate Champion', 'exam' => $Y], ['name' => 'Ayaan Chowdhury', 'result' => 'District Chess Champion', 'exam' => $Y]],
        'reviews' => [['name' => 'Mrs. Shirin Akter', 'role' => 'Parent of Class 7 student', 'text' => 'The teachers genuinely care. My daughter became confident and her results improved within a year.'], ['name' => 'Mr. Kamrul Hasan', 'role' => 'Parent of Class 4 student', 'text' => 'The online portal lets me see attendance, homework and fees from my phone — very convenient.'], ['name' => 'Tahmid Rahman', 'role' => 'Former student, now at BUET', 'text' => 'Green Valley taught me discipline and curiosity. I am grateful to every teacher.']],
        'calendar' => [['date' => "$Y-02-21", 'title' => 'International Mother Language Day', 'type' => 'holiday'], ['date' => "$Y-03-26", 'title' => 'Independence Day', 'type' => 'holiday'], ['date' => "$Y-04-14", 'title' => 'Pohela Boishakh', 'type' => 'holiday'], ['date' => "$Y-06-25", 'title' => 'Half Yearly Examination begins', 'type' => 'exam'], ['date' => "$Y-08-15", 'title' => 'National Mourning Day programme', 'type' => 'event'], ['date' => "$Y-12-10", 'title' => 'Annual Sports Day', 'type' => 'event'], ['date' => "$Y-12-15", 'title' => 'Annual Examination begins', 'type' => 'exam'], ['date' => "$Y-12-16", 'title' => 'Victory Day', 'type' => 'holiday'], ['date' => ($Y + 1) . '-01-02', 'title' => 'New session starts · book distribution', 'type' => 'event']],
        'examRoutine' => [['date' => "$Y-12-15", 'subject' => 'Bangla', 'time' => '10:00 AM – 1:00 PM'], ['date' => "$Y-12-17", 'subject' => 'English', 'time' => '10:00 AM – 1:00 PM'], ['date' => "$Y-12-19", 'subject' => 'Mathematics', 'time' => '10:00 AM – 1:00 PM'], ['date' => "$Y-12-21", 'subject' => 'Science / General Science', 'time' => '10:00 AM – 1:00 PM'], ['date' => "$Y-12-23", 'subject' => 'Bangladesh & Global Studies', 'time' => '10:00 AM – 1:00 PM'], ['date' => "$Y-12-26", 'subject' => 'Religion / ICT', 'time' => '10:00 AM – 12:00 PM']],
        'fees' => $fees,
        'periods' => ['08:30–09:10', '09:10–09:50', '09:50–10:30', '11:00–11:40', '11:40–12:20', '12:20–01:00'],
        'subjects' => seed_subjects(),
        'admission' => ['open' => true, 'session' => (string)($Y + 1), 'start' => "$Y-11-01", 'end' => "$Y-12-20", 'test' => 'Admission test: 27 December, 10:00 AM at the school campus (bring your admit slip).', 'formFee' => 500, 'classes' => $classes, 'seats' => 'KG: 60 · Class 1: 40 · Class 6: 30 · Class 9: 20 (other classes subject to vacancy)',
            'docs' => "Birth registration certificate (copy)\n2 passport-size photographs\nPrevious school transfer certificate (Class 1 and above)\nLatest report card / marksheet\nFather's and mother's NID (copy)", 'rules' => "Child must be 4+ years old for KG and 5+ for Class 1 by 1 January.\nAdmission is based on the admission test and interview (KG: interview only).\nSelected students must complete admission within 7 days of the result.\nThe application fee is non-refundable.", 'pay' => ['bkash' => '01700000000', 'nagad' => '01800000000']],
        'links' => ['facebook' => '', 'youtube' => ''],
    ];
    site_put('settings', $settings);

    $notices = [[-1, 'Admission open for session ' . ($Y + 1), 'admission', 'Online admission for KG to Class 9 has started. Apply from the Admission page and pay the application fee through bKash or Nagad.', 1], [-2, 'Half Yearly Examination results published', 'exam', 'Results are available on the Result page. Parents can also see the full marksheet in the guardian portal.', 1], [-4, 'Annual Examination routine published', 'exam', 'The Annual Examination begins on 15 December. See the exam routine on the Academics page. Students must bring the admit card.', 0], [-6, 'Monthly tuition fee — due date', 'fee', 'Monthly tuition fee must be paid by the 10th of each month. A late fee of ৳50 applies afterwards.', 0], [-9, 'Parents-teachers meeting', 'general', 'A parents-teachers meeting for Classes 6–10 will be held next Saturday at 10:00 AM in the auditorium.', 0], [-12, 'Winter uniform instructions', 'general', 'From next week students may wear the navy blue winter blazer over the regular uniform.', 0], [-16, 'Science Fair registration', 'event', 'Students of Classes 6–10 may register project ideas with their science teacher by the end of this month.', 0], [-22, 'Library membership renewal', 'general', 'Library cards must be renewed with the librarian before borrowing books for the new term.', 0]];
    foreach ($notices as $n) rec_put('notice', uid('no'), ['date' => seed_dt($n[0]), 'title' => $n[1], 'cat' => $n[2], 'text' => $n[3], 'pin' => $n[4]]);
    $news = [[-2, 'Our students shine at the Divisional Science Olympiad', 'Nabila Rahman of Class 9 won first place and four more Green Valley students reached the national round.', 'trophy', 0], [-8, 'Inter-school debate: Green Valley are champions', 'After four rounds against 24 schools, our team won the final on the motion “Technology makes learning better.”', 'mic', 1], [-15, 'Tree plantation programme', 'Students and teachers planted 300 saplings around the campus and the neighbourhood.', 'tree', 2], [-24, 'Cultural week celebrated with joy', 'Songs, dance, recitation and drama filled the auditorium during our annual cultural week.', 'music', 3], [-33, 'New computer lab inaugurated', 'Forty new computers with high-speed internet are now available for ICT classes.', 'laptop', 4]];
    foreach ($news as $n) rec_put('news', uid('nw'), ['date' => seed_dt($n[0]), 'title' => $n[1], 'text' => $n[2], 'ico' => $n[3], 'c' => $n[4]]);
    $events = [[6, 'Parents-Teachers Meeting', '10:00 AM', 'Auditorium'], [13, 'Science Fair', '9:00 AM', 'Science Block'], [24, 'Annual Sports Day', '8:30 AM', 'School Playground'], [35, 'Annual Examination begins', '10:00 AM', 'Classrooms'], [41, 'Victory Day programme', '9:00 AM', 'School Field'], [-10, 'Debate Competition (final)', '11:00 AM', 'Auditorium']];
    foreach ($events as $e) rec_put('event', uid('ev'), ['date' => seed_dt($e[0]), 'title' => $e[1], 'time' => $e[2], 'place' => $e[3]]);
    $gal = [['Annual Sports Day', 'sports', 'ball'], ['Science Fair', 'academic', 'flask'], ['Cultural Week', 'cultural', 'music'], ['Victory Day', 'national', 'flag'], ['Debate Champions', 'achievement', 'trophy'], ['Art Exhibition', 'cultural', 'palette'], ['Library Reading Hour', 'academic', 'book'], ['Tree Plantation', 'social', 'tree'], ['Graduation Day', 'national', 'cap'], ['Picnic Day', 'social', 'bus'], ['Computer Lab', 'academic', 'laptop'], ['Prize Giving', 'achievement', 'star']];
    foreach ($gal as $i => $g) rec_put('gallery', uid('ga'), ['title' => $g[0], 'cat' => $g[1], 'ico' => $g[2], 'c' => $i % 8, 'img' => '']);
    $tch = [['Mrs. Farida Yasmin', 'Assistant Head Teacher', 'English', 'M.A. in English, B.Ed', 2004], ['Mr. Shafiqul Islam', 'Senior Teacher', 'Mathematics', 'M.Sc. in Mathematics, B.Ed', 2006], ['Mrs. Nusrat Jahan', 'Senior Teacher', 'Bangla', 'M.A. in Bangla, B.Ed', 2008], ['Mr. Mahbubur Rahman', 'Senior Teacher', 'General Science', 'M.Sc. in Physics, B.Ed', 2007], ['Ms. Sharmin Sultana', 'Assistant Teacher', 'ICT', 'B.Sc. in CSE', 2016], ['Mr. Anisur Rahman', 'Assistant Teacher', 'Bangladesh & Global Studies', 'M.S.S. in Political Science', 2011], ['Mrs. Rokeya Begum', 'Assistant Teacher', 'Religion', 'M.A. in Islamic Studies', 2010], ['Mr. Jahangir Alam', 'Assistant Teacher', 'Mathematics', 'B.Sc. (Hons) in Mathematics', 2014], ['Ms. Tania Akter', 'Assistant Teacher', 'English', 'M.A. in English', 2018], ['Mr. Kamal Hossain', 'Physical Instructor', 'Physical Education', 'Diploma in Physical Education', 2012], ['Ms. Lipi Rani Das', 'Assistant Teacher', 'Science', 'B.Sc. in Botany', 2015], ['Mrs. Salma Khatun', 'Class Teacher (KG)', 'KG Programme', 'B.Ed., Montessori Certified', 2013]];
    foreach ($tch as $i => $t) rec_put('teacher', 'tc' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT), ['name' => $t[0], 'desig' => $t[1], 'subject' => $t[2], 'qual' => $t[3], 'since' => $t[4], 'phone' => '01710-0000' . str_pad((string)($i + 10), 2, '0', STR_PAD_LEFT), 'email' => 'teacher' . ($i + 1) . '@greenvalley.example', 'c' => $i % 8]);
    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu']; $tn = ['Bangla' => 'Mrs. Nusrat Jahan', 'English' => 'Mrs. Farida Yasmin', 'Mathematics' => 'Mr. Shafiqul Islam', 'Science' => 'Ms. Lipi Rani Das', 'General Science' => 'Mr. Mahbubur Rahman', 'Bangladesh & Global Studies' => 'Mr. Anisur Rahman', 'ICT' => 'Ms. Sharmin Sultana', 'Religion' => 'Mrs. Rokeya Begum'];
    foreach (['KG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'] as $ci => $c) {
        $sub = subj_for($c); $sub[] = $c === 'KG' ? 'Drawing' : 'Physical Education'; $tn['Drawing'] = 'Mrs. Salma Khatun'; $tn['Physical Education'] = 'Mr. Kamal Hossain'; $r = [];
        foreach ($days as $di => $dn) { $row = []; for ($p = 0; $p < 6; $p++) { $s = $sub[($di * 2 + $p + $ci) % count($sub)]; $row[] = ['s' => $s, 't' => $tn[$s] ?? 'Class Teacher']; } $r[$dn] = $row; }
        rec_put('routine', 'ro' . str_pad((string)$ci, 3, '0', STR_PAD_LEFT), ['cls' => $c, 'days' => $r]);
    }
}

function seed_demo($adminId = null) {
    mt_srand(11);
    $Y = (int)date('Y');
    $male = ['Rafiul', 'Sakib', 'Ayaan', 'Tahmid', 'Imran', 'Nahid', 'Fahim', 'Arif', 'Shihab', 'Rakib', 'Mahir', 'Zayan', 'Tanvir', 'Rifat', 'Sadman', 'Mehedi', 'Raihan', 'Shahriar'];
    $female = ['Tasnim', 'Nabila', 'Mehjabin', 'Sumaiya', 'Jannat', 'Farhana', 'Tanisha', 'Anika', 'Ishrat', 'Maliha', 'Nusrat', 'Samia', 'Priya', 'Rumana', 'Lamia', 'Afrin', 'Sadia', 'Mahiya'];
    $sur = ['Islam', 'Rahman', 'Hossain', 'Ahmed', 'Chowdhury', 'Akter', 'Khan', 'Haque', 'Uddin', 'Sarkar', 'Mia', 'Talukder', 'Karim', 'Biswas'];
    $fat = ['Md. Abdul Karim', 'Md. Shahidul Islam', 'Md. Rezaul Haque', 'Mr. Nurul Amin', 'Md. Faruk Hossain', 'Mr. Aminul Islam', 'Md. Delwar Hossain', 'Mr. Habibur Rahman', 'Md. Jashim Uddin', 'Mr. Sohel Rana'];
    $mot = ['Mrs. Shirin Akter', 'Mrs. Rehana Begum', 'Mrs. Nasima Khatun', 'Mrs. Parveen Sultana', 'Mrs. Morjina Begum', 'Mrs. Shahnaz Parvin', 'Mrs. Ayesha Siddika', 'Mrs. Rina Akter'];
    $areas = ['Sector 7, Uttara', 'Sector 11, Uttara', 'Azampur, Uttara', 'Dakshinkhan', 'Tongi', 'Abdullahpur'];
    $blood = ['A+', 'B+', 'O+', 'AB+', 'O-', 'A-'];
    $classes = ['KG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
    $students = []; $demoStudent = null;
    foreach ($classes as $ci => $c) {
        $n = $ci < 4 ? 4 : ($ci < 6 ? 5 : 6);
        for ($i = 1; $i <= $n; $i++) {
            $g = mt_rand(0, 1) ? 'Male' : 'Female'; $nm = ($g === 'Male' ? $male[mt_rand(0, 17)] : $female[mt_rand(0, 17)]) . ' ' . $sur[mt_rand(0, 13)];
            $id = 'st' . str_pad((string)(count($students) + 1), 4, '0', STR_PAD_LEFT);
            $age = $ci === 0 ? 5 : $ci + 5; $dob = ($Y - $age) . '-' . str_pad((string)mt_rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad((string)mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
            $s = ['name' => $nm, 'cls' => $c, 'sec' => ($i % 2) ? 'A' : 'B', 'roll' => $i, 'gender' => $g, 'dob' => $dob, 'blood' => $blood[mt_rand(0, 5)], 'father' => $fat[mt_rand(0, 9)] . ' ' . explode(' ', $nm)[1], 'mother' => $mot[mt_rand(0, 7)], 'phone' => '017' . mt_rand(10000000, 99999999), 'addr' => 'House ' . mt_rand(1, 60) . ', ' . $areas[mt_rand(0, 5)] . ', Dhaka', 'status' => 'active', 'admitted' => ($Y - max(0, $ci - 1)) . '-01-05', 'religion' => 'Islam'];
            if ($c === '8' && $i === 1) { $s['name'] = 'Rafiul Islam'; $s['gender'] = 'Male'; $s['father'] = 'Md. Shahidul Islam'; $demoStudent = $id; }
            rec_put('student', $id, $s); $students[] = $s + ['id' => $id];
        }
    }
    foreach ($students as $s) make_accounts($s['id'], $s['name']);
    // exams + results
    $ex = [['ex001', 'Annual Examination ' . ($Y - 1), ($Y - 1) . '-12-20', true], ['ex002', 'Half Yearly Examination ' . $Y, "$Y-06-25", true], ['ex003', 'Annual Examination ' . $Y, "$Y-12-15", false]];
    foreach ($ex as $e) rec_put('exam', $e[0], ['name' => $e[1], 'year' => substr($e[2], 0, 4), 'date' => $e[2], 'published' => $e[3]]);
    foreach ($students as $s) {
        $base = mt_rand(52, 90);
        foreach (['ex001', 'ex002'] as $eid) {
            $m = []; foreach (subj_for($s['cls']) as $sb) { $v = $base + mt_rand(-16, 10); if (mt_rand(0, 40) === 0) $v = mt_rand(22, 32); $m[$sb] = max(15, min(99, $v)); }
            rec_put('result', uid('rs'), ['sid' => $s['id'], 'cls' => $s['cls'], 'exam' => $eid, 'name' => $s['name'], 'marks' => $m]);
        }
    }
    // attendance: last 4 weeks (Sun–Thu)
    $ins = db()->prepare('INSERT INTO att (day,sid,cls,st) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE st=VALUES(st)');
    for ($d = 1; $d <= 28; $d++) {
        $day = date('Y-m-d', strtotime("-$d days")); $w = (int)date('w', strtotime($day)); if ($w === 5 || $w === 6) continue;
        foreach ($students as $si => $s) { $x = mt_rand(1, 100); $st = $x <= 91 ? 'P' : ($x <= 96 ? 'A' : 'L'); if ($s['id'] === $demoStudent && $x > 94) $st = 'A'; $ins->execute([$day, $s['id'], $s['cls'], $st]); }
    }
    // fees
    foreach ($students as $s) {
        $ci = $s['cls'] === 'KG' ? 0 : (int)$s['cls']; $amt = 700 + $ci * 100;
        for ($k = 5; $k >= 0; $k--) {
            $mt = strtotime(date('Y-m-01') . " -$k month"); $paid = $k > 0 ? (mt_rand(1, 10) > 1) : (mt_rand(1, 10) > 5); if ($s['id'] === $demoStudent) $paid = $k > 0;
            $f = ['sid' => $s['id'], 'cls' => $s['cls'], 'name' => $s['name'], 'title' => 'Tuition Fee — ' . date('M Y', $mt), 'amount' => $amt, 'due' => date('Y-m', $mt) . '-10', 'paid' => $paid ? 1 : 0];
            if ($paid) { $f['paidOn'] = date('Y-m', $mt) . '-0' . mt_rand(2, 9); $f['method'] = ['Cash', 'bKash', 'Nagad'][mt_rand(0, 2)]; $f['receipt'] = 'R-' . date('ym', $mt) . '-' . strtoupper(substr(md5($s['id'] . $k), 0, 4)); }
            rec_put('fee', uid('fe'), $f);
        }
        rec_put('fee', uid('fe'), ['sid' => $s['id'], 'cls' => $s['cls'], 'name' => $s['name'], 'title' => 'Exam Fee — Half Yearly ' . $Y, 'amount' => 400 + $ci * 50, 'due' => "$Y-06-10", 'paid' => 1, 'paidOn' => "$Y-06-05", 'method' => 'Cash', 'receipt' => 'R-' . $Y . '06-' . strtoupper(substr(md5($s['id']), 0, 4))]);
    }
    // expenses (6 months)
    for ($k = 5; $k >= 0; $k--) {
        $m = date('Y-m', strtotime(date('Y-m-01') . " -$k month"));
        foreach ([['Teachers & staff salary', 285000], ['Electricity & utilities', mt_rand(14, 22) * 1000], ['Maintenance', mt_rand(8, 30) * 1000], ['Stationery & lab supplies', mt_rand(6, 16) * 1000]] as $e) rec_put('expense', uid('ex'), ['date' => $m . '-' . str_pad((string)mt_rand(2, 26), 2, '0', STR_PAD_LEFT), 'title' => $e[0], 'amount' => $e[1], 'cat' => 'General']);
    }
    // homework
    $hw = [['Complete exercise 4.2 (questions 1–10) and learn the new words.', 'Mathematics'], ['Write a paragraph on “My School”. Minimum 10 lines.', 'English'], ['Draw and label the parts of a plant cell.', 'Science'], ['Read chapter 5 and answer the questions at the end.', 'Bangla'], ['Prepare a one-page note on “Our Liberation War”.', 'Bangladesh & Global Studies']];
    foreach ($classes as $ci => $c) for ($j = 0; $j < 2; $j++) { $h = $hw[($ci + $j) % 5]; rec_put('homework', uid('hw'), ['cls' => $c, 'subject' => $h[1], 'title' => $h[1] . ' homework', 'text' => $h[0], 'date' => seed_dt(-$j - 1), 'due' => seed_dt(2 + $j), 'by' => 'Class Teacher']); }
    // admissions
    $adm = [['Ishrat Jahan', 'Female', 'KG', 'pending', ''], ['Zayan Ahmed', 'Male', '1', 'pending', ''], ['Sumaiya Akter', 'Female', '6', 'accepted', 'Selected. Please complete admission within 7 days.'], ['Rakib Hasan', 'Male', '9', 'waitlist', 'On the waiting list — a seat may open next week.'], ['Maliha Khan', 'Female', '3', 'rejected', 'Did not meet the minimum age requirement.'], ['Nahid Hossain', 'Male', '5', 'pending', ''], ['Anika Rahman', 'Female', 'KG', 'accepted', 'Selected after the interview.']];
    foreach ($adm as $i => $a) rec_put('admission', uid('ad'), ['ref' => 'ADM-' . $Y . '-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT), 'name' => $a[0], 'name_bn' => '', 'dob' => ($Y - 6) . '-03-1' . $i, 'gender' => $a[1], 'blood' => 'B+', 'cls' => $a[2], 'prevschool' => $a[2] === 'KG' ? '' : 'Sunrise Kindergarten', 'father' => 'Md. Anwar Hossain', 'fjob' => 'Business', 'fphone' => '0171' . str_pad((string)(1000000 + $i * 1111), 7, '0', STR_PAD_LEFT), 'mother' => 'Mrs. Rubina Hossain', 'mphone' => '', 'present' => 'Sector ' . ($i + 3) . ', Uttara, Dhaka', 'permanent' => 'Same as present', 'religion' => 'Islam', 'payvia' => 'bKash', 'trx' => 'TX' . (7000 + $i * 37), 'status' => $a[3], 'note_admin' => $a[4], 'date' => seed_dt(-$i * 2 - 1)]);
    // messages, leave, class messages
    foreach ([['Mrs. Parveen Akter', '01712-345678', 'Is a school bus available for Sector 12, Uttara? And what is the monthly bus fee?'], ['Mr. Rashed Karim', '01911-222333', 'We want to visit the school before admission. Which day is suitable?'], ['Dr. Maruf Hasan', '01811-909090', 'Do you provide a scholarship for meritorious students from Class 6?']] as $i => $m) rec_put('message', uid('ms'), ['name' => $m[0], 'phone' => $m[1], 'email' => '', 'text' => $m[2], 'date' => seed_dt(-$i) . ' 1' . $i . ':20', 'read' => $i > 1]);
    $lv = [['Fever — doctor advised rest', 'pending', -1, 1], ['Family function in Chattogram', 'approved', -20, -18], ['Personal reason', 'rejected', -35, -34]];
    foreach ($lv as $l) rec_put('leave', uid('lv'), ['sid' => $demoStudent, 'cls' => '8', 'name' => 'Rafiul Islam', 'from' => seed_dt($l[2]), 'to' => seed_dt($l[3]), 'reason' => $l[0], 'status' => $l[1], 'date' => seed_dt($l[2] - 1), 'by' => 'parent']);
    foreach ($students as $s) if ($s['cls'] === '6' && $s['roll'] <= 2) rec_put('leave', uid('lv'), ['sid' => $s['id'], 'cls' => '6', 'name' => $s['name'], 'from' => seed_dt(1), 'to' => seed_dt(2), 'reason' => 'Attending a wedding', 'status' => 'pending', 'date' => seed_dt(0), 'by' => 'parent']);
    rec_put('msg', uid('mg'), ['cls' => '8', 'sid' => '', 'from' => 'Mrs. Farida Yasmin (Class Teacher)', 'text' => 'Dear parents, please make sure students bring their English reader and dictionary every day this week.', 'date' => seed_dt(-1)]);
    rec_put('msg', uid('mg'), ['cls' => '8', 'sid' => $demoStudent, 'from' => 'Mr. Shafiqul Islam (Mathematics)', 'text' => 'Rafiul has done excellent work in algebra. Please encourage him to join the Math Olympiad club.', 'date' => seed_dt(-3)]);
    rec_put('msg', uid('mg'), ['cls' => '', 'sid' => '', 'from' => 'Principal', 'text' => 'The school will remain closed on the day of the national holiday. Regular classes resume the next day.', 'date' => seed_dt(-5)]);
    // demo logins (random unknown password — reachable only through the one-click demo buttons while Demo mode is ON)
    $tid = 'tc002';
    foreach ([['demo-admin', 'admin', '', 'Demo Admin'], ['demo-accountant', 'accountant', '', 'Demo Accountant'], ['demo-teacher', 'teacher', $tid, 'Mr. Shafiqul Islam (demo)'], ['demo-student', 'student', $demoStudent, 'Rafiul Islam (demo)'], ['demo-parent', 'parent', $demoStudent, 'Parent of Rafiul (demo)']] as $u)
        q('INSERT INTO users (id,login,pass_hash,role,ref,name,active,demo,t) VALUES (?,?,?,?,?,?,1,1,?)', [uid('u'), $u[0], password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT), $u[1], $u[2], $u[3], now_ms()]);
    site_put('demo', ['on' => true]);
    log_act(['name' => 'System', 'role' => 'admin'], 'Demo data installed');
}
