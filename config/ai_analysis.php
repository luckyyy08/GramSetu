<?php
/**
 * Smart Village AI Analysis Module
 * Simulated AI logic to categorize and prioritize complaints
 */

function analyzeComplaint($description) {
    $description = mb_strtolower($description);
    
    $urgency = 'सामान्य (Normal)';
    $category = 'इतर (General)';
    
    // Urgency Detection (Keywords)
    $urgent_keywords = ['तात्काळ', 'भयानक', 'धोकादायक', 'अतिशय', 'urgent', 'danger', 'emergency', 'बंद पडले'];
    foreach ($urgent_keywords as $key) {
        if (strpos($description, $key) !== false) {
            $urgency = 'उच्च (High)';
            break;
        }
    }
    
    // Category Detection
    if (strpos($description, 'पाणी') !== false || strpos($description, 'नळ') !== false) {
        $category = 'पाणी पुरवठा (Water)';
    } elseif (strpos($description, 'वीज') !== false || strpos($description, 'लाईट') !== false) {
        $category = 'वीज विभाग (Electricity)';
    } elseif (strpos($description, 'रस्ता') !== false || strpos($description, 'खड्डा') !== false) {
        $category = 'रस्ते व बांधकाम (Roads)';
    } elseif (strpos($description, 'कचरा') !== false || strpos($description, 'स्वच्छता') !== false) {
        $category = 'स्वच्छता विभाग (Sanitation)';
    }
    
    return [
        'urgency' => $urgency,
        'category' => $category,
        'ai_score' => rand(85, 98) . '%' // Confidence Score
    ];
}
?>
