<?php 
    header("Expires: on, 01 Jan 1970 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    include 'LIB_project1.php'; 
    $activePage="index";
    $product  = null;  //product object
    $message = null;  //determines if something is wrong
    $alertObj = null; //alert object for message
        
    //open a connection
        $mysqli = getConnect();
    /* add product to cart and reduce number */
        if (isset($_POST['addtocart'])) {

            $id = $_POST['id'];
            $name = $_POST['name'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'] - 1;
            
            if($quantity >= 0){
            //creates a product object for modal
            $product  = new Product($name, $desc, $price, '1', '', '', '');

             //Add product to cart table
            $queryString = "insert into cart values (?, ?,?,?)";
            if($stmt = $mysqli->prepare($queryString)){
                $amount = 1;
                $stmt->bind_param("ssid", $name, $desc, $amount, $price);
                $stmt->execute();
                //echo "<script type='text/javascript'>alert('$name has been added to your shopping cart. id -> $id quantity -> $quantity');</script>";
            }      
            
            //Update quantity
            $queryString = "update products set quantity=? where product_id= ?";
            if($stmt= $mysqli->prepare($queryString)){
                $stmt->bind_param("ii",$quantity, $id);
                $stmt->execute();                
            }
                $message = "success";
            }
            else{
            //error - out of inventory
            $message = "error";
            $alertObj = new Alert("No Inventory", "alert-info", "Sorry, we're out of that item.");
           // echo "<script type='text/javascript'>alert('error');</script>";
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
    <header class="intro-header" style="background-image: url('imgs/bg.jpg')">
        <?php include 'partials/nav.php'; ?>
    </header>
    
     <!-- Main Content -->
    <div class="container">
    <?php 
        //open a connection
        $mysqli = getConnect();

        //get page number
        if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
        $start_from = ($page-1) * 5; 

        //selects records on sale
        $queryString = "SELECT * FROM products WHERE sale_price!='0'";        
        if($stmt = $mysqli->prepare($queryString)){
	        $stmt->execute();
	        $stmt->store_result();
            $numRows = $stmt->num_rows;
	        $stmt->bind_result($id,$name,$desc,$price,$quantity,$image,$sale_price);
            echo "<h1 class='pad-bottom'>Sale</h1>".
                 "<div class='row list-group'>";
            
            while($row = $stmt->fetch()){
                    echo "<div class='container modal fade' id='$id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-                             hidden='true'>
                      <div class='modal-pic'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span>                                <span class='sr-only'>Close</span></button>
                            <h4 class='modal-title' id='myModalLabel'>$name</h4>
                          </div>
                          <div class='modal-body row'>
                              <img class='img-responsive' src='product_images/$image' />
                            </div>
                            <div class='modal-footer'>
                            <button type='button' class='closey btn btn-default' data-dismiss='modal'>Close</button>
                          </div>
                        </div>
                      </div>
                    </div>".
                        "<div class='item  col-lg-3 col-sm-4 col-xs-12'>".
                         "<div class='thumbnail'>".
                    "<div class='ImageWrapper'> <a href='#$id' data-toggle='modal' data-target='#$id'>".
                    "<img  class='group grid-group-item' src='product_images/$image' alt='' />".
                    "<div class='ImageOverlayH'></div>
                    <div class='StyleTi'>
					<span></span> 
					<span class='WhiteHollowRounded' > <img src='imgs/hover.png'/>
					</span> 							
				    <span></span>
				</div></a></div>".
                    "<div class='caption'>".
                    "<h4 class='group inner list-group-item-heading wrap-text'>$name</h4>".
                    "<p class='group inner list-group-item-text wrap-text'>$desc</p>".
                    "<div class='row'>".
                    "<div class='col-xs-12'>".
                    "<h4>Sale price: $".number_format($sale_price,2)."</h4>".
                    "<h5>Regular price: $".number_format($price,2)."</h5>".
                    "<h5>Quantity: $quantity</h5>".
                    "<div><form action='index.php?page=$page' method='post' >
                         <input type='hidden' name='id' value='$id' />
                         <input type='hidden' name='name' value='$name' />
                         <input type='hidden' name='desc' value='$desc' />
                         <input type='hidden' name='quantity' value='$quantity' />
                         <input type='hidden' name='price' value='$sale_price' />
                         <input type='submit' name='addtocart' value='Add To Cart' class='btn btn-default'/></form></div>".
                    "</div>
                        </div>
                        </div>
                    </div>
                    </div>";
                        
                }
            echo "</div>";
        }
        
        echo "<hr class='hrclass'>";

        //gets products not on sale    
        $queryString = "SELECT * FROM products where sale_price='0' LIMIT $start_from, 5"; 
        if($stmt = $mysqli->prepare($queryString)){
                $stmt->execute();
                $stmt->store_result();
                $numRows = $stmt->num_rows;
                $stmt->bind_result($id,$name,$desc,$price,$quantity,$image,$sale_price);
                echo "<h1 class='pad-bottom'>Catalogue</h1>".
                 "<div class='row list-group'>";
            
                while($row = $stmt->fetch()){
                        echo "<div class='container modal fade' id='$id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                      <div class='modal-pic'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span                                   class='sr-only'>Close</span></button>
                            <h4 class='modal-title' id='myModalLabel'>$name</h4>
                          </div>
                          <div class='modal-body row'>
                              <img class='img-responsive' src='product_images/$image' />
                            </div>
                            <div class='modal-footer'>
                            <button type='button' class='closey btn btn-default' data-dismiss='modal'>Close</button>
                          </div>
                        </div>
                      </div>
                    </div>".
                    "<div id='cat' class='item  col-lg-3 col-sm-4 col-xs-12'>".
                         "<div class='thumbnail' >".
                            "<div class='ImageWrapper'> <a href='#$id' data-toggle='modal' data-target='#$id'>".
                    "<img  class='group grid-group-item' src='product_images/$image' alt='' />".
                    "<div class='ImageOverlayH'></div>
                    <div class='StyleTi'>
					<span></span> 
					<span class='WhiteHollowRounded' > <img src='imgs/hover.png'/>
					</span> 							
				    <span></span>
				</div></a></div>".
                            
                    "<div class='caption'>".
                    "<div class='row'>".
                    "<div class='col-xs-12'>".
                    
                    "<h4 class='group inner list-group-item-heading wrap-text'>$name</h4>".
                    "<p class='group inner list-group-item-text wrap-text'>$desc</p>".
                    "<h5>Regular price: $".number_format($price,2)."</h5>".
                    "<h5>Quantity: $quantity</h5>".
                    "<div><form action='index.php?page=$page' method='post' >
                         <input type='hidden' name='id' value='$id' />
                         <input type='hidden' name='name' value='$name' />
                         <input type='hidden' name='desc' value='$desc' />
                         <input type='hidden' name='quantity' value='$quantity' />
                         <input type='hidden' name='price' value='$price' />
                         <input type='submit' name='addtocart' value='Add To Cart' class='btn btn-default'/></form></div>".
                    "</div>
                        </div>
                        </div>
                    </div>
                    </div>";
                    }
            echo "</div>";
        }

        //creates pagination
        $queryString = "SELECT count(`name`) FROM `products` WHERE sale_price='0'"; 
        if($stmt = $mysqli->prepare($queryString)){
            $stmt->execute();
            $stmt->store_result();  
            $stmt->bind_result($count);
            while($row = $stmt->fetch()){
            $total_records = $count;
            }
            $total_pages = ceil($total_records / 5); 
            
            echo "<div class='text-center'><ul class='pagination'>";
            echo "<li><a href='index.php?page=1'>&laquo;</a>";
            for ($i=1; $i<=$total_pages; $i++) { 
                $active = '';
                if($i == $page){
                    $active = 'active';
                }
                echo "<li class='".$active."'><a href='index.php?page=".$i."'>".$i."</a></li> "; 
            }; 
            echo "<li><a href='index.php?page=$total_pages'>&raquo;</a>";
            echo "</ul></div><hr class='hrclass'>";
        }        
        
       //close connection
       mysqli_close($mysqli);
    ?>
    </div>
    

    <footer>
        <?php include 'partials/footer.php'; ?>
    </footer>
    
    <!-- Load Modal and scripts -->
    <?php include 'partials/modal.php'; ?>
    
</body>
</html>

<?php 
 if ($message == 'success') {
 //shows modal 
     echo "<script type='text/javascript'>$('#myModal').modal('show');</script>";
    }
 else if ($message == 'error') {
 //shows modal 
     echo "<script type='text/javascript'>$('#messageModal').modal('show');</script>";
    }
else{}
?>