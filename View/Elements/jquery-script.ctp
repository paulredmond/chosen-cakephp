<script>
    $(document).ready(function(){
        $('.<?php echo $class ?>').chosen();
        $(".<?php echo $class ?>-deselect").chosen({
            allow_single_deselect:true
        });
    });
</script>