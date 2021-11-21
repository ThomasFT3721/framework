<?php

namespace Zaacom\Models;

use Zaacom\Environment\EnvironmentVariable;
use Zaacom\Environment\EnvironmentVariablesIdentifiers;
use PDO;
use Throwable;
use PDOException;

class Database
{

    private static array $connexions = [];

    /**
     * Execute SQL request
     * 
     * @param string $database databaseName
     * @param string $sql SQL request
     * @param array $params parameters
     * @return \PDOStatement
     */
    public static function executerRequete(string $database, string $sql, $params = null)
    {
        try {
            if (empty($params)) {
                $results = self::getBdd($database)->query($sql);
            } else {
                $results = self::getBdd($database)->prepare($sql);
                foreach ($params as $key => $value) {
                    if (is_null($value)) {
                        $results->bindValue($key, $value, PDO::PARAM_NULL);
                    } else {
                        $results->bindValue($key, $value);
                    }
                }

                $results->execute();
            }
        } catch (Throwable $th) {
            throw $th;
        }
        return $results;
    }

    /**
     * Retourne les résultat de la requete sour forme d'un tableau associatif
     * 
     * @param string $database databaseName
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function getData(string $database, string $sql, $params = null)
    {
        $data = self::executerRequete($database, $sql, $params);

        try {
            return $data->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retourne la première colonne du résultat d'une requete sous forme d'un tableau plat
     * 
     * @param string $database databaseName
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function  getColumn(string $database, string $sql, $params = null)
    {
        return array_map(function ($r) {
            return $r[0];
        }, self::executerRequete($database, $sql, $params)->fetchAll(PDO::FETCH_NUM));
    }

    /**
     * Retourne la première ligne du résultat d'une requête
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function getRow(string $database, string $sql, $params = null)
    {
        $sql .= ' LIMIT 1';

        $data = self::getData($database, $sql, $params);

        if (isset($data[0])) return $data[0];
        else return false;
    }

    /**
     * Retourne la valeur du premier champ de la première ligne dans une requête
     * 
     * @param string $sql
     * @param array $params
     * @return string
     */
    public static function getValue(string $database, string $sql, $params = null)
    {
        $data = self::getRow($database, $sql, $params);
        if ($data) return array_shift($data);
        else return false;
    }

    /**
     * Retourne les valeurs du premier champ de chaque ligne dans une requête
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function getValues(string $database, string $sql, $params = null)
    {
        $res = [];
        $data = self::getData($database, $sql, $params);
        foreach ($data as $row) {
            $res[] = array_shift($row);
        }
        return $res;
    }

    /**
     * Exécute une requête INSERT
     * 
     * @param string $table Nom de la table
     * @param array $data Tableau assciatif des données à insérer
     * @param bool $multiple FALSE par défaut
     * @param string $insert_type Type d'insertion, par défaut "INSERT"
     *
        public static function insert($table, $data, $multiple = FALSE, $insert_type = 'INSERT')
        {
            $keys = array();
            $values = array();
            $params = array();

            // Si insertion multiple
            if ($multiple) {
                $values_stringified = array();

                foreach ($data as $index => $data_row) {
                    foreach ($data_row as $key => $value) {
                        if (!in_array("`$key`", $keys)) $keys[] = "`$key`";

                        $param = ":$key$index";

                        $values[] = $param;

                        $params[$param] = $value;
                    }

                    $values_stringified[] = '(' . implode(', ', $values) . ')';
                }

                $keys_stringified = implode(', ', $keys);

                $sql = $insert_type . ' INTO `' . $table . '` (' . $keys_stringified . ')  VALUES ' . implode(', ', $values_stringified);
            } else {
                foreach ($data as $key => $value) {
                    $keys[] = "`$key`";

                    $param = ":$key";

                    $values[] = $param;

                    $params[$param] = $value;
                }

                $keys_stringified = implode(', ', $keys);
                $values_stringified = implode(', ', $values);

                $sql = $insert_type . ' INTO `' . $table . '` (' . $keys_stringified . ')  VALUES (' . $values_stringified . ')';
            }
            $exec = self::executerRequete($sql, $params);
            if ($exec)
                return true;
            return false;
        }

        /**
     * Exécute une requête UPDATE
     * 
     * @param string $table Nom de la table
     * @param array $data Tableau associatif des avec comme clé le nom du champ
     * @param string $where Condition WHERE
     *
        public static function update($table, $data, $where = '', $where_params = array())
        {
            $params = array();
            $fields = array();

            foreach ($data as $key => $value) {
                $fields[] = "`$key` = :$key";

                $params[":$key"] = $value;
            }

            $fields_stringified = implode(', ', $fields);

            $sql = 'UPDATE `' . $table . '` SET ' . $fields_stringified;

            if ($where) $sql .= ' WHERE ' . $where;

            if ($where_params) {
                foreach ($where_params as $key_param => $param) {
                    $params[$key_param] = $param;
                }
            }

            if (self::executerRequete($sql, $params))
                return true;
            return false;
        }

        /**
     * Exécute une requête DELETE
     * 
     * @param string $table Nom de la table
     * @param string $where Condition WHERE
     * 
     * @author ThomasFONTAINE--TUFFERY
     *
        public static function delete($table, $where = '', $where_params = array())
        {
            $params = array();
            $fields = array();

            $fields_stringified = implode(', ', $fields);

            $sql = 'DELETE FROM `' . $table . '`';

            if ($where) $sql .= ' WHERE ' . $where;

            if ($where_params) {
                foreach ($where_params as $key_param => $param) {
                    $params[$key_param] = $param;
                }
            }
            if (self::executerRequete($sql, $params)->errorCode() == "00000")
                return true;
            return false;
        }
     */


    /**
     * Renvoie un objet de connexion à la BDD en initialisant la connexion au besoin
     * 
     * @return PDO Objet PDO de connexion à la BDD
     */
    public static function getBdd(string $database)
    {

        if (!array_key_exists($database, self::$connexions)) {
            $server = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_HOST);
            $port = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_PORT);
            $username = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_USERNAME);
            $password = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_PASSWORD);

            try {
                self::$connexions[$database] = new PDO("mysql:host=$server;dbname=$database;port=$port", $username, $password);
            } catch (PDOException $e) {
                throw $e;
            }
            self::$connexions[$database]->query("SET NAMES UTF8");
        }

        return self::$connexions[$database];
    }
}
