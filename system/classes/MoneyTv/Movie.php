<?php

namespace MoneyTv;

use HCStudio\Orm;
use HCStudio\Util;

use MoneyTv\CatalogMovieGender;

class Movie extends Orm {
  protected $tblName  = 'movie';
  const TOP_TEN_STATUS = 3;
  public function __construct() {
    parent::__construct();
  }
  
  public static function addMovieTopTen(array $movie = null) : bool
  {
    if(isset($movie) === true) {
      $Movie = new Movie;
      
      $Movie->loadWhere('link = ?',$movie['link']);
      $Movie->title = 'TopTen';
      $Movie->link = $movie['link'];
      $Movie->image = $movie['image'];
      $Movie->status = self::TOP_TEN_STATUS;
      $Movie->create_date = time();
  
      return $Movie->save();
    }

    return false;
  }

  public static function addMoviesTopTen(array $movies = null) : bool
  {
    $saved = 0;
    foreach($movies as $movie) {
      if(self::addMovieTopTen($movie))
      {
        $saved++;
      }
    }

    return $saved === sizeof($movies);
  }

  public static function getFilter(array $filter = null) 
  {
    $query = "";

    if($filter['catalog_movie_gender_id'])
    {
      $ids = implode(",",$filter['catalog_movie_gender_id']);
      
      $query .= " AND movie.catalog_movie_gender_id LIKE '%{$ids}%'";
    } else if($filter['title']) {
      $query .= " AND movie.title LIKE '%{$filter['title']}%' OR movie.description LIKE '%{$filter['title']}%'";
    }

    return $query;
  }

  public function _getAll(string $filter = null) 
  {
    if($movies = $this->getAll($filter))
    {
      $CatalogMovieGender = new CatalogMovieGender;

      return array_map(function($movie) use($CatalogMovieGender){
        if($movie['catalog_movie_gender_id'])
        {
          $movie['catalog_movie_gender_id'] = json_decode($movie['catalog_movie_gender_id'],true);
          $movie['genders'] = $CatalogMovieGender->getGendersIn(implode(",",$movie['catalog_movie_gender_id']));
        } else {
          $movie['genders'] = false;
        }

        return $movie;
      },$movies);
    }
  }
  
  public function getAll(string $filter = null) 
  {
    $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.title,
                {$this->tblName}.year,
                {$this->tblName}.image,
                {$this->tblName}.catalog_movie_gender_id,
                {$this->tblName}.description,
                {$this->tblName}.link
            FROM
                {$this->tblName}
            WHERE
                {$this->tblName}.status = '1'
                {$filter}
            ";

    return $this->connection()->rows($sql);
  }
  
  public function get(int $movie_id = null) 
  {
    if(isset($movie_id) === true)
    {
      $sql = "SELECT 
                  {$this->tblName}.{$this->tblName}_id,
                  {$this->tblName}.title,
                  {$this->tblName}.year,
                  {$this->tblName}.image,
                  {$this->tblName}.description,
                  {$this->tblName}.player,
                  {$this->tblName}.link
              FROM
                  {$this->tblName}
              WHERE
                  {$this->tblName}.movie_id = '{$movie_id}'
              AND 
                  {$this->tblName}.status = '1'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function exist(string $title = null) : bool
  {
    if(isset($title) === true)
    {
      $title = Util::sanitizeString($title, true);

      $sql = "SELECT 
                  {$this->tblName}.{$this->tblName}_id
              FROM
                  {$this->tblName}
              WHERE
                  {$this->tblName}.title LIKE '%{$title}%'
              AND 
                  {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public function getLastTopTenMovies() 
  {
    $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.title,
                {$this->tblName}.year,
                {$this->tblName}.image,
                {$this->tblName}.description,
                {$this->tblName}.link
            FROM
                {$this->tblName}
            WHERE
                {$this->tblName}.status = '".self::TOP_TEN_STATUS."'
            ORDER BY
                {$this->tblName}.create_date
            DESC 
            LIMIT 10
            ";

    return $this->connection()->rows($sql);
  }
}