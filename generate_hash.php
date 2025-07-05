<?php 
echo "Manager Password Hash: " . password_hash("manager123", PASSWORD_DEFAULT) . "<br>";
echo"Stockkeeper Password Hash: " . password_hash("stockkeeper123", PASSWORD_DEFAULT) . "<br>";
echo"Cashier Password Hash: " . password_hash("cashier123", PASSWORD_DEFAULT) ;

?>

