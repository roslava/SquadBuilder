<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo '<p style="font-family: Arial, sans-serif; color: #797979; font-size:  20px; font-weight: bold; font-style: italic;">This is a simple API that manage players with some skills and select the best players with the desired position/skill for the dream team.</p>';
});
