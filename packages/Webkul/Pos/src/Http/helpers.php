<?php
    use Webkul\Pos\Pos;
    
    if (! function_exists('bagisto_pos')) {
        function bagisto_pos()
        {
            return app()->make(Pos::class);
        }
    }
?>