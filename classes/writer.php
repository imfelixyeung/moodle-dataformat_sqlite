<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace dataformat_sqlite;

use core\dataformat\base;
use core_text;
use PDO;
use PDOStatement;
use function count;

/**
 * SQLite Database format writer.
 *
 * @package   dataformat_sqlite
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends base {
    /** @var string */
    protected $mimetype = 'application/vnd.sqlite3';

    /** @var string $extension */
    protected $extension = '.sqlite';

    /** @var string[] $columns */
    protected $columns = [];

    /** @var string */
    protected string $sqlitepath;

    /** @var string */
    protected string $tablename = 'export';

    /** @var PDO */
    protected PDO $pdo;

    /** @var PDOStatement */
    protected PDOStatement $insertstatement;

    /**
     * Initialise the database that we will be adding data to.
     *
     * @param string[] $columns
     * @return void
     */
    public function start_sheet($columns) {
        $this->columns = array_map(
            fn($column) => core_text::strtolower(clean_param($column, PARAM_ALPHANUMEXT)),
            $columns,
        );

        $this->sqlitepath = make_request_directory() . '/export.sqlite';
        $this->create_database();
        $this->create_table();
        $this->prepare_insert_statement();
        return;
    }

    /**
     * Creates the PDO for SQLite database.
     * @return void
     */
    protected function create_database(): void {
        $this->pdo = new PDO("sqlite:$this->sqlitepath");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Creates the table for export.
     * @return void
     */
    protected function create_table(): void {
        $sqlcreatecolumns = [
            'row INTEGER PRIMARY KEY',
            ...array_map(fn($column) => "`col_$column` TEXT", $this->columns),
        ];
        $sqlcreatecolumnsstr = implode(', ', $sqlcreatecolumns);
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tablename} ($sqlcreatecolumnsstr)";
        $this->pdo->exec($sql);
        return;
    }

    /**
     * Creates the prepared statement for inserting data into the database.
     * @return void
     */
    protected function prepare_insert_statement(): void {
        $sqlinsertcolumns = implode(',', array_fill(0, count($this->columns) + 1, '?'));
        $this->insertstatement = $this->pdo->prepare("INSERT INTO {$this->tablename} VALUES ($sqlinsertcolumns)");
        return;
    }

    /**
     * Write a single record to DB.
     *
     * @param string[] $record
     * @param int $rownum
     * @return void
     */
    public function write_record($record, $rownum) {
        $this->insertstatement->execute([$rownum, ...$record]);
        return;
    }

    /**
     * Cleanup.
     *
     * @param string[] $columns
     * @return void
     */
    public function close_sheet($columns) {
        $this->pdo = null;
        $this->insertstatement = null;
    }

    /**
     * Send the entire sqlite dump.
     * @return void
     */
    public function close_output() {
        if (ob_get_level() > 0) {
            echo file_get_contents($this->sqlitepath);
        } else {
            readfile($this->sqlitepath);
        }
        return;
    }
}
