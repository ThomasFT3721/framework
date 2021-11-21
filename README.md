

    /**
     * Récupere un objet de Type passé en paramètre
     * 
     * @param string $objectClass Nom de la class
     * @param string $id id de l'objet
     * 
     * @author ThomasFONTAINE--TUFFERY
     */
    public static function get(string $objectClass, $id)
    {
        return self::getObject($objectClass, "SELECT * FROM `" . $objectClass::$table . "` WHERE `" . $objectClass::$id . "`=:id", [':id' => $id]);
    }

    /**
     * Récupere un objet de Type passé en paramètre
     * 
     * @param string $objectClass Nom de la class
     * @param string $where where string
     * @param array $whereParams where params
     * @param string $orderBy order by string
     * @param string $innerJoin inner join string
     * @param int $limit limit of result
     * 
     * @author ThomasFONTAINE--TUFFERY
     */
    public static function getAll(string $objectClass, string $where = "", array $whereParams = [], string $orderBy = "", string $join = "", string $select = "*", int $limit = -1, int $offset = -1, bool $printLogSQL = false, string $groupBy = ""): array
    {
        $query = "SELECT " . $select . " FROM `" . $objectClass::$table . "`" . ($join != "" ? " " . $join : "") . ($where != "" ? " WHERE " . $where : "") . ($groupBy != "" ? " GROUP BY " . $groupBy : "") . ($orderBy != "" ? " ORDER BY " . $orderBy : "") . ($limit != -1 ? " LIMIT " . $limit : "") . ($offset != -1 ? " OFFSET " . $offset : "");
        if ($printLogSQL || self::SHOW_LOG) {
            Functions::printLog("Query SQL [\n     ObjectClass:\"" . $objectClass . "\"\n     Query:\"" . $query . "\"\n     Parameters:\"" . json_encode($whereParams) . "\"\n]");
        }
        return self::getObjects($objectClass, $query, $whereParams);
    }
    /**
     * Récupere un objet de Type passé en paramètre
     * 
     * @param string $objectClass Nom de la class
     * @param string $where where string
     * @param array $whereParams where params
     * @param string $orderBy order by string
     * @param string $innerJoin inner join string
     * @param int $limit limit of result
     * 
     * @author ThomasFONTAINE--TUFFERY
     */
    public static function getBy(string $objectClass, string $where = "", array $whereParams = [], string $orderBy = "", string $join = "", string $select = "*", int $limit = -1, int $offset = -1, bool $printLogSQL = false, string $groupBy = "")
    {
        $query = "SELECT " . $select . " FROM `" . $objectClass::$table . "`" . ($join != "" ? " " . $join : "") . ($where != "" ? " WHERE " . $where : "") . ($groupBy != "" ? " GROUP BY " . $groupBy : "") . ($orderBy != "" ? " ORDER BY " . $orderBy : "") . ($offset != -1 ? " OFFSET " . $offset : "");
        if ($printLogSQL || self::SHOW_LOG) {
            Functions::printLog("Query SQL [\n     ObjectClass:\"" . $objectClass . "\"\n     Query:\"" . $query . "\"\n     Parameters:\"" . json_encode($whereParams) . "\"\n]");
        }
        return self::getObject($objectClass, $query, $whereParams);
    }

    /**
     * Récupere une liste d'objet de Type passé en paramètre
     * 
     * @param string $objectClass Nom de la class
     * @param string|array $array Tableau contanant les tableau de clé/valeur
     * @param boolean $one = false Si on veut convertir en un seul objet
     * 
     * @author ThomasFONTAINE--TUFFERY
     */
    public static function getObjects(string $objectClass, $reqOrArray, array $params = [])
    {
        if (is_array($reqOrArray) == true) {
            return self::toObjectsArray($objectClass, $reqOrArray);
        } else {
            return self::toObjectsReq($objectClass, $reqOrArray, $params);
        }
    }
    private static function toObjectsReq($objectClass, $req, $params = [])
    {
        $res = [];
        foreach (self::getData($req, $params) as $key => $row) {
            $res[] = self::getObject($objectClass, $row);
        }
        return $res;
    }
    private static function toObjectsArray($objectClass, $array)
    {
        $res = [];
        foreach ($array as $key => $row) {
            $res[] = self::getObject($objectClass, $row);
        }
        return $res;
    }

    /**
     * Récupere un objet de Type passé en paramètre
     * 
     * @param string $objectClass Nom de la class
     * @param string|array $reqOrArray Tableau contanant les tableau de clé/valeur
     * @param array $params Tableau contanant les paramètres si c'est une requête
     * 
     * @author ThomasFONTAINE--TUFFERY
     */
    public static function getObject($objectClass, $reqOrArray, array $params = [])
    {
        if (is_array($reqOrArray) == true) {
            return self::toObjectArray($objectClass, $reqOrArray);
        } else {
            return self::toObjectReq($objectClass, $reqOrArray, $params);
        }
    }

    private static function toObjectReq($objectClass, $req, $params = [])
    {
        try {
            return new $objectClass(self::getRow($req, $params));
        } catch (\Throwable $th) {
            return null;
        }
    }
    private static function toObjectArray($objectClass, $array)
    {
        try {
            return new $objectClass($array);
        } catch (\Throwable $th) {
            return null;
        }
    }