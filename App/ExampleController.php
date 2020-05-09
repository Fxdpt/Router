<?php

namespace App;

class ExampleController
{
    public function show()
    {
        echo "C'est l'example controller qui te parle";
    }

    public function showOne(int $id)
    {
        echo "C'est l'example $id";
    }
}