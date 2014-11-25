<?php

namespace Base;

use \Notiz as ChildNotiz;
use \NotizQuery as ChildNotizQuery;
use \Exception;
use \PDO;
use Map\NotizTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'notiz' table.
 *
 *
 *
 * @method     ChildNotizQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNotizQuery orderByBesitzerId($order = Criteria::ASC) Order by the besitzer_id column
 * @method     ChildNotizQuery orderByProjektId($order = Criteria::ASC) Order by the projekt_id column
 * @method     ChildNotizQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildNotizQuery orderByText($order = Criteria::ASC) Order by the text column
 *
 * @method     ChildNotizQuery groupById() Group by the id column
 * @method     ChildNotizQuery groupByBesitzerId() Group by the besitzer_id column
 * @method     ChildNotizQuery groupByProjektId() Group by the projekt_id column
 * @method     ChildNotizQuery groupByName() Group by the name column
 * @method     ChildNotizQuery groupByText() Group by the text column
 *
 * @method     ChildNotizQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNotizQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNotizQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNotizQuery leftJoinBesitzer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Besitzer relation
 * @method     ChildNotizQuery rightJoinBesitzer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Besitzer relation
 * @method     ChildNotizQuery innerJoinBesitzer($relationAlias = null) Adds a INNER JOIN clause to the query using the Besitzer relation
 *
 * @method     ChildNotizQuery leftJoinProjekt($relationAlias = null) Adds a LEFT JOIN clause to the query using the Projekt relation
 * @method     ChildNotizQuery rightJoinProjekt($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Projekt relation
 * @method     ChildNotizQuery innerJoinProjekt($relationAlias = null) Adds a INNER JOIN clause to the query using the Projekt relation
 *
 * @method     ChildNotizQuery leftJoinToDoNotiz($relationAlias = null) Adds a LEFT JOIN clause to the query using the ToDoNotiz relation
 * @method     ChildNotizQuery rightJoinToDoNotiz($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ToDoNotiz relation
 * @method     ChildNotizQuery innerJoinToDoNotiz($relationAlias = null) Adds a INNER JOIN clause to the query using the ToDoNotiz relation
 *
 * @method     ChildNotizQuery leftJoinRezept($relationAlias = null) Adds a LEFT JOIN clause to the query using the Rezept relation
 * @method     ChildNotizQuery rightJoinRezept($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Rezept relation
 * @method     ChildNotizQuery innerJoinRezept($relationAlias = null) Adds a INNER JOIN clause to the query using the Rezept relation
 *
 * @method     ChildNotizQuery leftJoinRezeptNotiz($relationAlias = null) Adds a LEFT JOIN clause to the query using the RezeptNotiz relation
 * @method     ChildNotizQuery rightJoinRezeptNotiz($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RezeptNotiz relation
 * @method     ChildNotizQuery innerJoinRezeptNotiz($relationAlias = null) Adds a INNER JOIN clause to the query using the RezeptNotiz relation
 *
 * @method     \PersonQuery|\ProjektQuery|\ToDoNotizQuery|\RezeptQuery|\RezeptNotizQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildNotiz findOne(ConnectionInterface $con = null) Return the first ChildNotiz matching the query
 * @method     ChildNotiz findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNotiz matching the query, or a new ChildNotiz object populated from the query conditions when no match is found
 *
 * @method     ChildNotiz findOneById(int $id) Return the first ChildNotiz filtered by the id column
 * @method     ChildNotiz findOneByBesitzerId(int $besitzer_id) Return the first ChildNotiz filtered by the besitzer_id column
 * @method     ChildNotiz findOneByProjektId(int $projekt_id) Return the first ChildNotiz filtered by the projekt_id column
 * @method     ChildNotiz findOneByName(string $name) Return the first ChildNotiz filtered by the name column
 * @method     ChildNotiz findOneByText(string $text) Return the first ChildNotiz filtered by the text column
 *
 * @method     ChildNotiz[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildNotiz objects based on current ModelCriteria
 * @method     ChildNotiz[]|ObjectCollection findById(int $id) Return ChildNotiz objects filtered by the id column
 * @method     ChildNotiz[]|ObjectCollection findByBesitzerId(int $besitzer_id) Return ChildNotiz objects filtered by the besitzer_id column
 * @method     ChildNotiz[]|ObjectCollection findByProjektId(int $projekt_id) Return ChildNotiz objects filtered by the projekt_id column
 * @method     ChildNotiz[]|ObjectCollection findByName(string $name) Return ChildNotiz objects filtered by the name column
 * @method     ChildNotiz[]|ObjectCollection findByText(string $text) Return ChildNotiz objects filtered by the text column
 * @method     ChildNotiz[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class NotizQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Base\NotizQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'notizverwaltung', $modelName = '\\Notiz', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNotizQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNotizQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildNotizQuery) {
            return $criteria;
        }
        $query = new ChildNotizQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildNotiz|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NotizTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NotizTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNotiz A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, besitzer_id, projekt_id, name, text FROM notiz WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildNotiz $obj */
            $obj = new ChildNotiz();
            $obj->hydrate($row);
            NotizTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildNotiz|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NotizTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NotizTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NotizTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NotizTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotizTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the besitzer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBesitzerId(1234); // WHERE besitzer_id = 1234
     * $query->filterByBesitzerId(array(12, 34)); // WHERE besitzer_id IN (12, 34)
     * $query->filterByBesitzerId(array('min' => 12)); // WHERE besitzer_id > 12
     * </code>
     *
     * @see       filterByBesitzer()
     *
     * @param     mixed $besitzerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByBesitzerId($besitzerId = null, $comparison = null)
    {
        if (is_array($besitzerId)) {
            $useMinMax = false;
            if (isset($besitzerId['min'])) {
                $this->addUsingAlias(NotizTableMap::COL_BESITZER_ID, $besitzerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($besitzerId['max'])) {
                $this->addUsingAlias(NotizTableMap::COL_BESITZER_ID, $besitzerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotizTableMap::COL_BESITZER_ID, $besitzerId, $comparison);
    }

    /**
     * Filter the query on the projekt_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProjektId(1234); // WHERE projekt_id = 1234
     * $query->filterByProjektId(array(12, 34)); // WHERE projekt_id IN (12, 34)
     * $query->filterByProjektId(array('min' => 12)); // WHERE projekt_id > 12
     * </code>
     *
     * @see       filterByProjekt()
     *
     * @param     mixed $projektId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByProjektId($projektId = null, $comparison = null)
    {
        if (is_array($projektId)) {
            $useMinMax = false;
            if (isset($projektId['min'])) {
                $this->addUsingAlias(NotizTableMap::COL_PROJEKT_ID, $projektId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($projektId['max'])) {
                $this->addUsingAlias(NotizTableMap::COL_PROJEKT_ID, $projektId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotizTableMap::COL_PROJEKT_ID, $projektId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotizTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the text column
     *
     * Example usage:
     * <code>
     * $query->filterByText('fooValue');   // WHERE text = 'fooValue'
     * $query->filterByText('%fooValue%'); // WHERE text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $text The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function filterByText($text = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($text)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $text)) {
                $text = str_replace('*', '%', $text);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotizTableMap::COL_TEXT, $text, $comparison);
    }

    /**
     * Filter the query by a related \Person object
     *
     * @param \Person|ObjectCollection $person The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByBesitzer($person, $comparison = null)
    {
        if ($person instanceof \Person) {
            return $this
                ->addUsingAlias(NotizTableMap::COL_BESITZER_ID, $person->getId(), $comparison);
        } elseif ($person instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NotizTableMap::COL_BESITZER_ID, $person->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByBesitzer() only accepts arguments of type \Person or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Besitzer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function joinBesitzer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Besitzer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Besitzer');
        }

        return $this;
    }

    /**
     * Use the Besitzer relation Person object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PersonQuery A secondary query class using the current class as primary query
     */
    public function useBesitzerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBesitzer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Besitzer', '\PersonQuery');
    }

    /**
     * Filter the query by a related \Projekt object
     *
     * @param \Projekt|ObjectCollection $projekt The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByProjekt($projekt, $comparison = null)
    {
        if ($projekt instanceof \Projekt) {
            return $this
                ->addUsingAlias(NotizTableMap::COL_PROJEKT_ID, $projekt->getId(), $comparison);
        } elseif ($projekt instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NotizTableMap::COL_PROJEKT_ID, $projekt->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProjekt() only accepts arguments of type \Projekt or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Projekt relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function joinProjekt($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Projekt');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Projekt');
        }

        return $this;
    }

    /**
     * Use the Projekt relation Projekt object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ProjektQuery A secondary query class using the current class as primary query
     */
    public function useProjektQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProjekt($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Projekt', '\ProjektQuery');
    }

    /**
     * Filter the query by a related \ToDoNotiz object
     *
     * @param \ToDoNotiz|ObjectCollection $toDoNotiz  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByToDoNotiz($toDoNotiz, $comparison = null)
    {
        if ($toDoNotiz instanceof \ToDoNotiz) {
            return $this
                ->addUsingAlias(NotizTableMap::COL_ID, $toDoNotiz->getNotizId(), $comparison);
        } elseif ($toDoNotiz instanceof ObjectCollection) {
            return $this
                ->useToDoNotizQuery()
                ->filterByPrimaryKeys($toDoNotiz->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByToDoNotiz() only accepts arguments of type \ToDoNotiz or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ToDoNotiz relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function joinToDoNotiz($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ToDoNotiz');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ToDoNotiz');
        }

        return $this;
    }

    /**
     * Use the ToDoNotiz relation ToDoNotiz object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ToDoNotizQuery A secondary query class using the current class as primary query
     */
    public function useToDoNotizQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinToDoNotiz($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ToDoNotiz', '\ToDoNotizQuery');
    }

    /**
     * Filter the query by a related \Rezept object
     *
     * @param \Rezept|ObjectCollection $rezept  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByRezept($rezept, $comparison = null)
    {
        if ($rezept instanceof \Rezept) {
            return $this
                ->addUsingAlias(NotizTableMap::COL_ID, $rezept->getNotizId(), $comparison);
        } elseif ($rezept instanceof ObjectCollection) {
            return $this
                ->useRezeptQuery()
                ->filterByPrimaryKeys($rezept->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRezept() only accepts arguments of type \Rezept or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Rezept relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function joinRezept($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Rezept');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Rezept');
        }

        return $this;
    }

    /**
     * Use the Rezept relation Rezept object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \RezeptQuery A secondary query class using the current class as primary query
     */
    public function useRezeptQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRezept($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Rezept', '\RezeptQuery');
    }

    /**
     * Filter the query by a related \RezeptNotiz object
     *
     * @param \RezeptNotiz|ObjectCollection $rezeptNotiz  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByRezeptNotiz($rezeptNotiz, $comparison = null)
    {
        if ($rezeptNotiz instanceof \RezeptNotiz) {
            return $this
                ->addUsingAlias(NotizTableMap::COL_ID, $rezeptNotiz->getNotizId(), $comparison);
        } elseif ($rezeptNotiz instanceof ObjectCollection) {
            return $this
                ->useRezeptNotizQuery()
                ->filterByPrimaryKeys($rezeptNotiz->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRezeptNotiz() only accepts arguments of type \RezeptNotiz or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RezeptNotiz relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function joinRezeptNotiz($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RezeptNotiz');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'RezeptNotiz');
        }

        return $this;
    }

    /**
     * Use the RezeptNotiz relation RezeptNotiz object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \RezeptNotizQuery A secondary query class using the current class as primary query
     */
    public function useRezeptNotizQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRezeptNotiz($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RezeptNotiz', '\RezeptNotizQuery');
    }

    /**
     * Filter the query by a related Rezept object
     * using the rezept_notiz table as cross reference
     *
     * @param Rezept $rezept the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotizQuery The current query, for fluid interface
     */
    public function filterByRezept($rezept, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useRezeptNotizQuery()
            ->filterByRezept($rezept, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNotiz $notiz Object to remove from the list of results
     *
     * @return $this|ChildNotizQuery The current query, for fluid interface
     */
    public function prune($notiz = null)
    {
        if ($notiz) {
            $this->addUsingAlias(NotizTableMap::COL_ID, $notiz->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the notiz table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotizTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            NotizTableMap::clearInstancePool();
            NotizTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotizTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NotizTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            NotizTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            NotizTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // NotizQuery