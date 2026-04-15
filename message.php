<?php

if (isset($_SESSION['success'])) {
?>

    <div class="alert alert-success">
        <?php echo $_SESSION['success']; ?>
    </div>

    <script>
        setTimeout(function() {
            document.querySelector(".alert").remove();
        }, 3000);
    </script>

<?php
    unset($_SESSION['success']);
}


if (isset($_SESSION['error'])) {
?>

    <div class="alert alert-error">
        <?php echo $_SESSION['error']; ?>
    </div>

    <script>
        setTimeout(function() {
            document.querySelector(".alert").remove();
        }, 3000);
    </script>

<?php
    unset($_SESSION['error']);
}

?>