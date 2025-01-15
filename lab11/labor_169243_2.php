<?php
  // Informacje podstawowe
  $nr_indeksu = '169243';
  $nrGrupy = '2';

  echo 'Dominik Gutowski ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';

  // Zadanie 2 - Opis poszczególnych podpunktów

  // a) Metoda include(), require_once()
  echo 'a) Metoda include(), require_once()<br />';
  echo 'Metoda include() pozwala na dołączenie zawartości jednego pliku PHP do innego. Jest użyteczna, gdy chcemy uniknąć powielania kodu.<br />';
  echo 'Metoda require_once() działa podobnie jak include(), ale dołącza plik tylko raz. Jeśli plik już został dołączony, to nie będzie dodawany ponownie.<br />';
  
  // Przykład zastosowania include i require_once
  echo 'Przykład użycia include i require_once:<br />';
  include('jakisplik.php'); // mozna zastapic jakisplik jakims plikiem istniejacym
  require_once('somefile.php');
  echo '<br /><br />';

  // b) Warunki if, else, elseif, switch
  echo 'b) Warunki if, else, elseif, switch<br />';
  $x = 10;
  
  // Przykład z if, elseif i else
  if ($x < 5) {
      echo 'x jest mniejsze niż 5<br />';
  } elseif ($x < 15) {
      echo 'x jest mniejsze niż 15<br />';
  } else {
      echo 'x jest większe lub równe 15<br />';
  }

  // Przykład switch
  $day = 'poniedziałek';
  echo 'Dzień tygodnia: ';
  switch ($day) {
      case 'poniedziałek':
          echo 'To jest poniedziałek<br />';
          break;
      case 'wtorek':
          echo 'To jest wtorek<br />';
          break;
      default:
          echo 'To jest inny dzień<br />';
          break;
  }
  echo '<br /><br />';

  // c) Pętla while i for
  echo 'c) Pętla while i for<br />';
  
  // Przykład pętli while
  $i = 0;
  echo 'Pętla while:<br />';
  while ($i < 5) {
      echo 'Liczba ' . $i . '<br />';
      $i++;
  }

  // Przykład pętli for
  echo 'Pętla for:<br />';
  for ($j = 0; $j < 5; $j++) {
      echo 'Liczba ' . $j . '<br />';
  }
  echo '<br /><br />';

  // d) Typy zmiennych $_GET, $_POST, $_SESSION
  echo 'd) Typy zmiennych $_GET, $_POST, $_SESSION<br />';
  echo '$_GET - Używane do przesyłania danych w adresie URL, zwykle w zapytaniach GET.<br />';
  echo '$_POST - Używane do przesyłania danych z formularzy metodą POST, bez wyświetlania ich w URL.<br />';
  echo '$_SESSION - Używane do przechowywania danych na serwerze w trakcie sesji użytkownika.<br /><br />';

  // Przykłady użycia $_GET, $_POST i $_SESSION
  session_start();
  $_SESSION['user'] = 'Dominik Gutowski';

  echo 'Przykład $_SESSION: Nazwa użytkownika to ' . $_SESSION['user'] . '<br />';

  echo 'Przykład $_GET: <a href="?name=Dominik">Kliknij tutaj</a><br />';
  if (isset($_GET['name'])) {
      echo 'Witaj, ' . htmlspecialchars($_GET['name']) . '<br />';
  }
?>
