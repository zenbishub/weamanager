<?php
class Sqlite
{
    private $werk;
    private $conn;
    private $path;
    public function __construct($werknummer, $path = "../")
    {
        $this->werk = $werknummer;
        $this->path = $path;
        $this->connection();
        error_reporting(0);
    }
    public function connection()
    {
        $db = new PDO('sqlite:' . $this->path . 'db/' . $this->werk . '/sqlite/weaDB.db');
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->conn = $db;
        return $this->conn;
    }
    public function sqliteSelect($q)
    {

        $res = $this->conn->query($q);
        foreach ($res as $row) {
            $array[] = $row;
        }
        return $array;
    }
    public function sqliteQuery($q)
    {
        return $this->conn->exec($q);
    }
    public function sqliteNumRows($q)
    {
        $res = $this->conn->prepare($q);
        $res->execute();
        $count = $res->rowCount();
        return $count;
    }
}