<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $_GET['titlePage'] ?>
    </title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/277f72a273.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="./utilities.js"></script>
</head>

<body id='<?php echo $_GET['bodyID'] ?>' class='<?php echo $_GET['bodyClass'] ?>'>
    <header class="headerTitle">
        <h1>IETI ABP POLLS</h1>

        <?php

        if (isset($_SESSION['ID'])) {
            echo '
            <a class="buttonLogout" href="./logout.php">
            <i class="fa fa-solid fa-right-from-bracket"></i>
            <div class="logout">SORTIR</div>
            </a>
            ';
        }

        ?>

        <?php
        $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (strpos($url, 'teacher.php')) {
            echo '
                <a class="anchorGoBack" href="./dashboard.php">
                <i class="fa-solid fa-arrow-left-long"></i>
                <div class="textGoBack">DASHBOARD</div>
                </a>
                ';
        }
        else if(strpos($url, 'stats.php')) {
            echo '
                <a class="anchorGoBack" href="./dashboard.php">
                <i class="fa-solid fa-arrow-left-long"></i>
                <div class="textGoBack">DASHBOARD</div>
                </a>
                ';
        };
        ?>


        

    </header>