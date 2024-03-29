<?php

namespace Base;

use \Notiz as ChildNotiz;
use \NotizQuery as ChildNotizQuery;
use \Person as ChildPerson;
use \PersonProjekt as ChildPersonProjekt;
use \PersonProjektQuery as ChildPersonProjektQuery;
use \PersonQuery as ChildPersonQuery;
use \Projekt as ChildProjekt;
use \ProjektQuery as ChildProjektQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\ProjektTableMap;
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
 * Base class that represents a row from the 'projekt' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Projekt implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ProjektTableMap';


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
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the sdate field.
     * @var        \DateTime
     */
    protected $sdate;

    /**
     * The value for the edate field.
     * @var        \DateTime
     */
    protected $edate;

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
     * @var        ObjectCollection|ChildNotiz[] Collection to store aggregation of ChildNotiz objects.
     */
    protected $collNotizs;
    protected $collNotizsPartial;

    /**
     * @var        ObjectCollection|ChildPersonProjekt[] Collection to store aggregation of ChildPersonProjekt objects.
     */
    protected $collPersonProjekts;
    protected $collPersonProjektsPartial;

    /**
     * @var        ObjectCollection|ChildPerson[] Cross Collection to store aggregation of ChildPerson objects.
     */
    protected $collPeople;

    /**
     * @var bool
     */
    protected $collPeoplePartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPerson[]
     */
    protected $peopleScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildNotiz[]
     */
    protected $notizsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPersonProjekt[]
     */
    protected $personProjektsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Projekt object.
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
     * Compares this with another <code>Projekt</code> instance.  If
     * <code>obj</code> is an instance of <code>Projekt</code>, delegates to
     * <code>equals(Projekt)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Projekt The current object, for fluid interface
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [optionally formatted] temporal [sdate] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getSdate($format = NULL)
    {
        if ($format === null) {
            return $this->sdate;
        } else {
            return $this->sdate instanceof \DateTime ? $this->sdate->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [edate] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEdate($format = NULL)
    {
        if ($format === null) {
            return $this->edate;
        } else {
            return $this->edate instanceof \DateTime ? $this->edate->format($format) : null;
        }
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
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ProjektTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[ProjektTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Sets the value of [sdate] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setSdate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->sdate !== null || $dt !== null) {
            if ($dt !== $this->sdate) {
                $this->sdate = $dt;
                $this->modifiedColumns[ProjektTableMap::COL_SDATE] = true;
            }
        } // if either are not null

        return $this;
    } // setSdate()

    /**
     * Sets the value of [edate] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setEdate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->edate !== null || $dt !== null) {
            if ($dt !== $this->edate) {
                $this->edate = $dt;
                $this->modifiedColumns[ProjektTableMap::COL_EDATE] = true;
            }
        } // if either are not null

        return $this;
    } // setEdate()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[ProjektTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[ProjektTableMap::COL_UPDATED_AT] = true;
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProjektTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProjektTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProjektTableMap::translateFieldName('Sdate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->sdate = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProjektTableMap::translateFieldName('Edate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->edate = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ProjektTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ProjektTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 6; // 6 = ProjektTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Projekt'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(ProjektTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProjektQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collNotizs = null;

            $this->collPersonProjekts = null;

            $this->collPeople = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Projekt::setDeleted()
     * @see Projekt::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjektTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildProjektQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProjektTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(ProjektTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ProjektTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProjektTableMap::COL_UPDATED_AT)) {
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
                ProjektTableMap::addInstanceToPool($this);
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

            if ($this->peopleScheduledForDeletion !== null) {
                if (!$this->peopleScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->peopleScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[1] = $this->getId();
                        $entryPk[0] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \PersonProjektQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->peopleScheduledForDeletion = null;
                }

            }

            if ($this->collPeople) {
                foreach ($this->collPeople as $person) {
                    if (!$person->isDeleted() && ($person->isNew() || $person->isModified())) {
                        $person->save($con);
                    }
                }
            }


            if ($this->notizsScheduledForDeletion !== null) {
                if (!$this->notizsScheduledForDeletion->isEmpty()) {
                    foreach ($this->notizsScheduledForDeletion as $notiz) {
                        // need to save related object because we set the relation to null
                        $notiz->save($con);
                    }
                    $this->notizsScheduledForDeletion = null;
                }
            }

            if ($this->collNotizs !== null) {
                foreach ($this->collNotizs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->personProjektsScheduledForDeletion !== null) {
                if (!$this->personProjektsScheduledForDeletion->isEmpty()) {
                    \PersonProjektQuery::create()
                        ->filterByPrimaryKeys($this->personProjektsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->personProjektsScheduledForDeletion = null;
                }
            }

            if ($this->collPersonProjekts !== null) {
                foreach ($this->collPersonProjekts as $referrerFK) {
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

        $this->modifiedColumns[ProjektTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjektTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjektTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ProjektTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(ProjektTableMap::COL_SDATE)) {
            $modifiedColumns[':p' . $index++]  = 'sdate';
        }
        if ($this->isColumnModified(ProjektTableMap::COL_EDATE)) {
            $modifiedColumns[':p' . $index++]  = 'edate';
        }
        if ($this->isColumnModified(ProjektTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(ProjektTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO projekt (%s) VALUES (%s)',
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
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'sdate':
                        $stmt->bindValue($identifier, $this->sdate ? $this->sdate->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'edate':
                        $stmt->bindValue($identifier, $this->edate ? $this->edate->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
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
        $pos = ProjektTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getSdate();
                break;
            case 3:
                return $this->getEdate();
                break;
            case 4:
                return $this->getCreatedAt();
                break;
            case 5:
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

        if (isset($alreadyDumpedObjects['Projekt'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Projekt'][$this->hashCode()] = true;
        $keys = ProjektTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getSdate(),
            $keys[3] => $this->getEdate(),
            $keys[4] => $this->getCreatedAt(),
            $keys[5] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collNotizs) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'notizs';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'notizs';
                        break;
                    default:
                        $key = 'Notizs';
                }

                $result[$key] = $this->collNotizs->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPersonProjekts) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'personProjekts';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'person_projekts';
                        break;
                    default:
                        $key = 'PersonProjekts';
                }

                $result[$key] = $this->collPersonProjekts->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Projekt
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProjektTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Projekt
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setSdate($value);
                break;
            case 3:
                $this->setEdate($value);
                break;
            case 4:
                $this->setCreatedAt($value);
                break;
            case 5:
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
        $keys = ProjektTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setSdate($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setEdate($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setCreatedAt($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setUpdatedAt($arr[$keys[5]]);
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
     * @return $this|\Projekt The current object, for fluid interface
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
        $criteria = new Criteria(ProjektTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProjektTableMap::COL_ID)) {
            $criteria->add(ProjektTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ProjektTableMap::COL_NAME)) {
            $criteria->add(ProjektTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(ProjektTableMap::COL_SDATE)) {
            $criteria->add(ProjektTableMap::COL_SDATE, $this->sdate);
        }
        if ($this->isColumnModified(ProjektTableMap::COL_EDATE)) {
            $criteria->add(ProjektTableMap::COL_EDATE, $this->edate);
        }
        if ($this->isColumnModified(ProjektTableMap::COL_CREATED_AT)) {
            $criteria->add(ProjektTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(ProjektTableMap::COL_UPDATED_AT)) {
            $criteria->add(ProjektTableMap::COL_UPDATED_AT, $this->updated_at);
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
        $criteria = ChildProjektQuery::create();
        $criteria->add(ProjektTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Projekt (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setSdate($this->getSdate());
        $copyObj->setEdate($this->getEdate());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getNotizs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotiz($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPersonProjekts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPersonProjekt($relObj->copy($deepCopy));
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
     * @return \Projekt Clone of current object.
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
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Notiz' == $relationName) {
            return $this->initNotizs();
        }
        if ('PersonProjekt' == $relationName) {
            return $this->initPersonProjekts();
        }
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
     * Reset is the collNotizs collection loaded partially.
     */
    public function resetPartialNotizs($v = true)
    {
        $this->collNotizsPartial = $v;
    }

    /**
     * Initializes the collNotizs collection.
     *
     * By default this just sets the collNotizs collection to an empty array (like clearcollNotizs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotizs($overrideExisting = true)
    {
        if (null !== $this->collNotizs && !$overrideExisting) {
            return;
        }
        $this->collNotizs = new ObjectCollection();
        $this->collNotizs->setModel('\Notiz');
    }

    /**
     * Gets an array of ChildNotiz objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProjekt is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildNotiz[] List of ChildNotiz objects
     * @throws PropelException
     */
    public function getNotizs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNotizsPartial && !$this->isNew();
        if (null === $this->collNotizs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotizs) {
                // return empty collection
                $this->initNotizs();
            } else {
                $collNotizs = ChildNotizQuery::create(null, $criteria)
                    ->filterByProjekt($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collNotizsPartial && count($collNotizs)) {
                        $this->initNotizs(false);

                        foreach ($collNotizs as $obj) {
                            if (false == $this->collNotizs->contains($obj)) {
                                $this->collNotizs->append($obj);
                            }
                        }

                        $this->collNotizsPartial = true;
                    }

                    return $collNotizs;
                }

                if ($partial && $this->collNotizs) {
                    foreach ($this->collNotizs as $obj) {
                        if ($obj->isNew()) {
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
     * Sets a collection of ChildNotiz objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $notizs A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProjekt The current object (for fluent API support)
     */
    public function setNotizs(Collection $notizs, ConnectionInterface $con = null)
    {
        /** @var ChildNotiz[] $notizsToDelete */
        $notizsToDelete = $this->getNotizs(new Criteria(), $con)->diff($notizs);


        $this->notizsScheduledForDeletion = $notizsToDelete;

        foreach ($notizsToDelete as $notizRemoved) {
            $notizRemoved->setProjekt(null);
        }

        $this->collNotizs = null;
        foreach ($notizs as $notiz) {
            $this->addNotiz($notiz);
        }

        $this->collNotizs = $notizs;
        $this->collNotizsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Notiz objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Notiz objects.
     * @throws PropelException
     */
    public function countNotizs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNotizsPartial && !$this->isNew();
        if (null === $this->collNotizs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotizs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNotizs());
            }

            $query = ChildNotizQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProjekt($this)
                ->count($con);
        }

        return count($this->collNotizs);
    }

    /**
     * Method called to associate a ChildNotiz object to this object
     * through the ChildNotiz foreign key attribute.
     *
     * @param  ChildNotiz $l ChildNotiz
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function addNotiz(ChildNotiz $l)
    {
        if ($this->collNotizs === null) {
            $this->initNotizs();
            $this->collNotizsPartial = true;
        }

        if (!$this->collNotizs->contains($l)) {
            $this->doAddNotiz($l);
        }

        return $this;
    }

    /**
     * @param ChildNotiz $notiz The ChildNotiz object to add.
     */
    protected function doAddNotiz(ChildNotiz $notiz)
    {
        $this->collNotizs[]= $notiz;
        $notiz->setProjekt($this);
    }

    /**
     * @param  ChildNotiz $notiz The ChildNotiz object to remove.
     * @return $this|ChildProjekt The current object (for fluent API support)
     */
    public function removeNotiz(ChildNotiz $notiz)
    {
        if ($this->getNotizs()->contains($notiz)) {
            $pos = $this->collNotizs->search($notiz);
            $this->collNotizs->remove($pos);
            if (null === $this->notizsScheduledForDeletion) {
                $this->notizsScheduledForDeletion = clone $this->collNotizs;
                $this->notizsScheduledForDeletion->clear();
            }
            $this->notizsScheduledForDeletion[]= $notiz;
            $notiz->setProjekt(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Projekt is new, it will return
     * an empty collection; or if this Projekt has previously
     * been saved, it will retrieve related Notizs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Projekt.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildNotiz[] List of ChildNotiz objects
     */
    public function getNotizsJoinBesitzer(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildNotizQuery::create(null, $criteria);
        $query->joinWith('Besitzer', $joinBehavior);

        return $this->getNotizs($query, $con);
    }

    /**
     * Clears out the collPersonProjekts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPersonProjekts()
     */
    public function clearPersonProjekts()
    {
        $this->collPersonProjekts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPersonProjekts collection loaded partially.
     */
    public function resetPartialPersonProjekts($v = true)
    {
        $this->collPersonProjektsPartial = $v;
    }

    /**
     * Initializes the collPersonProjekts collection.
     *
     * By default this just sets the collPersonProjekts collection to an empty array (like clearcollPersonProjekts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPersonProjekts($overrideExisting = true)
    {
        if (null !== $this->collPersonProjekts && !$overrideExisting) {
            return;
        }
        $this->collPersonProjekts = new ObjectCollection();
        $this->collPersonProjekts->setModel('\PersonProjekt');
    }

    /**
     * Gets an array of ChildPersonProjekt objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProjekt is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPersonProjekt[] List of ChildPersonProjekt objects
     * @throws PropelException
     */
    public function getPersonProjekts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPersonProjektsPartial && !$this->isNew();
        if (null === $this->collPersonProjekts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPersonProjekts) {
                // return empty collection
                $this->initPersonProjekts();
            } else {
                $collPersonProjekts = ChildPersonProjektQuery::create(null, $criteria)
                    ->filterByProjekt($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPersonProjektsPartial && count($collPersonProjekts)) {
                        $this->initPersonProjekts(false);

                        foreach ($collPersonProjekts as $obj) {
                            if (false == $this->collPersonProjekts->contains($obj)) {
                                $this->collPersonProjekts->append($obj);
                            }
                        }

                        $this->collPersonProjektsPartial = true;
                    }

                    return $collPersonProjekts;
                }

                if ($partial && $this->collPersonProjekts) {
                    foreach ($this->collPersonProjekts as $obj) {
                        if ($obj->isNew()) {
                            $collPersonProjekts[] = $obj;
                        }
                    }
                }

                $this->collPersonProjekts = $collPersonProjekts;
                $this->collPersonProjektsPartial = false;
            }
        }

        return $this->collPersonProjekts;
    }

    /**
     * Sets a collection of ChildPersonProjekt objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $personProjekts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProjekt The current object (for fluent API support)
     */
    public function setPersonProjekts(Collection $personProjekts, ConnectionInterface $con = null)
    {
        /** @var ChildPersonProjekt[] $personProjektsToDelete */
        $personProjektsToDelete = $this->getPersonProjekts(new Criteria(), $con)->diff($personProjekts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->personProjektsScheduledForDeletion = clone $personProjektsToDelete;

        foreach ($personProjektsToDelete as $personProjektRemoved) {
            $personProjektRemoved->setProjekt(null);
        }

        $this->collPersonProjekts = null;
        foreach ($personProjekts as $personProjekt) {
            $this->addPersonProjekt($personProjekt);
        }

        $this->collPersonProjekts = $personProjekts;
        $this->collPersonProjektsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PersonProjekt objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PersonProjekt objects.
     * @throws PropelException
     */
    public function countPersonProjekts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPersonProjektsPartial && !$this->isNew();
        if (null === $this->collPersonProjekts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPersonProjekts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPersonProjekts());
            }

            $query = ChildPersonProjektQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProjekt($this)
                ->count($con);
        }

        return count($this->collPersonProjekts);
    }

    /**
     * Method called to associate a ChildPersonProjekt object to this object
     * through the ChildPersonProjekt foreign key attribute.
     *
     * @param  ChildPersonProjekt $l ChildPersonProjekt
     * @return $this|\Projekt The current object (for fluent API support)
     */
    public function addPersonProjekt(ChildPersonProjekt $l)
    {
        if ($this->collPersonProjekts === null) {
            $this->initPersonProjekts();
            $this->collPersonProjektsPartial = true;
        }

        if (!$this->collPersonProjekts->contains($l)) {
            $this->doAddPersonProjekt($l);
        }

        return $this;
    }

    /**
     * @param ChildPersonProjekt $personProjekt The ChildPersonProjekt object to add.
     */
    protected function doAddPersonProjekt(ChildPersonProjekt $personProjekt)
    {
        $this->collPersonProjekts[]= $personProjekt;
        $personProjekt->setProjekt($this);
    }

    /**
     * @param  ChildPersonProjekt $personProjekt The ChildPersonProjekt object to remove.
     * @return $this|ChildProjekt The current object (for fluent API support)
     */
    public function removePersonProjekt(ChildPersonProjekt $personProjekt)
    {
        if ($this->getPersonProjekts()->contains($personProjekt)) {
            $pos = $this->collPersonProjekts->search($personProjekt);
            $this->collPersonProjekts->remove($pos);
            if (null === $this->personProjektsScheduledForDeletion) {
                $this->personProjektsScheduledForDeletion = clone $this->collPersonProjekts;
                $this->personProjektsScheduledForDeletion->clear();
            }
            $this->personProjektsScheduledForDeletion[]= clone $personProjekt;
            $personProjekt->setProjekt(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Projekt is new, it will return
     * an empty collection; or if this Projekt has previously
     * been saved, it will retrieve related PersonProjekts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Projekt.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildPersonProjekt[] List of ChildPersonProjekt objects
     */
    public function getPersonProjektsJoinPerson(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildPersonProjektQuery::create(null, $criteria);
        $query->joinWith('Person', $joinBehavior);

        return $this->getPersonProjekts($query, $con);
    }

    /**
     * Clears out the collPeople collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPeople()
     */
    public function clearPeople()
    {
        $this->collPeople = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collPeople crossRef collection.
     *
     * By default this just sets the collPeople collection to an empty collection (like clearPeople());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initPeople()
    {
        $this->collPeople = new ObjectCollection();
        $this->collPeoplePartial = true;

        $this->collPeople->setModel('\Person');
    }

    /**
     * Checks if the collPeople collection is loaded.
     *
     * @return bool
     */
    public function isPeopleLoaded()
    {
        return null !== $this->collPeople;
    }

    /**
     * Gets a collection of ChildPerson objects related by a many-to-many relationship
     * to the current object by way of the person_projekt cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProjekt is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildPerson[] List of ChildPerson objects
     */
    public function getPeople(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPeoplePartial && !$this->isNew();
        if (null === $this->collPeople || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collPeople) {
                    $this->initPeople();
                }
            } else {

                $query = ChildPersonQuery::create(null, $criteria)
                    ->filterByProjekt($this);
                $collPeople = $query->find($con);
                if (null !== $criteria) {
                    return $collPeople;
                }

                if ($partial && $this->collPeople) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collPeople as $obj) {
                        if (!$collPeople->contains($obj)) {
                            $collPeople[] = $obj;
                        }
                    }
                }

                $this->collPeople = $collPeople;
                $this->collPeoplePartial = false;
            }
        }

        return $this->collPeople;
    }

    /**
     * Sets a collection of Person objects related by a many-to-many relationship
     * to the current object by way of the person_projekt cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $people A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildProjekt The current object (for fluent API support)
     */
    public function setPeople(Collection $people, ConnectionInterface $con = null)
    {
        $this->clearPeople();
        $currentPeople = $this->getPeople();

        $peopleScheduledForDeletion = $currentPeople->diff($people);

        foreach ($peopleScheduledForDeletion as $toDelete) {
            $this->removePerson($toDelete);
        }

        foreach ($people as $person) {
            if (!$currentPeople->contains($person)) {
                $this->doAddPerson($person);
            }
        }

        $this->collPeoplePartial = false;
        $this->collPeople = $people;

        return $this;
    }

    /**
     * Gets the number of Person objects related by a many-to-many relationship
     * to the current object by way of the person_projekt cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Person objects
     */
    public function countPeople(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPeoplePartial && !$this->isNew();
        if (null === $this->collPeople || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPeople) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getPeople());
                }

                $query = ChildPersonQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByProjekt($this)
                    ->count($con);
            }
        } else {
            return count($this->collPeople);
        }
    }

    /**
     * Associate a ChildPerson to this object
     * through the person_projekt cross reference table.
     *
     * @param ChildPerson $person
     * @return ChildProjekt The current object (for fluent API support)
     */
    public function addPerson(ChildPerson $person)
    {
        if ($this->collPeople === null) {
            $this->initPeople();
        }

        if (!$this->getPeople()->contains($person)) {
            // only add it if the **same** object is not already associated
            $this->collPeople->push($person);
            $this->doAddPerson($person);
        }

        return $this;
    }

    /**
     *
     * @param ChildPerson $person
     */
    protected function doAddPerson(ChildPerson $person)
    {
        $personProjekt = new ChildPersonProjekt();

        $personProjekt->setPerson($person);

        $personProjekt->setProjekt($this);

        $this->addPersonProjekt($personProjekt);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$person->isProjektsLoaded()) {
            $person->initProjekts();
            $person->getProjekts()->push($this);
        } elseif (!$person->getProjekts()->contains($this)) {
            $person->getProjekts()->push($this);
        }

    }

    /**
     * Remove person of this object
     * through the person_projekt cross reference table.
     *
     * @param ChildPerson $person
     * @return ChildProjekt The current object (for fluent API support)
     */
    public function removePerson(ChildPerson $person)
    {
        if ($this->getPeople()->contains($person)) { $personProjekt = new ChildPersonProjekt();

            $personProjekt->setPerson($person);
            if ($person->isProjektsLoaded()) {
                //remove the back reference if available
                $person->getProjekts()->removeObject($this);
            }

            $personProjekt->setProjekt($this);
            $this->removePersonProjekt(clone $personProjekt);
            $personProjekt->clear();

            $this->collPeople->remove($this->collPeople->search($person));

            if (null === $this->peopleScheduledForDeletion) {
                $this->peopleScheduledForDeletion = clone $this->collPeople;
                $this->peopleScheduledForDeletion->clear();
            }

            $this->peopleScheduledForDeletion->push($person);
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
        $this->id = null;
        $this->name = null;
        $this->sdate = null;
        $this->edate = null;
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
            if ($this->collNotizs) {
                foreach ($this->collNotizs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPersonProjekts) {
                foreach ($this->collPersonProjekts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPeople) {
                foreach ($this->collPeople as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collNotizs = null;
        $this->collPersonProjekts = null;
        $this->collPeople = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProjektTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildProjekt The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ProjektTableMap::COL_UPDATED_AT] = true;

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
