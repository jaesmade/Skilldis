<?php
function calculateSynergyScore($extracted_text, $required_skills_string) {
    if (empty($extracted_text) || empty($required_skills_string)) {
        return 0.00;
    }

    $text = strtolower($extracted_text);
    
    $required_skills = explode(',', strtolower($required_skills_string));
    $total_skills = count($required_skills);
    
    if ($total_skills === 0) return 0.00;

    $matched_count = 0;
    foreach ($required_skills as $skill) {
        $trimmed_skill = trim($skill);
        if (!empty($trimmed_skill) && strpos($text, $trimmed_skill) !== false) {
            $matched_count++;
        }
    }

    $score = ($matched_count / $total_skills) * 100;
    
    if ($score > 0) {
        return round(50 + ($score / 2), 2);
    }
    
    return 0.00;
}
?>