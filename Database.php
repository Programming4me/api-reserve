<?php
/*
* simple class for mysql
* writer : @programming4
* Language : PHP
*/

require_once('funcs.php');

class Database extends mysqli
{
    public static $instance;
    public static $messages = [
        'required' => "این فیلد الزامی می باشد",
        'unique' => "این فیلد تکراری می باشد",
        'no_result' => "موردی پیدا نشد ",
    ];

    public static function setInstance($args)
    {
        if (!self::$instance) return new Database($args);
        return self::$instance;
    }


    public $errors = [];

    public function __construct($db = [])
    {
        $localhost = $db['localhost'];
        $username = $db['username'];
        $password = $db['password'];
        $database = $db['database'];
        parent::__construct($localhost, $username, $password, $database);
    }

    public function row_exists($mysqli_result)
    {
        if (!$mysqli_result) return false;
        return $mysqli_result->num_rows > 0;
    }

    private function runQuery($query)
    {
        try {
            return $this->query($query);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }

    }

    public function fetch_all($mysqli_result)
    {
        if (!$mysqli_result) return false;
        $rows = [];
        while ($row = $mysqli_result->fetch_assoc()) $rows[] = $row;
        return $rows;
    }

    public function select($table, $where = '', $row = '*', $fetch_all = false)
    {
        $query = 'SELECT ' . $row . ' FROM ' . $table;
        if (!empty($where)) $query .= ' WHERE ' . $where;
        return $this->runQuery($query);
    }

    public function insert($table = '', array $array = [])
    {
        if (is_null($array)) return false;
        $query = 'INSERT INTO `' . $table . '` (' . implode(',', array_keys($array)) . ') VALUES (\'' . implode("','", array_values($array)) . '\')';
        return $this->runQuery($query);
    }

    public function update($table = '', array $array = [], $where = '')
    {
        $query = 'UPDATE `' . $table . '` SET ';
        $string = '';
        foreach ($array as $key => $obj) {
            $string .= "$key ='$obj' , ";
        }
        $string = rtrim($string, ' ,');
        $query .= $string;
        if (!is_null($where)) $query .= ' WHERE ' . $where;
        return $this->runQuery($query);
    }

    public function create($table, array $cloumns = [])
    {
        $query = 'CREATE TABLE IF NOT EXISTS ' . $table . '(' . rtrim(implode(',', $cloumns), ',') . ')';


        return $this->runQuery($query);
    }

    public function search($str = '')
    {
        $words = explode(' ', $str);
        $query = "SELECT * FROM `sounds` WHERE ";
        $like = "(query LIKE '%$str%' OR title LIKE '%$str%') OR ";
        foreach ($words as $word) {
            $like .= "(query LIKE '%$word%' OR title LIKE '%$word%') OR ";
        }
        $order = " ORDER BY (query LIKE '%$str%' OR title LIKE '%$str%') DESC";
        $query .= rtrim($like, ' OR') . $order;
        $result = $this->runQuery($query);
        if (!$result) return false;
        if ($this->row_exists($result)) return $this->fetch_all($result);
        return false;
    }

    public function delete($table = '', $where = '')
    {
        $query = 'DELETE FROM ' . $table . ' ';
        if (!empty($where)) $query .= 'WHERE ' . $where;

        return $this->runQuery($query);
    }

    /*  user */
    public function insertUser($userid)
    {
        $res = $this->select('users', "id='$userid'");
        if (!$this->row_exists($res)) return $this->insert('users', ['user_id' => $userid]);
        return false;
    }

    public function insertUserByUsername($username, $columns)
    {
        $res = $this->select('users', "username='$username'");
        if (!$this->row_exists($res)) return $this->insert('users', $columns);
        return false;
    }

