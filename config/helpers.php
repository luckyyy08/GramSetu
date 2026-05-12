<?php
// Helper Functions

function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatTime($time) {
    return date('h:i A', strtotime($time));
}
?>
