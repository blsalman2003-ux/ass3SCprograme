<?php

$fileName = "users.txt";

function hashPassword($password, $salt)
{
    return hash("sha256", $password . $salt);
}

function registerUser()
{
    global $fileName;

    $username = trim(readline("Enter username: "));
    $password = readline("Enter password: ");

    if (file_exists($fileName)) {
        $file = fopen($fileName, "r");

        while (($line = fgets($file)) !== false) {
            $parts = explode("|", trim($line));

            if (count($parts) == 3 && $parts[0] === $username) {
                echo "Username already exists.\n";
                fclose($file);
                return;
            }
        }

        fclose($file);
    }

    // Generate a random salt
    $salt = bin2hex(random_bytes(16));

    // Hash password with salt
    $hashedPassword = hashPassword($password, $salt);

    // Store username, salt and hashed password
    $file = fopen($fileName, "a");

    fwrite(
        $file,
        $username . "|" . $salt . "|" . $hashedPassword . PHP_EOL
    );

    fclose($file);

    echo "Registration successful.\n";
}

function loginUser()
{
    global $fileName;

    $username = trim(readline("Enter username: "));
    $password = readline("Enter password: ");

    if (!file_exists($fileName)) {
        echo "No registered users found.\n";
        return;
    }

    $file = fopen($fileName, "r");

    while (($line = fgets($file)) !== false) {

        $parts = explode("|", trim($line));

        if (count($parts) == 3) {

            $storedUsername = $parts[0];
            $storedSalt = $parts[1];
            $storedHash = $parts[2];

            if ($storedUsername === $username) {

                // Hash entered password using stored salt
                $enteredHash = hashPassword($password, $storedSalt);

                if (hash_equals($storedHash, $enteredHash)) {
                    echo "Login successful.\n";
                } else {
                    echo "Incorrect password.\n";
                }

                fclose($file);
                return;
            }
        }
    }

    fclose($file);

    echo "Username not found.\n";
}

function main()
{
    while (true) {

        echo "\n";
        echo "===== User Authentication System =====\n";
        echo "1. Register\n";
        echo "2. Login\n";
        echo "3. Exit\n";

        $choice = trim(readline("Choose an option: "));

        switch ($choice) {

            case "1":
                registerUser();
                break;

            case "2":
                loginUser();
                break;

            case "3":
                echo "Goodbye.\n";
                exit;

            default:
                echo "Invalid choice.\n";
        }
    }
}

main();

?>