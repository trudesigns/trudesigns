<?php
# SP - (Insert) into [Proposals]
include_once($_SERVER["DOCUMENT_ROOT"] . "/Data/Master.php");
$data = new Data;
$fullName = $_POST["FullName"];
$emailAddress = $_POST["EmailAddress"];
$phoneNumber = $_POST["PhoneNumber"];
mysqli_query($data->DBConnection(), "INSERT INTO Proposals(
    FullName,
    EmailAddress,
    PhoneNumber
) VALUES(
    '$fullName',
    '$emailAddress',
    '$phoneNumber'
)");
header("Location: ../../approved-submission.html?requestedInfo=true")
?>