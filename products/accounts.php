<?php
session_start();


if (isset($_SESSION['account'])) {
    if (!$_SESSION['account']['is_staff']) {
        header('location: login.php');
        exit;
    }
} else {
    header('location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <style>
    
        p.search {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <a href="addaccount.php">Add Account</a> 
    
    <?php
    require_once 'account.class.php'; 

    $accountObj = new Account();
    $accounts = $accountObj->fetchAll(); 
    ?>

    <table border="1">
        <tr>
            <th>No.</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        
        <?php
        $i = 1;
        if (empty($accounts)) {
        ?>
            <tr>
                <td colspan="5"><p class="search">No accounts found.</p></td>
            </tr>
        <?php
        } else {
            foreach ($accounts as $account) {
        ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($account['first_name']) ?></td>
                <td><?= htmlspecialchars($account['last_name']) ?></td>
                <td><?= htmlspecialchars($account['role']) ?></td>
                <td>
                    <a href="editaccount.php?id=<?= $account['id'] ?>">Edit</a>
                    <?php
                    if ($_SESSION['account']['is_admin']) {
                    ?>
                    <a href="#" class="deleteBtn" data-id="<?= $account['id'] ?>" data-name="<?= $account['first_name'] ?>">Delete</a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php
                $i++;
            }
        }
        ?>
    </table>
    
    <script src="./account.js"></script>
</body>
</html>
