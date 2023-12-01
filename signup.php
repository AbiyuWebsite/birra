<?php

$userErr = $passErr = $repassErr = "";

$usePtrn = "/^ [a-z][A-Z] $/";
$passPtrn = "/^ [a-z][A-z][0-9][\w] $/";

include("contact.html");
if (isset($_POST['signup'])) {

    createDB();
    createtable();

    if (empty($_POST['user'])) {
        $userErr = "Username is required!";
    }
    if (!empty($_POST['user'])) {
        $user = sanitize($_POST['user']);
    }
    if (empty($usePtrn)) {
        $userErr = "User name is not alphabet";
    }

    if (empty($_POST['pass'])) {
        $passErr = "Password is required!";
    }
    if (!empty($_POST['pass'])) {
        $pass = sanitize($_POST['pass']);
    }
    if (empty($passPtrn)) {
        $passErr = "Your password is not strong";
    }
    if (strlen($_POST['pass']) < 6 && !empty($_POST['pass'])) {
        $passErr = "Password should include atleast six characters";
    }
    if (empty($userErr) && empty($passErr)) {
        registerUser($user, $pass);
    }
}

function createDB()
{
    $servername = "localhost";
    $username = "root";
    $password = "";

    try {

        $conn = new PDO("mysql:host=$servername", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqldb = "CREATE DATABASE contactdb";
        $conn->exec($sqldb);
        echo "database created succesfully";
    } catch (PDOException $ex) {
        echo "database is not created";
    }
}
function connect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "contactdb";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $ex) {
        echo "connected ERROR";
    }
}
function createtable()
{
    $conn = connect();
    $sqltable = "CREATE TABLE student(
        id int(10) auto_increment primary key,
        username varchar(50) not null,
        password varchar(50) not null,
        regdate timestamp default current_timestamp on update current_timestamp
    )";
    try {
        $conn->exec($sqltable);
        echo "Table created succesfully";
    } catch (PDOException $ex) {
        echo "table error";
    }
}

function registerUser($user, $pass)
{
    $conn = connect();
    //To avoid sql injection attack
    $sqlReg = "INSERT INTO student(username,password) VALUES (:user,:pass)";
    try {
        empty($_POST['user']) && empty($_POST['pass']);


        $insStmt = $conn->prepare($sqlReg);
        $insStmt->bindParam(':user', $user);
        $insStmt->bindParam(':pass', $pass);

        $insStmt->execute();
        echo "Rigster succesfully";
    } catch (PDOException $ex) {
        echo "Rigster Error. Please try again";
    }
    $conn = null;
}

function sanitize($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);

    return $input;
}
