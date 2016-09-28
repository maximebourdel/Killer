<?php

namespace Games\KillerBundle\Password;

class Password
{
    
    
    protected $array; // get all the characters into an array

    public function __construct()
    {
        $this->array = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
    
    public function generateNewPassword(){
        
        shuffle($this->array); // randomize the array
        $listWords = array_slice($this->array, 0, 6); // get the first six (random) characters out
        
        return  implode('', $listWords); // smush them back into a string
    }
}
