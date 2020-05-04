<?php
# SP - Read Proposals
include_once($_SERVER["DOCUMENT_ROOT"] . "/Data/Master.php");
$data = new Data;
$json = new JSON;
$sql = mysqli_query($data->DBConnection(), "SELECT * FROM Proposals");
$result = array();
while ($r = $data->ResultValue($sql)) {
    $result[] = $r;
}
$proposals = $json->Read($json->Create($result));
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <h1>Proposals</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><b>Name</b></td>
            <td><b>Email Address</b></td>
            <td><b>Phone Number</b></td>
        </tr>
        <?php
        foreach($proposals as $proposal) {
            ?>
            <tr>
                <td><?php echo $proposal["FullName"]; ?></td>
                <td><?php echo $proposal["EmailAddress"]; ?></td>
                <td><?php echo $proposal["PhoneNumber"]; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>