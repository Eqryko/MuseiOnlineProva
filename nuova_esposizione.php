<?php
session_start();

$conn = new mysqli('localhost','root','','museionline');
if($conn->connect_error){
    die("Connessione fallita: {$conn->connect_error}");
}

$codice = $_POST['codice'] ?? '';
$titolo = $_POST['titolo'] ?? '';
$tariffa = $_POST['tariffa'] ?? '';
$dataInizio = $_POST['dataInizio'] ?? '';
$dataFine = $_POST['dataFine'] ?? '';
$result1 = '';
if (!empty($codice) && !empty($titolo) && !empty($tariffa) && !empty($dataInizio) && !empty($dataFine)) {
    $sqlInsert = "INSERT INTO Visita (id_visita, titolo, tariffa, dataInizio, dataFine) VALUES ('$codice', '$titolo', $tariffa, '$dataInizio', '$dataFine')";
    if ($conn->query($sqlInsert) === TRUE) {
        $result1 = "Nuova esposizione creata con successo.";
    } else {
        $result1 = "Errore durante la creazione dell'esposizione: " . $conn->error;
    }
}

$conn->close();
?>

<html>
<head>
    <title> Musei Online </title>
</head>
<body>
    <h1> Musei Online </h1>

    <h2> Inserisci Esposizione </h2>
    <form method="get">
        <p> Inserisci Codice
            <input type="text" name="codice">
        </p>
        <p> Inserisci Titolo
            <input type="text" name="titolo">
        </p>
        <p> Inserisci tariffa
            <input type="number" name="tariffa">
        </p>
        <p> Inserisci Data Inizio (YYYY-MM-DD)
            <input type="text" name="dataInizio">
        </p>
        <p> Inserisci Data Fine (YYYY-MM-DD)
            <input type="text" name="dataFine">
        </p>
        <p> <?= $result1 ?> </p>
        <input type="submit" id="invio">
    </form>

    <a href="nuova_esposizione.php"> Crea Nuova Esposizione</a>
</body>
</html>