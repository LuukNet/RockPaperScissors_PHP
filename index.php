<?php

session_start();

$choiceHTML = file_get_contents("Html/choice.html");
$sectionHTML = file_get_contents("Html/section.html");
$formHTML = file_get_contents("Html/form.html");
$resultHTML = file_get_contents("Html/result.html");

function setPlayer($sectionHTML, $num, $player, $state, $form)
{
    $sectionHTML = str_replace("{Player $num}", $player, $sectionHTML);
    $sectionHTML = str_replace("{State $num}", $state, $sectionHTML);
    $sectionHTML = str_replace("{Form $num}", $form, $sectionHTML);

    return $sectionHTML;
}

function setSections($sectionHTML, $formHTML)
{
    if ($_SESSION['gameType'] == 'CPU vs Player') {
        if (!isset($_SESSION['player2'])) {
            $sectionHTML = setPlayer($sectionHTML, '1', 'CPU', 'Made Their Choice', '');
            $sectionHTML = setPlayer($sectionHTML, '2', 'Player 1', 'Make Your Choice:', $formHTML);
        } else {
            $choice1 = $_SESSION['player1'];
            $choice2 = $_SESSION['player2'];

            $sectionHTML = setPlayer($sectionHTML, '1', 'CPU', "Choose: $choice1", '');
            $sectionHTML = setPlayer($sectionHTML, '2', 'Player 1', "Choose: $choice2", '');
        }
    } else {
        if (!isset($_SESSION['player1'])) {
            $sectionHTML = setPlayer($sectionHTML, '1', 'Player 1', 'Make Your Choice:', $formHTML);
            $sectionHTML = setPlayer($sectionHTML, '2', 'Player 2', 'Wait For Player 1', '');
        } elseif (!isset($_SESSION['player2'])) {
            $sectionHTML = setPlayer($sectionHTML, '1', 'Player 1', 'Made Their Choice', '');
            $sectionHTML = setPlayer($sectionHTML, '2', 'Player 2', 'Make Your Choice:', $formHTML);
        } else {
            $choice1 = $_SESSION['player1'];
            $choice2 = $_SESSION['player2'];
    
            $sectionHTML = setPlayer($sectionHTML, '1', 'Player 1', "Choose: $choice1", '');
            $sectionHTML = setPlayer($sectionHTML, '2', 'Player 2', "Choose: $choice2", '');
        }
    }

    return $sectionHTML;
}

function setGame($sectionHTML, $formHTML, $resultHTML)
{
    $gameHTML = file_get_contents("game.html");

    if (isset($_POST['choice']) && !isset($_SESSION['player1'])) {
        $_SESSION['player1'] = $_POST['choice'];
    } elseif (isset($_POST['choice'])) {
        $_SESSION['player2'] = $_POST['choice'];
    }
    
    if (!isset($_SESSION['player1'])) {
        $sectionHTML = setSections($sectionHTML, $formHTML);
    
        $gameHTML = str_replace('{Main}', $sectionHTML, $gameHTML);
        $gameHTML = str_replace('{End}', '', $gameHTML);
    
        return $gameHTML;
    } elseif (!isset($_SESSION['player2'])) {
        $sectionHTML = setSections($sectionHTML, $formHTML);
    
        $gameHTML = str_replace('{Main}', $sectionHTML, $gameHTML);
        $gameHTML = str_replace('{End}', '', $gameHTML);
    
        return $gameHTML;
    } else {
        $sectionHTML = setSections($sectionHTML, $formHTML);

        $gameHTML = str_replace('{Main}', $sectionHTML, $gameHTML);

        $winner = checkWinner();
        $resultHTML = str_replace('{Winner}', $winner, $resultHTML);

        $gameHTML = str_replace('{End}', $resultHTML, $gameHTML);

        return $gameHTML;
    }
}

function checkWinner()
{
    if ($_SESSION['player1'] == 'Rock') {
        $state = 'A';
    } elseif ($_SESSION['player1'] == 'Paper') {
        $state = 'B';
    } else {
        $state = 'C';
    }

    if ($_SESSION['player2'] == 'Rock') {
        $state .= 'A';
    } elseif ($_SESSION['player2'] == 'Paper') {
        $state .= 'B';
    } else {
        $state .= 'C';
    }

    if ($state == 'AC' || $state == 'BA' || $state == 'CB') {
        if ($_SESSION['gameType'] == 'CPU vs Player') {
            $winner = 'CPU won!';
        } else {
            $winner = 'Player 1 won!';
        }
    } elseif ($state == 'AB' || $state == 'BC' || $state == 'CA') {
        if ($_SESSION['gameType'] == 'CPU vs Player') {
            $winner = 'Player 1 won!';
        } else {
            $winner = 'Player 2 won!';
        }
    } else {
        $winner = 'Its a tie.';
    }

    return $winner;
}

if (isset($_POST['retry'])) {
    session_unset();
}

if (!isset($_POST['gameType']) && !isset($_SESSION['gameType'])) {
    $gameHTML = file_get_contents("game.html");

    $gameHTML = str_replace('{Main}', $choiceHTML, $gameHTML);
    $gameHTML = str_replace('{End}', '', $gameHTML);

    echo $gameHTML;
} elseif (isset($_POST['gameType']) && !isset($_SESSION['gameType'])) {
    $_SESSION['gameType'] = $_POST['gameType'];

    if ($_SESSION['gameType'] == 'CPU vs Player') {
        $options = ['Rock', 'Paper', "Scissors"];
        $number = rand(0, 2);
    
        $_SESSION['player1'] = $options[$number];
    }

    $gameHTML = setGame($sectionHTML, $formHTML, $resultHTML);

    echo $gameHTML;
} else {
    $gameHTML = setGame($sectionHTML, $formHTML, $resultHTML);

    echo $gameHTML;
}
