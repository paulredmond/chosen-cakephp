<script type="text/javascript">
    $(document).ready(function(){
        $('.<?= $class ?>').chosen();
        $(".<?= $class ?>-deselect").chosen({
            allow_single_deselect:true
        });
    });
</script>
