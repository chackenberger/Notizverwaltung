<?php

namespace Base;

use \Notiz as ChildNotiz;
use \NotizQuery as ChildNotizQuery;
use \Rezept as ChildRezept;
use \RezeptNotiz as ChildRezeptNotiz;
use \RezeptNotizQuery as ChildRezeptNotizQuery;
use \RezeptQuery as ChildRezeptQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\RezeptTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'rezept' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Rezept implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\RezeptTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the notiz_id field.
     * @var        int
     */
    protected $notiz_id;

    /**
     * The value for the created_at field.
     * @var        \DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        \DateTime
     */
    protected $updated_at;

    /**
     * @var        ChildNotiz
     */
    protected $aNotiz;

    /**
     * @var        ObjectCollection|ChildRezeptNotiz[] Collection to store aggregation of ChildRezeptNotiz objects.
     */
    protected $collRezeptNotizs;
    protected $collRezeptNotizsPartial;

    /**
     * @var        ObjectCollection|ChildNotiz[] Cross Collection to store aggregation of ChildNotiz objects.
     */
    protected $collNotizs;

    /**
     * @var bool
     */
    protected $collNotizsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildNotiz[]
     */
    protected $notizsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRezeptNotiz[]
     */
    protected $rezeptNotizsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Rezept object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Rezept</code> instance.  If
     * <code>obj</code> is an instance of <code>Rezept</code>, delegates to
     * <code>equals(Rezept)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Rezept The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [notiz_id] column value.
     *
     * @return int
     */
    public function getNotizId()
    {
        return $this->notiz_id;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return $this|\Rezept The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[RezeptTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [notiz_id] column.
     *
     * @param  int $v new value
     * @return $this|\Rezept The current object (for fluent API support)
     */
    public function setNotizId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->notiz_id !== $v) {
            $this->notiz_id = $v;
            $this->modifiedColumns[RezeptTableMap::COL_NOTIZ_ID] = true;
        }

        if ($this->aNotiz !== null && $this->aNotiz->getId() !== $v) {
            $this->aNotiz = null;
        }

        return $this;
    } // setNotizId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Rezept The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[RezeptTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Rezept The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[RezeptTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : RezeptTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : RezeptTableMap::translateFieldName('NotizId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->notiz_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : RezeptTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : RezeptTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 4; // 4 = RezeptTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Rezept'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aNotiz !== null && $this->notiz_id !== $this->aNotiz->getId()) {
            $this->aNotiz = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(RezeptTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildRezeptQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aNotiz = null;
            $this->collRezeptNotizs = null;

            $this->collNotizs = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Rezept::setDeleted()
     * @see Rezept::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(RezeptTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildRezeptQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(RezeptTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(RezeptTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(RezeptTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(RezeptTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                RezeptTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aNotiz !== null) {
                if ($this->aNotiz->isModified() || $this->aNotiz->isNew()) {
                    $affectedRows += $this->aNotiz->save($con);
                }
                $this->setNotiz($this->aNotiz);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->notizsScheduledForDeletion !== null) {
                if (!$this->notizsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->notizsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \RezeptNotizQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->notizsScheduledForDeletion = null;
                }

            }

            if ($this->collNotizs) {
                foreach ($this->collNotizs as $notiz) {
                    if (!$notiz->isDeleted() && ($notiz->isNew() || $notiz->isModified())) {
                        $notiz->save($con);
                    }
                }
            }


            if ($this->rezeptNotizsScheduledForDeletion !== null) {
                if (!$this->rezeptNotizsScheduledForDeletion->isEmpty()) {
                    \RezeptNotizQuery::create()
                        ->filterByPrimaryKeys($this->rezeptNotizsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->rezeptNotizsScheduledForDeletion = null;
                }
            }

            if ($this->collRezeptNotizs !== null) {
                foreach ($this->collRezeptNotizs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[RezeptTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . RezeptTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(RezeptTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(RezeptTableMap::COL_NOTIZ_ID)) {
            $modifiedColumns[':p' . $index++]  = 'notiz_id';
        }
        if ($this->isColumnModified(RezeptTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(RezeptTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO rezept (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'notiz_id':
                        $stmt->bindValue($identifier, $this->notiz_id, PDO::PARAM_INT);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = RezeptTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getNotizId();
                break;
            case 2:
                return $this->getCreatedAt();
                break;
            case 3:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Rezept'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Rezept'][$this->hashCode()] = true;
        $keys = RezeptTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getNotizId(),
            $keys[2] => $this->getCreatedAt(),
            $keys[3] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aNotiz) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'notiz';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'notiz';
                        break;
                    default:
                        $key = 'Notiz';
                }

                $result[$key] = $this->aNotiz->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collRezeptNotizs) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'rezeptNotizs';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'rezept_notizs';
                        break;
                    default:
                        $key = 'RezeptNotizs';
                }

                $result[$key] = $this->collRezeptNotizs->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Rezept
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = RezeptTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Rezept
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setNotizId($value);
                break;
            case 2:
                $this->setCreatedAt($value);
                break;
            case 3:
                $this->setUpdatedAt($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = RezeptTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setNotizId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setCreatedAt($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setUpdatedAt($arr[$keys[3]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return $this|\Rezept The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RezeptTableMap::DATABASE_NAME);

        if ($this->isColumnModified(RezeptTableMap::COL_ID)) {
            $criteria->add(RezeptTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(RezeptTableMap::COL_NOTIZ_ID)) {
            $criteria->add(RezeptTableMap::COL_NOTIZ_ID, $this->notiz_id);
        }
        if ($this->isColumnModified(RezeptTableMap::COL_CREATED_AT)) {
            $criteria->add(RezeptTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(RezeptTableMap::COL_UPDATED_AT)) {
            $criteria->add(RezeptTableMap::COL_UPDATED_AT, $this->updated_at);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildRezeptQuery::create();
        $criteria->add(RezeptTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Rezept (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setNotizId($this->getNotizId());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getRezeptNotizs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRezeptNotiz($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Rezept Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildNotiz object.
     *
     * @param  ChildNotiz $v
     * @return $this|\Rezept The current object (for fluent API support)
     * @throws PropelException
     */
    public function setNotiz(ChildNotiz $v = null)
    {
        if ($v === null) {
            $this->setNotizId(NULL);
        } else {
            $this->setNotizId($v->getId());
        }

        $this->aNotiz = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildNotiz object, it will not be re-added.
        if ($v !== null) {
            $v->addRezept($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildNotiz object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildNotiz The associated ChildNotiz object.
     * @throws PropelException
     */
    public function getNotiz(ConnectionInterface $con = null)
    {
        if ($this->aNotiz === null && ($this->notiz_id !== null)) {
            $this->aNotiz = ChildNotizQuery::create()->findPk($this->notiz_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aNotiz->addRezepts($this);
             */
        }

        return $this->aNotiz;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('RezeptNotiz' == $relationName) {
            return $this->initRezeptNotizs();
        }
    }

    /**
     * Clears out the collRezeptNotizs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRezeptNotizs()
     */
    public function clearRezeptNotizs()
    {
        $this->collRezeptNotizs = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collRezeptNotizs collection loaded partially.
     */
    public function resetPartialRezeptNotizs($v = true)
    {
        $this->collRezeptNotizsPartial = $v;
    }

    /**
     * Initializes the collRezeptNotizs collection.
     *
     * By default this just sets the collRezeptNotizs collection to an empty array (like clearcollRezeptNotizs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRezeptNotizs($overrideExisting = true)
    {
        if (null !== $this->collRezeptNotizs && !$overrideExisting) {
            return;
        }
        $this->collRezeptNotizs = new ObjectCollection();
        $this->collRezeptNotizs->setModel('\RezeptNotiz');
    }

    /**
     * Gets an array of ChildRezeptNotiz objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildRezept is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildRezeptNotiz[] List of ChildRezeptNotiz objects
     * @throws PropelException
     */
    public function getRezeptNotizs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptNotizsPartial && !$this->isNew();
        if (null === $this->collRezeptNotizs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRezeptNotizs) {
                // return empty collection
                $this->initRezeptNotizs();
            } else {
                $collRezeptNotizs = ChildRezeptNotizQuery::create(null, $criteria)
                    ->filterByRezept($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collRezeptNotizsPartial && count($collRezeptNotizs)) {
                        $this->initRezeptNotizs(false);

                        foreach ($collRezeptNotizs as $obj) {
                            if (false == $this->collRezeptNotizs->contains($obj)) {
                                $this->collRezeptNotizs->append($obj);
                            }
                        }

                        $this->collRezeptNotizsPartial = true;
                    }

                    return $collRezeptNotizs;
                }

                if ($partial && $this->collRezeptNotizs) {
                    foreach ($this->collRezeptNotizs as $obj) {
                        if ($obj->isNew()) {
                            $collRezeptNotizs[] = $obj;
                        }
                    }
                }

                $this->collRezeptNotizs = $collRezeptNotizs;
                $this->collRezeptNotizsPartial = false;
            }
        }

        return $this->collRezeptNotizs;
    }

    /**
     * Sets a collection of ChildRezeptNotiz objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $rezeptNotizs A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildRezept The current object (for fluent API support)
     */
    public function setRezeptNotizs(Collection $rezeptNotizs, ConnectionInterface $con = null)
    {
        /** @var ChildRezeptNotiz[] $rezeptNotizsToDelete */
        $rezeptNotizsToDelete = $this->getRezeptNotizs(new Criteria(), $con)->diff($rezeptNotizs);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->rezeptNotizsScheduledForDeletion = clone $rezeptNotizsToDelete;

        foreach ($rezeptNotizsToDelete as $rezeptNotizRemoved) {
            $rezeptNotizRemoved->setRezept(null);
        }

        $this->collRezeptNotizs = null;
        foreach ($rezeptNotizs as $rezeptNotiz) {
            $this->addRezeptNotiz($rezeptNotiz);
        }

        $this->collRezeptNotizs = $rezeptNotizs;
        $this->collRezeptNotizsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RezeptNotiz objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related RezeptNotiz objects.
     * @throws PropelException
     */
    public function countRezeptNotizs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptNotizsPartial && !$this->isNew();
        if (null === $this->collRezeptNotizs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRezeptNotizs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRezeptNotizs());
            }

            $query = ChildRezeptNotizQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByRezept($this)
                ->count($con);
        }

        return count($this->collRezeptNotizs);
    }

    /**
     * Method called to associate a ChildRezeptNotiz object to this object
     * through the ChildRezeptNotiz foreign key attribute.
     *
     * @param  ChildRezeptNotiz $l ChildRezeptNotiz
     * @return $this|\Rezept The current object (for fluent API support)
     */
    public function addRezeptNotiz(ChildRezeptNotiz $l)
    {
        if ($this->collRezeptNotizs === null) {
            $this->initRezeptNotizs();
            $this->collRezeptNotizsPartial = true;
        }

        if (!$this->collRezeptNotizs->contains($l)) {
            $this->doAddRezeptNotiz($l);
        }

        return $this;
    }

    /**
     * @param ChildRezeptNotiz $rezeptNotiz The ChildRezeptNotiz object to add.
     */
    protected function doAddRezeptNotiz(ChildRezeptNotiz $rezeptNotiz)
    {
        $this->collRezeptNotizs[]= $rezeptNotiz;
        $rezeptNotiz->setRezept($this);
    }

    /**
     * @param  ChildRezeptNotiz $rezeptNotiz The ChildRezeptNotiz object to remove.
     * @return $this|ChildRezept The current object (for fluent API support)
     */
    public function removeRezeptNotiz(ChildRezeptNotiz $rezeptNotiz)
    {
        if ($this->getRezeptNotizs()->contains($rezeptNotiz)) {
            $pos = $this->collRezeptNotizs->search($rezeptNotiz);
            $this->collRezeptNotizs->remove($pos);
            if (null === $this->rezeptNotizsScheduledForDeletion) {
                $this->rezeptNotizsScheduledForDeletion = clone $this->collRezeptNotizs;
                $this->rezeptNotizsScheduledForDeletion->clear();
            }
            $this->rezeptNotizsScheduledForDeletion[]= clone $rezeptNotiz;
            $rezeptNotiz->setRezept(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Rezept is new, it will return
     * an empty collection; or if this Rezept has previously
     * been saved, it will retrieve related RezeptNotizs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Rezept.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildRezeptNotiz[] List of ChildRezeptNotiz objects
     */
    public function getRezeptNotizsJoinNotiz(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildRezeptNotizQuery::create(null, $criteria);
        $query->joinWith('Notiz', $joinBehavior);

        return $this->getRezeptNotizs($query, $con);
    }

    /**
     * Clears out the collNotizs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addNotizs()
     */
    public function clearNotizs()
    {
        $this->collNotizs = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collNotizs crossRef collection.
     *
     * By default this just sets the collNotizs collection to an empty collection (like clearNotizs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initNotizs()
    {
        $this->collNotizs = new ObjectCollection();
        $this->collNotizsPartial = true;

        $this->collNotizs->setModel('\Notiz');
    }

    /**
     * Checks if the collNotizs collection is loaded.
     *
     * @return bool
     */
    public function isNotizsLoaded()
    {
        return null !== $this->collNotizs;
    }

    /**
     * Gets a collection of ChildNotiz objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildRezept is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildNotiz[] List of ChildNotiz objects
     */
    public function getNotizs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNotizsPartial && !$this->isNew();
        if (null === $this->collNotizs || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collNotizs) {
                    $this->initNotizs();
                }
            } else {

                $query = ChildNotizQuery::create(null, $criteria)
                    ->filterByRezept($this);
                $collNotizs = $query->find($con);
                if (null !== $criteria) {
                    return $collNotizs;
                }

                if ($partial && $this->collNotizs) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collNotizs as $obj) {
                        if (!$collNotizs->contains($obj)) {
                            $collNotizs[] = $obj;
                        }
                    }
                }

                $this->collNotizs = $collNotizs;
                $this->collNotizsPartial = false;
            }
        }

        return $this->collNotizs;
    }

    /**
     * Sets a collection of Notiz objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $notizs A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildRezept The current object (for fluent API support)
     */
    public function setNotizs(Collection $notizs, ConnectionInterface $con = null)
    {
        $this->clearNotizs();
        $currentNotizs = $this->getNotizs();

        $notizsScheduledForDeletion = $currentNotizs->diff($notizs);

        foreach ($notizsScheduledForDeletion as $toDelete) {
            $this->removeNotiz($toDelete);
        }

        foreach ($notizs as $notiz) {
            if (!$currentNotizs->contains($notiz)) {
                $this->doAddNotiz($notiz);
            }
        }

        $this->collNotizsPartial = false;
        $this->collNotizs = $notizs;

        return $this;
    }

    /**
     * Gets the number of Notiz objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Notiz objects
     */
    public function countNotizs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNotizsPartial && !$this->isNew();
        if (null === $this->collNotizs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotizs) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getNotizs());
                }

                $query = ChildNotizQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByRezept($this)
                    ->count($con);
            }
        } else {
            return count($this->collNotizs);
        }
    }

    /**
     * Associate a ChildNotiz to this object
     * through the rezept_notiz cross reference table.
     *
     * @param ChildNotiz $notiz
     * @return ChildRezept The current object (for fluent API support)
     */
    public function addNotiz(ChildNotiz $notiz)
    {
        if ($this->collNotizs === null) {
            $this->initNotizs();
        }

        if (!$this->getNotizs()->contains($notiz)) {
            // only add it if the **same** object is not already associated
            $this->collNotizs->push($notiz);
            $this->doAddNotiz($notiz);
        }

        return $this;
    }

    /**
     *
     * @param ChildNotiz $notiz
     */
    protected function doAddNotiz(ChildNotiz $notiz)
    {
        $rezeptNotiz = new ChildRezeptNotiz();

        $rezeptNotiz->setNotiz($notiz);

        $rezeptNotiz->setRezept($this);

        $this->addRezeptNotiz($rezeptNotiz);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$notiz->isRezeptsLoaded()) {
            $notiz->initRezepts();
            $notiz->getRezepts()->push($this);
        } elseif (!$notiz->getRezepts()->contains($this)) {
            $notiz->getRezepts()->push($this);
        }

    }

    /**
     * Remove notiz of this object
     * through the rezept_notiz cross reference table.
     *
     * @param ChildNotiz $notiz
     * @return ChildRezept The current object (for fluent API support)
     */
    public function removeNotiz(ChildNotiz $notiz)
    {
        if ($this->getNotizs()->contains($notiz)) { $rezeptNotiz = new ChildRezeptNotiz();

            $rezeptNotiz->setNotiz($notiz);
            if ($notiz->isRezeptsLoaded()) {
                //remove the back reference if available
                $notiz->getRezepts()->removeObject($this);
            }

            $rezeptNotiz->setRezept($this);
            $this->removeRezeptNotiz(clone $rezeptNotiz);
            $rezeptNotiz->clear();

            $this->collNotizs->remove($this->collNotizs->search($notiz));

            if (null === $this->notizsScheduledForDeletion) {
                $this->notizsScheduledForDeletion = clone $this->collNotizs;
                $this->notizsScheduledForDeletion->clear();
            }

            $this->notizsScheduledForDeletion->push($notiz);
        }


        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aNotiz) {
            $this->aNotiz->removeRezept($this);
        }
        $this->id = null;
        $this->notiz_id = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collRezeptNotizs) {
                foreach ($this->collRezeptNotizs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotizs) {
                foreach ($this->collNotizs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collRezeptNotizs = null;
        $this->collNotizs = null;
        $this->aNotiz = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(RezeptTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildRezept The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[RezeptTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
