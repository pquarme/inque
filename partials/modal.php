<!-- jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<!-- Error -->
<?php if($alertObj !== null) : ?>
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $alertObj->getTitle(); ?></h4>
      </div>
      <div class="modal-body">
        <div  <?php echo "class='alert ".$alertObj->getType()."'"; ?> role='alert'>  <p class='alert-link'><?php echo $alertObj->getMessage(); ?></p></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="closey btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<!--Add to cart Modal -->
<?php if($product !== null) : ?>
<div class="container modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Added to Cart</h4>
      </div>
      <div class="modal-body">
    <?php echo           "<h4>".$product->getName()."</h4>".
                         "<p>".$product->getDesc()."</p>".
                         "<p></span>Price: ".number_format($product->getPrice(),2)." Quantity: ".$product->getQuantity()."</p>";
    ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="closey btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


