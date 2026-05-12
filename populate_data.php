<?php
require_once 'config/init.php';

try {
    // 1. Add Sample Notices
    $notices = [
        ['ग्रामसभा २०२६ चे आयोजन', 'येत्या रविवारी सकाळी १०:०० वाजता ग्रामपंचायत कार्यालयात महत्वाची ग्रामसभा आयोजित केली आहे. सर्वांनी उपस्थित रहावे.', 'सामान्य', 1],
        ['मोफत आरोग्य शिबीर', 'प्राथमिक आरोग्य केंद्रात मोफत डोळे तपासणी आणि औषध वाटप शिबीर आयोजित करण्यात आले आहे.', 'आरोग्य', 0],
        ['पाणी पुरवठा बंद सूचना', 'पाईपलाईन दुरुस्तीच्या कामामुळे उद्या गावाचा पाणी पुरवठा बंद राहील. कृपया सहकार्य करावे.', 'पाणी', 1]
    ];
    
    $pdo->exec("DELETE FROM notices"); // Clear existing
    $stmt = $pdo->prepare("INSERT INTO notices (title, content, category, is_important) VALUES (?, ?, ?, ?)");
    foreach ($notices as $n) $stmt->execute($n);

    // 2. Add Sample Schemes
    $schemes = [
        ['शेतकरी सन्मान योजना २०२६', 'गावातील अल्पभूधारक शेतकऱ्यांसाठी वार्षिक ६००० रुपये आर्थिक मदत दिली जाईल.', 'सर्व शेतकरी', '2026-06-30', 'https://pmkisan.gov.in/'],
        ['प्रधानमंत्री आवास योजना (घरकुल)', 'ज्यांच्याकडे हक्काचे घर नाही अशा नागरिकांसाठी शासनाकडून घर बांधण्यासाठी निधी उपलब्ध.', 'बेघर नागरिक', '2026-08-15', 'https://pmaymis.gov.in/'],
        ['मोफत बी-बियाणे वाटप', 'खरीप हंगामासाठी सोयाबीन आणि कापूस बियाण्याचे मोफत वाटप ग्रामपंचायत मार्फत केले जाईल.', 'नोंदणीकृत शेतकरी', '2026-05-25', '']
    ];

    $pdo->exec("DELETE FROM schemes"); // Clear existing
    $stmt = $pdo->prepare("INSERT INTO schemes (title, description, eligibility, deadline, link) VALUES (?, ?, ?, ?, ?)");
    foreach ($schemes as $s) $stmt->execute($s);

    // 3. Add Sample Events
    $events = [
        ['गाव स्वच्छता अभियान', 'गाडगे बाबा जयंती निमित्त संपूर्ण गाव मिळून श्रमदान आणि स्वच्छता मोहीम राबवणार आहोत.', '2026-05-20', '07:00:00', 'ग्रामपंचायत चौक', 'other'],
        ['स्वातंत्र्य दिन उत्सव', '१५ ऑगस्ट निमित्त ग्रामपंचायत कार्यालयात ध्वजारोहण आणि सांस्कृतिक कार्यक्रम.', '2026-08-15', '08:00:00', 'शाळा मैदान', 'festival'],
        ['शेतकरी मेळावा', 'आधुनिक शेती तंत्रज्ञान आणि खत व्यवस्थापन यावर कृषी तज्ज्ञांचे मार्गदर्शन.', '2026-06-10', '11:00:00', 'सभा गृह', 'meeting']
    ];

    $pdo->exec("DELETE FROM events"); // Clear existing
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, category) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($events as $e) $stmt->execute($e);

    echo "✅ सर्व सॅम्पल डेटा यशस्वीरित्या भरला गेला आहे!";
} catch (Exception $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
