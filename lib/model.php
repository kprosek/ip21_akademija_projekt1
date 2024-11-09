<?php
function getApiData($api)
{
    $json = file_get_contents($api);
    if (empty($json)) {
        return false;
    }

    $dataArray = json_decode($json, true);
    if ($dataArray === null) {
        return false;
    }

    return $dataArray;
}

// GET List
function getList($api, $key)
{
    $listData = getApiData($api);
    $list = [];
    foreach ($listData['data'] as $data) {
        $list[] = $data[$key];
    }
    return $list;
}
