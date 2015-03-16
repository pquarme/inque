<?php 

/* Creates a new mysql connection */
function getConnect(){
    //Server details
    $host = "";
    $user="";
    $pswd="";
    $db="";
    
    //open a connection
    $mysqli = new mysqli($host, $user, $pswd, $db);

    //check the connection
	if ($mysqli->connect_error){
        echo "Connection failed: " . mysqli_connect_errno();
        exit();
    }
    
    return $mysqli;
}

/* a product class */
class Product{
    private $name, $description, $price, $quantity, $id, $sale_price, $image;
    
    function __construct($name, $description, $price, $quantity, $id, $sale_price, $image){
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->id = $id;
        $this->sale_price = $sale_price;
        $this->image = $image;
    }
    
    function getName(){
        return $this->name;
    }
    
    function getDesc(){
        return $this->description;
    }
    
    function getPrice(){
        return $this->price;
    }
    function getQuantity(){
        return $this->quantity;
    }
    function getId(){
        return $this->id;
    }
    
    function getSalePrice(){
        return $this->sale_price;
    }
    function getImage(){
        return $this->image;
    }
}

/* Error Object */
class Alert{
    private $title, $type, $message;
    
    function __construct($title, $type, $message){
        $this->title = $title;
        $this->type = $type;
        $this->message = $message;
    }
    
    function getTitle(){
        return $this->title;
    }
    
    function getType(){
        return $this->type;
    }
    
    function getMessage(){
        return $this->message;
    }
}

function updateSQL($id, $name, $desc, $price, $quantity, $image, $sale_price){
    $mysqli = getConnect();
    
    $queryString = "UPDATE products SET `product_id`=?, `name`=?, `desc`=?, `price`=?, `quantity`=?, `image`=?, `sale_price`=? WHERE `product_id`=?";
          if($stmt = $mysqli->prepare($queryString)){
              //echo "<script type='text/javascript'>alert('worked')</script>";
                $stmt->bind_param('issdisdi', $id, $name, $desc, $price, $quantity, $image, $sale_price, $id);
                $stmt->execute();
    }
}
?>