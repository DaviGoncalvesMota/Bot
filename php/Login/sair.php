<?php
    session_start();
    session_destroy();
?>

<title>BOTPAINEL</title>

<?php
    header(header: 'Location: login.php');
?>
