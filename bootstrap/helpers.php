<?php
function route_class()
{
    str_replace('.', '-', Route::currentRouteName());
}