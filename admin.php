<?php 
    include 'LIB_project1.php'; 
    $activePage="admin";
    session_start();
    //open a connection
    $mysqli = getConnect();
    
    //Retrieve details for specific product
    $getProduct = 1;
    $product = null;

    //alert object for message
    $message = "false";
    $alertObj = null; 

    //num of item on sale
    $maxSale = null;

    //if user signs in
    if (isset($_POST['signin'])) {

        //echo "<script type='text/javascript'>alert('worked')</script>";
        $username = $_POST['username'];
        $password = $_POST['password'];
        
         //Update quantity
         $queryString = "Select password from users where username = ?";

          if($stmt= $mysqli->prepare($queryString)){
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();  
            $stmt->bind_result($pwd);
            $svr_password= null;
            while($row = $stmt->fetch()){
                $svr_password = $pwd;
            }
        
          if( $svr_password == $password){
            $_SESSION["login"] = "true";
            
          }
          else{
              $message = "true";
              $alertObj = new Alert("You shall not pass!", "alert-warning", "Check username or password"); //failed to sign in
          }
        }                         
      }

    //gets num of item on sale
    $queryString = "SELECT count(`name`) FROM `products` WHERE sale_price>'0'"; 
    if($stmt = $mysqli->prepare($queryString)){
        $stmt->execute();
        $stmt->store_result();  
        $stmt->bind_result($count);
        while($row = $stmt->fetch()){
        $maxSale = $count;
        }
    }
        
    //update product
    if (isset($_POST['updateProduct'])){
        $id = $_POST['id'];
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $image = $_POST['image'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $sale_price = $_POST['sale_price'];
        
        //Upload file and change file name
        if(basename( $_FILES['userFile']['name']) != null){
            $target_path = "product_images/";
            $target_path = $target_path.basename( $_FILES['userFile']['name']);
            if(move_uploaded_file($_FILES['userFile']['tmp_name'], $target_path)) {
                //echo "The file ".  basename( $_FILES['userFile']['name']). 
                " has been uploaded";
                $image = basename( $_FILES['userFile']['name']);
            } else{
                //echo "There was an error uploading the file, please try again!";
                $message = "true";
                $alertObj = new Alert("Error", "alert-danger", "There was an error uploading the file, please try again!"); //error uploading image
            }
        }
        
      if($sale_price > 0 && $maxSale == 5){
          if($_POST['alreadyOnSale'] == "true"){
              updateSQL($id, $name, $desc, $price, $quantity, $image, $sale_price); //function to update product
              $message = "true";
              $alertObj = new Alert("Product Updated", "alert-success", $name." has been updated");
          }else{
          $message = "true";
          $alertObj = new Alert("Error", "alert-danger", "Maximum number of items on sales."); //error max number of item on sale
          }
      }
      else{
          updateSQL($id, $name, $desc, $price, $quantity, $image, $sale_price); //function to update product
          $message = "true";
          $alertObj = new Alert("Product Updated", "alert-success", $name." has been updated");
      }
     //sets get Product id for details retrieval
     $getProduct = $id;
    }

    //Add product
    if (isset($_POST['addProduct'])){
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $image = null;
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $sale_price = $_POST['sale_price'];
        
        //Upload file and change file name
        if(basename( $_FILES['userFile']['name']) != null){
            $target_path = "product_images/";
            $target_path = $target_path.basename( $_FILES['userFile']['name']);
            if(move_uploaded_file($_FILES['userFile']['tmp_name'], $target_path)) {
                //echo "The file ".  basename( $_FILES['userFile']['name']). 
                " has been uploaded";
                $image = basename( $_FILES['userFile']['name']);
            } else{
                $message = "true";
                $alertObj = new Alert("Error", "alert-danger", "There was an error uploading the file, please try again!"); //error uploading image
            }
        }
        
      if($sale_price > 0 && $maxSale == 5){
          $message = "true";
          $alertObj = new Alert("Error", "alert-danger", "Maximum number of items on sales."); //product updated
      }
     else{
          //update product details
          $queryString = "INSERT INTO products(`name`, `desc`, `price`, `quantity`, `image`, `sale_price`) VALUES (?,?,?,?,?,?)";
          if($stmt = $mysqli->prepare($queryString)){
                $stmt->bind_param('ssdisd', $name, $desc, $price, $quantity, $image, $sale_price);
                $stmt->execute();
          }

         $message = "true";
         $alertObj = new Alert("Product Added", "alert-success", $name." has been added"); //product updated
     }
    }

    //sets get Product id from drop down list
    if (isset($_POST['getProduct'])){
        $getProduct = $_POST['getProduct'];
        }
    
    //Retrieves product details
    if ($getProduct >= 1) {        
        $queryString = "SELECT * FROM products WHERE product_id= ?";        
        if($stmt = $mysqli->prepare($queryString)){
            $stmt->bind_param('i', $getProduct);
	        $stmt->execute();
	        $stmt->store_result();
            $numRows = $stmt->num_rows;
	        $stmt->bind_result($id,$name,$desc,$price,$quantity,$image,$sale_price);
            while($row = $stmt->fetch()){
                $product  = new Product($name, $desc, $price, $quantity, $id, $sale_price, $image);
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'partials/head.php'; ?>
</head>

<body>
    <!-- Header -->
    <header class="intro-header" style="background-image: url('imgs/bg3.jpg')">
        <?php include 'partials/nav.php'; ?>
    </header>

    <!-- Main Content -->    
    <div class="container padr">
        <?php if(isset($_SESSION["login"]) && $_SESSION["login"] == "true"): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Edit Item</div>
                  <div class="panel-body">
                      <div class="row">
                    <label for="select" class="col-lg-2 col-md-4 col-sm-2 control-label">Iventory</label>
                      <div class="col-lg-10 col-md-8 col-sm-12">
                          <form role="form" action="admin.php" method="POST" >
                        <select name="getProduct" class="form-control" id="select" onchange="form.submit()" >
                          <?php
                                //Select all products
                                    $queryString = "SELECT * FROM products";        
                                    if($stmt = $mysqli->prepare($queryString)){
                                        $stmt->execute();
                                        $stmt->store_result();
                                        $stmt->bind_result($id,$name,$desc,$price,$quantity,$image,$sale_price);
                                        $int = 1;
                                        while($row = $stmt->fetch()){
                                            $selected ='';
                                            if($id==$getProduct) $selected ='selected="selected"'; 
                                            echo "<option value='$id'".$selected.">".$int.". ".$name."</option>";
                                            $int++;
                                        }
                                      }
                            ?>
                        </select>
                              </form>
                          <noscript><input type="submit" value="Submit"></noscript>
                              </form>
                          </div>
                          </div>                     
                    
                        <form role="form" action="admin.php" method="POST" enctype="multipart/form-data">
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Name</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input type='hidden' name='alreadyOnSale' value='<?php echo ($product->getSalePrice() > 0) ? "true" : "false"; ?>' />
                                <input type='hidden' name='id' value='<?php echo $product->getId(); ?>' />
                                <input class="form-control" name="name" value="<?php echo $product->getName(); ?>" type="text"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Description</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="desc" value="<?php echo $product->getDesc(); ?>" type="text"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Price</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="price" value="<?php echo number_format($product->getPrice(),2); ?>" type="text"/>
                              </div>
                            </div>                            
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Quantity</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="quantity" value="<?php echo $product->getQuantity(); ?>" type="text"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Sale Price</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="sale_price" value="<?php echo number_format($product->getSalePrice(),2); ?>" type="text"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Send Image</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input type='hidden' name='image' value='<?php echo $product->getImage(); ?>' />  
                                 <span class="btn-file">
                                     <input name="userFile" type="file" />
                                </span>
                              </div>
                            </div>
                            <div class="row pad-topr">
                                <div class="custom">
                                <input type='submit'  name='updateProduct' value='Submit' class='adminy btn btn-default'/></form>
                                </div>
                            </div>
                        </form>
                      </div>
                  </div>
                </div>
            <div class="col-lg-12">
                <div class="panel panel-default">
                  <div class="panel-heading ">Add Item</div>
                  <div class="panel-body">
                    <form role="form" action="admin.php" method="POST" enctype="multipart/form-data">
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Name</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="name" value="" type="text" required="required"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Description</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="desc" value="" type="text" required="required"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Price</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="price" value="" type="text" required="required"/>
                              </div>
                            </div>                            
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Quantity</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="quantity" value="" type="text" required="required"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Sale Price</label>
                              <div class="col-lg-10 col-md-8 col-sm-12">
                                <input class="form-control" name="sale_price" value="" type="text" required="required"/>
                              </div>
                            </div>
                            <div class="row pad-topr">
                            <label for="inputEmail" class="col-lg-2 col-md-4 col-sm-2 control-label">Send Image</label>
                              <div class="col-lg-8 col-md-8 col-sm-10"> 
                                <span class="btn-file">
                                    <input name="userFile" type="file" required="required"/>
                                  </span>
                              </div>
                            </div>
                            <div class="row pad-topr">
                                <div class="custom">
                                <input type='submit'  name='addProduct' value='Submit' class='adminy btn btn-default'/></form>
                                </div>
                            </div>
                        </form>
                  </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div id="wrapper">
              <div class="container row">
                  <div class="col-md-4 col-md-offset-4 sign-in txt-color">
                      <h2 >Sign in</h2>
                            <div class="row pad-topr">
                                <div class="col-xs-12">
                          <form role="form" action="admin.php" method="POST">
                          <div class="form-group ">
                            <label for="exampleInputEmail1">Username</label>
                            <input type="username" class="form-control" name="username" required="required">
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" name="password" required="required">
                          </div>                              
                          <div class="form-group fl-center"><button type="submit" class="adminy btn btn-default btn-large" name="signin" >Sign In</button></div>                            
                        </form>
                       </div>  
                      </div>
                </div>
              </div>
            </div>
        <?php endif; ?>
    </div>
    <footer>
        <?php include 'partials/footer.php'; ?>
    </footer>

    <!-- Load Modal and scripts -->
    <?php include 'partials/modal.php'; ?>
</body>
</html>
<?php 
 if ($message == 'true') {
     //shows modal 
     echo "<script type='text/javascript'>$('#messageModal').modal('show');</script>";
}

    //close connection
    mysqli_close($mysqli);
?>