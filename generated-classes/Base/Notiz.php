<?php

namespace Base;

use \Notiz as ChildNotiz;
use \NotizQuery as ChildNotizQuery;
use \Person as ChildPerson;
use \PersonQuery as ChildPersonQuery;
use \Projekt as ChildProjekt;
use \ProjektQuery as ChildProjektQuery;
use \Rezept as ChildRezept;
use \RezeptNotiz as ChildRezeptNotiz;
use \RezeptNotizQuery as ChildRezeptNotizQuery;
use \RezeptQuery as ChildRezeptQuery;
use \ToDoNotiz as ChildToDoNotiz;
use \ToDoNotizQuery as ChildToDoNotizQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\NotizTableMap;
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
 * Base class that represents a row from the 'notiz' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Notiz implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\NotizTableMap';


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
     * The value for the besitzer_id field.
     * @var        int
     */
    protected $besitzer_id;

    /**
     * The value for the projekt_id field.
     * @var        int
     */
    protected $projekt_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the text field.
     * @var        string
     */
    protected $text;

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
     * @var        ChildPerson
     */
    protected $aBesitzer;

    /**
     * @var        ChildProjekt
     */
    protected $aProjekt;

    /**
     * @var        ObjectCollection|ChildToDoNotiz[] Collection to store aggregation of ChildToDoNotiz objects.
     */
    protected $collToDoNotizs;
    protected $collToDoNotizsPartial;

    /**
     * @var        ObjectCollection|ChildRezept[] Collection to store aggregation of ChildRezept objects.
     */
    protected $collRezepts;
    protected $collRezeptsPartial;

    /**
     * @var        ObjectCollection|ChildRezeptNotiz[] Collection to store aggregation of ChildRezeptNotiz objects.
     */
    protected $collRezeptNotizs;
    protected $collRezeptNotizsPartial;

    /**
     * @var        ObjectCollection|ChildRezept[] Cross Collection to store aggregation of ChildRezept objects.
     */
    protected $collRezepts;

    /**
     * @var bool
     */
    protected $collRezeptsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRezept[]
     */
    protected $rezeptsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildToDoNotiz[]
     */
    protected $toDoNotizsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRezept[]
     */
    protected $rezeptsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRezeptNotiz[]
     */
    protected $rezeptNotizsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Notiz object.
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
     * Compares this with another <code>Notiz</code> instance.  If
     * <code>obj</code> is an instance of <code>Notiz</code>, delegates to
     * <code>equals(Notiz)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Notiz The current object, for fluid interface
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
     * Get the [besitzer_id] column value.
     *
     * @return int
     */
    public function getBesitzerId()
    {
        return $this->besitzer_id;
    }

    /**
     * Get the [projekt_id] column value.
     *
     * @return int
     */
    public function getProjektId()
    {
        return $this->projekt_id;
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
     * Get the [text] column value.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[NotizTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [besitzer_id] column.
     *
     * @param  int $v new value
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setBesitzerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->besitzer_id !== $v) {
            $this->besitzer_id = $v;
            $this->modifiedColumns[NotizTableMap::COL_BESITZER_ID] = true;
        }

        if ($this->aBesitzer !== null && $this->aBesitzer->getId() !== $v) {
            $this->aBesitzer = null;
        }

        return $this;
    } // setBesitzerId()

    /**
     * Set the value of [projekt_id] column.
     *
     * @param  int $v new value
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setProjektId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->projekt_id !== $v) {
            $this->projekt_id = $v;
            $this->modifiedColumns[NotizTableMap::COL_PROJEKT_ID] = true;
        }

        if ($this->aProjekt !== null && $this->aProjekt->getId() !== $v) {
            $this->aProjekt = null;
        }

        return $this;
    } // setProjektId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[NotizTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [text] column.
     *
     * @param  string $v new value
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setText($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->text !== $v) {
            $this->text = $v;
            $this->modifiedColumns[NotizTableMap::COL_TEXT] = true;
        }

        return $this;
    } // setText()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[NotizTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[NotizTableMap::COL_UPDATED_AT] = true;
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : NotizTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : NotizTableMap::translateFieldName('BesitzerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->besitzer_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : NotizTableMap::translateFieldName('ProjektId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->projekt_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : NotizTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : NotizTableMap::translateFieldName('Text', TableMap::TYPE_PHPNAME, $indexType)];
            $this->text = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : NotizTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : NotizTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = NotizTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Notiz'), 0, $e);
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
        if ($this->aBesitzer !== null && $this->besitzer_id !== $this->aBesitzer->getId()) {
            $this->aBesitzer = null;
        }
        if ($this->aProjekt !== null && $this->projekt_id !== $this->aProjekt->getId()) {
            $this->aProjekt = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(NotizTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildNotizQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aBesitzer = null;
            $this->aProjekt = null;
            $this->collToDoNotizs = null;

            $this->collRezepts = null;

            $this->collRezeptNotizs = null;

            $this->collRezepts = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Notiz::setDeleted()
     * @see Notiz::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotizTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildNotizQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(NotizTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(NotizTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(NotizTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(NotizTableMap::COL_UPDATED_AT)) {
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
                NotizTableMap::addInstanceToPool($this);
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

            if ($this->aBesitzer !== null) {
                if ($this->aBesitzer->isModified() || $this->aBesitzer->isNew()) {
                    $affectedRows += $this->aBesitzer->save($con);
                }
                $this->setBesitzer($this->aBesitzer);
            }

            if ($this->aProjekt !== null) {
                if ($this->aProjekt->isModified() || $this->aProjekt->isNew()) {
                    $affectedRows += $this->aProjekt->save($con);
                }
                $this->setProjekt($this->aProjekt);
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

            if ($this->rezeptsScheduledForDeletion !== null) {
                if (!$this->rezeptsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->rezeptsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[1] = $this->getId();
                        $entryPk[0] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \RezeptNotizQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->rezeptsScheduledForDeletion = null;
                }

            }

            if ($this->collRezepts) {
                foreach ($this->collRezepts as $rezept) {
                    if (!$rezept->isDeleted() && ($rezept->isNew() || $rezept->isModified())) {
                        $rezept->save($con);
                    }
                }
            }


            if ($this->toDoNotizsScheduledForDeletion !== null) {
                if (!$this->toDoNotizsScheduledForDeletion->isEmpty()) {
                    \ToDoNotizQuery::create()
                        ->filterByPrimaryKeys($this->toDoNotizsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->toDoNotizsScheduledForDeletion = null;
                }
            }

            if ($this->collToDoNotizs !== null) {
                foreach ($this->collToDoNotizs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->rezeptsScheduledForDeletion !== null) {
                if (!$this->rezeptsScheduledForDeletion->isEmpty()) {
                    \RezeptQuery::create()
                        ->filterByPrimaryKeys($this->rezeptsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->rezeptsScheduledForDeletion = null;
                }
            }

            if ($this->collRezepts !== null) {
                foreach ($this->collRezepts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
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

        $this->modifiedColumns[NotizTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . NotizTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(NotizTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(NotizTableMap::COL_BESITZER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'besitzer_id';
        }
        if ($this->isColumnModified(NotizTableMap::COL_PROJEKT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'projekt_id';
        }
        if ($this->isColumnModified(NotizTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(NotizTableMap::COL_TEXT)) {
            $modifiedColumns[':p' . $index++]  = 'text';
        }
        if ($this->isColumnModified(NotizTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(NotizTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO notiz (%s) VALUES (%s)',
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
                    case 'besitzer_id':
                        $stmt->bindValue($identifier, $this->besitzer_id, PDO::PARAM_INT);
                        break;
                    case 'projekt_id':
                        $stmt->bindValue($identifier, $this->projekt_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'text':
                        $stmt->bindValue($identifier, $this->text, PDO::PARAM_STR);
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
        $pos = NotizTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getBesitzerId();
                break;
            case 2:
                return $this->getProjektId();
                break;
            case 3:
                return $this->getName();
                break;
            case 4:
                return $this->getText();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
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

        if (isset($alreadyDumpedObjects['Notiz'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Notiz'][$this->hashCode()] = true;
        $keys = NotizTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getBesitzerId(),
            $keys[2] => $this->getProjektId(),
            $keys[3] => $this->getName(),
            $keys[4] => $this->getText(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aBesitzer) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'person';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'person';
                        break;
                    default:
                        $key = 'Person';
                }

                $result[$key] = $this->aBesitzer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProjekt) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'projekt';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'projekt';
                        break;
                    default:
                        $key = 'Projekt';
                }

                $result[$key] = $this->aProjekt->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collToDoNotizs) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'toDoNotizs';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'todo_notizs';
                        break;
                    default:
                        $key = 'ToDoNotizs';
                }

                $result[$key] = $this->collToDoNotizs->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRezepts) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'rezepts';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'rezepts';
                        break;
                    default:
                        $key = 'Rezepts';
                }

                $result[$key] = $this->collRezepts->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Notiz
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = NotizTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Notiz
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setBesitzerId($value);
                break;
            case 2:
                $this->setProjektId($value);
                break;
            case 3:
                $this->setName($value);
                break;
            case 4:
                $this->setText($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
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
        $keys = NotizTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setBesitzerId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setProjektId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setName($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setText($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setCreatedAt($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setUpdatedAt($arr[$keys[6]]);
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
     * @return $this|\Notiz The current object, for fluid interface
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
        $criteria = new Criteria(NotizTableMap::DATABASE_NAME);

        if ($this->isColumnModified(NotizTableMap::COL_ID)) {
            $criteria->add(NotizTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(NotizTableMap::COL_BESITZER_ID)) {
            $criteria->add(NotizTableMap::COL_BESITZER_ID, $this->besitzer_id);
        }
        if ($this->isColumnModified(NotizTableMap::COL_PROJEKT_ID)) {
            $criteria->add(NotizTableMap::COL_PROJEKT_ID, $this->projekt_id);
        }
        if ($this->isColumnModified(NotizTableMap::COL_NAME)) {
            $criteria->add(NotizTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(NotizTableMap::COL_TEXT)) {
            $criteria->add(NotizTableMap::COL_TEXT, $this->text);
        }
        if ($this->isColumnModified(NotizTableMap::COL_CREATED_AT)) {
            $criteria->add(NotizTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(NotizTableMap::COL_UPDATED_AT)) {
            $criteria->add(NotizTableMap::COL_UPDATED_AT, $this->updated_at);
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
        $criteria = ChildNotizQuery::create();
        $criteria->add(NotizTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Notiz (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setBesitzerId($this->getBesitzerId());
        $copyObj->setProjektId($this->getProjektId());
        $copyObj->setName($this->getName());
        $copyObj->setText($this->getText());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getToDoNotizs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addToDoNotiz($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRezepts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRezept($relObj->copy($deepCopy));
                }
            }

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
     * @return \Notiz Clone of current object.
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
     * Declares an association between this object and a ChildPerson object.
     *
     * @param  ChildPerson $v
     * @return $this|\Notiz The current object (for fluent API support)
     * @throws PropelException
     */
    public function setBesitzer(ChildPerson $v = null)
    {
        if ($v === null) {
            $this->setBesitzerId(NULL);
        } else {
            $this->setBesitzerId($v->getId());
        }

        $this->aBesitzer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildPerson object, it will not be re-added.
        if ($v !== null) {
            $v->addNotiz($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildPerson object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildPerson The associated ChildPerson object.
     * @throws PropelException
     */
    public function getBesitzer(ConnectionInterface $con = null)
    {
        if ($this->aBesitzer === null && ($this->besitzer_id !== null)) {
            $this->aBesitzer = ChildPersonQuery::create()->findPk($this->besitzer_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aBesitzer->addNotizs($this);
             */
        }

        return $this->aBesitzer;
    }

    /**
     * Declares an association between this object and a ChildProjekt object.
     *
     * @param  ChildProjekt $v
     * @return $this|\Notiz The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProjekt(ChildProjekt $v = null)
    {
        if ($v === null) {
            $this->setProjektId(NULL);
        } else {
            $this->setProjektId($v->getId());
        }

        $this->aProjekt = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProjekt object, it will not be re-added.
        if ($v !== null) {
            $v->addNotiz($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildProjekt object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildProjekt The associated ChildProjekt object.
     * @throws PropelException
     */
    public function getProjekt(ConnectionInterface $con = null)
    {
        if ($this->aProjekt === null && ($this->projekt_id !== null)) {
            $this->aProjekt = ChildProjektQuery::create()->findPk($this->projekt_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProjekt->addNotizs($this);
             */
        }

        return $this->aProjekt;
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
        if ('ToDoNotiz' == $relationName) {
            return $this->initToDoNotizs();
        }
        if ('Rezept' == $relationName) {
            return $this->initRezepts();
        }
        if ('RezeptNotiz' == $relationName) {
            return $this->initRezeptNotizs();
        }
    }

    /**
     * Clears out the collToDoNotizs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addToDoNotizs()
     */
    public function clearToDoNotizs()
    {
        $this->collToDoNotizs = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collToDoNotizs collection loaded partially.
     */
    public function resetPartialToDoNotizs($v = true)
    {
        $this->collToDoNotizsPartial = $v;
    }

    /**
     * Initializes the collToDoNotizs collection.
     *
     * By default this just sets the collToDoNotizs collection to an empty array (like clearcollToDoNotizs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initToDoNotizs($overrideExisting = true)
    {
        if (null !== $this->collToDoNotizs && !$overrideExisting) {
            return;
        }
        $this->collToDoNotizs = new ObjectCollection();
        $this->collToDoNotizs->setModel('\ToDoNotiz');
    }

    /**
     * Gets an array of ChildToDoNotiz objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildNotiz is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildToDoNotiz[] List of ChildToDoNotiz objects
     * @throws PropelException
     */
    public function getToDoNotizs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collToDoNotizsPartial && !$this->isNew();
        if (null === $this->collToDoNotizs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collToDoNotizs) {
                // return empty collection
                $this->initToDoNotizs();
            } else {
                $collToDoNotizs = ChildToDoNotizQuery::create(null, $criteria)
                    ->filterByNotiz($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collToDoNotizsPartial && count($collToDoNotizs)) {
                        $this->initToDoNotizs(false);

                        foreach ($collToDoNotizs as $obj) {
                            if (false == $this->collToDoNotizs->contains($obj)) {
                                $this->collToDoNotizs->append($obj);
                            }
                        }

                        $this->collToDoNotizsPartial = true;
                    }

                    return $collToDoNotizs;
                }

                if ($partial && $this->collToDoNotizs) {
                    foreach ($this->collToDoNotizs as $obj) {
                        if ($obj->isNew()) {
                            $collToDoNotizs[] = $obj;
                        }
                    }
                }

                $this->collToDoNotizs = $collToDoNotizs;
                $this->collToDoNotizsPartial = false;
            }
        }

        return $this->collToDoNotizs;
    }

    /**
     * Sets a collection of ChildToDoNotiz objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $toDoNotizs A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildNotiz The current object (for fluent API support)
     */
    public function setToDoNotizs(Collection $toDoNotizs, ConnectionInterface $con = null)
    {
        /** @var ChildToDoNotiz[] $toDoNotizsToDelete */
        $toDoNotizsToDelete = $this->getToDoNotizs(new Criteria(), $con)->diff($toDoNotizs);


        $this->toDoNotizsScheduledForDeletion = $toDoNotizsToDelete;

        foreach ($toDoNotizsToDelete as $toDoNotizRemoved) {
            $toDoNotizRemoved->setNotiz(null);
        }

        $this->collToDoNotizs = null;
        foreach ($toDoNotizs as $toDoNotiz) {
            $this->addToDoNotiz($toDoNotiz);
        }

        $this->collToDoNotizs = $toDoNotizs;
        $this->collToDoNotizsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ToDoNotiz objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ToDoNotiz objects.
     * @throws PropelException
     */
    public function countToDoNotizs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collToDoNotizsPartial && !$this->isNew();
        if (null === $this->collToDoNotizs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collToDoNotizs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getToDoNotizs());
            }

            $query = ChildToDoNotizQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByNotiz($this)
                ->count($con);
        }

        return count($this->collToDoNotizs);
    }

    /**
     * Method called to associate a ChildToDoNotiz object to this object
     * through the ChildToDoNotiz foreign key attribute.
     *
     * @param  ChildToDoNotiz $l ChildToDoNotiz
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function addToDoNotiz(ChildToDoNotiz $l)
    {
        if ($this->collToDoNotizs === null) {
            $this->initToDoNotizs();
            $this->collToDoNotizsPartial = true;
        }

        if (!$this->collToDoNotizs->contains($l)) {
            $this->doAddToDoNotiz($l);
        }

        return $this;
    }

    /**
     * @param ChildToDoNotiz $toDoNotiz The ChildToDoNotiz object to add.
     */
    protected function doAddToDoNotiz(ChildToDoNotiz $toDoNotiz)
    {
        $this->collToDoNotizs[]= $toDoNotiz;
        $toDoNotiz->setNotiz($this);
    }

    /**
     * @param  ChildToDoNotiz $toDoNotiz The ChildToDoNotiz object to remove.
     * @return $this|ChildNotiz The current object (for fluent API support)
     */
    public function removeToDoNotiz(ChildToDoNotiz $toDoNotiz)
    {
        if ($this->getToDoNotizs()->contains($toDoNotiz)) {
            $pos = $this->collToDoNotizs->search($toDoNotiz);
            $this->collToDoNotizs->remove($pos);
            if (null === $this->toDoNotizsScheduledForDeletion) {
                $this->toDoNotizsScheduledForDeletion = clone $this->collToDoNotizs;
                $this->toDoNotizsScheduledForDeletion->clear();
            }
            $this->toDoNotizsScheduledForDeletion[]= clone $toDoNotiz;
            $toDoNotiz->setNotiz(null);
        }

        return $this;
    }

    /**
     * Clears out the collRezepts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRezepts()
     */
    public function clearRezepts()
    {
        $this->collRezepts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collRezepts collection loaded partially.
     */
    public function resetPartialRezepts($v = true)
    {
        $this->collRezeptsPartial = $v;
    }

    /**
     * Initializes the collRezepts collection.
     *
     * By default this just sets the collRezepts collection to an empty array (like clearcollRezepts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRezepts($overrideExisting = true)
    {
        if (null !== $this->collRezepts && !$overrideExisting) {
            return;
        }
        $this->collRezepts = new ObjectCollection();
        $this->collRezepts->setModel('\Rezept');
    }

    /**
     * Gets an array of ChildRezept objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildNotiz is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildRezept[] List of ChildRezept objects
     * @throws PropelException
     */
    public function getRezepts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptsPartial && !$this->isNew();
        if (null === $this->collRezepts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRezepts) {
                // return empty collection
                $this->initRezepts();
            } else {
                $collRezepts = ChildRezeptQuery::create(null, $criteria)
                    ->filterByNotiz($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collRezeptsPartial && count($collRezepts)) {
                        $this->initRezepts(false);

                        foreach ($collRezepts as $obj) {
                            if (false == $this->collRezepts->contains($obj)) {
                                $this->collRezepts->append($obj);
                            }
                        }

                        $this->collRezeptsPartial = true;
                    }

                    return $collRezepts;
                }

                if ($partial && $this->collRezepts) {
                    foreach ($this->collRezepts as $obj) {
                        if ($obj->isNew()) {
                            $collRezepts[] = $obj;
                        }
                    }
                }

                $this->collRezepts = $collRezepts;
                $this->collRezeptsPartial = false;
            }
        }

        return $this->collRezepts;
    }

    /**
     * Sets a collection of ChildRezept objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $rezepts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildNotiz The current object (for fluent API support)
     */
    public function setRezepts(Collection $rezepts, ConnectionInterface $con = null)
    {
        /** @var ChildRezept[] $rezeptsToDelete */
        $rezeptsToDelete = $this->getRezepts(new Criteria(), $con)->diff($rezepts);


        $this->rezeptsScheduledForDeletion = $rezeptsToDelete;

        foreach ($rezeptsToDelete as $rezeptRemoved) {
            $rezeptRemoved->setNotiz(null);
        }

        $this->collRezepts = null;
        foreach ($rezepts as $rezept) {
            $this->addRezept($rezept);
        }

        $this->collRezepts = $rezepts;
        $this->collRezeptsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Rezept objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Rezept objects.
     * @throws PropelException
     */
    public function countRezepts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptsPartial && !$this->isNew();
        if (null === $this->collRezepts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRezepts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRezepts());
            }

            $query = ChildRezeptQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByNotiz($this)
                ->count($con);
        }

        return count($this->collRezepts);
    }

    /**
     * Method called to associate a ChildRezept object to this object
     * through the ChildRezept foreign key attribute.
     *
     * @param  ChildRezept $l ChildRezept
     * @return $this|\Notiz The current object (for fluent API support)
     */
    public function addRezept(ChildRezept $l)
    {
        if ($this->collRezepts === null) {
            $this->initRezepts();
            $this->collRezeptsPartial = true;
        }

        if (!$this->collRezepts->contains($l)) {
            $this->doAddRezept($l);
        }

        return $this;
    }

    /**
     * @param ChildRezept $rezept The ChildRezept object to add.
     */
    protected function doAddRezept(ChildRezept $rezept)
    {
        $this->collRezepts[]= $rezept;
        $rezept->setNotiz($this);
    }

    /**
     * @param  ChildRezept $rezept The ChildRezept object to remove.
     * @return $this|ChildNotiz The current object (for fluent API support)
     */
    public function removeRezept(ChildRezept $rezept)
    {
        if ($this->getRezepts()->contains($rezept)) {
            $pos = $this->collRezepts->search($rezept);
            $this->collRezepts->remove($pos);
            if (null === $this->rezeptsScheduledForDeletion) {
                $this->rezeptsScheduledForDeletion = clone $this->collRezepts;
                $this->rezeptsScheduledForDeletion->clear();
            }
            $this->rezeptsScheduledForDeletion[]= clone $rezept;
            $rezept->setNotiz(null);
        }

        return $this;
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
     * If this ChildNotiz is new, it will return
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
                    ->filterByNotiz($this)
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
     * @return $this|ChildNotiz The current object (for fluent API support)
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
            $rezeptNotizRemoved->setNotiz(null);
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
                ->filterByNotiz($this)
                ->count($con);
        }

        return count($this->collRezeptNotizs);
    }

    /**
     * Method called to associate a ChildRezeptNotiz object to this object
     * through the ChildRezeptNotiz foreign key attribute.
     *
     * @param  ChildRezeptNotiz $l ChildRezeptNotiz
     * @return $this|\Notiz The current object (for fluent API support)
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
        $rezeptNotiz->setNotiz($this);
    }

    /**
     * @param  ChildRezeptNotiz $rezeptNotiz The ChildRezeptNotiz object to remove.
     * @return $this|ChildNotiz The current object (for fluent API support)
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
            $rezeptNotiz->setNotiz(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Notiz is new, it will return
     * an empty collection; or if this Notiz has previously
     * been saved, it will retrieve related RezeptNotizs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Notiz.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildRezeptNotiz[] List of ChildRezeptNotiz objects
     */
    public function getRezeptNotizsJoinRezept(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildRezeptNotizQuery::create(null, $criteria);
        $query->joinWith('Rezept', $joinBehavior);

        return $this->getRezeptNotizs($query, $con);
    }

    /**
     * Clears out the collRezepts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRezepts()
     */
    public function clearRezepts()
    {
        $this->collRezepts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collRezepts crossRef collection.
     *
     * By default this just sets the collRezepts collection to an empty collection (like clearRezepts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initRezepts()
    {
        $this->collRezepts = new ObjectCollection();
        $this->collRezeptsPartial = true;

        $this->collRezepts->setModel('\Rezept');
    }

    /**
     * Checks if the collRezepts collection is loaded.
     *
     * @return bool
     */
    public function isRezeptsLoaded()
    {
        return null !== $this->collRezepts;
    }

    /**
     * Gets a collection of ChildRezept objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildNotiz is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildRezept[] List of ChildRezept objects
     */
    public function getRezepts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptsPartial && !$this->isNew();
        if (null === $this->collRezepts || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collRezepts) {
                    $this->initRezepts();
                }
            } else {

                $query = ChildRezeptQuery::create(null, $criteria)
                    ->filterByNotiz($this);
                $collRezepts = $query->find($con);
                if (null !== $criteria) {
                    return $collRezepts;
                }

                if ($partial && $this->collRezepts) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collRezepts as $obj) {
                        if (!$collRezepts->contains($obj)) {
                            $collRezepts[] = $obj;
                        }
                    }
                }

                $this->collRezepts = $collRezepts;
                $this->collRezeptsPartial = false;
            }
        }

        return $this->collRezepts;
    }

    /**
     * Sets a collection of Rezept objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $rezepts A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildNotiz The current object (for fluent API support)
     */
    public function setRezepts(Collection $rezepts, ConnectionInterface $con = null)
    {
        $this->clearRezepts();
        $currentRezepts = $this->getRezepts();

        $rezeptsScheduledForDeletion = $currentRezepts->diff($rezepts);

        foreach ($rezeptsScheduledForDeletion as $toDelete) {
            $this->removeRezept($toDelete);
        }

        foreach ($rezepts as $rezept) {
            if (!$currentRezepts->contains($rezept)) {
                $this->doAddRezept($rezept);
            }
        }

        $this->collRezeptsPartial = false;
        $this->collRezepts = $rezepts;

        return $this;
    }

    /**
     * Gets the number of Rezept objects related by a many-to-many relationship
     * to the current object by way of the rezept_notiz cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Rezept objects
     */
    public function countRezepts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRezeptsPartial && !$this->isNew();
        if (null === $this->collRezepts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRezepts) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getRezepts());
                }

                $query = ChildRezeptQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByNotiz($this)
                    ->count($con);
            }
        } else {
            return count($this->collRezepts);
        }
    }

    /**
     * Associate a ChildRezept to this object
     * through the rezept_notiz cross reference table.
     *
     * @param ChildRezept $rezept
     * @return ChildNotiz The current object (for fluent API support)
     */
    public function addRezept(ChildRezept $rezept)
    {
        if ($this->collRezepts === null) {
            $this->initRezepts();
        }

        if (!$this->getRezepts()->contains($rezept)) {
            // only add it if the **same** object is not already associated
            $this->collRezepts->push($rezept);
            $this->doAddRezept($rezept);
        }

        return $this;
    }

    /**
     *
     * @param ChildRezept $rezept
     */
    protected function doAddRezept(ChildRezept $rezept)
    {
        $rezeptNotiz = new ChildRezeptNotiz();

        $rezeptNotiz->setRezept($rezept);

        $rezeptNotiz->setNotiz($this);

        $this->addRezeptNotiz($rezeptNotiz);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$rezept->isNotizsLoaded()) {
            $rezept->initNotizs();
            $rezept->getNotizs()->push($this);
        } elseif (!$rezept->getNotizs()->contains($this)) {
            $rezept->getNotizs()->push($this);
        }

    }

    /**
     * Remove rezept of this object
     * through the rezept_notiz cross reference table.
     *
     * @param ChildRezept $rezept
     * @return ChildNotiz The current object (for fluent API support)
     */
    public function removeRezept(ChildRezept $rezept)
    {
        if ($this->getRezepts()->contains($rezept)) { $rezeptNotiz = new ChildRezeptNotiz();

            $rezeptNotiz->setRezept($rezept);
            if ($rezept->isNotizsLoaded()) {
                //remove the back reference if available
                $rezept->getNotizs()->removeObject($this);
            }

            $rezeptNotiz->setNotiz($this);
            $this->removeRezeptNotiz(clone $rezeptNotiz);
            $rezeptNotiz->clear();

            $this->collRezepts->remove($this->collRezepts->search($rezept));

            if (null === $this->rezeptsScheduledForDeletion) {
                $this->rezeptsScheduledForDeletion = clone $this->collRezepts;
                $this->rezeptsScheduledForDeletion->clear();
            }

            $this->rezeptsScheduledForDeletion->push($rezept);
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
        if (null !== $this->aBesitzer) {
            $this->aBesitzer->removeNotiz($this);
        }
        if (null !== $this->aProjekt) {
            $this->aProjekt->removeNotiz($this);
        }
        $this->id = null;
        $this->besitzer_id = null;
        $this->projekt_id = null;
        $this->name = null;
        $this->text = null;
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
            if ($this->collToDoNotizs) {
                foreach ($this->collToDoNotizs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRezepts) {
                foreach ($this->collRezepts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRezeptNotizs) {
                foreach ($this->collRezeptNotizs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRezepts) {
                foreach ($this->collRezepts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collToDoNotizs = null;
        $this->collRezepts = null;
        $this->collRezeptNotizs = null;
        $this->collRezepts = null;
        $this->aBesitzer = null;
        $this->aProjekt = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(NotizTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildNotiz The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[NotizTableMap::COL_UPDATED_AT] = true;

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
