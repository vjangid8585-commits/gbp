<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_strength {

    public function calculate($location) {
        $score = 0;
        $total = 100;
        $recommendations = [];

        $data = json_decode($location->data_json, true);

        // 1. Basic Info (40%)
        if (!empty($location->business_name)) $score += 10;
        if (!empty($data['websiteUri'])) $score += 10; else $recommendations[] = "Add a Website URL";
        if (!empty($data['phoneNumbers'])) $score += 10; else $recommendations[] = "Add Phone Number";
        if (!empty($data['regularHours'])) $score += 10; else $recommendations[] = "Add Business Hours";

        // 2. Activity (30%) - Requires DB checks, passing location ID would be better but simplified here
        // We will assume data passed includes counts or we do querying outside.
        // For MVP, we'll check fields present in data_json or passed separately.
        
        // 3. Content (30%)
        if (!empty($data['profile']['description'])) $score += 15; else $recommendations[] = "Add Business Description";
        if (!empty($data['categories'])) $score += 15; else $recommendations[] = "Add Categories";

        return [
            'score' => $score,
            'recommendations' => $recommendations
        ];
    }
}
?>
