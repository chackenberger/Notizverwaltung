<?php

namespace Base;

use \Person as ChildPerson;
use \PersonQuery as ChildPersonQuery;
use \Exception;
use \PDO;
use Map\PersonTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'person' table.
 *
 *
 *
 * @method     ChildPersonQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPersonQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildPersonQuery orderByDesc($order = Criteria::ASC) Order by the desc column
 *
 * @method     ChildPersonQuery groupById() Group by the id column
 * @method     ChildPersonQuery groupByName() Group by the name column
 * @method     ChildPersonQuery groupByDesc() Group by the desc column
 *
 * @method     ChildPersonQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPersonQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPersonQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPersonQuery leftJoinNotiz($relationAlias = null) Adds a LEFT JOIN clause to the query using the Notiz relation
 * @method     ChildPersonQuery rightJoinNotiz($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Notiz relation
 * @method     ChildPersonQuery innerJoinNotiz($relationAlias = null) Adds a INNER JOIN clause to the query using the Notiz relation
 *
 * @method     ChildPersonQuery leftJoinPersonProjekt($relationAlias = null) Adds a LEFT JOIN clause to the query using the PersonProjekt relation
 * @method     ChildPersonQuery rightJoinPersonProjekt($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PersonProjekt relation
 * @method     ChildPersonQuery innerJoinPersonProjekt($relationAlias = null) Adds a INNER JOIN clause to the query using the PersonProjekt relation
 *
 * @method     \NotizQuery|\PersonProjektQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildPerson findOne(ConnectionInterface $con = null) Return the first ChildPerson matching the query
 * @method     ChildPerson findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPerson matching the query, or a new ChildPerson object populated from the query conditions when no match is found
 *
 * @method     ChildPerson findOneById(int $id) Return the first ChildPerson filtered by the id column
 * @method     ChildPerson findOneByName(string $name) Return the first ChildPerson filtered by the name column
 * @method     ChildPerson findOneByDesc(string $desc) Return the first ChildPerson filtered by the desc column
 *
 * @method     ChildPerson[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildPerson objects based on current ModelCriteria
 * @method     ChildPerson[]|ObjectCollection findById(int $id) Return ChildPerson objects filtered by the id column
 * @method     ChildPerson[]|ObjectCollection findByName(string $name) Return ChildPerson objects filtered by the name column
 * @method     ChildPerson[]|ObjectCollection findByDesc(string $desc) Return ChildPerson objects filtered by the desc column
 * @method     ChildPerson[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class PersonQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Base\PersonQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'notizverwaltung', $modelName = '\\Person', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPersonQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPersonQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildPersonQuery) {
            return $criteria;
        }
        $query = new ChildPersonQuery();
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
     * @return ChildPerson|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PersonTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PersonTableMap::DATABASE_NAME);
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
     * @return ChildPerson A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, desc FROM person WHERE id = :p0';
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
            /** @var ChildPerson $obj */
            $obj = new ChildPerson();
            $obj->hydrate($row);
            PersonTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildPerson|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildPersonQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PersonTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildPersonQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PersonTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildPersonQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PersonTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PersonTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PersonTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildPersonQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PersonTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the desc column
     *
     * Example usage:
     * <code>
     * $query->filterByDesc('fooValue');   // WHERE desc = 'fooValue'
     * $query->filterByDesc('%fooValue%'); // WHERE desc LIKE '%fooValue%'
     * </code>
     *
     * @param     string $desc The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPersonQuery The current query, for fluid interface
     */
    public function filterByDesc($desc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($desc)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $desc)) {
                $desc = str_replace('*', '%', $desc);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PersonTableMap::COL_DESC, $desc, $comparison);
    }

    /**
     * Filter the query by a related \Notiz object
     *
     * @param \Notiz|ObjectCollection $notiz  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPersonQuery The current query, for fluid interface
     */
    public function filterByNotiz($notiz, $comparison = null)
    {
        if ($notiz instanceof \Notiz) {
            return $this
                ->addUsingAlias(PersonTableMap::COL_ID, $notiz->getBesitzerId(), $comparison);
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
     * @return $this|ChildPersonQuery The current query, for fluid interface
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
     * Filter the query by a related \PersonProjekt object
     *
     * @param \PersonProjekt|ObjectCollection $personProjekt  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPersonQuery The current query, for fluid interface
     */
    public function filterByPersonProjekt($personProjekt, $comparison = null)
    {
        if ($personProjekt instanceof \PersonProjekt) {
            return $this
                ->addUsingAlias(PersonTableMap::COL_ID, $personProjekt->getPersonId(), $comparison);
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
     * @return $this|ChildPersonQuery The current query, for fluid interface
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
     * Filter the query by a related Projekt object
     * using the person_projekt table as cross reference
     *
     * @param Projekt $projekt the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPersonQuery The current query, for fluid interface
     */
    public function filterByProjekt($projekt, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePersonProjektQuery()
            ->filterByProjekt($projekt, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPerson $person Object to remove from the list of results
     *
     * @return $this|ChildPersonQuery The current query, for fluid interface
     */
    public function prune($person = null)
    {
        if ($person) {
            $this->addUsingAlias(PersonTableMap::COL_ID, $person->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the person table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PersonTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PersonTableMap::clearInstancePool();
            PersonTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(PersonTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PersonTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            PersonTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            PersonTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // PersonQuery
