<?php
require 'Connection.class.php'; 
require 'Database.class.php'; 
require 'Links.class.php';
require 'Clicks.class.php';  

$links = new Links();
$clicks = new Clicks();

/*Funkcija koja generiše 5 random karaktera*/
define("LINK_LENGTH",5);
function linkShorten(){
    $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";//karakteri od kojih se generiše 5 random karaktera
    $shorterLink = "";
    for($i=0; $i < LINK_LENGTH; $i++){
        $shorterLink .= $characters[rand(0,strlen($characters) - 1)];
    }
    return $shorterLink;
}

/*Ukoliko je kliknuto na dugme "Kraći link" provjerava se da li je unešen link, te ukoliko jeste poziva se funkcija koja će generisati link od pet random karaktera. Takođe, provjerava se da li u tabeli "links" već postoji takav kratki link, te ukoliko postoji, ponovo se poziva ista funkcija. Kada se izgeneriše kratki link koji ne postoji u tabeli "links", tada se upisuju podaci u tu tabelu, te ispisuje korisniku koji je kratki link generisan.
Ukoliko nije unešen link, korisniku će se prikazati poruka "Morate unijeti link".*/
$message = "";
if(isset($_POST["createShorterLink"])){
    if(!empty($_POST["link"])){
        $link = $_POST["link"];
            if(preg_match("/^(https|http|www)/", $link)) {//Provjera da li unešeni link počinje sa https,http,www
                $shorterLink = linkShorten();
                $lin = $links->select("link_shorten","links");
                for($j = 0; $j < count($lin);$j++){
                    if($lin[$j]["link_shorten"] == $shorterLink){
                        $shorterLink = linkShorten();
                        $j = 0;
                        continue;
                    }
                }
                $links->insert("links","link_full, link_shorten","'" . $link . "','" . $shorterLink ."'");
                $shorterLinkAll = $_SERVER['SERVER_NAME'] . '/linkshortener/' . $shorterLink;
                $message = "Kratki URL je:<b> " . $shorterLinkAll . "</b>";
        }else {
            $message = "Morate unijeti ispravan link";
        }
    }else {
        $message = "Morate unijeti link";
    }
} 

/*Ukoliko je kliknuto na dugme "Broj klikova", provjerava se da li je unešen link, te ukoliko jeste odvaja se kratki link, te se iz tabele "clicks" dobavlja ukupan broj klikova ta taj kratki link.
Ukoliko nije unešen link, korisniku će se prikazati poruka "Morate unijeti kratki link".*/
$messageClick="";
if(isset($_POST["click"])){
    if(!empty($_POST["address"])){
        $address = $_POST["address"];
        if(preg_match('/linkshortener/i', $address)){//Provjera da li unešeni link sadrži "linkshortener"
            $addressExplode = explode('/linkshortener/', $address);
            $sh = $addressExplode[1];
            $clickNumber = $links->select("COUNT('$sh') AS clicks","clicks","WHERE link_shorten='$sh'");
            if(!empty($clickNumber)){
                $messageClick = "Broj klikova za ". $address . " je:<b> " . $clickNumber[0]['clicks'] . "</b>";
            }else {
                header('Location:index.php');              
            }
        }else {
            $messageClick = "Morate unijeti ispravan kratki link";
        }
    }else{
        $messageClick = "Morate unijeti kratki link";
    }
}

/*Na osnovu URL-a odvaja se dio koji se odnosi na kratki link, te ukoliko postoji takav kratki link u tabeli "links", u tabelu "clicks" upiše se taj kratki link. Zatim se na osnovu kratkog linka uzima unešeni, dugi link i vrši redirekcija.
Ukoliko ne postoji kratki link u tabeli "links", radi se redirekcija na index.php*/
if(isset($_GET['url'])){
    $url = $_GET['url'];
    $url = explode('/', $url);
    $short = $url[0];
    $l = $links->select("*","links","WHERE link_shorten='$short'");
    if(!empty($l)){
        $clicks->insert("clicks","link_shorten","'" . $short . "'");
        $fullUrl = $l[0]["link_full"];
        $checkUrl = parse_url($fullUrl);
        if($checkUrl['scheme'] == 'https' || $checkUrl['scheme'] == 'http'){
            header("location:" . $fullUrl);
        }else {
            header("location://" . $fullUrl);
        } 
    }else {
        header('Location:index.php');
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <title>Link Shortener</title>
</head>
<body>
    <div id="container">
        <div class="title">
            <h1>Skraćivanje linkova</h1>
        </div>
        <!-- Forma za unos linka i njegovo skraćivanje -->
        <div class="form">
            <form action="" method="POST">
                <p class="formTitle">Zalijepite URL koji je potrebno skratiti</p>
                <hr>
                <div class="formInput">
                    <input type="text" name="link" placeholder="Unesite link">
                    <input type="submit" name="createShorterLink" value="Kraći link">
                </div>
            </form>
            <p class="message"><?=$message?></p>
        </div>
        <!-- Kraj forme -->
        <!-- Forma za provjeru klikova kratkog linka -->
        <div class="form">
            <form action="" method="POST">
            <p class="formTitle">Provjerite broj klikova za kratki URL</p>
            <hr>
                <div class="formInput">
                    <input type="text" placeholder="Unesite kratki link" name="address">
                    <input type="submit" value="Broj klikova" name="click">
                </div>
            </form>
            <p class="message"><?=$messageClick?></p>
        </div>
        <!-- Kraj forme -->
    </div>
</body>
</html>