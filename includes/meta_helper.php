<?php
    // Function to fetch meta tags for a specific page
    function fetch_page_meta($conn, $page_name) {
        if (!isset($conn) || !$conn) {
            return [
                'meta_title' => 'Luxora - Luxury Redefined',
                'meta_description' => 'Experience the epitome of luxury with Luxora.',
                'meta_keywords' => 'luxury, fashion, lifestyle'
            ];
        }
    
        $stmt = $conn->prepare("SELECT meta_title, meta_description, meta_keywords FROM meta_tags WHERE page_name = ?");
        $stmt->bind_param("s", $page_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            // Fallback defaults
            return [
                'meta_title' => 'Luxora - ' . ucfirst($page_name),
                'meta_description' => '',
                'meta_keywords' => ''
            ];
        }
    }
?>
