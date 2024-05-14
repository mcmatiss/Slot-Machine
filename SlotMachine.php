<?php
$baseBet = 5;
$rows = 3;
$columns = 3;
$winConditions = [
    // 'row column'
    ["1 1", "1 2", "1 3"],
    ["2 1", "2 2", "2 3"],
    ["3 1", "3 2", "3 3"],
    ["1 1", "2 1", "3 1"],
    ["1 2", "2 2", "3 2"],
    ["1 3", "2 3", "3 3"],
];
$symbolOccurrence = [
    "7" => 25, //25%
    '$' => 50, //25%
    "★" => 75, //25%
    "❤" => 85, //10%
    "♦" => 95, //10%
    "ϟ" => 100, //5%
];
$symbolMultipliers = [
    "7" => 4,
    '$' => 4,
    "★" => 4,
    "❤" => 10,
    "♦" => 10,
    "ϟ" => 20,
];
$symbolValue = "";

do {
    $vCoinAmount = (int) readline(
        "Enter start amount of virtual coins to play with (min $baseBet): "
    );
} while ($vCoinAmount < $baseBet);
do {
    echo "\nYour balance: $vCoinAmount\nBase bet is $baseBet.\n";
    $bet = (int) readline(
        "Enter BET amount per single game round (min $baseBet): "
    );
} while ($bet < $baseBet || $bet > $vCoinAmount);
do {
    // Generates a new game board
    $gameBoard = [];
    for ($i = 1; $i <= $rows; $i++) {
        for ($j = 1; $j <= $columns; $j++) {
            $randomSymbol = rand(1, 100);
            foreach ($symbolOccurrence as $key => $value) {
                if ($randomSymbol <= $value) {
                    $symbolValue = $key;
                    break;
                }
            }
            $symbol = new stdClass();
            $symbol->value = $symbolValue;
            $symbol->multiplier = $symbolMultipliers[$symbol->value];
            $symbol->row = $i;
            $symbol->column = $j;
            $gameBoard[] = $symbol;
        }
    }
    // Displays game board
    echo "\n";
    for ($i = 0; $i < count($gameBoard); $i++) {
        if ($i % $columns === 0 && $i !== 0) {
            echo "\n";
        }
        echo $gameBoard[$i]->value;
    }
    echo "\n";
    // Checks for win conditions
    $payout = 0;
    foreach ($symbolMultipliers as $symbol => $symbolMultiplier) {
        for ($i = 0; $i < count($winConditions); $i++) {
            $score = 0;
            for ($j = 0; $j < count($winConditions[$i]); $j++) {
                $rowCheck = "";
                $columnCheck = "";
                $checkRows = true;
                // Converts $winConditions to rows and columns
                for ($k = 0; $k < strlen($winConditions[$i][$j]); $k++) {
                    if ($checkRows) {
                        $rowCheck = $rowCheck . $winConditions[$i][$j][$k];
                    }
                    if ($winConditions[$i][$j][$k] === " ") {
                        $checkRows = false;
                    }
                    if ($checkRows === false) {
                        $columnCheck =
                            $columnCheck . $winConditions[$i][$j][$k];
                    }
                }
                // Checks if win condition is met
                foreach ($gameBoard as $element) {
                    if (
                        $element->row === (int) $rowCheck &&
                        $element->column === (int) $columnCheck &&
                        $element->value === $symbol
                    ) {
                        $score++;
                    }
                }
                // Calculates payout
                if ($score === count($winConditions[$i])) {
                    $payout =
                        $payout +
                        $bet +
                        $symbolMultiplier *
                        count($winConditions[$i]) *
                        ($bet / $baseBet);
                }
            }
        }
    }
    // Adds payout to players total coin amount
    $vCoinAmount = $vCoinAmount + $payout - $bet;
    echo "\nYour balance: $vCoinAmount\nBet: $bet\n";
    if ($vCoinAmount < $baseBet || $vCoinAmount < $bet) {
        echo "Out of funds...\n";
        do {
            $addFunds = readline("Add more funds? [y - yes | n - no]: ");
            if (strtolower($addFunds) === "n") {
                exit("\nThanks for playing!\n");
            }
        } while ($addFunds !== "y");
        do {
            $vCoinAmount = (int) readline(
                "Enter the amount of virtual coins to play with (min $baseBet): "
            );
        } while ($vCoinAmount < $baseBet);
    }
    do {
        $continuePlaying = readline(
            "Continue playing? [y - yes | n - no | b - change bet]: "
        );
        if (strtolower($continuePlaying) === "n") {
            exit("\nYour balance: $vCoinAmount\nThanks for playing!\n");
        }
        if ($continuePlaying === "b") {
            do {
                echo "\nYour balance: $vCoinAmount\nBase bet is $baseBet.\n";
                $bet = (int) readline(
                    "Enter BET amount per single game round (min $baseBet): "
                );
            } while ($bet < $baseBet || $bet > $vCoinAmount);
        }
    } while ($continuePlaying !== "y");
} while ($vCoinAmount >= $baseBet);