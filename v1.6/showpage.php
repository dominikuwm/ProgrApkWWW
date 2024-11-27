<?php
function PokazPodstrone($id)
{
    
    include('cfg.php'); 

    
    $id_clear = intval($id); 

   
    $query = "SELECT * FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_clear);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $web = $row['page_content']; 
    } else {
        $web = "[nie_znaleziono_strony]"; 
    }

    
    return $web;
}
?>
