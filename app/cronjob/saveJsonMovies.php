<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = file_get_contents(TO_ROOT."/src/files/json/movies.json");
$data = json_decode($data,true);


array_map(function($movie){
    $Movie = new Infinity\Movie;
    $Movie->title = $movie['title'];
    $Movie->description = $movie['description'];
    $Movie->link = $movie['link'];
    $Movie->image = 'https://Infinity.site/src/files/img/movie-bg.png';
    $Movie->create_date = time();
    $Movie->save();
},$data);

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 