<?php 
    header("Expires: on, 01 Jan 1970 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    include 'LIB_project1.php'; 
    $activePage="cart";

    //open a connection
    $mysqli = getConnect();

    if (isset($_POST['emptycart'])) {


                    //Update quantity
                    $queryString = "delete from cart";

                    if($stmt= $mysqli->prepare($queryString)){
                      $stmt->execute();
                    
                    //refresh page  
                        
            }
            }
           //close connection
           mysqli_close($mysqli);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'partials/head.php'; ?>
</head>

<body>
    <!-- Header -->
    <header class="intro-header" style="background-image: url('imgs/bg2.jpg')">
        <?php include 'partials/nav.php'; ?>
    </header>

    <!-- Main Content -->    
    <div class="container padr">
        <h1 class='pad-bottom'>Shopping Cart</h1>
        <div class='col-lg-12'>
    <?php
            //open a connection
            $mysqli = getConnect();
            $total = 0; //total for price

            $queryString = "Select * from cart";
            
            if($stmt = $mysqli->prepare($queryString)){
                $stmt->execute();
	            $stmt->store_result();
                $numRows = $stmt->num_rows;
	            $stmt->bind_result($name,$desc, $quantity,$price);
                
                
                if($numRows <= 0){
                    echo "<div class='alert alert-info' role='alert'>  <p class='alert-link'>Your shopping cart is empty.</p></div>";
                    
                }
                else{
                    while($row = $stmt->fetch()){

                        echo "<div class='row list-group bg-color '>".
                             "<div class='item  col-xs-4 col-lg-4 listr-group-item'>".
                             "<h4 class='group inner list-group-item-heading '>$name</h4>".
                             "<p class='group inner list-group-item-text'>$desc</p>".
                             "<h5 class='pull-left'></span>Price - $".number_format($price,2)." Quantity - $quantity</h5>".
                             "</div></div>";
                        $total += $price;
                    }     
                
                echo "<h4 class='pad-left'>Total Purchase = $".number_format($total, 2)."</h4>";   
                echo "<div class='pull-right'><form action='cart.php' method='post' ><input class='adminy btn btn-default'                         type='submit' name='emptycart' value='Empty Cart' /></form></div>";
}
            }
            
            if (isset($_POST['emptycart'])) {


                    //Update quantity
                    $queryString = "delete from cart";

                    if($stmt= $mysqli->prepare($queryString)){
                      $stmt->execute();
                    
                    //refresh page  
                        
            }
            }
           //close connection
           mysqli_close($mysqli);

          
        ?>
    </div>
    </div>
    <footer>
        <?php include 'partials/footer.php'; ?>
    </footer>
</body>

</html>