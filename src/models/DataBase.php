<?php

namespace Zaacom\models;


use Exception;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class DataBase
{

    private static array $connexions = [];

	/**
	 * Execute SQL request
	 *
	 * @param string $database databaseName
	 * @param string $sql      SQL request
	 * @param array  $params   parameters
	 *
	 * @return PDOStatement
	 * @throws \Throwable
	 */
    public static function executerRequete(string $database, string $sql, array $params = []): PDOStatement
	{
		$results = self::getBdd($database)->prepare($sql);
		if (!empty($params)) {
			foreach ($params as $key => $value) {
				if (is_null($value)) {
					$results->bindValue($key, $value, PDO::PARAM_NULL);
				} else {
					$results->bindValue($key, $value);
				}
			}
		}
		$success = $results->execute();
		$results->successExecuteRequest = $success;
		return $results;
    }

	/**
	 * Retourne les résultat de la requete sour forme d'un tableau associatif
	 *
	 * @param string $database databaseName
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return array
	 * @throws Throwable
	 */
    public static function getData(string $database, string $sql, array $params = []): array
	{
        $data = self::executerRequete($database, $sql, $params);

		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Retourne la première colonne du résultat d'une requete sous forme d'un tableau plat
	 *
	 * @param string $database databaseName
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return array
	 * @throws Throwable
	 */
    public static function getColumn(string $database, string $sql, array $params = []): array
	{
        return array_map(function ($r) {
            return $r[0];
        }, self::executerRequete($database, $sql, $params)->fetchAll(PDO::FETCH_NUM));
    }

	/**
	 * Retourne la première ligne du résultat d'une requête
	 *
	 * @param string $database
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return array|false
	 * @throws Throwable
	 */
    public static function getRow(string $database, string $sql, array $params = []): bool|array
	{
        $sql .= ' LIMIT 1';

        $data = self::getData($database, $sql, $params);

		return $data[0] ?? false;
	}

	/**
	 * Retourne la valeur du premier champ de la première ligne dans une requête
	 *
	 * @param string $database
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return string|false
	 * @throws Throwable
	 */
    public static function getValue(string $database, string $sql, array $params = []): bool|string
	{
        $data = self::getRow($database, $sql, $params);
        if ($data) return array_shift($data);
        else return false;
    }

	/**
	 * Retourne les valeurs du premier champ de chaque ligne dans une requête
	 *
	 * @param string $database
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return array
	 * @throws Throwable
	 */
    public static function getValues(string $database, string $sql, array $params = []): array
	{
        $res = [];
        $data = self::getData($database, $sql, $params);
        foreach ($data as $row) {
            $res[] = array_shift($row);
        }
        return $res;
    }

	/**
	 * @param string $database
	 *
	 * @return int id du dernier élément inséré dans la BDD
	 * @throws Exception
	 */
	public static function getLastInsertId(string $database): int
	{
		return self::getBdd($database)->lastInsertId();
	}

	/**
	 * Renvoie un objet de connexion à la BDD en initialisant la connexion au besoin
	 *
	 * @return PDO Objet PDO de connexion à la BDD
	 * @throws Exception
	 */
    public static function getBdd(string $database)
    {

        if (!array_key_exists($database, self::$connexions)) {
            $server = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_HOST);
            $port = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_PORT);
            $username = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_USERNAME);
            $password = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_PASSWORD);

			self::$connexions[$database] = new PDO("mysql:host=$server;dbname=$database;port=$port", $username, $password);
			self::$connexions[$database]->query("SET NAMES UTF8");
        }

        return self::$connexions[$database];
    }
}
