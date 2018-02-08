<?php
function ReturnPennies($amount) 
{
    $amount = $amount * 100;
    return $amount;
}
function ReturnDollars($amount) 
{
    $amount = $amount / 100;
    return $amount;
}