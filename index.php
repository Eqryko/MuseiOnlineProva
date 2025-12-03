<?php
session_start();

// Connessione al database
$conn = new mysqli('localhost','root','','museionline');
if($conn->connect_error){
    die("Connessione fallita: {$conn->connect_error}");
}

$codice = $_GET['codice'] ?? '';
if (!empty($codice)) {
    setcookie("ultimaRicercaCodice", $codice, time() + 600);  // 600 sec = 10 min
    setcookie("ultimaRicercaTime", time(), time() + 600);
}

if (isset($_COOKIE['ultimaRicercaCodice']) && isset($_COOKIE['ultimaRicercaTime'])) {
    $tempo_trascorso = time() - $_COOKIE['ultimaRicercaTime'];

    if ($tempo_trascorso <= 600) {
        echo "<p>Ultima esposizione ricercata negli ultimi 10 minuti: <strong>{$_COOKIE['ultimaRicercaCodice']}</strong></p>";
    }
}


$utente = $_GET['utente'] ?? '';

$result1 = '';
$result2 = '';
$result3 = '';

$sql1 = "SELECT COUNT(*) AS nBigliettiEmessi FROM Biglietto WHERE cod_visita = '$codice'";
$result1 = $conn->query($sql1);

if($result1 && $result1->num_rows > 0){
    $row = $result1->fetch_assoc();
    $result1 = "Numero di biglietti emessi: " . $row['nBigliettiEmessi'];
}


$sql2 = "SELECT SUM(v.tariffa) AS totaleRicavato FROM Biglietto b INNER JOIN Visita v  ON b.cod_visita = v.id_visita WHERE b.cod_visita = '$codice'";
$result2 = $conn->query($sql2);

if($result2 && $result2->num_rows > 0){
    $row = $result2->fetch_assoc();
    $result2 = "Totale ricavato: " . $row['totaleRicavato'] . " Euro";
}


$sql3 = "SELECT b.id_biglietto, 'acquistato' AS tipo FROM Biglietto b INNER JOIN Utenti u ON b.cod_utenteAcq = u.id_utente WHERE u.nome = '$utente' UNION SELECT b.id_biglietto, 'intestato' AS tipo FROM Biglietto b INNER JOIN Utenti u ON b.cod_utenteInt = u.id_utente WHERE u.nome = '$utente'";
$result3 = $conn->query($sql3);
$ut="Nessun biglietto trovato per quest'utente.";
if($result3 && $result3->num_rows > 0){
    $acquistati = [];
    $intestati = [];
    while($row = $result3->fetch_assoc()){
        if($row['tipo'] === 'acquistato'){
            $acquistati[] = $row['id_biglietto'];
        } else {
            $intestati[] = $row['id_biglietto'];
        }
    }
    $ut = "Acquistati: " . implode(", ", $acquistati) . " | Intestati: " . implode(", ", $intestati);
}

$conn->close();
?>


<html>
<head>
    <title> Musei Online </title>
</head>
<body>
    <h1> Musei Online </h1>

    <h2> Ricerca Esposizione </h2>
    <form method="get">
        <p> Inserisci Codice Esposizione
            <input type="text" name="codice">
        </p>
        <p> Inserisci Nome Utente
            <input type="text" name="utente">
        </p>
        <p> <?= $result1 ?> </p>
        <p> <?= $result2 ?> </p>
        <p> <?= $ut ?> </p>
        <input type="submit" id="invio">
    </form>

    <a href="nuova_esposizione.php"> Crea Nuova Esposizione</a>
</body>
</html>