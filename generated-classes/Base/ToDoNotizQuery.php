<?php

namespace Base;

use \ToDoNotiz as ChildToDoNotiz;
use \ToDoNotizQuery as ChildToDoNotizQuery;
use \Exception;
use \PDO;
use Map\ToDoNotizTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'todo_notiz' table.
 *
 *
 *
 * @method     ChildToDoNotizQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildToDoNotizQuery orderByNotizId($order = Criteria::ASC) Order by the notiz_id column
 * @method     ChildToDoNotizQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildToDoNotizQuery orderByPrior($order = Criteria::ASC) Order by the prior column
 *
 * @method     ChildToDoNotizQuery groupById() Group by the id column
 * @method     ChildToDoNotizQuery groupByNotizId() Group by the notiz_id column
 * @method     ChildToDoNotizQuery groupByStatus() Group by the status column
 * @method     ChildToDoNotizQuery groupByPrior() Group by the prior column
 *
 * @method     ChildToDoNotizQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildToDoNotizQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildToDoNotizQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildToDoNotizQuery leftJoinNotiz($relationAlias = null) Adds a LEFT JOIN clause to the query using the Notiz relation
 * @method     ChildToDoNotizQuery rightJoinNotiz($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Notiz relation
 * @method     ChildToDoNotizQuery innerJoinNotiz($relationAlias = null) Adds a INNER JOIN clause to the query using the Notiz relation
 *
 * @method     \NotizQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildToDoNotiz findOne(ConnectionInterface $con = null) Return the first ChildToDoNotiz matching the query
 * @method     ChildToDoNotiz findOneOrCreate(ConnectionInterface $con = null) Return the first ChildToDoNotiz matching the query, or a new ChildToDoNotiz object populated from the query conditions when no match is found
 *
 * @method     ChildToDoNotiz findOneById(int $id) Return the first ChildToDoNotiz filtered by the id column
 * @method     ChildToDoNotiz findOneByNotizId(int $notiz_id) Return the first ChildToDoNotiz filtered by the notiz_id column
 * @method     ChildToDoNotiz findOneByStatus(int $status) Return the first ChildToDoNotiz filtered by the status column
 * @method     ChildToDoNotiz findOneByPrior(int $prior) Return the first ChildToDoNotiz filtered by the prior column
 *
 * @method     ChildToDoNotiz[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildToDoNotiz objects based on current ModelCriteria
 * @method     ChildToDoNotiz[]|ObjectCollection findById(int $id) Return ChildToDoNotiz objects filtered by the id column
 * @method     ChildToDoNotiz[]|ObjectCollection findByNotizId(int $notiz_id) Return ChildToDoNotiz objects filtered by the notiz_id column
 * @method     ChildToDoNotiz[]|ObjectCollection findByStatus(int $status) Return ChildToDoNotiz objects filtered by the status column
 * @method     ChildToDoNotiz[]|ObjectCollection findByPrior(int $prior) Return ChildToDoNotiz objects filtered by the prior column
 * @method     ChildToDoNotiz[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ToDoNotizQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Base\ToDoNotizQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'notizverwaltung', $modelName = '\\ToDoNotiz', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildToDoNotizQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildToDoNotizQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildToDoNotizQuery) {
            return $criteria;
        }
        $query = new ChildToDoNotizQuery();
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
     * @return ChildToDoNotiz|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ToDoNotizTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ToDoNotizTableMap::DATABASE_NAME);
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
     * @return ChildToDoNotiz A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, notiz_id, status, prior FROM todo_notiz WHERE id = :p0';
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
            /** @var ChildToDoNotiz $obj */
            $obj = new ChildToDoNotiz();
            $obj->hydrate($row);
            ToDoNotizTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildToDoNotiz|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the notiz_id column
     *
     * Example usage:
     * <code>
     * $query->filterByNotizId(1234); // WHERE notiz_id = 1234
     * $query->filterByNotizId(array(12, 34)); // WHERE notiz_id IN (12, 34)
     * $query->filterByNotizId(array('min' => 12)); // WHERE notiz_id > 12
     * </code>
     *
     * @see       filterByNotiz()
     *
     * @param     mixed $notizId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByNotizId($notizId = null, $comparison = null)
    {
        if (is_array($notizId)) {
            $useMinMax = false;
            if (isset($notizId['min'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_NOTIZ_ID, $notizId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($notizId['max'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_NOTIZ_ID, $notizId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ToDoNotizTableMap::COL_NOTIZ_ID, $notizId, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus(1234); // WHERE status = 1234
     * $query->filterByStatus(array(12, 34)); // WHERE status IN (12, 34)
     * $query->filterByStatus(array('min' => 12)); // WHERE status > 12
     * </code>
     *
     * @param     mixed $status The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (is_array($status)) {
            $useMinMax = false;
            if (isset($status['min'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_STATUS, $status['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($status['max'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_STATUS, $status['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ToDoNotizTableMap::COL_STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the prior column
     *
     * Example usage:
     * <code>
     * $query->filterByPrior(1234); // WHERE prior = 1234
     * $query->filterByPrior(array(12, 34)); // WHERE prior IN (12, 34)
     * $query->filterByPrior(array('min' => 12)); // WHERE prior > 12
     * </code>
     *
     * @param     mixed $prior The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByPrior($prior = null, $comparison = null)
    {
        if (is_array($prior)) {
            $useMinMax = false;
            if (isset($prior['min'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_PRIOR, $prior['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($prior['max'])) {
                $this->addUsingAlias(ToDoNotizTableMap::COL_PRIOR, $prior['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ToDoNotizTableMap::COL_PRIOR, $prior, $comparison);
    }

    /**
     * Filter the query by a related \Notiz object
     *
     * @param \Notiz|ObjectCollection $notiz The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildToDoNotizQuery The current query, for fluid interface
     */
    public function filterByNotiz($notiz, $comparison = null)
    {
        if ($notiz instanceof \Notiz) {
            return $this
                ->addUsingAlias(ToDoNotizTableMap::COL_NOTIZ_ID, $notiz->getId(), $comparison);
        } elseif ($notiz instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ToDoNotizTableMap::COL_NOTIZ_ID, $notiz->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function joinNotiz($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useNotizQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinNotiz($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Notiz', '\NotizQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildToDoNotiz $toDoNotiz Object to remove from the list of results
     *
     * @return $this|ChildToDoNotizQuery The current query, for fluid interface
     */
    public function prune($toDoNotiz = null)
    {
        if ($toDoNotiz) {
            $this->addUsingAlias(ToDoNotizTableMap::COL_ID, $toDoNotiz->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the todo_notiz table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ToDoNotizTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ToDoNotizTableMap::clearInstancePool();
            ToDoNotizTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ToDoNotizTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ToDoNotizTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ToDoNotizTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ToDoNotizTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ToDoNotizQuery
