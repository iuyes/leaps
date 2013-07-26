<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
include $this->view ( 'header' );
?>

<div class="pad_10">
  <div class="table-list">
    <xmp><?php echo $structure?></xmp>
  </div>
</div>
</body></html>