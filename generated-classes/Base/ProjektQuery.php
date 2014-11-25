<?php

namespace Base;

use \Projekt as ChildProjekt;
use \ProjektQuery as ChildProjektQuery;
use \Exception;
use \PDO;
use Map\ProjektTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'projekt' table.
 *
 *
 *
 * @method     ChildProjektQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProjektQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildProjektQuery orderBySdate($order = Criteria::ASC) Order by the sdate column
 * @method     ChildProjektQuery orderByEdate($order = Criteria::ASC) Order by the edate column
 *
 * @method     ChildProjektQuery groupById() Group by the id column
 * @method     ChildProjektQuery groupByName() Group by the name column
 * @method     ChildProjektQuery groupBySdate() Group by the sdate column
 * @method     ChildProjektQuery groupByEdate() Group by the edate column
 *
 * @method     ChildProjektQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProjektQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProjektQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProjektQuery leftJoinNotiz($relationAlias = null) Adds a LEFT JOIN clause to the query using the Notiz relation
 * @method     ChildProjektQuery rightJoinNotiz($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Notiz relation
 * @method     ChildProjektQuery innerJoinNotiz($relationAlias = null) Adds a INNER JOIN clause to the query using the Notiz relation
 *
 * @method     ChildProjektQuery leftJoinPersonProjekt($relationAlias = null) Adds a LEFT JOIN clause to the query using the PersonProjekt relation
 * @method     ChildProjektQuery rightJoinPersonProjekt($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PersonProjekt relation
 * @method     ChildProjektQuery innerJoinPersonProjekt($relationAlias = null) Adds a INNER JOIN clause to the query using the PersonProjekt relation
 *
 * @method     \NotizQuery|\PersonProjektQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildProjekt findOne(ConnectionInterface $con = null) Return the first ChildProjekt matching the query
 * @method     ChildProjekt findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProjekt matching the query, or a new ChildProjekt object populated from the query conditions when no match is found
 *
 * @method     ChildProjekt findOneById(int $id) Return the first ChildProjekt filtered by the id column
 * @method     ChildProjekt findOneByName(string $name) Return the first ChildProjekt filtered by the name column
 * @method     ChildProjekt findOneBySdate(string $sdate) Return the first ChildProjekt filtered by the sdate column
 * @method     ChildProjekt findOneByEdate(string $edate) Return the first ChildProjekt filtered by the edate column
 *
 * @method     ChildProjekt[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildProjekt objects based on current ModelCriteria
 * @method     ChildProjekt[]|ObjectCollection findById(int $id) Return ChildProjekt objects filtered by the id column
 * @method     ChildProjekt[]|ObjectCollection findByName(string $name) Return ChildProjekt objects filtered by the name column
 * @method     ChildProjekt[]|ObjectCollection findBySdate(string $sdate) Return ChildProjekt objects filtered by the sdate column
 * @method     ChildProjekt[]|ObjectCollection findByEdate(string $edate) Return ChildProjekt objects filtered by the edate column
 * @method     ChildProjekt[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ProjektQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Base\ProjektQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'notizverwaltung', $modelName = '\\Projekt', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProjektQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProjektQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildProjektQuery) {
            return $criteria;
        }
        $query = new ChildProjektQuery();
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
     * @return ChildProjekt|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProjektTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProjektTableMap::DATABASE_NAME);
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
     * @return ChildProjekt A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, sdate, edate FROM projekt WHERE id = :p0';
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
            /** @var ChildProjekt $obj */
            $obj = new ChildProjekt();
            $obj->hydrate($row);
            ProjektTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProjekt|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProjektTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProjektTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProjektTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProjektTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjektTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildProjektQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ProjektTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the sdate column
     *
     * Example usage:
     * <code>
     * $query->filterBySdate('2011-03-14'); // WHERE sdate = '2011-03-14'
     * $query->filterBySdate('now'); // WHERE sdate = '2011-03-14'
     * $query->filterBySdate(array('max' => 'yesterday')); // WHERE sdate > '2011-03-13'
     * </code>
     *
     * @param     mixed $sdate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function filterBySdate($sdate = null, $comparison = null)
    {
        if (is_array($sdate)) {
            $useMinMax = false;
            if (isset($sdate['min'])) {
                $this->addUsingAlias(ProjektTableMap::COL_SDATE, $sdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sdate['max'])) {
                $this->addUsingAlias(ProjektTableMap::COL_SDATE, $sdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjektTableMap::COL_SDATE, $sdate, $comparison);
    }

    /**
     * Filter the query on the edate column
     *
     * Example usage:
     * <code>
     * $query->filterByEdate('2011-03-14'); // WHERE edate = '2011-03-14'
     * $query->filterByEdate('now'); // WHERE edate = '2011-03-14'
     * $query->filterByEdate(array('max' => 'yesterday')); // WHERE edate > '2011-03-13'
     * </code>
     *
     * @param     mixed $edate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function filterByEdate($edate = null, $comparison = null)
    {
        if (is_array($edate)) {
            $useMinMax = false;
            if (isset($edate['min'])) {
                $this->addUsingAlias(ProjektTableMap::COL_EDATE, $edate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($edate['max'])) {
                $this->addUsingAlias(ProjektTableMap::COL_EDATE, $edate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjektTableMap::COL_EDATE, $edate, $comparison);
    }

    /**
     * Filter the query by a related \Notiz object
     *
     * @param \Notiz|ObjectCollection $notiz  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjektQuery The current query, for fluid interface
     */
    public function filterByNotiz($notiz, $comparison = null)
    {
        if ($notiz instanceof \Notiz) {
            return $this
                ->addUsingAlias(ProjektTableMap::COL_ID, $notiz->getProjektId(), $comparison);
        } elseif ($notiz instanceof ObjectCollection) {
            return $this
                ->useNotizQuery()
                ->filterByPrimaryKeys($notiz->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByNotiz() only accepts arguments of type \Notiz or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Notiz relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function joinNotiz($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Notiz');

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
            $this->addJoinObject($join, 'Notiz');
        }

        return $this;
    }

    /**
     * Use the Notiz relation Notiz object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \NotizQuery A secondary query class using the current class as primary query
     */
    public function useNotizQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinNotiz($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Notiz', '\NotizQuery');
    }

    /**
     * Filter the query by a related \PersonProjekt object
     *
     * @param \PersonProjekt|ObjectCollection $personProjekt  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjektQuery The current query, for fluid interface
     */
    public function filterByPersonProjekt($personProjekt, $comparison = null)
    {
        if ($personProjekt instanceof \PersonProjekt) {
            return $this
                ->addUsingAlias(ProjektTableMap::COL_ID, $personProjekt->getProjektId(), $comparison);
        } elseif ($personProjekt instanceof ObjectCollection) {
            return $this
                ->usePersonProjektQuery()
                ->filterByPrimaryKeys($personProjekt->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPersonProjekt() only accepts arguments of type \PersonProjekt or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PersonProjekt relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function joinPersonProjekt($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PersonProjekt');

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
            $this->addJoinObject($join, 'PersonProjekt');
        }

        return $this;
    }

    /**
     * Use the PersonProjekt relation PersonProjekt object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PersonProjektQuery A secondary query class using the current class as primary query
     */
    public function usePersonProjektQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPersonProjekt($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PersonProjekt', '\PersonProjektQuery');
    }

    /**
     * Filter the query by a related Person object
     * using the person_projekt table as cross reference
     *
     * @param Person $person the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjektQuery The current query, for fluid interface
     */
    public function filterByPerson($person, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePersonProjektQuery()
            ->filterByPerson($person, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProjekt $projekt Object to remove from the list of results
     *
     * @return $this|ChildProjektQuery The current query, for fluid interface
     */
    public function prune($projekt = null)
    {
        if ($projekt) {
            $this->addUsingAlias(ProjektTableMap::COL_ID, $projekt->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the projekt table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjektTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ProjektTableMap::clearInstancePool();
            ProjektTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ProjektTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProjektTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ProjektTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ProjektTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ProjektQuery
