<?php
function route_class()
{
    str_replace('.', '-', Route::currentRouteName());
}

function res($status, $msg, $data = [])
{
    $arr = [
        'status' => $status,
        'msg' => $msg
    ];
    if (count($data) > 0) {
        $arr['data'] = $data;
    }
    return $arr;
}