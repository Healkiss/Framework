<?php
    require $this -> module -> getCore() -> getBasePath() .'global/templates/HeaderView.php';
    require $this -> module -> getCore() -> getBasePath() .'global/templates/BanniereView.php';
    $this -> module -> getTemplate() -> showView('trace', 'trace');
    $this -> module -> getTemplate() -> showView('menu', 'Menu');
    echo "<div id='contenuEtFooter'>";
        echo "<div id='contenu'>";
            echo $this->module -> getController()->getContent();
        echo "</div>";
        require $this -> module -> getCore() -> getBasePath() .'global/templates/FooterView.php';
    echo "</div>";
?>