    public function getUserByToken($token)
    {
        $res = $this->select('users', "access_token='$token'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getUserReportsByToken($token)
    {
        $user_id = $this->getUserByToken($token)['id'];
        $res = $this->select('reports', "user_id='$user_id'");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    /* user */

    public function getMp3Likes($hash)
    {
        $res = $this->select('sounds', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc()['likes'];
        return false;
    }

    public function getMp3Dislikes($hash)
    {
        $res = $this->select('sounds', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc()['dislikes'];
        return false;
    }

    public function likeMp3($hash, $userid)
    {
        $res = $this->select('dislikes', "user_id='$userid' AND hash='$hash'");
        $dislikes = $this->getMp3disLikes($hash);
        if ($this->row_exists($res)) {
            $this->update('sounds', ['dislikes' => $dislikes - 1], "hash='$hash'");
            $this->delete('dislikes', "user_id='$userid' AND hash='$hash'");
        }
        $res = $this->select('likes', "user_id='$userid' AND hash='$hash'");
        $likes = $this->getMp3Likes($hash);
        if (!$this->row_exists($res)) {
            $this->update('sounds', ['likes' => $likes + 1], "hash='$hash'");
            $this->insert('likes', ['user_id' => $userid, 'hash' => $hash]);
            return true;
        }
        return false;
    }

    public function dislikeMp3($hash, $userid)
    {
        $res = $this->select('likes', "user_id='$userid' AND hash='$hash'");
        $likes = $this->getMp3Likes($hash);
        if ($this->row_exists($res)) {
            $this->update('sounds', ['likes' => $likes - 1], "hash='$hash'");
            $this->delete('likes', "user_id='$userid' AND hash='$hash'");
        }
        $res = $this->select('dislikes', "user_id='$userid' AND hash='$hash'");
        $dislikes = $this->getMp3disLikes($hash);
        if (!$this->row_exists($res)) {
            $this->update('sounds', ['dislikes' => $dislikes + 1], "hash='$hash'");
            $this->insert('dislikes', ['user_id' => $userid, 'hash' => $hash]);
            return true;
        }
        return false;
    }

    public function addToDownloadList($hash, $userid)
    {
        $res = $this->select('downloads', "user_id='$userid' AND hash='$hash'");
        if (!$this->row_exists($res)) return $this->insert('downloads', ['user_id' => $userid, 'hash' => $hash]);
        return false;
    }

    public function getDownloadsCount($hash)
    {
        $res = $this->select('sounds', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc()['downloads'];
        return false;
    }

    public function addMp3DownloadCount($hash)
    {
        $res = $this->select('sounds', "hash='$hash'");
        $downloads = $this->getDownloadsCount($hash);
        if ($this->row_exists($res)) {
            $this->update('sounds', ['downloads' => $downloads + 1], "hash='$hash'");
        }
    }

    public function getMostDownloads()
    {
        $res = $this->select('sounds', 'downloads > 10 ORDER BY downloads DESC');
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getLatestMusics()
    {
        $res = $this->runQuery('SELECT * FROM `latest` LEFT JOIN `sounds` ON sounds.hash = latest.hash WHERE latest.hash IS NOT NULL AND sounds.hash IS NOT NULL ORDER BY latest.id DESC');
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getMostLiked()
    {
        $res = $this->select('sounds', 'likes > 10 ORDER BY likes DESC');
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getRecentlyDownloads($userid)
    {
        $res = $this->runQuery("SELECT * FROM `downloads` LEFT JOIN `sounds` ON sounds.hash = downloads.hash WHERE user_id=$userid ORDER BY downloads.id DESC");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getUserLiked($userid)
    {
        $res = $this->runQuery("SELECT * FROM `likes` LEFT JOIN `sounds` ON sounds.hash = likes.hash WHERE user_id=$userid ORDER BY likes.id DESC");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getUserFavorites($userid)
    {
        $res = $res = $this->runQuery("SELECT * FROM `favorites` LEFT JOIN `sounds` ON sounds.hash = favorites.hash WHERE user_id=$userid ORDER BY favorites.id DESC");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function isMp3ExistsFavorites($hash = '', $userid)
    {
        $res = $this->select('favorites', "hash='$hash' AND user_id='$userid'");
        return $this->row_exists($res);
    }

    public function addToFavoritesList($hash, $userid)
    {
        if ($this->isMp3ExistsFavorites($hash, $userid)) return false;
        return $this->insert('favorites', ['user_id' => $userid, 'hash' => $hash]);
    }

    public function deleteFromFavoritesList($hash, $userid)
    {
        if (!$this->isMp3ExistsFavorites($hash, $userid)) return false;
        return $this->delete('favorites', "user_id ='$userid'AND hash = '$hash'");
    }

    public function getMp3Info($hash = '')
    {
        $res = $this->select('sounds', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getNewAlbums()
    {
        $res = $this->select('albums', "hash IS NOT NULL ORDER BY id DESC");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getAlbumMusics($hash = '')
    {
        $res = $this->select('sounds', "album_id='$hash'");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getAlbumInfo($hash = '')
    {
        $res = $this->select('albums', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getMusicByTitle($title = '', $artist = '')
    {
        $res = $this->select('sounds', "query LIKE '%$title%' AND query LIKE '%$artist'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getLastId($table = '')
    {
        $res = $this->select($table, '', 'id');
        if ($this->row_exists($res)) return $res->fetch_assoc()['id'];
        return false;
    }

    public function getFirstMessage()
    {
        $res = $this->select('sendmsg');
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getFisrtId($table = '')
    {
        $res = $this->select($table, 'id IS NOT NULL ORDER BY id DESC', 'id');
        if ($this->row_exists($res)) return $res->fetch_assoc()['id'];
        return false;
    }

    public function updateStep($userid, $step)
    {
        $res = $this->select('users', "user_id=$userid");
        if ($this->row_exists($res)) return $this->update('users', ['step' => $step], "user_id=$userid");
        return false;
    }

    public function user($userid)
    {
        $res = $this->select('users', "user_id='$userid'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function getAdsList()
    {
        $res = $this->select('ads', "users_count IS NOT NULL ORDER BY id DESC");
        if ($this->row_exists($res)) return $this->fetch_all($res);
        return false;
    }

    public function getAdsByHash($hash = '')
    {
        $res = $this->select('ads', "hash='$hash'");
        if ($this->row_exists($res)) return $res->fetch_assoc();
        return false;
    }

    public function deleteAds($hash = '')
    {
        $res = $this->select('ads', "hash='$hash'");
        if ($this->row_exists($res)) return $this->delete('ads', "hash='$hash'");
        return false;
    }

    public function getFirstAds()
    {
        $res = $this->select('ads', "users_count IS NOT NULL ORDER BY id DESC");
        if ($this->row_exists($res)) return $res->fetch_assoc();;
        return false;
    }

    public function getEditedMp3()
    {
        $res = $this->select('sounds', "edit='1'");
        if ($this->row_exists($res)) return $res->num_rows;
        return 0;
    }
}
