<?php

    set_exception_handler(function ($exception) {
        echo "Uncaught exception: " , $exception->getMessage(), "\n";
    });

?>